<?php
class Calculator extends AppModel {
	public $name = 'Calculator';
	public $useTable = false;

	public function money_format($number) {
		if ($number < 0) {
			// Avoid "-$0" for values between 0 and -1
			$formatted = number_format(-1 * $number);
			return $formatted == 0 ? '$0' : '-$'.$formatted;
		} else {
			return '$'.number_format($number);
		}
	}

	// Takes a string and strips it to just a whole number
	// e.g. $50,000.99 -> 50000
	public function cleanNumber($number) {
		if ($pos_of_dp = strpos($number, '.') !== false) {
			// Remove cents (if decimal point found)
			$value = substr($number, 0, $pos_of_dp);
		}
		return preg_replace('/\D/', '', $number);
	}

	public function getStateIncomeTaxRate($state_id) {
		$this->setSource('tax_rates');
		$result = $this->find('first', array(
			'conditions' => array(
				'loc_type' => 'state',
				'loc_id' => $state_id,
				'category_id' => 2
			),
			'contain' => false,
			'fields' => array('value')
		));
		return $result ? $result['Calculator']['value'] : false;
    }

	public function getStateIncomeTax($agi, $state_id) {
		$rate = $this->getStateIncomeTaxRate($state_id);
		return $rate ? $agi * $rate / 100 : false;
	}

	public function getCountyIncomeTaxRate($county_id) {
		$this->setSource('tax_rates');
		$result = $this->find('first', array(
			'conditions' => array(
				'loc_type' => 'county',
				'loc_id' => $county_id,
				'category_id' => 1
			),
			'contain' => false,
			'fields' => array('value')
		));
		return $result ? $result['Calculator']['value'] : false;
	}

	public function getCountyIncomeTax($agi, $county_id) {
		$rate = $this->getCountyIncomeTaxRate($county_id);
		return $rate ? $agi * $rate / 100 : false;
	}

	/* 	HV: Home Value
	  	Adjustment = (HV < 75k) ? (60% of HV) : (HV - 45k)
		RV (Remainder Value) = HV - Adjustment
		SHD (Supplemental Homestead Deduction) = (RV <= 600k) ? (35% of RV) : (25% of RV)
		AHV (Assessed Home Value) = RV - SHD
		Property tax = AHV * Property tax rate
		Maximum Property tax = 1% of HV
		Property tax due = Lesser of Property tax or Maximum Property tax
	*/
	public function getPropertyTax($home_value, $county_id, $state_abbrev) {
		$net_ahv = $this->getNetAHV($home_value, $state_abbrev);
		$rate = $this->getPropertyTaxRate($county_id);
		$uncapped_value = $net_ahv * $rate / 100;
		if ($state_abbrev == 'IN') {
			$capped_value = $home_value * 0.01;
			return min($uncapped_value, $capped_value);
		} else {
			return $uncapped_value;
		}
	}

	// Assessed Home Value
	public function getNetAHV($home_value, $state_abbrev) {
		switch ($state_abbrev) {
			case 'IN':
				// Remainder home value
				$rv = $this->getRV($home_value, $state_abbrev);
				// Supplemental homestead deduction
				$shd = $this->getSHD($home_value, $state_abbrev);
				return $rv - $shd;
			case 'IL':
				return $home_value;
		}
	}

	public function getAHVFormula($home_value, $state_abbrev) {
		switch ($state_abbrev) {
			case 'IN':
				return 'RHV - SHD';
			case 'IL':
				return '';
		}
	}

	// Remainder value (after standard deduction)
	public function getRV($home_value, $state_abbrev) {
		switch ($state_abbrev) {
			case 'IN':
				return ($home_value < 75000) ? ($home_value * .6) : ($home_value - 45000);
			case 'IL':
				return $home_value;
		}
	}

	// Supplemental homestead deduction
	public function getSHD($home_value, $state_abbrev) {
		switch ($state_abbrev) {
			case 'IN':
				$rv = $this->getRV($home_value, $state_abbrev);
				if ($rv <= 600000) {
					return ($rv * .35);
				} else {
					return (600000 * .35) + (($rv - 600000) * .25);
				}
			case 'IL':
				return 0;
		}
	}

	public function getSHDFormula($home_value, $state_abbrev) {
		switch ($state_abbrev) {
			case 'IN':
				$rv = $this->getRV($home_value, $state_abbrev);
				if ($rv <= 600000) {
					return '35% of RHV';
				} else {
					return '$210,000 + 25% of (RHV - $600,000)';
				}
			case 'IL':
				return '';
		}
	}

