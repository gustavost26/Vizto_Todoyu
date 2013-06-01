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
 * Base class for select filters
 *
 * @package		Todoyu
 * @subpackage	Reporting
 * @abstract
 */
abstract class TodoyuReportingFilter_Select extends TodoyuReportingFilter {

	/**
	 * Initialize select filter
	 */
	protected function init() {
		parent::init();
	}



	/**
	 * Set filter value
	 * If not an array, the value will be the first value in the array
	 *
	 * @param	Array|String	$value
	 */
	public function setValue($value) {
		if( is_null($value) ) {
			$value = array();
		} else {
			$value	= TodoyuArray::assure($value, true);
		}

		parent::setValue($value);
	}



	/**
	 * Get filter value
	 *
	 * @return	Array
	 */
	public function getValue() {
		return TodoyuArray::assure(parent::getValue());
	}



	/**
	 * Check whether the filter has a valid value
	 * At least one item is selected
	 *
	 * @return	Boolean
	 */
	public function hasValidValue() {
		return sizeof($this->value) > 0 && $this->value[0] != 0;
	}



	/**
	 * Get render template
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return 'core/view/select-inline.tmpl';
	}



	/**
	 * Get template render data
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		$data	= parent::getTemplateData();

		if( is_array($this->settings['renderData']) ) {
			$data	= array_merge($data, $this->settings['renderData']);
		}

		$data['onchange']	= 'Todoyu.Ext.reporting.refreshReport()';
		$data['options']	= TodoyuArray::assure($this->getOptions());
		$data['size']		= 1;
		$data['multiple']	= false;

		return $data;
	}



	/**
	 * Get option items from config if config is from widget
	 *
	 * @return	Array[]
	 */
	protected function getOptionsFromConfig() {
		$defs	= TodoyuFunction::callUserFunction($this->config['wConf']['FuncRef'], array());

		return TodoyuArray::assure($defs['options']);
	}



	/**
	 * Get options for the select
	 *
	 * @return	Array[]
	 */
	abstract protected function getOptions();

}

?>