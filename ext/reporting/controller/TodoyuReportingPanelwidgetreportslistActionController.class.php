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
 * Panelwidget action controller
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingPanelwidgetreportslistActionController extends TodoyuActionController {

	/**
	 * @param	Array	$params
	 */
	public function init(array $params) {
		Todoyu::restrict('reporting', 'general:use');
	}



	/**
	 * Render content of panelwidget
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function refreshAction(array $params) {
		$widget	= TodoyuPanelWidgetManager::getPanelWidget('reporting', 'ReportsList');

		/**
		 * @var	TodoyuReportingPanelWidgetReportsList	$widget
		 */

		return $widget->renderContent();
	}



	/**
	 * Save order of reports in a group
	 *
	 * @param	Array		$params
	 */
	public function saveorderAction(array $params) {
		$type		= trim($params['type']);
		$reportIDs	= TodoyuArray::intExplode(',', $params['items'], true, true);

		TodoyuReportingReportManager::saveReportsOrder($type, $reportIDs);
	}



	/**
	 * Toggle given type expand state from given one, save preference
	 *
	 * @param	Array	$params
	 */
	public function savetoggleAction(array $params) {
		$type		= trim($params['type']);
		$expanded	= intval($params['expanded']) === 1;

		TodoyuReportingPreferences::saveReportTypeToggle($type, $expanded);
	}

}

?>