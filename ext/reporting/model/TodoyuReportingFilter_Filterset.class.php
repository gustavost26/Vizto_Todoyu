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
abstract class TodoyuReportingFilter_Filterset extends TodoyuReportingFilter_Select {

	/**
	 * Initialize filterset filter
	 */
	protected function init() {
		parent::init();

		$this->config = array();
	}



	/**
	 * Get filterset as options
	 *
	 * @return	Array
	 */
	protected function getOptions() {
		$defs	= TodoyuFunction::callUserFunction($this->config['wConf']['FuncRef'], array());

		return $defs['options'];
	}



	/**
	 * Get template render data
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		$data	= parent::getTemplateData();

		$data['size']			= 1;
		$data['multiple']		= false;
		$data['noPleaseSelect']	= false;

		return $data;
	}



	/**
	 * Get ID of selected filterset
	 *
	 * @return	Integer
	 */
	protected function getFiltersetID() {
		$selectedOptions = $this->getValue();

		return intval($selectedOptions[0]);
	}



	/**
	 * Get item IDs for selected filterset
	 *
	 * @return	Array
	 */
	abstract public function getItemIDs();

}

?>