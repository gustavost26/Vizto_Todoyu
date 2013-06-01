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
class TodoyuReportingReportManager {

	/**
	 * Default table for database requests
	 */
	const TABLE = 'ext_reporting_report';



	/**
	 * Get report of given type
	 *
	 * @param	Integer		$idReport
	 * @param	String		$reportType
	 * @return	TodoyuReportingReport
	 */
	public static function getReport($idReport, $reportType = '') {
		if( TodoyuReportingReportManager::isReportID($idReport) ) {
			$reportData	= TodoyuRecordManager::getRecordData(self::TABLE, $idReport);

			if( !$reportData || $reportData['deleted'] == 1 ) {
				return false;
			}
		} else {
			$reportData	= array(
				'id'			=> $idReport,
				'reporttype'	=> $reportType
			);
		}

		try {
			$report			= TodoyuReportingReportTypeManager::getReportTypeInstance($reportData);

			if( isset($reportData['filtervalues']) ) {
				$filterValues	= unserialize($reportData['filtervalues']);

				$report->setFilterValues($filterValues);
			}

//			$filterValues	= isset($reportData['filtervalues']) ? unserialize($reportData['filtervalues']) : array();
//			$report->setFilterValues($filterValues);

		} catch(TodoyuException $e) {
			return false;
		}

		return $report;
	}



	/**
	 * Save a report (update or create)
	 *
	 * @param	Integer		$idReport
	 * @param	String		$title
	 * @param	Array		$filterValues
	 * @param	String		$reportType
	 * @return	Integer
	 */
	public static function saveReport($idReport, $title, array $filterValues, $reportType = null) {
		$idReport	= intval($idReport);

		if( TodoyuReportingReportManager::isReportID($idReport) ) {
			self::updateReport($idReport, $filterValues);
		} else {
			$idReport = self::addReport($reportType, $title, $filterValues);
		}

		return $idReport;
	}



	/**
	 * Add a new report
	 *
	 * @param	String		$reportType
	 * @param	String		$title
	 * @param	Array		$filterValues
	 * @return	Integer		Report ID
	 */
	public static function addReport($reportType, $title, array $filterValues) {
		$data	= array(
			'reporttype'	=> $reportType,
			'title'			=> $title,
			'filtervalues'	=> serialize($filterValues)
		);

		return TodoyuRecordManager::addRecord(self::TABLE, $data);
	}



	/**
	 * Update a report (filter values)
	 *
	 * @param	Integer		$idReport
	 * @param	Array		$filterValues
	 */
	public static function updateReport($idReport, array $filterValues) {
		$idReport	= intval($idReport);
		$data	= array(
			'filtervalues'	=> serialize($filterValues)
		);

		TodoyuRecordManager::updateRecord(self::TABLE, $idReport, $data);
	}



	/**
	 * Rename report
	 *
	 * @param	Integer		$idReport
	 * @param	String		$title
	 */
	public static function renameReport($idReport, $title) {
		$data	= array(
			'title'	=> $title
		);

		TodoyuRecordManager::updateRecord(self::TABLE, $idReport, $data);
	}



	/**
	 * Delete a report
	 *
	 * @param	Integer		$idReport
	 */
	public static function deleteReport($idReport) {
		$idReport	= intval($idReport);

		TodoyuRecordManager::deleteRecord(self::TABLE, $idReport);
	}



	/**
	 * Get all saved reports of the user
	 *
	 * @return	Array
	 */
	public static function getReports() {
		$fields	= '*';
		$table	= self::TABLE;
		$where	= '		id_person_create= ' . Todoyu::personid()
				. ' AND deleted			= 0';
		$order	= '	reporttype,
					sorting,
					id';

		return Todoyu::db()->getArray($fields, $table, $where, '', $order);
	}



	/**
	 * Get saved reports grouped by type
	 * Has extra information per type
	 *
	 * @return	Array
	 */
	public static function getReportsByType() {
		$reports		= self::getReports();
		$grouped		= array();
		$reportTypes	= TodoyuReportingReportTypeManager::getReportTypes();
		$types			= TodoyuArray::getColumnUnique($reports, 'reporttype');
		$installedTypes	= TodoyuReportingReportTypeManager::getActiveReportTypes();

		foreach($types as $type) {
			if( in_array($type, $installedTypes) ) {
				$grouped[$type] = array(
					'type'		=> $type,
					'label'		=> $reportTypes[$type]['label'],
					'expanded'	=> TodoyuReportingPreferences::getReportTypeToggle($type),
					'reports'	=> array()
				);
			}
		}

			// Group by types
		foreach($reports as $report) {
			if( in_array($report['reporttype'], $installedTypes) ) {
				$grouped[$report['reporttype']]['reports'][] = $report;
			}
		}

		return $grouped;
	}



	/**
	 *
	 * @param	String		$idReport
	 * @return	Boolean
	 */
	public static function isReportID($idReport) {
		return is_numeric($idReport) && trim($idReport) !== '0';
	}



	/**
	 * Save order of the reports of a type
	 *
	 * @param	String		$type
	 * @param	Array		$reportIDs
	 */
	public static function saveReportsOrder($type, array $reportIDs) {
		$where	= 'reporttype = ' . TodoyuSql::quote($type, true);

		TodoyuDbHelper::saveItemSorting(self::TABLE, $reportIDs, 'sorting', $where);
	}

}

?>