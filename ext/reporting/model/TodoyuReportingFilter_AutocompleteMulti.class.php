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
abstract class TodoyuReportingFilter_AutocompleteMulti extends TodoyuReportingFilter_Autocomplete {

	/**
	 * Initialize autocompleter with custom select handler for multiple results
	 */
	protected function init() {
		parent::init();
	}



	/**
	 * Get multi AC template data
	 * AC field is empty, list of selected values
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		$data	= parent::getTemplateData();

		$data['valueLabel']		= '';
		$data['selectedItems']	= $this->getSelectedItems();

		return $data;
	}



	/**
	 * Get template to render autocomplete multi filter
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return 'core/view/autocomplete-inline-multi.tmpl';
	}



	/**
	 * Get selected items
	 *
	 * @return	Array
	 */
	public function getValue() {
		return TodoyuArray::intExplode(',', parent::getValue(), true, true);
	}



	/**
	 * Get selected items as concatenated string to submit in hidden field
	 *
	 * @return	String
	 */
	public function getInputValue() {
		return implode(',', $this->getValue());
	}



	/**
	 * Check whether the filter has a valid value
	 * True if at least one element is selected
	 *
	 * @return	Boolean
	 */
	public function hasValidValue() {
		return sizeof($this->getValue()) > 0;
	}



	/**
	 * Data function the get labels for selected items
	 *
	 * @abstract
	 * @return	Array
	 */
	abstract protected function getSelectedItems();

}

?>