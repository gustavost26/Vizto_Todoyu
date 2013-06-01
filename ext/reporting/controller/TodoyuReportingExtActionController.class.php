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
 * Ext action controller
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingExtActionController extends TodoyuActionController {

	/**
	 * Initialize
	 *
	 * @param	Array	 $params
	 */
	public function init(array $params) {
		Todoyu::restrict('reporting', 'general:use');
	}



	/**
	 * Default action: render reporting module page
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		Todoyu::restrict('reporting', 'general:area');

			// Set reporting tab
		TodoyuFrontend::setActiveTab('reporting');

			// Add highcharts scripts
		TodoyuHighcharts::addHighcharts();

			// Init page
		TodoyuPage::init('ext/reporting/view/ext.tmpl');

		TodoyuPage::setTitle('reporting.ext.page.title');

		$panelWidgets	= TodoyuReportingRenderer::renderPanelWidgets();
		$reportTabs		= TodoyuReportingRenderer::renderReportTabs();
		$content		= TodoyuReportingRenderer::renderActiveReport();

		TodoyuPage::setTabs($reportTabs);
		TodoyuPage::setPanelWidgets($panelWidgets);
		TodoyuPage::setContent($content);

		return TodoyuPage::render();
	}



	/**
	 * Save open reports
	 *
	 * @param	Array		$params
	 */
	public function openreportsAction(array $params) {
		$reportIDs	= TodoyuArray::intExplode(',', $params['reports'], true, true);

		TodoyuReportingPreferences::saveOpenReports($reportIDs);
	}


	/**
	 * Render message when no open report found
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function noreportopenedAction(array $params) {
		return TodoyuReportingRenderer::renderNoReportFound();
	}



	/**
	 * Toggle given filter from given state, save preference
	 *
	 * @param	Array	$params
	 */
	public function filtertoggleAction(array $params) {
		$idReport	= intval($params['report']);
		$toggled	= intval($params['toggled']) === 1;

		TodoyuReportingPreferences::saveFilterToggle($idReport, $toggled);
	}

}

?>