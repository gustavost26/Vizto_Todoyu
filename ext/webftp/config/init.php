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

//TodoyuPanelWidgetManager::addPanelWidget('webftp', 'webftp', 'ReportsList', 20);

Todoyu::$CONFIG['EXT']['webftp'] = array(
	'defaultTab'	=> 'icon',
	'tabs' => array(
		'icon'	=> array(
			'key'		=> 'icon',
			'id'		=> 'icon',
			'label'		=> 'webftp.ext.icon',
			'require'	=> 'webftp.general:area',
			'position'	=> 105
		),
		'list'	=> array(
			'key'		=> 'list',
			'id'		=> 'list',
			'label'		=> 'webftp.ext.list',
			'require'	=> 'webftp.general:area',
			'position'	=> 110
		)
	),
);