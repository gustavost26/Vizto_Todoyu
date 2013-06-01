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
 * Panelwidget with selector to create new reports from types
 * and list of all saved reports of the user
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingPanelWidgetReportsList extends TodoyuPanelWidget {

	/**
	 * Initialize panel widget
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 */
	public function __construct(array $config, array $params = array()) {
			// Construct PanelWidget (init basic configuration)
		parent::__construct(
			'reporting',									// ext key
			'reportslist',									// panel widget ID
			'reporting.panelwidget-reportslist.title',	// widget title text
			$config,										// widget config array
			$params											// widget parameters
		);

		$this->addHasIconClass();

			// Init widget JS (observers)
		TodoyuPage::addJsInit('Todoyu.Ext.reporting.PanelWidget.ReportsList.init()');
	}



	/**
	 * Render the panel widget content
	 *
	 * @return	String
	 */
	public function renderContent() {
		$tmpl	= 'ext/reporting/view/panelwidget-reportslist.tmpl';
		$data	= array(
			'id'			=> $this->getID(),
			'reportTypes'	=> $this->getReportTypeOptions(),
			'reports'		=> TodoyuReportingReportManager::getReportsByType()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get options for report type selector
	 *
	 * @return	Array
	 */
	private function getReportTypeOptions() {
		$reportTypes= TodoyuReportingReportTypeManager::getReportTypes();
		$reformConfig		= array(
			'label'	=> 'label',
			'type'	=> 'value'
		);

		return TodoyuArray::reform($reportTypes, $reformConfig);
	}

}

?>