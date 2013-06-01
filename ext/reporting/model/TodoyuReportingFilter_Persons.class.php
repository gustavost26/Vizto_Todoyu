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
 * Autocompleter for multiple persons
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingFilter_Persons extends TodoyuReportingFilter_AutocompleteMulti {

	/**
	 * Initialize as normal person autocompleter
	 */
	protected function init() {
		parent::init();

		$this->config = Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['assignedPerson'];
	}



	/**
	 * Get selected persons data (id and label)
	 *
	 * @return	Array
	 */
	protected function getSelectedItems() {
		$personIDs	= $this->getValue();
		$persons	= array();

		if( sizeof($personIDs) ) {
			$fields	= '	id,
						CONCAT(lastname, \' \', firstname) as label';
			$table	= 'ext_contact_person';
			$where	= 'id IN(' . implode(',', $personIDs) . ')';
			$order	= 'FIND_IN_SET(id, \'' . implode(',', $personIDs) . '\')';

			$persons= Todoyu::db()->getArray($fields, $table, $where, '', $order);
		}

		return $persons;
	}

}

?>