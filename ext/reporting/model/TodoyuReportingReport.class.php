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
 * Basic report class
 *
 * @package		Todoyu
 * @subpackage	Reporting
 * @abstract
 */
abstract class TodoyuReportingReport {

	/**
	 * @var	Boolean
	 */
	private $resultViewsInitialized = false;

	/**
	 * List with filters of the report
	 *
	 * @var TodoyuReportingFilterList
	 */
	protected $filterList;

	/**
	 * List with resultViews of the report
	 *
	 * @var TodoyuReportingResultViewList
	 */
	protected $resultViewList;

	/**
	 * Report record data
	 *
	 * @var	Array
	 */
	protected $data = array();

	/**
	 * Time range (cached)
	 *
	 * @var	Array|null
	 */
	protected $timerange = null;

	/**
	 * Cache query results and calculations
	 *
	 * @var	Array
	 */
	protected $cache = array();



	/**
	 * Initialize report object with record data
	 *
	 * @param	Array		$data
	 */
	public final function __construct(array $data) {
		$this->data	= $data;

			// Create filter and resultView lists
		$this->filterList		= new TodoyuReportingFilterList($this);
		$this->resultViewList	= new TodoyuReportingResultViewList($this);

			// Create random ID if non set (for new reports)
		if( $this->data['id'] === '0' ) {
			$this->data['id'] = 'r' . substr(md5(NOW), 0, 6);
		}

			// Call init() function of instance. Replacement for constructor
		$this->init();

			// Initialize filters
		$this->initFilters();
	}



	/**
	 * Get report ID
	 *
	 * @return	Integer|String
	 */
	public function getID() {
		return $this->data['id'];
	}



	/**
	 * Get reporttype
	 *
	 * @return	String
	 */
	public function getType() {
		return $this->data['reporttype'];
	}



	/**
	 * Get reporttype configuration
	 *
	 * @return	Array
	 */
	protected function getTypeConfig() {
		return TodoyuReportingReportTypeManager::getReportTypeConfig($this->data['reporttype']);
	}



	/**
	 * Get report title
	 *
	 * @return	String
	 */
	public function getTitle() {
		$config	= $this->getTypeConfig();

		if( empty($this->data['title']) ) {
			$title = Todoyu::Label('reporting.ext.new') . ' (' . $config['label'] . ')';
		} else {
			$title = $this->data['title'];
		}

		return $title;
	}



	/**
	 * Add a new filter to the report
	 *
	 * @param	String		$name
	 * @param	String		$class
	 * @param	Integer		$position
	 * @param	Array		$settings
	 */
	protected final function addFilter($name, $class, $position = 100, array $settings = array()) {
		$filter	= new $class($this, $name, $settings);

		$this->filterList->addFilter($filter, $position);
	}



	/**
	 * Add result view to report
	 *
	 * @param	TodoyuReportingResultView	$resultView
	 * @param	Integer						$position
	 */
	protected final function addResultView(TodoyuReportingResultView $resultView, $position = 100) {
		$this->resultViewList->addResultView($resultView, $position);
	}



	/**
	 * Get filter list
	 *
	 * @return	TodoyuReportingFilterList
	 */
	public final function getFilterList() {
		return $this->filterList;
	}



	/**
	 * Get a filter from the report
	 *
	 * @param	String		$name
	 * @return	TodoyuReportingFilter|Boolean
	 */
	public final function getFilter($name) {
		return $this->getFilterList()->getFilter($name);
	}



	/**
	 * Check whether the report contains the filter
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public final function hasFilter($name) {
		return $this->getFilterList()->hasFilter($name);
	}



	/**
	 * Get filter values
	 *
	 * @return	Array
	 */
	public function getFilterValues() {
		return $this->getFilterList()->getFilterValues();
	}



	/**
	 * Get filter value
	 *
	 * @param	String		$name
	 * @return	Mixed
	 */
	public final function getFilterValue($name) {
		return $this->getFilterList()->getFilterValue($name);
	}



	/**
	 * Check whether a filter has a valid value
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public final function hasFilterValidValue($name) {
		return $this->hasFilter($name) ? $this->getFilter($name)->hasValidValue() : false;
	}



	/**
	 * Check whether all given filters have valid values
	 *
	 * @param	String[]	$filterNames
	 * @return	Boolean
	 */
	public final function haveFiltersValidValues(array $filterNames) {
		foreach($filterNames as $filterName) {
			if( !$this->hasFilterValidValue($filterName) ) {
				return false;
			}
		}

		return true;
	}



