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
 * List of result views
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingResultViewList {

	/**
	 * List of result views
	 *
	 * @var	Array
	 */
	protected $resultViews = array();

	/**
	 * Parent report
	 *
	 * @var	TodoyuReportingReport
	 */
	protected $report;


	/**
	 * Initialize list with parent report
	 *
	 * @param	TodoyuReportingReport	$report
	 */
	public function __construct(TodoyuReportingReport $report) {
		$this->report = $report;
	}



	/**
	 * Add a new result view to the list
	 *
	 * @param	TodoyuReportingResultView	$view
	 * @param	Integer						$position
	 */
	public function addResultView(TodoyuReportingResultView $view, $position = 100) {
		$this->resultViews[$view->getName()] = array(
			'view'		=> $view,
			'position'	=> intval($position)
		);
	}



	/**
	 * Get a result view
	 *
	 * @param	String		$name
	 * @return	TodoyuReportingResultView
	 */
	public function getResultView($name) {
		return $this->resultViews[$name]['view'];
	}



	/**
	 * Get the parent report
	 *
	 * @return	TodoyuReportingReport
	 */
	public function getReport() {
		return $this->report;
	}



	/**
	 * Get result views in list
	 *
	 * @return	Array
	 */
	public function getResultViews() {
		$resultViews	= TodoyuArray::sortByLabel($this->resultViews, 'position');

		return TodoyuArray::getColumn($resultViews, 'view');
	}



	/**
	 * Render the result view list with all result views
	 *
	 * @return	String
	 */
	public function render() {
		$tmpl	= 'ext/reporting/view/resultView-list.tmpl';
		$data	= array(
			'resultViews'	=> array(),
			'idReport'		=> $this->getReport()->getID()
		);

		foreach($this->getResultViews() as $resultView) {
			/**
			 * @var	TodoyuReportingResultView	$resultView
			 */
			$data['resultViews'][] = array(
				'content'	=> $resultView->render(),
				'title'		=> $resultView->getTitle(),
				'name'		=> $resultView->getName(),
				'labelExtra'=> $resultView->renderLabelExtra(),
				'classes'	=> $resultView->getClasses()
			);
		}

		return Todoyu::render($tmpl, $data);
	}

}

?>