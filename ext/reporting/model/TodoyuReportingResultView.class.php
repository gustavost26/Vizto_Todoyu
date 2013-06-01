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
 * Base class for result views
 *
 * @package		Todoyu
 * @subpackage	Reporting
 * @abstract
 */
abstract class TodoyuReportingResultView {

	/**
	 * Parent report
	 *
	 * @var	TodoyuReportingReport
	 */
	protected $report;

	/**
	 * Result view name
	 *
	 * @var	String
	 */
	protected $name;

	/**
	 * Result view title
	 *
	 * @var	String
	 */
	protected $title = '';

	/**
	 * Resutl view data
	 *
	 * @var	Array
	 */
	public $data = array();

	/**
	 * Counter for unique key
	 *
	 * @var	Integer
	 */
	protected static $counter = 1;

	/**
	 * Unique result view key
	 *
	 * @var	String
	 */
	protected $key;

	/**
	 * Custom CSS classes for view
	 *
	 * @var	Array
	 */
	protected $classes = array();



	/**
	 * Initialize result view
	 *
	 * @param	TodoyuReportingReport	$report
	 * @param	String		$name
	 * @param	String		$title
	 * @param	Array		$data
	 */
	public final function __construct(TodoyuReportingReport $report, $name, $title = '', array $data = array()) {
		$this->report	= $report;
		$this->name		= $name;
		$this->title	= Todoyu::Label($title);
		$this->key		= ($this->getReportID() * 1000) + self::$counter++;

		$this->init();
		$this->addData($data);
	}



	/**
	 * Initialize result view
	 */
	protected function init() {

	}



	/**
	 * Get name of result view
	 *
	 * @return	String
	 */
	public function getName() {
		return $this->name;
	}


	public function getFullName() {
		return 'report-' . $this->getReportID() . '-chart-' . $this->getName();
	}



	/**
	 * Get title of result view
	 *
	 * @return	String
	 */
	public function getTitle() {
		return $this->title;
	}



	/**
	 * Get parent report
	 *
	 * @return	TodoyuReportingReport
	 */
	public function getReport() {
		return $this->report;
	}



	/**
	 * Get report ID
	 *
	 * @return	String
	 */
	public function getReportID() {
		return $this->getReport()->getID();
	}



	/**
	 * Set/overwrite report data
	 *
	 * @param	Array		$data
	 */
	public function setData(array $data) {
		$this->data = $data;
	}



	/**
	 * Get report key
	 *
	 * @return	Integer
	 */
	protected function getKey() {
		return $this->key;
	}



	/**
	 * Get values of all filters
	 *
	 * @return	Array
	 */
	protected function getFilterValues() {
		return $this->getReport()->getFilterValues();
	}



	/**
	 * Render content for extra part of result label
	 *
	 * @return	String
	 */
	public function renderLabelExtra() {
		return '';
	}


	/**
	 * Add class for view container
	 *
	 * @param	String		$class
	 */
	public function addClass($class) {
		$this->classes[] = $class;
	}



	/**
	 * Get classes for view container
	 *
	 * @return	Array
	 */
	public function getClasses() {
		$classParts		= explode('_', get_class($this), 2);
		$basicClasses	= array(
			'type' . $classParts[1],
		);

		return array_unique(array_merge($this->classes, $basicClasses));
	}



	/**
	 * Add extra data which will be merged into the config array
	 *
	 * @param	Array		$data
	 */
	public function addData(array $data) {
		$this->data	= TodoyuArray::mergeRecursive($this->data, $data);
	}



	/**
	 * Get template data
	 *
	 * @abstract
	 * @return	Array
	 */
	abstract protected function getTemplateData();



	/**
	 * Render result view
	 *
	 * @abstract
	 * @return	String
	 */
	abstract public function render();

}

?>