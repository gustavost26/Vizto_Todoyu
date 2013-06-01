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
 * [Enter Class Description]
 *
 * @package		Todoyu
 * @subpackage	Reporting
 * @abstract
 */
abstract class TodoyuReportingFilter {

	/**
	 * Filter name
	 *
	 * @var	String
	 */
	protected $name;

	/**
	 * @var	Mixed
	 */
	protected $value = null;

	/**
	 * Filter configuration
	 *
	 * @var	Array
	 */
	protected $config = array();

	/**
	 * Custom filter setting for report
	 *
	 * @var	Array
	 */
	protected $settings = array();

	/**
	 * @var	TodoyuReportingReport
	 */
	protected $report;

	/**
	 * Filter is inactive because other filters are preferred
	 *
	 * @var	Boolean
	 */
	protected $inactive = false;



	/**
	 * Initialize filter
	 *
	 * @param	TodoyuReportingReport	$report			Report which contains the filter
	 * @param	String					$name			Name of the filter
	 * @param	Array					$settings		Custom filter instance settings
	 */
	public final function __construct(TodoyuReportingReport $report, $name, array $settings = array()) {
		$this->report	= $report;
		$this->name		= $name;
		$this->settings	= $settings;

		TodoyuExtensions::loadAllFilters();

		$this->init();
	}



	/**
	 * Alternative to the constructor
	 */
	protected function init() {
		// Empty in base class. Overwrite
	}



	/**
	 * Get parent report
	 *
	 * @return	TodoyuReportingReport
	 */
	public final function getReport() {
		return $this->report;
	}



	/**
	 * Get name of the filter
	 *
	 * @return	String
	 */
	public final function getName() {
		return $this->name;
	}



	/**
	 * Get name of the input in the form
	 *
	 * @param	Boolean		$multiple
	 * @return	String
	 */
	protected function getFieldName($multiple = false) {
		return 'filter[' . $this->getName() . ']' . ($multiple ? '[]' : '');
	}



	/**
	 * Get label
	 * By default from widget config
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Todoyu::Label($this->config['label']);
	}



	/**
	 * Get filter type
	 *
	 * @return	String
	 */
	public final function getType() {
		return str_replace('TodoyuReportingFilter_', '', get_class($this));
	}



	/**
	 * Get class names of all parent classes of the filter
	 *
	 * @return Array
	 */
	protected function getTypeClassNames() {
		$types		= array();
		$classes	= array_slice(class_parents($this), 0, -1);
		$classes[]	= get_class($this);

		foreach($classes as $class) {
			$types[] = str_replace('TodoyuReportingFilter_', '', $class);
		}

		return TodoyuArray::prefix($types, 'type');
	}



	/**
	 * Get css class string for filter element
	 *
	 * @return	String
	 */
	protected function getFilterClassString() {
		$classes	= $this->getTypeClassNames();
		$classes[]	= 'filter';
		$classes[]	= 'name' . ucfirst($this->getName());

		if( $this->inactive ) {
			$classes[] = 'inactive';
		}

		return implode(' ', $classes);
	}



	/**
	 * Set filter value
	 *
	 * @param	Mixed	$value
	 */
	public function setValue($value) {
		$this->value = $value;
	}



	/**
	 * Get filter value
	 * For internal usage
	 *
	 * @see		getFieldValue
	 * @return	Mixed
	 */
	public function getValue() {
		return $this->value;
	}



	/**
	 * Get value which is written in the form field (hidden, as list, etc)
	 *
	 * @return	String
	 */
	public function getInputValue() {
		return $this->value;
	}



	/**
	 * Check whether all required filters have a valid value
	 * Requirement are set in the $settings of the filter
	 * The "require" part can contain filternames and arrays of filternames
	 * String: Filter is required
	 * Array: Only one of the filters is required (at least one)
	 *
	 * @return	Boolean
	 */
	public function areRequirementsSet() {
			// Check whether the requirement config is set
		if( is_array($this->settings['require']) ) {
				// Check all requirements
			foreach($this->settings['require'] as $filterName) {
					// Check whether requirement is an array (OR) or a string
				if( is_array($filterName) ) {
					if( ! $this->areFiltersValid($filterName) ) {
						return false;
					}
				} else {
					if( ! $this->areFiltersValid(array($filterName), true) ) {
						return false;
					}
				}
			}
		}

		return true;
	}



