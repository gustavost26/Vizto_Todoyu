<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * [Enter Class Description]
 *
 * @package		Todoyu
 * @subpackage	Reporting
 * @abstract
 */
abstract class TodoyuReportingFilter_Autocomplete extends TodoyuReportingFilter {

	/**
	 * Initialize AC
	 */
	protected function init() {
		parent::init();
	}



	/**
	 * Get template data for AC
	 * Label for the selected record
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		$data	= parent::getTemplateData();

		$data['valueLabel']	= $this->getValueLabel();

		return $data;
	}



	/**
	 * Get template to render autocomplete filter
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return 'core/view/autocomplete-inline.tmpl';
	}



	/**
	 * Check whether the autocomplete filter has a valid value
	 *
	 * @return	Boolean
	 */
	public function hasValidValue() {
		$value	= $this->getValue();

		if( is_null($value) ) {
			return false;
		}

		if( is_array($value) ) {
			return sizeof($value) > 0;
		}

		if( is_string($value) ) {
			return $value !== '';
		}

		if( is_integer($value) ) {
			return $value !== 0;
		}

		return true;
	}



	/**
	 * Get label for the selected value
	 *
	 * @return	String
	 */
	protected function getValueLabel() {
		if( ! $this->hasValidValue() ) {
			return '';
		}

		$defs = TodoyuFunction::callUserFunction($this->config['wConf']['LabelFuncRef'], array(
			'value'	=> $this->getValue()
		));

		return $defs['value_label'];
	}



	/**
	 * Get autocomplete values from configured callback function
	 *
	 * @param	String		$search
	 * @return	Array
	 */
	public function getAutocompleteValues($search) {
		$acFuncRef		= $this->config['wConf']['FuncRef'];
		$filterValues	= $this->getReport()->getFilterValues();

		return TodoyuFunction::callUserFunction($acFuncRef, $search, $filterValues);
	}

}

?>