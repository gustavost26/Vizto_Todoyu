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
class TodoyuReportingFilter_Jobtypes extends TodoyuReportingFilter_SelectMulti {

	/**
	 * Initialize as normal person autocompleter
	 */
	protected function init() {
		parent::init();

		$this->config = array(
			'label'		=> 'contact.ext.jobtype'
		);
	}



	/**
	 * Get options for jobtype selector filter
	 *
	 * @return	Array
	 */
	protected function getOptions() {
		return TodoyuContactJobTypeManager::getJobTypeOptions();
	}



	/**
	 * Get selected persons data (id and label)
	 *
	 * @return	Array
	 */
	protected function getSelectedItems() {
		$jobtypeIDs	= $this->getValue();
		$jobtypes	= array();

		if( sizeof($jobtypeIDs) > 0 ) {
			$fields	= '	id,
						title as label';
			$table	= '	ext_contact_jobtype';
			$where	= '	id IN(' . implode(',', $jobtypeIDs) . ')';
			$order	= '	FIND_IN_SET(id, \'' . implode(',', $jobtypeIDs) . '\')';

			$jobtypes= Todoyu::db()->getArray($fields, $table, $where, '', $order);
		}

		return $jobtypes;
	}



	/**
	 * Get persons which have one of the selected jobtypes
	 *
	 * @return	Array
	 */
	public function getPersonIDs() {
		$jobtypeIDs	= $this->getValue();
		$personIDs	= array();

		if( sizeof($jobtypeIDs) > 0 ) {
			$fields	= '	p.id';
			$tables	= '	ext_contact_mm_company_person cp,
						ext_contact_person p';
			$where	= '		cp.id_person	= p.id'
					. ' AND	p.deleted		= 0'
					. ' AND cp.id_jobtype IN(' . implode(',', $jobtypeIDs) . ')';
			$field	= 'id';

			$personIDs	= Todoyu::db()->getColumn($fields, $tables, $where, '', '', '', $field);
		}

		return $personIDs;
	}

}

?>