	public function getRHVFormula($home_value, $state_abbrev) {
		switch ($state_abbrev) {
			case 'IN':
				return ($home_value < 75000) ? "60% of home value" : "home value - $45,000";
			case 'IL':
				return '';
		}
	}

	public function getPropertyTaxRate($county_id) {
		$this->setSource('tax_rates');
		$result = $this->find('first', array(
			'conditions' => array(
				'loc_type' => 'county',
				'loc_id' => $county_id,
				'category_id' => 3
			),
			'contain' => false,
			'fields' => array('value')
		));
		return $result ? $result['Calculator']['value'] : false;
	}

	// This method bases the result entirely on income, rather than number of dependents
	public function getSalesTax($type, $income, $state_abbrev) {
		$expenditure_rate = $this->getExpenditureRate($type, $income) / 100;
		$aae = $income * $this->getAvgAnnualExpendituresPercent($income) / 100;
		$spent = $aae * $expenditure_rate;
		$tax_rate_range = $this->getSalesTaxRate($type, $state_abbrev);
		return array(
			'min' => $spent * $tax_rate_range['min'] / 100,
			'max' => $spent * $tax_rate_range['max'] / 100
		);
	}

	/*
	public function getSalesTax_usingDependents($type, $income, $dependents, $state_abbrev) {
		// Expenditure rates
		$expenditure_rates = array(
			'food at home' => array(
				// For 1 person in household (no dependents), 6.6%
				// For 2 people (1 + 1 dependent), 7%, etc.
				1 => 6.6, 2 => 7, 3 => 7.9, 4 => 7.9, 5 => 10
			),
			'food away from home' => array(
				1 => 5.1, 2 => 5.2, 3 => 5.4, 4 => 5.4, 5 => 5.8
			),
			'housekeeping supplies' => array(
				1 => 1.2, 2 => 1.5, 3 => 1.4, 4 => 1.2, 5 => 1.3
			),
			'apparel and services' => array(
				1 => 3.3, 2 => 3, 3 => 3.6, 4 => 3.9, 5 => 4.4
			)
		);

		$expenditures = $income * ($expenditure_rates[$type][$dependents + 1] / 100);
		if ($state_abbrev == 'IN') {
			if ($type == 'food at home') {
				$tax_rate_min = $tax_rate_max = 0;
			} else {
				$tax_rate_min = $tax_rate_max = 7;
			}
		} elseif ($state_abbrev == 'IL') {
			if ($type == 'food at home') {
				$tax_rate_min = $tax_rate_max = 1;
			} else {
				$tax_rate_min = 6.25;
				$tax_rate_max = 11.50;
			}
		}
		return array(
			'min' => $expenditures * $tax_rate_min / 100,
			'max' => $expenditures * $tax_rate_max / 100
		);
	}
	*/

	public function formatMinMaxValue($min, $max) {
		$min = $this->money_format($min);
		$max = $this->money_format($max);
		return ($min == $max) ? $min : "$min to $max";
	}

	public function getAvgAnnualExpendituresPercent($income) {
		/* 	Income range		% of income
			< 10,000 			750.98%
			10,000-14,999		172.58%
			15,000-19,999		135.25%
			20,000-29,999		118.12%
			30,000-39,999		103.48%
			40,000-49,999		88.42%
			50,000-69,999		82.87%
			70,000-79,999		77.53%
			80,000-99,999		72.99%
			100,000-119,999		70.13%
			120,000-149,999		64.73%
			>= 150,000			52.62%
		*/
		if ($income < 10000) {
			$percent = 750.98;
		} elseif ($income < 15000) {
			$percent = 172.58;
		} elseif ($income < 20000) {
			$percent = 135.25;
		} elseif ($income < 30000) {
			$percent = 118.12;
		} elseif ($income < 40000) {
			$percent = 103.48;
		} elseif ($income < 50000) {
			$percent = 88.42;
		} elseif ($income < 70000) {
			$percent = 82.87;
		} elseif ($income < 80000) {
			$percent = 77.53;
		} elseif ($income < 100000) {
			$percent = 72.99;
		} elseif ($income < 120000) {
			$percent = 70.13;
		} elseif ($income < 150000) {
			$percent = 64.73;
		} else {
			$percent = 52.62;
		}
		return $percent;
	}

	public function getAvgAnnualExpenditures($income) {
		return $income * $this->getAvgAnnualExpendituresPercent($income) / 100;
	}

