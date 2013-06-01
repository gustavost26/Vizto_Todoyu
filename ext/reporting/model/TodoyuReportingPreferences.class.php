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
 * @subpackage	[Subpackage]
 */
class TodoyuReportingPreferences {

	/**
	 * Save a preference for project
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Boolean		$unique
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 */
	public static function savePref($preference, $value, $idItem = 0, $unique = false, $idArea = 0, $idPerson = 0) {
		TodoyuPreferenceManager::savePreference(EXTID_REPORTING, $preference, $value, $idItem, $unique, $idArea, $idPerson);
	}



	/**
	 * Get a preference
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Boolean		$unserialize
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function getPref($preference, $idItem = 0, $idArea = 0, $unserialize = false, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreference(EXTID_REPORTING, $preference, $idItem, $idArea, $unserialize, $idPerson);
	}



	/**
	 * Get all preferences of project
	 *
	 * @param	String		$preference
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getPrefs($preference, $idItem = 0, $idArea = 0, $idPerson = 0) {
		return TodoyuPreferenceManager::getPreferences(EXTID_REPORTING, $preference, $idItem, $idArea, $idPerson);
	}



	/**
	 * Delete project preference
	 *
	 * @param	String		$preference
	 * @param	String		$value
	 * @param	Integer		$idItem
	 * @param	Integer		$idArea
	 * @param	Integer		$idPerson
	 */
	public static function deletePref($preference, $value = null, $idItem = 0, $idArea = 0, $idPerson = 0) {
		TodoyuPreferenceManager::deletePreference(EXTID_REPORTING, $preference, $value, $idItem, $idArea, $idPerson);
	}



	/**
	 * Save current open reports
	 *
	 * @param	Array		$openReportIDs
	 */
	public static function saveOpenReports(array $openReportIDs) {
		$openReportIDs	= TodoyuArray::intval($openReportIDs, true, true);
		$openReportIDs	= array_slice($openReportIDs, 0, 3);
		$value			= implode(',', $openReportIDs);

		self::savePref('reports', $value, 0, true);
	}



	/**
	 * Get open report IDs
	 *
	 * @return	Array
	 */
	public static function getOpenReportIDs() {
		$value			= self::getPref('reports');

		return TodoyuArray::intExplode(',', $value);
	}



	/**
	 * Get active report
	 *
	 * @return	Integer
	 */
	public static function getActiveReport() {
		$reports	= self::getOpenReportIDs();

		return intval($reports[0]);
	}



	/**
	 * Save preference of expand toggling state of given report type
	 *
	 * @param	String		$type
	 * @param	Boolean		$expanded
	 */
	public static function saveReportTypeToggle($type, $expanded) {
		$pref	= 'typetoggle:' . $type;
		$value	= $expanded ? 1 : 0;

		self::savePref($pref, $value, 0, true);
	}



	/**
	 * Get current expanded state of given report type
	 *
	 * @param	String		$type
	 * @return	Boolean
	 */
	public static function getReportTypeToggle($type) {
		$pref	= 'typetoggle:' . $type;
		$value	= self::getPref($pref);

		return ( $value === false || $value == 1 ) ? true : false;
	}



	/**
	 * Save toggle status of the report filter area
	 *
	 * @param	Integer		$idReport
	 * @param	Boolean		$toggled
	 */
	public static function saveFilterToggle($idReport, $toggled) {
		self::savePref('filtertoggle', $toggled?1:0, $idReport, true);
	}



	/**
	 * Get filter area toggle status for report
	 *
	 * @param	Integer		$idReport
	 * @return	Boolean
	 */
	public static function getFilterToggle($idReport) {
		return self::getPref('filtertoggle', $idReport) == 1;
	}

}

?>