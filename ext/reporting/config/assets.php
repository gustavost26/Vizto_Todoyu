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
 * Assets (JS, CSS) requirements for reporting extension
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */

Todoyu::$CONFIG['EXT']['reporting']['assets'] = array(
	'js' => array(
		array(
			'file'		=> 'ext/reporting/asset/js/Ext.js',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/reporting/asset/js/Filter.js',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/reporting/asset/js/PanelWidgetReportsList.js',
			'position'	=> 101
		),
		array(
			'file'		=> 'ext/reporting/asset/js/ResultView.js',
			'position'	=> 102
		),
		array(
			'file'		=> 'ext/reporting/asset/js/Tab.js',
			'position'	=> 102
		),
		array(
			'file'		=> 'ext/reporting/asset/js/Formatter.js',
			'position'	=> 102
		),
		array(
			'file'		=> 'lib/js/tablekit/tablekit.js',
			'position'	=> 110
		)
	),
	'css' => array(
		array(
			'file'		=> 'ext/reporting/asset/css/ext.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/reporting/asset/css/filters.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/reporting/asset/css/result.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/reporting/asset/css/panelwidget-reportslist.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/reporting/asset/css/views.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'ext/reporting/asset/css/reports.scss',
			'position'	=> 100
		),
		array(
			'file'		=> 'core/asset/js/tablekit/style.scss',
			'position'	=> 100
		)
	)
);

?>