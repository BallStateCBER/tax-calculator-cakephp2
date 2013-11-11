<div class="box">
	<?php echo $this->element('output_table'); ?>
	<?php echo $this->Html->image(
		'/data_center/img/loading_small.gif',
		array(
			'alt' => 'Loading',
			'title' => 'Loading',
			'style' => 'display: none;',
			'id' => 'calc_loading'
		)
	); ?>
</div>
<br />
<div id="additional_info" class="box">
	<h2>
		<?php echo $this->Html->link(
			'How was this calculated?',
			array('url' => '#'),
			array('escape' => false, 'title' => 'How was this calculated?', 'id' => 'toggle_formulas')
		); ?>
	</h2>
	<?php echo $this->element('formulas_used'); ?>

	<h2>
		<?php echo $this->Html->link(
			'Data sources',
			array('url' => '#'),
			array('escape' => false, 'title' => 'Data sources', 'id' => 'toggle_sources')
		); ?>
	</h2>
	<?php echo $this->element('sources'); ?>

	<h2>
		<?php echo $this->Html->link(
			'Footnotes',
			array('url' => '#'),
			array('escape' => false, 'title' => 'Footnotes', 'id' => 'toggle_footnotes')
		); ?>
	</h2>
	<?php echo $this->element('footnotes'); ?>
</div>
<?php $this->Js->buffer("
	$('#calc_input_income').change(function (event) {
		forceMoneyFormat('calc_input_income');
	});
	$('#calc_input_home_value_before').change(function (event) {
		forceMoneyFormat('calc_input_home_value_before');
	});
	$('#calc_input_home_value_after').change(function (event) {
		forceMoneyFormat('calc_input_home_value_after');
	});
	$('#recalculate_form').submit(function (event) {
		recalculate(event);
	});
	$('#toggle_edit').click(function (event) {
		toggle_edit(event);
	});
	$('#toggle_sales_taxes').click(function (event) {
		toggle_sales_taxes(event);
	});
	$('#toggle_formulas').click(function (event) {
		toggle_formulas(event);
	});
	$('#toggle_sources').click(function (event) {
		toggle_sources(event);
	});
	$('#toggle_footnotes').click(function (event) {
		toggle_footnotes(event);
	});
	$('#show_download_options').click(function (event) {
		toggle_download_options(event);
	});
"); ?>