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

/* ------------------------
	Add report types
   ------------------------ */
if( Todoyu::allowed('reporting', 'report:workloadperproject') ) {
	TodoyuReportingReportTypeManager::addReportType('workloadperproject', 'TodoyuReportingReportType_WorkloadPerProject', 'reporting.basicreports.workloadPerProject');
}
if( Todoyu::allowed('reporting', 'report:timetracking') ) {
	TodoyuReportingReportTypeManager::addReportType('timetracking', 'TodoyuReportingReportType_Timetracking', 'reporting.basicreports.timetracking');
}
if( Todoyu::allowed('reporting', 'report:estimatedtracked') ) {
	TodoyuReportingReportTypeManager::addReportType('estimatedtracked', 'TodoyuReportingReportType_EstimatedTracked', 'reporting.basicreports.estimatedTracked');
}



/* ----------------------------
	Configure panel widgets
   ---------------------------- */
	// Reportlist
TodoyuPanelWidgetManager::addPanelWidget('reporting', 'reporting', 'ReportsList', 20);

?>