	public function getExpenditureRate($type, $income) {
		if ($income < 10000) {
			$expenditure_rates = array(
				'food at home' 			=> 11.4,
				'food away from home' 	=> 5.45,
				'housekeeping supplies' => 1.45,
				'apparel and services' 	=> 3.95,
				'household furnishings and equipment' => 2.55,
				'personal care products' => 1.25
			);
		} elseif ($income < 15000) {
			$expenditure_rates = array(
				'food at home' 			=> 12.4,
				'food away from home' 	=> 4.7,
				'housekeeping supplies' => 1.7,
				'apparel and services' 	=> 5,
				'household furnishings and equipment' => 2.6,
				'personal care products' => 1.3
			);
		} elseif ($income < 20000) {
			$expenditure_rates = array(
				'food at home' 			=> 10.1,
				'food away from home' 	=> 4.1,
				'housekeeping supplies' => 1.6,
				'apparel and services' 	=> 3.2,
				'household furnishings and equipment' => 3,
				'personal care products' => 1.2
			);
		} elseif ($income < 30000) {
			$expenditure_rates = array(
				'food at home' 			=> 10.2,
				'food away from home' 	=> 4.8,
				'housekeeping supplies' => 1.7,
				'apparel and services' 	=> 3.7,
				'household furnishings and equipment' => 2.6,
				'personal care products' => 1.3
			);
		} elseif ($income < 40000) {
			$expenditure_rates = array(
				'food at home' 			=> 8.2,
				'food away from home' 	=> 4.9,
				'housekeeping supplies' => 1.4,
				'apparel and services' 	=> 3.4,
				'household furnishings and equipment' => 2.8,
				'personal care products' => 1.2
			);
		} elseif ($income < 50000) {
			$expenditure_rates = array(
				'food at home' 			=> 8.5,
				'food away from home' 	=> 5.1,
				'housekeeping supplies' => 1.4,
				'apparel and services' 	=> 3.4,
				'household furnishings and equipment' => 2.7,
				'personal care products' => 1.2
			);
		} elseif ($income < 70000) {
			$expenditure_rates = array(
				'food at home' 			=> 7.7,
				'food away from home' 	=> 5.5,
				'housekeeping supplies' => 1.3,
				'apparel and services' 	=> 3.3,
				'household furnishings and equipment' => 2.9,
				'personal care products' => 1.2
			);
		} elseif ($income < 80000) {
			$expenditure_rates = array(
				'food at home' 			=> 7.7,
				'food away from home' 	=> 5.8,
				'housekeeping supplies' => 1.3,
				'apparel and services' 	=> 3.1,
				'household furnishings and equipment' => 3,
				'personal care products' => 1.1
			);
		} elseif ($income < 100000) {
			$expenditure_rates = array(
				'food at home' 			=> 7.2,
				'food away from home' 	=> 5.6,
				'housekeeping supplies' => 1.3,
				'apparel and services' 	=> 3.7,
				'household furnishings and equipment' => 3.5,
				'personal care products' => 1.2
			);
		} elseif ($income < 120000) {
			$expenditure_rates = array(
				'food at home' 			=> 7,
				'food away from home' 	=> 5.7,
				'housekeeping supplies' => 1.2,
				'apparel and services' 	=> 3.4,
				'household furnishings and equipment' => 3.4,
				'personal care products' => 1.3
			);
		} elseif ($income < 150000) {
			$expenditure_rates = array(
				'food at home' 			=> 6.1,
				'food away from home' 	=> 5.5,
				'housekeeping supplies' => 1.2,
				'apparel and services' 	=> 3.4,
				'household furnishings and equipment' => 3.4,
				'personal care products' => 1.3
			);
		} else {
			$expenditure_rates = array(
				'food at home' 			=> 5.3,
				'food away from home' 	=> 5.4,
				'housekeeping supplies' => 1.2,
				'apparel and services' 	=> 3.6,
				'household furnishings and equipment' => 3.4,
				'personal care products' => 1.2
			);
		}
		return $expenditure_rates[$type];
	}

	public function getSalesTaxRate($type, $state_abbrev) {
		if ($state_abbrev == 'IN') {
			if ($type == 'food at home') {
				return array(
					'min' => 0,
					'max' => 0
				);
			} else {
				return array(
					'min' => 7,
					'max' => 7
				);
			}
		} elseif ($state_abbrev == 'IL') {
			if ($type == 'food at home') {
				return array(
					'min' => 1,
					'max' => 1
				);
			} else {
				return array(
					'min' => 6.25,
					'max' => 11.50
				);
			}
		}
	}

