<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2011, snowflake productions GmbH, Switzerland
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
 * @subpackage	Tutorial
 */
class TodoyuWebftpExtActionController extends TodoyuActionController {


	/**
	 * Default action for tutorial ext action controller
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
		//return 'Default action controller';
		Todoyu::restrict('webftp', 'general:area');

		// Set reporting tab
		TodoyuFrontend::setActiveTab('webftp');

		// Add highcharts scripts
		TodoyuHighcharts::addHighcharts();

		// Init page
		TodoyuPage::init('ext/webftp/view/ext.tmpl');

		TodoyuPage::setTitle('webftp.ext.page.title');

		$panelWidgets	= TodoyuReportingRenderer::renderPanelWidgets();
		$reportTabs		= TodoyuReportingRenderer::renderReportTabs();
		$content		= TodoyuReportingRenderer::renderActiveReport();

		TodoyuPage::setTabs($reportTabs);
		TodoyuPage::setPanelWidgets($panelWidgets);
		TodoyuPage::setContent($content);

		return TodoyuPage::render();
	}

}

?>