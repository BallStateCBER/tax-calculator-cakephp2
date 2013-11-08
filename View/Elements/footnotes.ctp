<?php $footnotes = array(
	'Income Tax' => array(
		'Indiana & Illinois' => array(
			"Household Income assessed at imputed value less the applicable personal and dependency exemptions. CBER has excluded all Add-Backs, Deductions, Other Taxes, and exemptions for the blind and individuals over 65. CBER has also elected to exclude all Tax Credits for the purpose of calculating income tax due.",
			"Household Income is defined as total receipts (salary, tips, professional fees, etc.) less non-income items, which is the equivalent to Federal adjusted gross income.",
			"Indiana has an additional deduction for dependents under 18 of $1500 which is not included, but would apply to several dependency incidences.",
			"Please include your spouse as a dependent."
		)
	),
	'Property Tax' => array(
		'Indiana' => array(
			"Home value entered by user is assumed to be Assessed Gross Home Value",
			"In order to calculate the assessed home value, there is an adjustment for the Homestead Standard Deduction Credit and also the Supplemental Homestead Deduction. The assessed home value is then multiplied by the property tax rate. Although this adjustment is made to all participants, this adjustment is only made in reality if you qualify according to the Indiana Code. As required to qualify, your Indiana homestead must be your primary residence. Thus, our calculations are based on the premises that you receive the Homestead Standard Deduction Credit and also the Supplemental Homestead Deduction.",
			"According to Indiana legislation, all property taxes have been capped at 1%. Thus, the maximum property tax bill for your homestead is equal to 1% of the home value.",
			"Property taxes are calculated by district, thus the county rates are an estimate of the exact value. These rates do not include any local tax credits.",
			"For La Porte County, we used a three year average (2007-2009) for the property tax rate.",
			"CBER has not considered township-based property tax reliefs or tax cap increases due to referendum."
		),
		'Illinois' => array(
			"Rates reflect the fact that residential property is assessed at 33% of its market value (excluding Cook County which is assessed at 16%).The rates are a percent of home value and reflect a five year average (2005-2009)."
		)
	),
	'Sales Tax' => array(
		'Indiana & Illinois' => array(
			"The Consumer Expenditure Survey was used to determine the value of expenditures and also how expenditures were distributed amongst food, housekeeping supplies, personal care items, etc. These expenditures were then used to evaluate sales tax expense.",
			"Since Illinois has a local option sales tax, the sales tax expense is determined based on a countywide range. This reflects the minimum and maximum total sales tax rate of a county in the state.  For a more precise determination of sales tax rates please visit https://www.revenue.state.il.us/app/trii/."
		)
	)
); ?>
<div id="footnotes">
	<div>
		<ul>
			<?php foreach ($footnotes  as $category => $locations): ?>
				<li>
					<h3><?php echo $category; ?></h3>
					<ul>
						<?php foreach ($locations as $location => $l_footnotes): ?>
							<li>
								<?php echo $location; ?>
								<ul>
									<?php foreach ($l_footnotes as $footnote): ?>
										<li><?php echo $footnote; ?></li>
									<?php endforeach; ?>
								</ul>
							</li>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>