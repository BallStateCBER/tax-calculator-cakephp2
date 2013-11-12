<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * Default helper
 *
 * @var array
 */
	public $helpers = array('Html', 'Session');

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

	public $objPHPExcel = null;

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function beforeRender() {
		parent::beforeRender();
	}

	public function clear_cache() {
		Cache::clear();
		clearCache();
		$this->render('/');
	}

	public function home() {
		$this->loadModel('Location');
		$this->set(array(
			'title_for_layout' => '',
			'counties' => $this->Location->getCountiesSortedByState()
		));
	}

	public function output() {
		$this->loadModel('Calculator');
		$this->set('title_for_layout', '');
		$this->layout = 'ajax';

		// Get output
		$output = $this->Calculator->calculate($this->data);
		extract($output);

		// Skip ahead to rendering error page if errors found
		if (! empty($errors)) {
			$this->set(compact('errors'));
			return $this->render();
		}

		// Get formulas
		$formulas = $this->Calculator->getFormulas($output);

		// Format output
		extract($this->Calculator->getFormattedOutput($output), EXTR_OVERWRITE);

		// Generate common query string for spreadsheet links
		$input_query = array();
		$expected_inputs = array('from_county', 'to_county', 'income', 'dependents', 'home_value_before', 'home_value_after');
		foreach ($expected_inputs as $input_var) {
			$input_query[] = $input_var.'='.$this->data[$input_var];
		}
		$input_query = implode('&', $input_query);

		// Send variables to view
		$this->loadModel('Location');
		$counties = $this->Location->getCountiesSortedByState();
		$this->set(compact(
			'counties', 'county_id', 'county_name', 'income',
			'dependents', 'home_value', 'taxes',
			'savings', 'sales_tax_types', 'avg_annual_expenditures',
			'formulas', 'state_abbrev', 'input_query'
		));
	}

	public function print_output() {
		$this->loadModel('Calculator');
		$this->set('title_for_layout', '');
		$this->layout = 'print_output';
		$this->set('suppress_auto_printing', isset($_GET['sap']));

		// Pull (only) the expected inputs out of $_GET and place them in $input
		$expected_inputs = array('from_county', 'to_county', 'income', 'dependents', 'home_value_before', 'home_value_after');
		foreach ($expected_inputs as $input_var) {
			$input[$input_var] = $_GET[$input_var];
		}

		// Get output
		$output = $this->Calculator->calculate($input);
		extract($output);

		// Skip ahead to rendering error page if errors found
		if (! empty($errors)) {
			$this->set(compact('errors'));
			return $this->render();
		}

		// Format output
		extract($this->Calculator->getFormattedOutput($output), EXTR_OVERWRITE);

		// Send variables to view
		$this->set(compact(
			'county_name', 'income',
			'dependents', 'home_value', 'taxes',
			'savings', 'sales_tax_types', 'avg_annual_expenditures',
			'state_abbrev'
		));
	}

	public function spreadsheet() {
		$this->loadModel('Calculator');
		$spreadsheet_type = $_GET['type'];

		// Pull (only) the expected inputs out of $_GET and place them in $input
		$expected_inputs = array('from_county', 'to_county', 'income', 'dependents', 'home_value_before', 'home_value_after');
		foreach ($expected_inputs as $input_var) {
			$input[$input_var] = $_GET[$input_var];
		}

		// Get output
		$output = $this->Calculator->calculate($input);
		extract($output);
		$title = "{$county_name['before']} to {$county_name['after']} Tax Savings";
		$this->set(array('filename' => $title));
		$author = 'Ball State University Center for Business and Economic Research';

		// Format output
		extract($this->Calculator->getFormattedOutput($output), EXTR_OVERWRITE);

		// Group together variables needed for spreadsheet-generated methods
		$vars_for_spreadsheet = compact(
			'home_value', 'income', 'dependents', 'county_name', 'taxes',
			'sales_tax_types', 'savings', 'title', 'author', 'spreadsheet_type'
		);

		// Generate and set variables needed by CSV or Excel view
		if ($spreadsheet_type == 'csv') {
			$rows = $this->__getSpreadsheetRows($vars_for_spreadsheet);

			// Add title row and blank row
			$rows = array_merge(
				array(array('Illinois to Indiana Tax Savings Calculator'), array()),
				$rows
			);

			$this->set(compact('rows'));
			$this->layout = isset($_GET['debug']) ? 'ajax' : 'spreadsheets/csv';
			return $this->render('/spreadsheets/csv');
		} else {
			$this->__setExcelData($vars_for_spreadsheet);
			$this->set(array(
				'output_type' => ucfirst($spreadsheet_type),
				//'values' => $this->ExcelReport->values,
				'objPHPExcel' => $this->objPHPExcel
			));
			$this->layout = isset($_GET['debug']) ? 'ajax' : "spreadsheets/$spreadsheet_type";
			return $this->render('/spreadsheets/excel');
		}
	}

	public function __getSpreadsheetRows($vars_for_spreadsheet) {
		extract($vars_for_spreadsheet);

		// Write each of the spreadsheet's rows in a multidimensional array
		$rows = array();
		$rows[] = array('Illinois home value:', $home_value['before']);
		$rows[] = array('Indiana home value:', $home_value['after']);
		$rows[] = array('Household income:', $income);
		$rows[] = array('Dependents:', $dependents);
		$rows[] = array();
		$rows[] = array('', $county_name['before'], $county_name['after']);
		$rows[] = array('State taxes', $taxes['state']['before'], $taxes['state']['after']);
		$rows[] = array('County taxes', $taxes['county']['before'], $taxes['county']['after']);
		$rows[] = array('Property taxes', $taxes['property']['before'], $taxes['property']['after']);
		$rows[] = array('Sales taxes...');
		foreach ($sales_tax_types as $sales_tax_type) {
			$rows[] = array('... on '.$sales_tax_type, $taxes['sales'][$sales_tax_type]['before']['displayed'], $taxes['sales'][$sales_tax_type]['after']['displayed']);
		}
		$rows[] = array('Total sales taxes', $taxes['sales']['total']['before']['displayed'], $taxes['sales']['total']['after']['displayed']);
		$rows[] = array('Total annual taxes', $taxes['total']['before']['displayed'], $taxes['total']['after']['displayed']);
		$rows[] = array('');
		$rows[] = array(strtoupper('Annual tax savings'), $savings['displayed']);
		return $rows;
	}


	// ------ Excel stuff below ------


	public function __setExcelData($vars_for_spreadsheet) {
		extract($vars_for_spreadsheet);

		// Start up
		switch ($spreadsheet_type) {
			case 'excel2007':
				App::import('Vendor','PHPExcel', array('file' => 'excel/PHPExcel.php'));
				App::import('Vendor','PHPExcelWriter', array('file' => "excel/PHPExcel/Writer/Excel2007.php"));
				App::import('Vendor','PHPExcelAdvancedValueBinder',array('file' => 'excel/PHPExcel/Cell/AdvancedValueBinder.php'));
				break;
			case 'excel5':
				App::import('Vendor','PHPExcel', array('file' => 'excel/PHPExcel.php'));
				App::import('Vendor','PHPExcelWriter', array('file' => "excel/PHPExcel/Writer/Excel5.php"));
				App::import('Vendor','PHPExcelAdvancedValueBinder',array('file' => 'excel/PHPExcel/Cell/AdvancedValueBinder.php'));
				break;
		}
		PHPExcel_Cell::setValueBinder(new PHPExcel_Cell_AdvancedValueBinder());
		$this->objPHPExcel = new PHPExcel();
		$this->objPHPExcel->setActiveSheetIndex(0);
		$this->objPHPExcel->getDefaultStyle()->getFont()->setName('Arial');
		$this->objPHPExcel->getDefaultStyle()->getFont()->setSize(11);

		// Meta data
		$this->objPHPExcel->getProperties()
			->setCreator($author)
			->setLastModifiedBy($author)
			->setTitle($title)
			->setSubject($title)
			->setDescription('');

		// Set title
		$this->__setExcelCell(0, 1, $title);
		$this->__setStylesFromArray('A1', 'A1', array('font' => array('bold' => true, 'size' => 24)));

		// Set the rest of the cells
		$rows = $this->__getSpreadsheetRows($vars_for_spreadsheet);
		$current_row = 3;
		foreach ($rows as $row => $cells) {
			foreach ($cells as $col => $value) {
				// Shift these cells to the right
				$this->__setExcelCell(($col + 1), $current_row, $value);
			}
			$current_row++;
		}

		// Format header cells
		$this->__setStylesFromArray('B3', 'B22', array('font' => array('bold' => true)));
		$this->__setStylesFromArray('C8', 'D8', array('font' => array('bold' => true)));
		$this->__setStylesFromArray('C22', 'C22', array('font' => array('bold' => true)));

		// Reduce the width of the first column
		//   (which contains only the title and overflow over the unoccupied cells to the right)
		$this->objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1.5);

		// Automatically adjust the width of all columns AFTER the first
		$last_col = 4;
		for ($c = 1; $c <= $last_col; $c++) {
			$col_letter = $this->__convertNumToLetter($c);
			$this->objPHPExcel->getActiveSheet()->getColumnDimension($col_letter)->setAutoSize(true);
		}
	}

	public function __setExcelCell($col, $row, $value) {
		$this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $value);
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
}