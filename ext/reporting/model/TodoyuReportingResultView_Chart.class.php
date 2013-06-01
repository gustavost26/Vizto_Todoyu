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
class TodoyuReportingResultView_Chart extends TodoyuReportingResultView {

	/**
	 * Height of the chart
	 * The height can depend on the number of elements
	 * If height is 0, highcharts automatically selects a hight for the graph
	 *
	 * @var	Integer
	 */
	private $height = 400;

	/**
	 * @var	Array
	 */
	private $jsCommands = array();


	/**
	 * Initialize chart
	 */
	protected function init() {
		$this->setContainer($this->getContainerID());
		$this->setTitle('');

			// Merge in default config
		$this->addData(array(
			'credits' => array(
				'enabled' => false
			),
			'xAxis'	=> array(
				'labels' => array(
					'rotation'	=> -45,
					'align'		=> 'right',
					'style'		=> 'font-size:9px;'
				)
			)
		));
	}



	/**
	 * Get template data for chart rendering
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		$data	= array(
			'name'			=> $this->getName(),
			'fullName'		=> $this->getFullName(),
			'config'		=> $this->getJsonEncodedConfig(),
			'containerID'	=> $this->getContainerID(),
			'height'		=> $this->height,
			'jsCommands'	=> $this->jsCommands
		);

		return $data;
	}



	/**
	 * Get JSON encoded config for highcharts
	 * Functions are prepared to work in JSON syntax
	 *
	 * @return	String
	 */
	protected function getJsonEncodedConfig() {
		return TodoyuString::enableJsFunctionInJSON(json_encode($this->data));
	}



	/**
	 * Get chart container ID
	 *
	 * @return	String
	 */
	protected function getContainerID() {
		return 'report-' . $this->getReportID() . '-chart-' . $this->getKey();
	}



	/**
	 * Set height of the chart
	 *
	 * @param	Integer		$height
	 */
	public function setHeight($height) {
		$this->height = intval($height);
	}



	/**
	 * Set chart type
	 *
	 * @param	String		$chartType
	 */
	public function setType($chartType) {
		$this->data['chart']['defaultSeriesType'] = $chartType;
	}



	/**
	 * Get default chart type
	 *
	 * @return	Array
	 */
	public function getType() {
		return $this->data['chart']['defaultSeriesType'];
	}



	/**
	 * Set container to render the chart in
	 *
	 * @param	String		$container		Container ID
	 */
	public function setContainer($container) {
		$this->data['chart']['renderTo'] = $container;
	}



	/**
	 * Set tick labels for X-Axis
	 *
	 * @param	Array	$values
	 */
	public function setXLabels(array $values) {
		$this->data['xAxis']['categories'] = $values;
	}



	/**
	 * Set X-Axis title
	 *
	 * @param	String		$title
	 * @param	Boolean		$noLabel
	 */
	public function setXTitle($title, $noLabel = false) {
		$this->data['xAxis']['title']['text'] = $noLabel ? $title : Todoyu::Label($title);
	}



	/**
	 * Set Y-Axis title
	 *
	 * @param	String		$title
	 */
	public function setYTitle($title) {
		$this->data['yAxis']['title']['text'] = Todoyu::Label($title);
	}



	/**
	 * Set chart title
	 *
	 * @param	String		$title
	 */
	public function setTitle($title) {
		$this->data['title']['text'] = Todoyu::Label($title);
	}



	/**
	 * Set function for tooltip formatter
	 * this will be the highcharts elements
	 * function will receive a that parameter which points to it's original scope
	 *
	 * @param	String		$functionName
	 */
	public function setTooltipFormatter($functionName) {
		$this->data['tooltip'] = array(
			'formatter'	=> TodoyuReportingManager::makeTooltipFormatter($functionName)
		);
	}



	/**
	 * Add a series
	 *
	 * @param	String		$name
	 * @param	Array		$data
	 * @param	Array		$options
	 */
	public function addSerie($name, array $data, array $options = array()) {
		$config	= array(
			'name'	=> Todoyu::Label($name),
			'data'	=> TodoyuArray::floatval(array_values($data))
		);

		if( sizeof($options) > 0 ) {
			$config = array_merge($config, $options);
		}

		$this->data['series'][] = $config;
	}



	/**
	 * Set plotOptions config
	 *
	 * @param	Array	$options
	 */
	public function addPlotOptions(array $options) {
		$temp	= array(
			'plotOptions' => TodoyuArray::assure($options)
		);

		$this->addData($temp);
	}



	/**
	 * Enable stacked mode for chart
	 *
	 * @param	String		$mode
	 */
	public function setStacked($mode = 'normal') {
		$this->addPlotOptions(array(
			'column'	=> array(
				'stacking' => $mode
			)
		));
	}



	/**
	 * Render content for extra part of the label
	 *
	 * @return	String
	 */
	public function renderLabelExtra() {
		return '<a href="javascript:void(0)" onclick="Todoyu.Ext.reporting.ResultView.showInPopup(\'' . $this->getContainerID() . '\', \'' . $this->getFullName() . '\')" class="maximize"></a>';
	}


	public function addJsCommand($jsCommand) {
		$this->jsCommands[] = $jsCommand;
	}



	/**
	 * Render the chart
	 *
	 * @return	String
	 */
	public function render() {
		$tmpl	= 'ext/reporting/view/result/chart.tmpl';
		$data	= $this->getTemplateData();

		return Todoyu::render($tmpl, $data);
	}

}

?>