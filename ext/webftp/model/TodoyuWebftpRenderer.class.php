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
 * @subpackage	Webftp
 */
class TodoyuWebftpRenderer {

	/**
	 * Extension key
	 *
	 * @var	String
	 */
	const EXTKEY = 'webftp';

	/**
	 * Render panel widgets for reporting area
	 *
	 * @return	String
	 */
	public static function renderPanelWidgets() {
		return TodoyuPanelWidgetRenderer::renderPanelWidgets(self::EXTKEY);
	}



	/**
	 * Render report tabs
	 *
	 * @return	String
	 */
	public static function renderReportTabs() {
		$name		= 'webftp';
		$jsHandler	= 'Todoyu.Ext.webftp.Tab.onSelect.bind(Todoyu.Ext.webftp.Tab)';
		$tabs		= TodoyuReportingManager::getOpenReportTabs();
		$active		= TodoyuReportingManager::getActiveReportID();

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $active);
	}



	/**
	 * Render the active report (or info screen if no report available)
	 *
	 * @return	String
	 */
	public static function renderActiveReport() {
		$idReport	= TodoyuReportingManager::getActiveReportID();

		if( $idReport === 0 ) {
			return self::renderNoReportFound();
		} else {
			return self::renderReport($idReport);
		}
	}



	/**
	 * Render a full report
	 *
	 * @param	Integer		$idReport
	 * @return	String
	 */
	public static function renderReport($idReport) {
		$idReport	= intval($idReport);

		$report		= TodoyuReportingReportManager::getReport($idReport);

		return $report->render();
	}



	/**
	 * @return	String
	 */
	public static function renderNoReportFound() {
		$tmpl	= PATH_EXT_WEBFTP.'/view/no-report.tmpl';
		$data	= array();

		return Todoyu::render($tmpl, $data);
	}

}

?>