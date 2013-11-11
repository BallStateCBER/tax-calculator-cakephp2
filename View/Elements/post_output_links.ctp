<div id="post_output_links">
	<div class="display_mode">
		<div class="display">
			<a href="#" id="toggle_edit">
				<img src="/img/calculator.png" />
				Edit
			</a>
		</div>
		<div class="input">
			<button id="recalculate_button" class="fake_link" type="submit">
				<img src="/img/calculator--arrow.png" alt="Recalculate" /> Recalculate
				<?php echo $this->Html->image(
					'/data_center/img/loading_small.gif',
					array(
						'alt' => 'Loading',
						'style' => 'display: none;',
						'id' => 'recalc_loading'
					)
				); ?>
			</button>
		</div>
	</div>
	<?php echo $this->Html->link(
		$this->Html->image('printer.png').' Print',
		array('controller' => 'pages', 'action' => 'print_output', '?' => $input_query),
		array('escape' => false, 'title' => 'Print', 'id' => 'print_output_link', 'target' => '_blank')
	); ?>
	<br />
	<?php echo $this->Html->link(
		$this->Html->image('document-excel-table.png').' Download',
		array('url' => '#'),
		array('escape' => false, 'title' => 'Download spreadsheet', 'id' => 'show_download_options')
	); ?>
	<div id="download_options" style="height: 0;">
		<div>
			<?php echo $this->Html->link(
				$this->Html->image('document-excel-table.png').' Excel 2007',
				array('controller' => 'pages', 'action' => 'spreadsheet', '?' => $input_query.'&type=excel2007'),
				array('escape' => false, 'title' => 'Download Excel 2007 spreadsheet')
			); ?>
			<?php echo $this->Html->link(
				$this->Html->image('document-excel-table.png').' Excel 5.0',
				array('controller' => 'pages', 'action' => 'spreadsheet', '?' => $input_query.'&type=excel5'),
				array('escape' => false, 'title' => 'Download Excel 5.0 spreadsheet')
			); ?>
			<?php echo $this->Html->link(
				$this->Html->image('document-excel-csv.png').' CSV',
				array('controller' => 'pages', 'action' => 'spreadsheet', '?' => $input_query.'&type=csv'),
				array('escape' => false, 'title' => 'Download CSV (comma-separated values) spreadsheet')
			); ?>
		</div>
	</div>
</div>