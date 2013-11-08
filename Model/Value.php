<?php
class Value extends AppModel {
    public $name = 'Value';
    public $displayField = 'value';
    public $useTable = 'tax_rates';

    /*
	// Gets a single datum corresponding to a specific date
	function getValue($category_id, $loc_type_id, $loc_id, $date) {
		// Convert if dates are in YYYY format instead of YYYYMMDD
		if (strlen($date) == 4) {
			$date = $date.'0000';
		}

		$results = $this->find('all', array(
			'conditions' => array(
				'Datum.category_id' => $category_id,
				'Datum.loc_type_id' => $loc_type_id,
				'Datum.loc_id' => $loc_id,
				'Datum.survey_date' => $date
			),
			'fields' => array('Datum.survey_date', 'Datum.value'),
			'order' => array('Datum.survey_date ASC'),
			'limit' => 1
		));

		$values = $dates = array();
		foreach ($results as $result) {
			// Remove trailing zeros (and trailing decimal point) to minimize query length
			$value = rtrim(trim($result['Datum']['value'], '0'), '.');
			return ($value == '') ? 0 : $value;
		}
		return false;
	}
	*/

	public function validateDatum($datum) {
		if ($datum['loc_type'] != 'county' && $datum['loc_type'] != 'state') {
			return 1; // Unrecognized location type
		}
		if (is_numeric($datum['loc_id'])) {
			if (false) { // Test to make sure that location exists
				return 2; // Unrecognized location
			}
		} else {
			return 3; // Non-numeric location ID
		}
		if (is_numeric($datum['category_id'])) {
			if (false) { // Test to make sure that category exists
				return 4; // Unrecognized category
			}
		} else {
			return 5; // Non-numeric category ID
		}
		if (! is_numeric($datum['value'])) {
			return 6; // Non-numeric value
		}
		return 0; // Validated
	}

    /* Returns -1 if redundant (same value)
     *          0 if not redundant
     * (Datum ID) if redundant with different value */
	public function isRedundant($datum) {
		$result = $this->find('first', array(
			'conditions' => array(
				'loc_type' => $datum['loc_type'],
				'loc_id' => $datum['loc_id'],
				'category_id' => $datum['category_id']
			),
			'fields' => array('id', 'value')
		));
		if (! $result) {
			// Datum not in database
			return 0;
		} elseif ($result['Value']['value'] == $datum['value']) {
			// Datum with matching value in database
			return -1;
		} else {
			// Datum with different value in database
			return $result['Value']['id'];
		}
	}

	public function add($datum, $overwrite = false) {
		$this->create();
		$this->id = null;
		if ($retval = $this->validateDatum($datum)) {
			return $retval; // Error with datum
		}
		$redundant = $this->isRedundant($datum);

		// Datum already imported with same value
		if ($redundant == -1) {
			return 0; // No action necessary

		// Datum already imported with different value
		} elseif ($redundant > 0) {
			if ($overwrite) {
				// Assign ID so the proper row is overwritten
				$this->id = $redundant;
			} else {
				return 7; // Need to overwrite, but can't
			}
		}
		$this->save(array('Value' => $datum));
		return 0; // Datum imported
	}
}