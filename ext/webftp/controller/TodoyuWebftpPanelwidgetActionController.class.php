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
 * Action controller for daytracks panelwidget
 *
 * @package		Todoyu
 * @subpackage	Daytracks
 */
class TodoyuWebftpPanelwidgetActionController extends TodoyuActionController {

	/**
	 * Init. Check rights for panelwidget
	 *
	 * @param	Array		$params
	 */
	public function init(array $params) {
		exit('alskfj');
		Todoyu::restrict('webftp', 'general:use');
	}



	/**
	 * Update the panelwidget content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function updateAction(array $params) {
		$panelWidget = TodoyuPanelWidgetManager::getPanelWidget('webftp', 'Fileoperation');

		return $panelWidget->getContent();
	}

}

?>