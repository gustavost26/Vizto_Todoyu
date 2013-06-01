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
 * Chart version of a result view
 * Data is rendered as graph with highcharts
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingResultView_ChartCombined extends TodoyuReportingResultView_ChartSingleAxis {

	/**
	 * Initialize line chart
	 */
	protected function init() {
		parent::init();

//		$this->addData(array());

//		$this->setType('column');
	}



	/**
	 * @todo	comment
	 *
	 * @param	String	$type
	 */
	public function setDefaultType($type) {
		$this->setType($type);
	}



	/**
	 * @todo	comment
	 *
	 * @param	String		$name
	 * @param	Array		$data
	 * @param	Boolean		$type
	 */
	public function addSerie($name, array $data, $type = false) {
		$type	= $type ? $type : $this->getType();

		$this->data['series'][] = array(
			'type'	=> $type,
			'name'	=> Todoyu::Label($name),
			'data'	=> TodoyuArray::floatval(array_values($data))
		);
	}

}

?>