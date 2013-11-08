<?php
// Adapted from ExcelReport from brownfields website
class Spreadsheet {
	public $useTable = false;

	/*
	var $title = '';
	var $author = 'Center for Business and Economic Research, Ball State University';
	var $columns = array();
	var $footnote = '';
	var $first_col_format = 'year';
	var $data_format = 'number';
	var $data_precision = 0;
	var $excel_type; 		//excel5 or excel2007
	var $output_type; 		//Excel5 or Excel2007
	var $objPHPExcel;
	var $current_row = 1; 	//Row iterator (first row is 1, not 0)
	var $col_labels;		//Set by setColumnLabels() and referenced by setValues
	var $row_labels;		//Set by setRowLabels() and referenced by setValues
	var $mockup; 			//[col][row] => value array outputted during debugging
	var $table = array();
	*/





	/****** ****** Generation of PHPExcel object ****** ******/


	// Sets up $this->objPHPExcel so that it's ready for output
	public function getOutput($topic) {
		// Run the topic-specific report-preparation method
		$this->{$topic}();

		// Translate the 'report type' value to a PHPExcel output type
		switch ($this->excel_type) {
			case 'excel2007':
				$this->output_type = 'Excel2007';
				break;
			case 'excel5':
				$this->output_type = 'Excel5';
				break;
		}

		// Start up
		PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());
		$this->objPHPExcel = new PHPExcel();
		$this->objPHPExcel->setActiveSheetIndex(0);

		// Populate the spreadsheet
		$this->__setMetaData(array(
			'author' => $this->author,
			'title' => $this->title,
			'description' => ''
		));
		$this->objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
		$this->objPHPExcel->getDefaultStyle()->getFont()->setSize(11);
		$this->__setTitle();				// Uses $this->title
		$this->__setSources();				// Uses requestAction()
		$this->__setColumnAndRowLabels();	// Uses $this->columns and $this->row_labels
		$this->__setValues();
		$this->__setFootnote();

