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
 * Multi select field with internal projects as value
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingFilter_ProjectsInternal extends TodoyuReportingFilter_SelectMulti {

	/**
	 * Initialize as normal person autocompleter
	 */
	protected function init() {
		parent::init();
	}



	/**
	 * Get options (internal projects)
	 *
	 * @return	Array
	 */
	protected function getOptions() {
		$fields	= '	p.id,
					p.title';
		$tables	= '	ext_project_project p,
					ext_contact_company c';
		$where	= '		p.id_company	= c.id'
				. '	AND c.is_internal	= 1'
				. ' AND p.deleted		= 0'
				. ' AND c.deleted		= 0';
		$order	= '	p.title';

		$projects	= Todoyu::db()->getArray($fields, $tables, $where, '', $order);
		$reformConfig		= array(
			'id'	=> 'value',
			'title'	=> 'label'
		);

		return TodoyuArray::reform($projects, $reformConfig);
	}



	/**
	 * Get filter label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Todoyu::Label('reporting.ext.filter.internalProjects');
	}



	/**
	 * Get selected item labels
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