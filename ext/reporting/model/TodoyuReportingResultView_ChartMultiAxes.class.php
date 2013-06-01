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
class TodoyuReportingResultView_ChartMultiAxes extends TodoyuReportingResultView_Chart {

	/**
	 * Initialize line chart
	 */
	protected function init() {
		parent::init();
	}



	/**
	 * Add config data for a serie
	 * Data key will be parsed as floats
	 *
	 * @param	Array	$serieConfig
	 */
	public function addSerieConfig(array $serieConfig) {
		$serieConfig['data']	= TodoyuArray::floatval($serieConfig['data']);
		$this->data['series'][] = $serieConfig;
	}



	/**
	 * Add custom Y axis
	 *
	 * @param	Array	$data
	 */
	public function addYAxis(array $data) {
		$this->data['yAxis'][] = $data;
	}



	/**
	 * Add Y axis with only a title
	 *
	 * @param	String		$label
	 */
	public function addYAxisTitle($label) {
		$this->addYAxis(array(
			'title'	=> array(
				'text' => Todoyu::Label($label)
			)
		));
	}

}

?>