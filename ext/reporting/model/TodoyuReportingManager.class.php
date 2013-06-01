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
class TodoyuReportingManager {

	/**
	 * Get tabs for open reports
	 *
	 * @return	Array
	 */
	public static function getOpenReportTabs() {
		$reportIDs				= TodoyuReportingPreferences::getOpenReportIDs();
		$installedReportTypes	= TodoyuReportingReportTypeManager::getActiveReportTypes();
		$tabs					= array();

			// Get tab data
		if( sizeof($reportIDs) > 0 ) {
			$reportList	= implode(',', $reportIDs);

			$fields	= '	id,
						title,
						reporttype';
			$table	= '	ext_reporting_report';
			$where	= '		id_person_create= ' . Todoyu::personid()
					. '	AND	deleted			= 0'
					. ' AND id IN(' . $reportList . ')';
			$order	= 'FIELD(id, ' . $reportList . ')';
			$limit	= 3;

			$reports= Todoyu::db()->getArray($fields, $table, $where, '', $order, $limit);

			foreach($reports as $report) {
				if( in_array($report['reporttype'], $installedReportTypes) ) {
					$tabs[] = array(
						'id'	=> $report['id'],
						'label'	=> $report['title']
					);
				}
			}
		}

			// If not tabs where found (maybe deleted), add dummy tab
		if( sizeof($tabs) === 0 ) {
				// Add dummy tab
			$tabs	= array(
				array(
					'id'	=> 'noid',
					'label'	=> 'No reports opened'
				)
			);
		}

		return $tabs;
	}



	/**
	 * Add a report to the opened list
	 *
	 * @param	Integer		$idReport
	 * @return	Array		List of now open reports
	 */
	public static function addOpenReport($idReport) {
		$idReport	= intval($idReport);

		$openReports= TodoyuReportingPreferences::getOpenReportIDs();

		array_unshift($openReports, $idReport);

		$openReports	= array_unique($openReports);

		TodoyuReportingPreferences::saveOpenReports($openReports);

		return $openReports;
	}



	/**
	 * Get the active report (first in opened list)
	 *
	 * @return	Integer
	 */
	public static function getActiveReportID() {
		$openReports	= TodoyuReportingPreferences::getOpenReportIDs();
		$activeReports	= TodoyuReportingReportTypeManager::getActiveReportTypes();
		$activeReport	= 0;

			// Abort if no reports are open
		if( sizeof($openReports) === 0 ) {
			return 0;
		}
			// Abort if no report types are installed
		if( sizeof($activeReports) === 0 ) {
			return 0;
		}

		if( sizeof($openReports) > 0 ) {
			$openList		= implode(',', $openReports);
			$activeTypeList	= TodoyuArray::implodeQuoted($activeReports);

			$field	= 'id';
			$table	= 'ext_reporting_report';
			$where	= '		id			IN(' . $openList . ')'
					. ' AND reporttype	IN(' . $activeTypeList . ')'
					. ' AND deleted		= 0';
			$order	= 'FIND_IN_SET(id, \'' . $openList . '\')';
			$limit	= '1';

			$idReport		= Todoyu::db()->getFieldValue($field, $table, $where, '', $order, $limit);
			$activeReport	= intval($idReport);
		}

		return $activeReport;
	}


	/**
	 * Make code to add a js function as tooltip formatter with correct bindings
	 *
	 * @param	String		$function
	 * @return	String
	 */
	public static function makeTooltipFormatter($function) {
		return 'function(){return Todoyu.getFunctionFromString(\'' . $function . '\').call(this, Todoyu.getContext(\'' . $function . '\'));}';
	}

}

?>