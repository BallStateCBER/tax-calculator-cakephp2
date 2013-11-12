<div id="post_output_links">
	<div class="display_mode">
		<div class="display">
			<a href="#" id="toggle_edit" class="with_icon">
				<img src="/img/calculator.png" />
				<span>
					Edit
				</span>
			</a>
		</div>
		<div class="input">
			<a href="#" id="recalculate_button" class="with_icon">
				<img src="/img/calculator--arrow.png" />
				<span>
					Recalculate
				</span>
				<?php echo $this->Html->image(
					'/data_center/img/loading_small.gif',
					array(
						'alt' => 'Loading',
						'style' => 'display: none;',
						'id' => 'recalc_loading'
					)
				); ?>
			</a>
		</div>
	</div>
	<?php echo $this->Html->link(
		$this->Html->image('printer.png').' <span>Print</span>',
		array(
			'controller' => 'pages', 
			'action' => 'print_output', 
			'?' => $input_query
		),
		array(
			'escape' => false, 
			'id' => 'print_output_link', 
			'target' => '_blank', 
			'class' => 'with_icon'
		)
	); ?>
	<br />
	<?php echo $this->Html->link(
		$this->Html->image('document-excel-table.png').' <span>Download</span>',
		array(
			'url' => '#'
		),
		array(
			'escape' => false, 
			'title' => 'Download spreadsheet', 
			'id' => 'show_download_options', 
			'class' => 'with_icon'
		)
	); ?>
	<div id="download_options" style="display: none;">
		<div>
			<?php echo $this->Html->link(
				$this->Html->image('document-excel-table.png').' <span>Excel 2007</span>',
				array(
					'controller' => 'pages', 
					'action' => 'spreadsheet', 
					'?' => $input_query.'&type=excel2007'
				),
				array(
					'escape' => false, 
					'title' => 'Download Excel 2007 spreadsheet',
					'class' => 'with_icon'
				)
			); ?>
			<br />
			<?php echo $this->Html->link(
				$this->Html->image('document-excel-table.png').' <span>Excel 5.0</span>',
				array(
					'controller' => 'pages', 
					'action' => 'spreadsheet', 
					'?' => $input_query.'&type=excel5'
				),
				array(
					'escape' => false, 
					'title' => 'Download Excel 5.0 spreadsheet',
					'class' => 'with_icon'
				)
			); ?>
			<br />
			<?php echo $this->Html->link(
				$this->Html->image('document-excel-csv.png').' <span>CSV</span>',
				array(
					'controller' => 'pages', 
					'action' => 'spreadsheet', 
					'?' => $input_query.'&type=csv'
				),
				array(
					'escape' => false, 
					'title' => 'Download CSV (comma-separated values) spreadsheet',
					'class' => 'with_icon'
				)
			); ?>
		</div>
	</div>
</div>