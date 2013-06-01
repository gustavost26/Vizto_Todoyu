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
 * Manager for report types
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingReportTypeManager {

	/**
	 * Report type configs
	 *
	 * @var	Array
	 */
	private static $reportTypes = array();



	/**
	 * Add a report type to config array
	 *
	 * @param	String		$type			Key to identify the type
	 * @param	String		$class			PHP class
	 * @param	String		$label
	 */
	public static function addReportType($type, $class, $label) {
		$type	= strtolower($type);

		self::$reportTypes[$type] = array(
			'type'	=> $type,
			'class'	=> $class,
			'label'	=> Todoyu::Label($label)
		);
	}



	/**
	 * Get configuration for report type
	 *
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getReportTypeConfig($type) {
		$type	= strtolower($type);

		return TodoyuArray::assure(self::$reportTypes[$type]);
	}



	/**
	 * Get report object for report type
	 *
	 * @param	Array						$reportData
	 * @return	TodoyuReportingReport
	 * @throws	TodoyuException
	 */
	public static function getReportTypeInstance(array $reportData) {
		$config	= self::getReportTypeConfig($reportData['reporttype']);

		if( class_exists($config['class']) ) {
			return new $config['class']($reportData);
		} else {
			throw new TodoyuException('No class found for report type "' . $reportData['reporttype'] . '"');
		}
	}



	/*
	 * Get all report types
	 *
	 * @return	Array
	 */
	public static function getReportTypes() {
		return self::$reportTypes;
	}



	/**
	 * Get installed report types
	 *
	 * @return	Array
	 */
	public static function getActiveReportTypes() {
		return array_keys(self::$reportTypes);
	}

}

?>