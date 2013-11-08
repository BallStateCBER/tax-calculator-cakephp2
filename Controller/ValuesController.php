<?php
class ValuesController extends AppController {
	public $name = 'Values';
	public $components = array('RequestHandler');
	public $helpers = array();
	public $uses = array('Value');	
	
	/* 
	ILLINOIS
		State sales tax			min		avg 	max
	  	Food at home			1%		1%		1%
		Food away from home		6.25%	8.22%	11.50%
		Housekeeping Supplies	6.25%	8.22%	11.50%
		Apparel and services	6.25%	8.22%	11.50%
	 	
	 	State income tax	5.00%
	 	
		Adjusted Income = Household Income - Exemptions 
		total Income taxes ( excluding federal) = Adjusted Income * State Income Tax 
		Property tax = Home value * Property tax rate
		
	
	INDIANA
		Assessed Home value = Home value - Adjustment 
		Property tax = Assessed Home value * Property tax rate
		State income tax 		3.40%
		
		Adjusted Gross Income = Household Income - Exemptions 
		County Income Tax due = Adjusted Gross Income * County Income Tax
		State Income Tax due = Adjusted Gross Income * State Income Tax
		Total State and Local Income taxes (excludes federal) = County Income Tax due + State Income Tax due

		Total Annual Sales Tax = Average annual expenditure * Sum of Expenditure Index * .01 * Sales Tax Rate
	 */
}