		// Reduce the width of the first column
		//   (which contains only the title and sources and overflow over the unoccupied cells to the right)
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1.5);

		// Automatically adjust the width of all columns AFTER the first
		$last_col = count($this->columns);
		for ($c = 1; $c <= $last_col; $c++) {
			$col_letter = $this->__convertNumToLetter($c);
			$this->objPHPExcel->getActiveSheet()->getColumnDimension($col_letter)->setAutoSize(true);
		}
	}

	public function __setMetaData($metadata) {
		// Metadata
		$this->objPHPExcel->getProperties()
			->setCreator($metadata['author'])
			->setLastModifiedBy($metadata['author'])
			->setTitle($metadata['title'])
			->setSubject($metadata['title'])
			->setDescription($metadata['description']);
	}

	public function __setTitle() {
		// Set title
		$this->__setCell(0, 1, $this->title);

		// Style title
		$this->__setStylesFromArray('A1', 'A1', array(
			'font' => array(
				'bold' => true,
				'size' => 24
			)
		));

		$this->current_row++;
	}

	public function __setSources() {
		$sources = $this->requestAction(
			array('controller' => 'reports', 'action' => 'switchboard'),
			array('pass' => array('source', $this->topic, $this->state_id, $this->county_id))
		);
		$col_count = count($this->columns);
		foreach ($sources as $source) {
			$this->__setCell(0, $this->current_row, "Source: $source");
			$this->current_row++;
		}

		// Blank row after sources
		$this->current_row++;
	}

	// Note that column headers and values start on the SECOND column
	public function __setColumnAndRowLabels() {
		// Write column labels
		foreach ($this->columns as $key => $column_label) {
			$col = $key + 1;
			$this->__setCell($col, $this->current_row, $column_label);
		}

		// Repeat column labels at top of every printed page
		$this->objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd($this->current_row, $this->current_row);

		// Style column labels
		$first_cell = 'B'.$this->current_row;
		$last_cell = $this->__convertNumToLetter(count($this->columns)).$this->current_row;
		$this->__setStylesFromArray($first_cell, $last_cell, array(
			'font' => array(
				'bold' => true,
				'size' => 12
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
			),
			'borders' => array(
				'bottom' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startcolor' => array(
					'argb' => 'FFFFFFFF'
				),
				'endcolor' => array(
					'argb' => 'FFDFDFDF'
				)
			)
		));

		// Enable autofilter on column headers
		$this->objPHPExcel->getActiveSheet()->setAutoFilter("$first_cell:$last_cell");

		$this->current_row++;

		// Write row labels
		$type = isset($this->first_col_format) ? $this->first_col_format : 'string';
		$row_iter = $this->current_row;
		foreach ($this->row_labels as $row_label) {
			$row_label = $this->__formatValue($row_label, $type);
			$this->__setCell(1, $row_iter++, $row_label);
		}

		// Style row labels
		$first_cell = 'B'.$this->current_row;
		$last_cell = 'B'.($this->current_row + count($this->row_labels) - 1);
		$this->__setStylesFromArray($first_cell, $last_cell, array(
			'font' => array(
				'bold' => true,
				'size' => 12
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
			),
			'borders' => array(
				'right' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		));
	}

	public function __setStylesFromArray($first_cell, $last_cell, $styles) {
		$this->objPHPExcel->getActiveSheet()->getStyle("$first_cell:$last_cell")->applyFromArray($styles);
	}

	// Used in converting coordinates (0,0) to Excel cell identifiers (A1)
	// Currently does not work past the 26th column
	public function __convertNumToLetter($number, $capitalize = true) {
		$letters = 'abcdefghijklmnopqrstuvwxyz';
		$letter = substr($letters, $number, 1);
		return $capitalize ? strtoupper($letter) : $letter;
	}

	/* Expects $this->values to be populated like this:
	 * 		$this->values[$col_num][$row_label] = $value
	 * with $col_num being zero-indexed. */
	public function __setValues() {
		// Freeze everything above data when scrolling
		$this->objPHPExcel->getActiveSheet()->freezePane('A'.($this->current_row));

		// Set values
		$row_count = 0;
		foreach ($this->values as $col_num => $values_in_column) {
			$row_count = max($row_count, count($values_in_column));
			foreach ($values_in_column as $row_label => $value) {

				// Get the proper row number and check for error
				$row_num = array_search($row_label, $this->row_labels);
				if ($row_num === false) {
					$this->error = 5;
					$this->error_message = "ExcelReport::values[$col_num][<b>$row_label</b>] uses an unrecognized row label. Recognized labels: <ul>";
					foreach ($this->row_labels as $row_label) {
						$this->error_message .= '<li>'.$row_label.'</li>';
					}
					$this->error_message .= '</ul>';
					return;
				}

				// Adjust column to the right
				$col_num_adjusted = ($col_num + 2);

				// Adjust row downward
				$row_num_adjusted = ($row_num + $this->current_row);

				// Write value
				$this->__setCell($col_num_adjusted, $row_num_adjusted, $value);

				// Optionally apply a number format
				if (isset($this->individual_value_formats[$col_num][$row_num])) {
					//if (isset($_GET['debug'])) echo "[$col_num][$row_num]: Formatting (".$this->individual_value_formats[$col_num][$row_num].")<br />";
					$this->__applyNumberFormatToCell($col_num_adjusted, $row_num_adjusted, $this->individual_value_formats[$col_num][$row_num]);
				} else {
					//if (isset($_GET['debug'])) echo "[$col_num][$row_num]: No formatting<br />";
				}
			}
		}

		// Style entire block of values
		$first_cell = 'C'.$this->current_row;
		$last_cell = $this->__convertNumToLetter(count($this->values) + 1).($this->current_row + count($this->row_labels) - 1);
		$this->__setStylesFromArray($first_cell, $last_cell, array(
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			)
		));

		$this->current_row += $row_count;
	}

	// $col_num and $row_num are zero-indexed and relative to the entire spreadsheet
	public function __applyNumberFormatToCell($col_num, $row_num, $format) {
		$excel_cell = $this->__convertNumToLetter($col_num).($row_num);
		$this->objPHPExcel->getActiveSheet()->getStyle($excel_cell)->getNumberFormat()->setFormatCode($format);
	}

	public function __formatValue($value, $mode = 'number', $precision = 0) {
		if ($value == '') {
			return $value;
		}
		switch ($mode) {
			case 'year':
				return substr($value, 0, 4);
			case 'number':
				return ($value < 1 ? '0.' : '').number_format($value, $precision);
			case 'percent':
				return number_format($value, $precision).'%'; //(($value < 1 && $value != 0) ? '0.' : '').
			case 'currency':
				return '$'.($value < 1 ? '0.' : '').number_format($value, $precision);
			case 'string':
			default:
				return $value;
		}
	}

	// Adds a footnote to the bottom of the spreadsheet
	// If a newline is in the footnote, splits up footnote into multiple rows
	public function __setFootnote() {
		if ($this->footnote) {
			$this->current_row++; // Blank line before footnote
			$footnote_lines = explode("\n", $this->footnote);
			foreach ($footnote_lines as $footnote_line) {
				$this->__setCell(0, $this->current_row, $footnote_line);
				$coordinates = $this->__getExcelCoordinates(0, $this->current_row);
				$this->objPHPExcel->getActiveSheet()->getStyle($coordinates)->getAlignment()->setWrapText(false);
				$this->current_row++;
			}
		}
	}

	public function __setCell($col, $row, $value) {
		if ($value !== null && $value !== false) {
			$this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
		}
		$this->mockup[$col][$row] = $value;
	}

	public function __getExcelCoordinates($col, $row) {
		return $this->__convertNumToLetter($col).($row);
	}
}