	// Adjusted Gross Income
	public function getAGI($income, $dependents, $state_abbrev) {
		return max(0, $income - $this->getExemptionsTotal($dependents, $state_abbrev));
	}

	public function getExemptionsTotal($dependents, $state_abbrev) {
		if ($state_abbrev == 'IN') {
			return 1000 + (1500 * $dependents);
		} elseif ($state_abbrev == 'IL') {
			return 2000 + (2000 * $dependents);
		}
	}

	public function getExemptionsFormula($state_abbrev) {
		if ($state_abbrev == 'IN') {
			return "$1,000 + ($1,500 &times; number of dependents)";
		} elseif ($state_abbrev == 'IL') {
			return "$2,000 + ($2,000 &times; number of dependents)";
		}
	}

	public function getSalesTaxTypes() {
		return array(
			'food at home',
			'food away from home',
			'housekeeping supplies',
			'apparel and services',
			'household furnishings and equipment',
			'personal care products'
		);
	}

	// Keys expected in $input array: from_county, to_county, income, dependents, home_value_before, home_value_after
	public function calculate($input) {
		// ------ PROCESS INPUT ------
		// County
		$county_id = array(
			'before' => $input['from_county'],
			'after' => $input['to_county']
		);

		// State
		$state_id = array(
			'before' => 13, // IL
			'after' => 14	// IN
		);
		$state_abbrev = array(
			'before' => 'IL',
			'after' => 'IN'
		);

		// Income
		$income = $this->cleanNumber($input['income']);

		// Dependents
		$dependents = $input['dependents'];

		// Home value
		$home_value = array(
			'before' => $this->cleanNumber($input['home_value_before']),
			'after' => $this->cleanNumber($input['home_value_after'])
		);

		// Average Annual Expenditures
		$avg_annual_expenditures = $this->getAvgAnnualExpenditures($income);



		// ------ VALIDATE INPUT ------
		/*
		$errors = array();
		if (! $income || ! is_numeric($income)) {
			$errors[] = "Please provide your household income";
		}
		foreach ($home_value as $for_county => &$value) {
			if (! $value || ! is_numeric($value)) {
				$which_home = $for_county == 'before'
					? 'your current home'
					: 'the home to which you will be moving';
				$errors[] = "Please provide the value of $which_home";
			}
		}
		*/
		// Validate counties by attempting to retrieve their names
		$county_name = array();
		$this->Location = ClassRegistry::init('Location');
		foreach (array('before', 'after') as $for_county) {
			if ($retrieved_name = $this->Location->getCountyNameFromID($county_id[$for_county])) {
				$county_name[$for_county] = $retrieved_name.' County, '.$state_abbrev[$for_county];
			} else {
				$preposition = $for_county == 'before' ? 'from' : 'to';
				$errors[] = "Invalid '$preposition' county selected";
			}
		}




		// ------ GENERATE OUTPUT ------
		$agi = array();
		$taxes = array();
		$sales_tax_types = $this->getSalesTaxTypes();
		foreach (array('before', 'after') as $for_county) {
			// Adjusted gross income
			$agi[$for_county] = $this->getAGI($income, $dependents, $state_abbrev[$for_county]);

			// Taxes paid
			$taxes['state'][$for_county] = $this->getStateIncomeTax($agi[$for_county], $state_id[$for_county]);
			$taxes['county'][$for_county] = $this->getCountyIncomeTax($agi[$for_county], $county_id[$for_county]);
			$taxes['property'][$for_county] = $this->getPropertyTax($home_value[$for_county], $county_id[$for_county], $state_abbrev[$for_county]);
			foreach ($sales_tax_types as $sales_tax_type) {
				$taxes['sales'][$sales_tax_type][$for_county] = $this->getSalesTax($sales_tax_type, $income, $state_abbrev[$for_county]);
			}

			// Calculate total sales tax
			foreach (array('min', 'max') as $bound) {
				$taxes['sales']['total'][$for_county][$bound] = 0;
				foreach ($sales_tax_types as $sales_tax_type) {
					$taxes['sales']['total'][$for_county][$bound] += $taxes['sales'][$sales_tax_type][$for_county][$bound];
				}
			}

			// Total taxes
			foreach (array('min', 'max') as $bound) {
				$taxes['total'][$for_county][$bound] =
					$taxes['state'][$for_county] +
					$taxes['county'][$for_county] +
					$taxes['property'][$for_county] +
					$taxes['sales']['total'][$for_county][$bound];
			}
		}

		// Total savings
		$savings = array(
			'min' => $taxes['total']['before']['min'] - $taxes['total']['after']['max'],
			'max' => $taxes['total']['before']['max'] - $taxes['total']['after']['min']
		);

		return compact(
			'errors', 'taxes', 'home_value', 'savings',
			'income', 'avg_annual_expenditures', 'county_id',
			'state_id', 'state_abbrev', 'sales_tax_types',
			'county_name', 'dependents'
		);
	}

