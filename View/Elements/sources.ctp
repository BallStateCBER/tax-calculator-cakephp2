<?php $sources = array(
	'General' => array(
		'http://www.bls.gov/cex/' => 'Consumer Expenditure Survey'
	),
	'Illinois' => array(
		'http://www.tax-rates.org/Illinois/sales-tax' => 'Illinois State Sales Tax Rate (2010-2011)',
		'http://tax.illinois.gov/taxforms/IncmCurrentYear/Individual/index.htm' => 'Individual Illinois Income Tax Forms 2010',
		'http://www.taxfoundation.org/taxdata/topic/89.html' => 'Illinois Property Taxes'
	),
	'Indiana' => array(
		'http://www.in.gov/legislative/pdf/TaxHandbook10_online.pdf' => 'Indiana Handbook of Taxes, Revenues, and Appropriations FY 2010',
		'http://www.in.gov/dor/4439.htm' => 'Individual Income Tax Forms 2010'
	)
); ?>
<div id="sources">
	<div>
		<ul>
			<?php foreach ($sources as $category => $c_sources): ?>
				<li>
					<?php echo $category; ?>
					<ul>
						<?php foreach ($c_sources as $url => $title): ?>
							<li>
								<?php echo $this->Html->link($title, $url); ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>