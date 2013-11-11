<?php echo $this->Form->create(false); ?>
<table class="calc_input">
	<tbody>
		<tr class="moving">
			<th>Moving</th>
			<th>from Illinois county:</th>
			<th>to Indiana county:</th>
		</tr>
		<tr class="moving">
			<th></th>
			<td>
				<?php echo $this->Form->input('from_county', array(
					'label' => false,
					'div' => false,
					'options' => $counties['IL']
				)); ?>
				<span class="arrow">&rarr;</span>
			</td>
			<td>
				<?php echo $this->Form->input('to_county', array(
					'label' => false,
					'div' => false,
					'options' => $counties['IN']
				)); ?>
			</td>
		</tr>
		<tr>
			<th>Home value:</th>
			<td>
				<?php echo $this->Form->input('home_value_before', array(
					'label' => false,
					'div' => false,
					'value' => isset($home_value) ? $home_value : null,
					'id' => 'calc_input_home_value_before'
				)); ?>
				<span class="arrow">&rarr;</span>
			</td>
			<td>
				<?php echo $this->Form->input('home_value_after', array(
					'label' => false,
					'div' => false,
					'value' => isset($home_value) ? $home_value : null,
					'id' => 'calc_input_home_value_after'
				)); ?>
			</td>
		</tr>
		<tr>
			<th>Household income:</th>
			<td colspan="2">
				<?php echo $this->Form->input('income', array(
					'label' => false,
					'div' => false,
					'value' => isset($income) ? $income : '$50,000',
					'id' => 'calc_input_income'
				)); ?>
			</td>
		</tr>
		<tr>
			<th>Dependents:</th>
			<td colspan="2">
				<?php echo $this->Form->input('dependents', array(
					'label' => false,
					'div' => false,
					'options' => array(
						'None',
						1,
						2,
						3,
						'4 or more'
					),
					'value' => isset($dependents) ? $dependents : null
				)); ?>
			</td>
		</tr>
		<tr>
			<th></th>
			<td colspan="2">
				<?php echo $this->Ajax->submit('Calculate Tax Savings', array(
					'url'=> array(
						'controller' => 'pages',
						'action' => 'output'
					),
					'update' => 'calc_output_container',
					'indicator' => 'calc_loading',
					'div' => false,
					'condition' => 'true',	// Replace with JS validation method call
					'before' => "$('calc_output_container').setStyle({opacity: 0})",
					'complete' => "$('calc_output_container').setStyle({opacity: 1})"
				)); ?>
				<?php echo $this->Html->image(
					'/data_center/img/loading_small.gif',
					array(
						'alt' => 'Loading',
						'title' => 'Loading',
						'style' => 'display: none;',
						'id' => 'calc_loading'
					)
				); ?>
			</td>
		</tr>
	</tbody>
</table>
<?php echo $this->Form->end(); ?>

<div id="calc_output_container"></div>

<?php $this->Js->buffer("
	$('#calc_input_income').change(function(event) {
		forceMoneyFormat('calc_input_income');
	});
	$('#calc_input_home_value_before').change(function(event) {
		forceMoneyFormat('calc_input_home_value_before');
		var after_field = $('#calc_input_home_value_after');
		if (after_field.val() == '') {
			after_field.val($('#calc_input_home_value_before').val());
		}
	});
	$('#calc_input_home_value_after').change(function(event) {
		forceMoneyFormat('calc_input_home_value_after');
	});
"); ?>