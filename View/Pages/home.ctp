<div id="calc_output_container">
	<div class="box">
		<?php echo $this->Form->create(false, array('id' => 'initial_input_form')); ?>

		<?php echo $this->Form->input('from_county', array(
			'label' => 'What Illinois county are you moving from?',
			'options' => $counties['IL']
		)); ?>

		<?php echo $this->Form->input('to_county', array(
			'label' => 'What Indiana county are you moving to?',
			'options' => $counties['IN']
		)); ?>

		<?php echo $this->Form->input('home_value_before', array(
			'label' => 'What is the value of your home in Illinois?',
			'value' => isset($home_value) ? $home_value : null,
			'id' => 'calc_input_home_value_before'
		)); ?>

		<?php echo $this->Form->input('home_value_after', array(
			'label' => 'What is the value of your home in Indiana?',
			'value' => isset($home_value) ? $home_value : null,
			'id' => 'calc_input_home_value_after'
		)); ?>

		<?php echo $this->Form->input('income', array(
			'label' => 'What is your household\'s annual income?',
			'value' => isset($income) ? $income : null,
			'id' => 'calc_input_income'
		)); ?>

		<?php echo $this->Form->input('dependents', array(
			'label' => 'How many dependents can you claim on your tax return?',
			'options' => array(
				'None',
				1,
				2,
				3,
				'4 or more'
			),
			'value' => isset($dependents) ? $dependents : null
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

		<?php echo $this->Form->end('Calculate Tax Savings'); ?>
	</div>
</div>

<?php $this->Js->buffer("
	Event.observe('calc_input_income', 'change', function(event) {
		forceMoneyFormat('calc_input_income');
	});
	Event.observe('calc_input_home_value_before', 'change', function(event) {
		forceMoneyFormat('calc_input_home_value_before');
		var after_field = $('calc_input_home_value_after');
		if (after_field.value == '') {
			after_field.value = $('calc_input_home_value_before').value;
		}
	});
	Event.observe('calc_input_home_value_after', 'change', function(event) {
		forceMoneyFormat('calc_input_home_value_after');
	});
	$('initial_input_form').observe('submit', function (event) {
		calculate(event);
	});
"); ?>