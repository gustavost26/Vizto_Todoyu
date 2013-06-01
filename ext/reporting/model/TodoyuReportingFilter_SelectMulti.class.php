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
 * Base class for multi select
 * Selected items are added to a list next to the select
 *
 * @package		Todoyu
 * @subpackage	Reporting
 * @abstract
 */
abstract class TodoyuReportingFilter_SelectMulti extends TodoyuReportingFilter_Select {

	/**
	 * Set value: Selected item IDs
	 *
	 * @param	String		$value		Comma separated
	 */
	public function setValue($value) {
		$values	= TodoyuArray::intExplode(',', $value, true, true);

		parent::setValue($values);
	}



	/**
	 * Get selected item IDs
	 *
	 * @return	Array
	 */
	public function getValue() {
		return TodoyuArray::intval(parent::getValue());
	}



	/**
	 * Get value for hidden input field
	 *
	 * @return	String
	 */
	public function getInputValue() {
		return implode(',', $this->getValue());
	}



	/**
	 * Get template for multi select
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return 'core/view/select-inline-multi.tmpl';
	}



	/**
	 * Get template data for multi select
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		$data	= parent::getTemplateData();

		$data['fieldname_multi']= $data['fieldname'];
		$data['fieldname']		= '';
		$data['selectedItems']	= $this->getSelectedItems();
		$data['multiple']		= true;
		$data['size']			= 5;
		$data['onchange']		= '';

		return $data;
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