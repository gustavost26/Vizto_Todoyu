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
class TodoyuReportingReportActionController extends TodoyuActionController {

	/**
	 * Load a new report and display it
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function loadAction(array $params) {
		$idReport	= trim($params['report']);
		$reportType	= $params['reporttype'];

		if( TodoyuReportingReportManager::isReportID($idReport) ) {
			TodoyuReportingManager::addOpenReport($idReport);
		}

		$report		= TodoyuReportingReportManager::getReport($idReport, $reportType);

		TodoyuHeader::sendTodoyuHeader('tablabel', $report->getTitle());
		TodoyuHeader::sendTodoyuHeader('report', $report->getID());

		return $report->render();
	}



	/**
	 * Save a report
	 * Send new report ID as header
	 *
	 * @param	Array	$params
	 */
	public function saveAction(array $params) {
		$idReport		= trim($params['report']);
		$title			= trim($params['title']);
		$filterValues	= TodoyuArray::assure($params['filter']);
		$reportType		= trim($params['reporttype']);

		$idReport		= TodoyuReportingReportManager::saveReport($idReport, $title, $filterValues, $reportType);

		TodoyuHeader::sendTodoyuHeader('report', $idReport);
	}



	/**
	 * Rename a report
	 *
	 * @param	Array	$params
	 */
	public function renameAction(array $params) {
		$idReport	= intval($params['report']);
		$title		= trim($params['title']);

		TodoyuReportingReportManager::renameReport($idReport, $title);
	}



	/**
	 * Delete a report
	 *
	 * @param	Array	$params
	 */
	public function deleteAction(array $params) {
		$idReport	= intval($params['report']);

		TodoyuReportingReportManager::deleteReport($idReport);
	}



	/**
	 * Refresh report (render again with new filter values)
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function refreshAction(array $params) {
		$idReport		= trim($params['report']);
		$reportType		= $params['reporttype'];
		$filterValues	= TodoyuArray::assure($params['filter']);

		$report		= TodoyuReportingReportManager::getReport($idReport, $reportType);

		$report->setFilterValues($filterValues);

		TodoyuHeader::sendTodoyuHeader('report', $report->getID());

		return $report->render();
	}



	/**
	 * Data for autocomplete elements
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function autocompleteAction(array $params) {
		$idReport	= intval($params['report']);
		$reportType	= $params['reporttype'];
		$filter		= $params['filtername'];
		$search		= $params['search'];

		$report	= TodoyuReportingReportManager::getReport($idReport, $reportType);

		$acValues	= $report->getFilter($filter)->getAutocompleteValues($search);

		return TodoyuRenderer::renderAutocompleteResults($acValues);
	}

}

?>