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
 * List of filters in a report
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingFilterList {

	/**
	 * Filters in the list
	 *
	 * @var	Array
	 */
	protected $filters = array();


	/**
	 * Parent report
	 *
	 * @var	TodoyuReportingReport
	 */
	protected $report;



	/**
	 * Initialize filter list with report reference
	 *
	 * @param	TodoyuReportingReport	$report
	 */
	public function __construct(TodoyuReportingReport $report) {
		$this->report = $report;
	}



	/**
	 * Add a filter to the list
	 *
	 * @param	TodoyuReportingFilter	$filter
	 * @param	Integer					$position
	 */
	public function addFilter(TodoyuReportingFilter $filter, $position = 100) {
		$this->filters[$filter->getName()] = array(
			'filter'	=> $filter,
			'position'	=> intval($position)
		);
	}



	/**
	 * Get filter (which was added to the list before)
	 * Returns false if filter was not found
	 *
	 * @param	String		$name
	 * @return	TodoyuReportingFilter|Boolean
	 */
	public function getFilter($name) {
		if( $this->hasFilter($name) ) {
			return $this->filters[$name]['filter'];
		} else {
			return false;
		}
	}



	/**
	 * Check whether the filter list contains the filter
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public function hasFilter($name) {
		return array_key_exists($name, $this->filters);
	}



	/**
	 * Get current values of the filters
	 *
	 * @return	Array
	 */
	public function getFilterValues() {
		$values	= array();

		foreach($this->filters as $filterData) {
			$values[$filterData['filter']->getName()] = $filterData['filter']->getValue();
		}

		return $values;
	}



	/**
	 * Get value of a filter
	 *
	 * @param	String		$name
	 * @return	Mixed|Boolean
	 */
	public function getFilterValue($name) {
		if( $this->hasFilter($name) ) {
			return $this->getFilter($name)->getValue();
		} else {
//			TodoyuDebug::printInFireBug('Filter <' . $name. '> not found!');
			return false;
		}
	}



	/**
	 * Set given values as filter data
	 *
	 * @param	Array	$filterValues
	 */
	public function setFilterValues(array $filterValues) {
		foreach($this->filters as $filterData) {
			$filterData['filter']->setValue($filterValues[$filterData['filter']->getName()]);
		}
	}



	/**
	 * Get report data
	 *
	 * @return	TodoyuReportingReport
	 */
	public function getReport() {
		return $this->report;
	}



	/**
	 * Get filters in the filter list
	 *
	 * @return	Array
	 */
	public function getFilters() {
		$filterList	= TodoyuArray::sortByLabel($this->filters, 'position');

		 return TodoyuArray::getColumn($filterList, 'filter');
	}

}

?>