	/**
	 * Check whether any of the given filters has a valid value
	 *
	 * @param	Array	$filterNames
	 * @return	Boolean
	 */
	public final function hasAnyFilterValidValue(array $filterNames) {
		foreach($filterNames as $filterName) {
			if( $this->hasFilterValidValue($filterName) ) {
				return true;
			}
		}

		return false;
	}



	/**
	 * Set new filter values
	 *
	 * @param	Array		$filterValues
	 */
	public function setFilterValues(array $filterValues) {
			// Reset cache timerange
		$this->timerange = null;
			// Update filter values
		$this->getFilterList()->setFilterValues($filterValues);
	}




	/**
	 * Get result view list
	 *
	 * @return	TodoyuReportingResultViewList
	 */
	public final function getResults() {
		if( ! $this->resultViewsInitialized ) {
			$this->resultViewsInitialized = true;
			$this->initResultViews();
		}

		return $this->resultViewList;
	}



	/**
	 * Render full report (filters and results)
	 *
	 * @return	String
	 */
	public final function render() {
		$tmpl	= 'ext/reporting/view/report.tmpl';
		$data	= array(
			'id'			=> $this->getID(),
			'title'			=> $this->getTitle(),
			'reporttype'	=> $this->getType(),
			'filters'		=> array(),
			'jumplist'		=> array(),
			'toggled'		=> TodoyuReportingPreferences::getFilterToggle($this->getID())
		);

		$this->disableFiltersByConfig();

			// Pre render filters
		$filters	= $this->getFilterList()->getFilters();

		foreach($filters as $filter) {
			/**
			 * @var	TodoyuReportingFilter	$filter
			 */
			$data['filters'][$filter->getName()] = $filter->render();
		}

		if( $this->isReadyForView() ) {
			$data['resultViewList'] = $this->getResults()->render();

			$data['jumplist'] = array();
			foreach($this->getResults()->getResultViews() as $resultView) {
				/**
				 * @var	TodoyuReportingResultView $resultView
				 */
				$data['jumplist'][$resultView->getName()] = array(
					'name'	=> $resultView->getName(),
					'label'	=> $resultView->getTitle()
				);
			}

		} else {
			$data['resultViewList'] = $this->renderResultViewNotReady();
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Set filters inactive if they are currently not preferred
	 */
	protected function disableFiltersByConfig() {
		foreach($this->getFilterList()->getFilters() as $filter) {
			/**
			 * @var	TodoyuReportingFilter	$filter
			 */
			$filter->disableOtherFiltersIfActive();
		}
	}



	/**
	 * Render message that the result view is not ready to render with current filter data
	 *
	 * @return	String
	 */
	protected function renderResultViewNotReady() {
		$tmpl	= 'ext/reporting/view/resultView-notready.tmpl';
		$data	= array(
			'requirements'	=> $this->getRequirementLabels()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Initialize report type (alternative to __constructor(), but optional
	 */
	protected function init() {
		// Optional overwriting
	}



	/**
	 * Get labels for requirements to render result views
	 * They explain why the result views are not rendered
	 *
	 * @return	Array
	 */
	protected function getRequirementLabels() {
		return array();
	}



	/**
	 * Add a dummy result view which informs that a view has no useful data and can't be displayed
	 *
	 * @param	String		$title
	 */
	protected function addResultViewNoData($title) {
		$resultView	= new TodoyuReportingResultView_NoData($this, 'nodata-' . substr(md5(rand()), 0, 5), $title);

		$this->addResultView($resultView);
	}



	/**
	 * Get timerange for reporting
	 *
	 * @return	Array|Boolean		[start,end] or false
	 */
	public final function getTimerange() {
		if( is_null($this->timerange) ) {
			$this->timerange = $this->calcTimerange();
		}

		return $this->timerange;
	}



	/**
	 * Dummy function to calculate timerange
	 * If your report should support timerange, overwrite this function
	 *
	 * @return	Array|Boolean
	 */
	protected function calcTimerange() {
		return false;
	}



	/**
	 * Initialize filters
	 */
	abstract protected function initFilters();



	/**
	 * Initialize result views
	 */
	abstract protected function initResultViews();



	/**
	 * Check whether the report is ready to render the views
	 * Can depend on a custom filter combination or anything else
	 *
	 * @return	Boolean
	 */
	abstract protected function isReadyForView();

}

?>