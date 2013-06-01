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
 */
class TodoyuReportingFilter_FiltersetProject extends TodoyuReportingFilter_Filterset {

	/**
	 * Initialize filter: task filterset
	 */
	protected function init() {
		parent::init();

		$this->config = Todoyu::$CONFIG['FILTERS']['PROJECT']['widgets']['filterSet'];
	}



	/**
	 * Get filter label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Todoyu::Label('reporting.ext.filter.filterset.project');
	}



	/**
	 * Get item IDs for selected task filterset
	 *
	 * @return	Array
	 */
	public function getItemIDs() {
		return TodoyuSearchFiltersetManager::getFiltersetResultItemIDs($this->getFiltersetID(), 0);
	}

}

?>