	// Generates formulas that explain to the user how our calculations are made
	// $input array includes keys for $income $state_abbrev $state_id $county_id $home_value $sales_tax_types
	public function getFormulas($input) {
		extract($input);
		$formulas = array();
		$formulas['aae'] = $this->getAvgAnnualExpendituresPercent($income).'% of income';
		foreach (array('before', 'after') as $for_county) {
			$formulas['exemptions'][$for_county] = $this->getExemptionsFormula($state_abbrev[$for_county]);
			$formulas['agi'][$for_county] = 'income &#8722; exemptions';
			$state_tax_rate = $this->getStateIncomeTaxRate($state_id[$for_county]);
			$formulas['taxes']['state'][$for_county] = round($state_tax_rate, 2).'% of AGI';
			$county_tax_rate = $this->getCountyIncomeTaxRate($county_id[$for_county]);
			$formulas['taxes']['county'][$for_county] = round($county_tax_rate, 2).'% of AGI';

			// Property taxes
			$formulas['rhv'][$for_county] = $this->getRHVFormula($home_value[$for_county], $state_abbrev[$for_county]);
			$formulas['shd'][$for_county] = $this->getSHDFormula($home_value[$for_county], $state_abbrev[$for_county]);
			$formulas['net_ahv'][$for_county] = $this->getAHVFormula($home_value[$for_county], $state_abbrev[$for_county]);
			$property_tax_rate = $this->getPropertyTaxRate($county_id[$for_county]);

			switch ($state_abbrev[$for_county]) {
				case 'IN':
					$formulas['taxes']['property'][$for_county] = round($property_tax_rate, 2).'% of Net AHV (capped at 1% of home value)';
					break;
				case 'IL':
					$formulas['taxes']['property'][$for_county] = round($property_tax_rate, 2).'% of home value';
					break;
			}

			foreach ($sales_tax_types as $sales_tax_type) {
				$e_rate = $this->getExpenditureRate($sales_tax_type, $income);
				$tax_rate = $this->getSalesTaxRate($sales_tax_type, $state_abbrev[$for_county]);
				$tax_rate = $tax_rate['min'] == $tax_rate['max']
					? $tax_rate['min'].'%'
					: $tax_rate['min'].'% to '.$tax_rate['max'].'%';
				$formulas['taxes']['sales'][$sales_tax_type][$for_county] =
					$tax_rate.' of AAE';
				$formulas['expenditures'][$sales_tax_type] = $e_rate.'% of income';
			}
		}
		return $formulas;
	}

	public function getFormattedOutput($output) {
		extract($output);
		foreach ($taxes['sales'] as $type => &$county_specific) {
			foreach ($county_specific as $for_county => &$values) {
				$values['displayed'] = $this->formatMinMaxValue($values['min'], $values['max']);
			}
		}
		foreach (array('before', 'after') as $for_county) {
			foreach (array('state', 'county', 'property') as $tax_type) {
				$taxes[$tax_type][$for_county] = $this->money_format($taxes[$tax_type][$for_county]);
			}
			$taxes['sales']['total'][$for_county]['displayed'] = $this->formatMinMaxValue(
				$taxes['sales']['total'][$for_county]['min'],
				$taxes['sales']['total'][$for_county]['max']
			);
			$taxes['total'][$for_county]['displayed'] = $this->formatMinMaxValue(
				$taxes['total'][$for_county]['min'],
				$taxes['total'][$for_county]['max']
			);
			$home_value[$for_county] = $this->money_format($home_value[$for_county]);
		}
		$savings['displayed'] = $this->formatMinMaxValue($savings['min'], $savings['max']);
		$income = $this->money_format($income);
		$avg_annual_expenditures = $this->money_format($avg_annual_expenditures);

		// Format dependents value shown to user
		switch ($dependents) {
			case 0:
				$dependents = 'None';
				break;
			case 4:
				$dependents = '4 or more';
				break;
		}

		return compact(
			'taxes', 'home_value', 'savings', 'income',
			'avg_annual_expenditures', 'dependents'
		);
	}
}