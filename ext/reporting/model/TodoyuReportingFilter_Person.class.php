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
 * Autocompleter for a single person
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingFilter_Person extends TodoyuReportingFilter_Autocomplete {

	/**
	 * Initialize filter
	 * Load widget config
	 */
	protected function init() {
		parent::init();

		$this->config = Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['assignedPerson'];
	}



	/**
	 * Get filter value
	 *
	 * @return	Integer		ID of selected person
	 */
	public function getValue() {
		return intval(parent::getValue());
	}

}

?>