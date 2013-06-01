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
class TodoyuReportingResultView_ChartSingleAxis extends TodoyuReportingResultView_Chart {

	/**
	 * Initialize line chart
	 */
	protected function init() {
		parent::init();

		$this->addData(array(
			'yAxis' => array(
				'min'	=> 0
			)
		));

		$this->setType('area');
	}



	/**
	 * Set formatter for Y axis
	 *
	 * @param	String		$function
	 */
	public function setYAxisFormatter($function) {
		$this->addData(array(
			'yAxis' => array(
				'labels' => array(
					'formatter' => TodoyuReportingManager::makeTooltipFormatter($function)
				)
			)
		));
	}


	public function setYAxisRange($min, $max) {
		$this->addData(array(
			'yAxis' => array(
				'min'	=> intval($min),
				'max'	=> intval($max)
			)
		));
	}

}

?>