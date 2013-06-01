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
 * Get status filter
 * Select where multiple selections are allowed
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingFilter_ProjectStatus extends TodoyuReportingFilter_Select {

	/**
	 * Initialize with widget config
	 */
	protected function init() {
		parent::init();

		$this->config = Todoyu::$CONFIG['FILTERS']['PROJECT']['widgets']['status'];
	}



	/**
	 * Get project status option items
	 *
	 * @return	Array
	 */
	protected function getOptions() {
		return $this->getOptionsFromConfig();
	}



	/**
	 * Get project status label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return 'Projekt Status';
	}



	/**
	 * Get template data
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		$data	= parent::getTemplateData();

		$data['multiple']	= true;
		$data['size']		= 5;

		return $data;
	}

}

?>