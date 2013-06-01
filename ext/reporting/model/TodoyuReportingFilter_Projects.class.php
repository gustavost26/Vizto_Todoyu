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
 * Autocompleter for multiple projects
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingFilter_Projects extends TodoyuReportingFilter_AutocompleteMulti {

	/**
	 * Initialize as normal person autocompleter
	 */
	protected function init() {
		parent::init();

		$this->config = Todoyu::$CONFIG['FILTERS']['TASK']['widgets']['project'];
	}



	/**
	 * Get selected persons data (id and label)
	 *
	 * @return	Array
	 */
	protected function getSelectedItems() {
		$projectIDs	= $this->getValue();
		$projects	= array();

		if( sizeof($projectIDs) ) {
			$fields	= '	p.id,
						CONCAT(c.shortname, \' - \', p.title) as label';
			$table	= '	ext_project_project p,
						ext_contact_company c';
			$where	= '		p.id_company	= c.id'
					. ' AND p.id IN(' . implode(',', $projectIDs) . ')';
			$order	= '	FIND_IN_SET(p.id, \'' . implode(',', $projectIDs) . '\')';

			$projects= Todoyu::db()->getArray($fields, $table, $where, '', $order);
		}

		return $projects;
	}

}

?>