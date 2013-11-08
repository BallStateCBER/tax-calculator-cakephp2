<?php $references = array(
	'General' => array(
		'http://www.taxfoundation.org' => 'Tax Foundation'
	),
	'Illinois' => array(
		'http://www.realtor.com/illinois/nbselil.asp' => 'Find the home value in Illinois by MLS listing'
	),
	'Indiana' => array(
		'http://www.realtor.com/indiana/nbselin.asp' => 'Find the home value in Indiana by MLS listing',
		'http://www.in.gov/dlgf/4932.htm' => 'Estimate your 2010 Indiana Property Tax Bill',
		'http://profiles.cberdata.org' => 'Indiana County Profiles',
		'http://brownfield.cberdata.org/' => 'Brownfield Grant Writers\' Tool (statistical information about Indiana counties)'
	)
); ?>
<h2>
	Additional Resources
</h2>
<?php foreach ($references as $category => $links): ?>
	<h3>
		<?php echo $category; ?>
	</h3>
	<ul>
		<?php foreach ($links as $url => $title): ?>
			<li>
				<?php echo $this->Html->link($title, $url, array('target' => '_blank')); ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endforeach; ?>