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
 * Result view chart pie
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingResultView_ChartPie extends TodoyuReportingResultView_Chart {

	/**
	 * Initialize chart
	 */
	public function init() {
		parent::init();

		$this->addData(array(
			'series' => array(
				0 => array(
					'type'	=> 'pie'
				)
			)
		));

		$this->setTooltipFormatter('Todoyu.Ext.reporting.Formatter.tooltipPie');
	}



	/**
	 * Add chart series. In pie charts, a series is only a label and a value
	 *
	 * @param	String		$label
	 * @param	Float		$value
	 * @param	Boolean		$noLabel
	 */
	public function addSerie($label, $value, $noLabel = false) {
		$this->addCustomSerie(array(
			$noLabel ? $label : Todoyu::Label($label),
			floatval($value)
		));
	}



	/**
	 * Add custom serie with more options
	 *
	 * @param	Array		$serie
	 */
	public function addCustomSerie(array $serie) {
		$this->data['series'][0]['data'][] = $serie;
	}



	/**
	 * Set chart name
	 *
	 * @param	String		$label
	 */
	public function setName($label) {
		$this->data['series'][0]['name'] = Todoyu::Label($label);
	}

}

?>