<div class="box">
	<?php echo $this->element('output_table'); ?>
	<?php echo $this->Html->image(
		'/data_center/img/loading_small.gif',
		array(
			'alt' => 'Loading...',
			'title' => 'Loading...',
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
			array(
				'url' => '#'
			),
			array(
				'escape' => false, 
				'id' => 'toggle_formulas'
			)
		); ?>
	</h2>
	<?php echo $this->element('formulas_used'); ?>

	<h2>
		<?php echo $this->Html->link(
			'Data sources',
			array(
				'url' => '#'
			),
			array(
				'escape' => false, 
				'id' => 'toggle_sources'
			)
		); ?>
	</h2>
	<?php echo $this->element('sources'); ?>

	<h2>
		<?php echo $this->Html->link(
			'Footnotes',
			array(
				'url' => '#'
			),
			array(
				'escape' => false, 
				'id' => 'toggle_footnotes'
			)
		); ?>
	</h2>
	<?php echo $this->element('footnotes'); ?>
	
	<h2>
		<?php echo $this->Html->link(
			'Additional Resources',
			array(
				'url' => '#'
			),
			array(
				'escape' => false, 
				'id' => 'toggle_resources'
			)
		); ?>
	</h2>
	<?php echo $this->element('resources'); ?>
</div>

<?php $this->Js->buffer("setupOutput();"); ?>