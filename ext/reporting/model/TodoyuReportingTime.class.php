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
 * Helper class to handle time and dates for reporting
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingTime {

	/**
	 * Cache for month maps
	 *
	 * @var	Array
	 */
	private static $monthMaps = array();



	/**
	 * Get month keys for a range
	 * Format: Year-month (2010-05)
	 *
	 * @param	Integer		$start
	 * @param	Integer		$end
	 * @return	Array
	 */
	public static function getMonthMapInRange($start, $end) {
		$start	= TodoyuTime::getMonthStart($start);
		$end	= TodoyuTime::getMonthStart($end);
		$key	= date('Y-m', $start) . '-' . date('Y-m', $end);

		if( ! array_key_exists($key, self::$monthMaps) ) {
			$keys	= array();
			$current= $start;

			while( $current <= $end ) {
				$keys[]	= date('Y-m', $current);

				$current= mktime(0, 0, 0, date('n', $current) + 1, 1, date('Y', $current));
			}

			$map	= array_flip($keys);

			foreach($map as $index => $value) {
				$map[$index] = 0;
			}

			self::$monthMaps[$key] = $map;
		}

		return self::$monthMaps[$key];
	}



	/**
	 * Get first and last tracking date for persons
	 *
	 * @param	Array	$personIDs
	 * @return	Array|Boolean		[start,end]
	 */
	public static function getRangeOfPersonTrackings(array $personIDs) {
		$personIDs	= TodoyuArray::intval($personIDs, true, true);

		if( sizeof($personIDs) === 0 ) {
			return false;
		}

		$fields	= '	MIN(date_track) as start,
					MAX(date_track) as end';
		$table	= '	ext_timetracking_track';
		$where	= '		date_track > 0'
				. ' AND id_person_create IN(' . implode(',', $personIDs) . ')';

		return Todoyu::db()->getRecordByQuery($fields, $table, $where);
	}



	/**
	 * Get range of the projects
	 * Max and min dates of start and end date of all projects
	 *
	 * @param	Integer[]		$projectIDs
	 * @return	Array|Boolean
	 */
	public static function getRangeOfProjects(array $projectIDs) {
		$projectIDs	= TodoyuArray::intval($projectIDs, true, true);

		if( sizeof($projectIDs) === 0 ) {
			return false;
		}

		$fields	= '	MIN(date_start) as start,
					MAX(date_end) as end,
					MAX(date_deadline) as deadline';
		$table	= '	ext_project_project';
		$where	= TodoyuSql::buildInListQueryPart($projectIDs, 'id');

		$data	= Todoyu::db()->getRecordByQuery($fields, $table, $where);

		return array(
			'start'	=> $data['start'],
			'end'	=> max($data['end'], $data['deadline'])
		);
	}



	/**
	 * Get range for all tasks in the project (which have a given status)
	 *
	 * @param	Integer[]		$projectIDs
	 * @param	Integer[]		$status
	 * @return	Integer[]|Boolean
	 */
	public static function getRangeOfProjectsTasks(array $projectIDs, array $status = array()) {
		$projectIDs	= TodoyuArray::intval($projectIDs, true, true);
		$status		= TodoyuArray::intval($status, true, true);

		if( sizeof($projectIDs) === 0 ) {
			return false;
		}

		$fields	= '	MIN(date_start) as start,
					MAX(date_end) as end,
					MAX(date_deadline) as deadline';
		$table	= '	ext_project_task';
		$where	= '		deleted	= 0'
				. ' AND	' . TodoyuSql::buildInListQueryPart($projectIDs, 'id_project');

		if( sizeof($status) > 0 ) {
			$where .= ' AND ' . TodoyuSql::buildInListQueryPart($status, 'status');
		}

		$data	= Todoyu::db()->getRecordByQuery($fields, $table, $where);

		return array(
			'start'	=> $data['start'],
			'end'	=> min(TodoyuTime::MAX, max($data['end'], $data['deadline']))
		);
	}



	/**
	 * Get range of all projects
	 *
	 * @return	Array
	 */
	public static function getRangeOfAllProjects() {
		$fields	= '	MIN(date_start) as start,
					MAX(date_end) as end,
					MIN(date_create) as date_create,
					MAX(date_update) as date_update ';
		$table	= '	ext_project_project';
		$data	= Todoyu::db()->getRecordByQuery($fields, $table);

		return array(
			'start'	=> max($data['start'], $data['date_create']),
			'end'	=> max($data['end'], $data['date_update'])
		);
	}


	/**
	 * Get range of projects with given statuses
	 *
	 * @param	Array		$statues
	 * @return	Array|Boolean
	 */
	public static function getRangeOfProjectsInStatus(array $statues) {
		$statues	= TodoyuArray::intval($statues, true, true);

		if( sizeof($statues) === 0 ) {
			return false;
		}

		$maxFutureDate	= TodoyuTime::addDays(NOW, 1000);

		$fields	= '	MIN(date_start) as start,
					MAX(date_end) as end,
					MIN(date_create) as date_create,
					MAX(date_update) as date_update ';
		$table	= '	ext_project_project';
		$where	= '	status IN(' . implode(',', $statues) . ')'
				. ' AND date_start < ' . NOW
				. ' AND date_end < ' . $maxFutureDate;

		$data	= Todoyu::db()->getRecordByQuery($fields, $table, $where);

		return array(
			'start'	=> max($data['start'], $data['date_create']),
			'end'	=> max($data['end'], $data['date_update'])
		);
	}



	/**
	 * Get range of projects of a company with given statuses
	 *
	 * @param	Integer		$idCompany
	 * @param	Array		$statuses
	 * @return	Array
	 */
	public static function getRangeOfCompanyProjects($idCompany, array $statuses = array()) {
		$idCompany	= intval($idCompany);
		$statuses	= TodoyuArray::intval($statuses, true, true);

		$fields	= '	MIN(date_start) as start,
					MAX(date_end) as end,
					MIN(date_create) as date_create,
					MAX(date_update) as date_update ';
		$table	= '	ext_project_project';
		$where	= '	id_company = ' . $idCompany;

		if( sizeof($statuses) > 0 ) {
			$where .= ' AND status IN(' . implode(',', $statuses) . ')';
		}

		$data	= Todoyu::db()->getRecordByQuery($fields, $table, $where);

		return array(
			'start'	=> max($data['start'], $data['date_create']),
			'end'	=> max($data['end'], $data['date_update'])
		);

	}



	/**
	 * Get labels for months in a range
	 *
	 * @param	Integer		$start
	 * @param	Integer		$end
	 * @param	String		$format		Format
	 * @return	Array
	 */
	public static function getMonthLabels($start, $end, $format = null) {
		$monthMap	= self::getMonthMapInRange($start, $end);
		$monthKeys	= array_keys($monthMap);
		$labels		= array();
		$format		= is_null($format) ? 'MlongY4' : $format;

		foreach($monthKeys as $monthKey) {
			list($year, $month) = explode('-', $monthKey);
			$labels[] = TodoyuTime::format(mktime(0, 0, 0, $month, 1, $year), $format);
		}

		return $labels;
	}



	/**
	 * Convert an array with seconds values to hours
	 *
	 * @param	Array		$array
	 * @param	Integer		$round		Rounding precision
	 * @return	Array
	 */
	public static function secondsToHours(array $array, $round = 0) {
		foreach($array as $index => $value) {
			$array[$index] = round($value/3600, $round);
		}

		return $array;
	}



	/**
	 * Format a month key (YEAR-MONTH) with given format
	 *
	 * @param	String		$monthString
	 * @param	String		$format
	 * @return	String
	 */
	public static function formatMonth($monthString, $format = 'MlongY4') {
		list($year, $month)	= explode('-', $monthString);
		$time	= mktime(0, 0, 0, $month, 1, $year);

		 return TodoyuTime::format($time, $format);
	}

}

?>