	/**
	 * Check whether the given filters are valid
	 *
	 * @param	Array		$filters		List of filternames
	 * @param	Boolean		$allRequired	True: all filters have to be valid. False: At least one filter has to be valid
	 * @return	Boolean
	 */
	private final function areFiltersValid(array $filters, $allRequired = false) {
		foreach($filters as $filterName) {
			if( $this->getReport()->hasFilter($filterName) ) {
				$valid	= $this->getFilter($filterName)->hasValidValue();

				if( $allRequired && !$valid ) {
					return false;
				} elseif( !$allRequired && $valid ) {
					return true;
				}
			}
		}

		return $allRequired;
	}



	/**
	 * Check if filter value is valid for usage
	 * Will be checked if another filter has the filter as requirement
	 *
	 * @return	Boolean
	 */
	public function hasValidValue() {
		return true;
	}



	/**
	 * Render the filter
	 *
	 * @final
	 * @return	String
	 */
	public final function render() {
		$tmpl	= 'ext/reporting/view/filter.tmpl';
		$data	= array(
			'idReport'		=> $this->getReportID(),
			'name'			=> $this->getName(),
			'filterClasses'	=> $this->getFilterClassString(),
			'label'			=> $this->getLabel(),
			'config'		=> TodoyuArray::assure($this->settings['config'])
		);

		if( $this->areRequirementsSet() ) {
			$data['inline'] = $this->renderInline();
		} else {
			$data['inline'] = $this->renderRequirementNotSet();
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render error message for inline filter part if requirements are not set
	 *
	 * @return	String
	 */
	private function renderRequirementNotSet() {
		$tmpl	= 'ext/reporting/view/filter-requirements-not-set.tmpl';
		$data	= array();

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get template data for inline rendering of filter
	 * This base version contains all basic data and should be extended by specific filters
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		return array(
			'idReport'		=> $this->getReport()->getID(),
			'htmlID'		=> $this->getHtmlID(),
			'fieldname'		=> $this->getFieldName(),
			'name'			=> $this->getName(),
			'inputValue'	=> $this->getInputValue(),
			'value'			=> $this->getValue()
		);
	}



	/**
	 * Get report ID. Just a shortcut
	 *
	 * @return	Integer|String
	 */
	public function getReportID() {
		return $this->getReport()->getID();
	}



	/**
	 * Get base ID for filter
	 * Format: reporting-filter-REPORT-NAME
	 *
	 * @return	String
	 */
	public function getHtmlID() {
		return 'report-' . $this->getReportID() . '-filter-' . $this->getName();
	}



	/**
	 * Get another filter
	 *
	 * @param	String		$name
	 * @return	Boolean|TodoyuReportingFilter
	 */
	protected function getFilter($name) {
		return $this->getReport()->getFilter($name);
	}



	/**
	 * Render inline part of filter
	 *
	 * @return	String
	 */
	public final function renderInline() {
		$tmpl	= $this->getTemplate();
		$data	= $this->getTemplateData();

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Set filter inactive
	 * This is only visual
	 */
	public final function disable() {
		$this->inactive = true;
	}




	/**
	 * Set filter inactive if a preferred filter has a valid value
	 */
	public final function disableOtherFiltersIfActive() {
		if( $this->hasValidValue() && is_array($this->settings['disable']) ) {
			foreach($this->settings['disable'] as $disableFilterName) {
				if( $this->getReport()->hasFilter($disableFilterName) ) {
					$this->getFilter($disableFilterName)->disable();
				}
			}
		}
	}



	/**
	 * Get template for inline rendering of filter
	 *
	 * @abstract
	 * @return	String
	 */
	abstract protected function getTemplate();

}

?>