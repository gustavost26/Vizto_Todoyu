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
 * Timerange filter
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingFilter_Timerange extends TodoyuReportingFilter {

	/**
	 * Initialize filter
	 */
	protected function init() {
		parent::init();
	}



	/**
	 * Get range from report
	 * The report calculates the available range from made inputs
	 *
	 * @return	Array
	 */
	protected function getRange() {
		return $this->getReport()->getTimerange();
	}



	/**
	 * Set filter value
	 *
	 * @param	Array|Mixed	$value
	 */
	public function setValue($value) {
		if( is_array($value) && sizeof($value) > 0 ) {
			parent::setValue(array(
				'start'	=> TodoyuTime::parseDate($value['start']),
				'end'	=> TodoyuTime::parseDate($value['end'])
			));
		}

			// Make sure value is in range
		$this->assertValueIsInRange();
	}




	/**
	 * Asserts that the current value is in the possible range
	 */
	protected function assertValueIsInRange() {
			// If value is set, make sure it's still in range
		if( ! is_null($this->value) ) {
				// Check if selected values are still in the range
			$range	= $this->getRange();

				// If selected start date if before range (or after end)
			if( $this->value['start'] < $range['start'] || $this->value['start'] > $range['end'] ) {
				$this->value['start'] = $range['start'];
			}
				// If selected end date is after range (or before start)
			if( $this->value['end'] > $range['end'] ||  $this->value['end'] < $range['start']) {
				$this->value['end'] = $range['end'];
			}
		}
	}



	/**
	 * Get value of timerange
	 *
	 * @return	Array|null
	 */
	public function getValue() {
		if( is_null($this->value) ) {
			if( method_exists($this->getReport(), 'getTimerangeDefault') ) {
				$this->value = $this->getReport()->getTimerangeDefault($this);
			} else {
				$this->value = $this->getRange();
			}
		}

		return $this->value;
	}



	/**
	 * Get dates of time selector
	 * If no dates are set, get first and last value of range
	 *
	 * @return	Array
	 */
	protected function getDates() {
		$value	= $this->getValue();

		if( is_array($value) ) {
			$dates	= $value;
		} else {
			$dates	= $this->getRange();
		}

		return $dates;
	}



	/**
	 * Get filter label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Todoyu::Label('reporting.ext.filter.timerange');
	}



	/**
	 * Check whether filter has valid value
	 * Range has to give a valid value
	 *
	 * @return	Boolean
	 */
	public function hasValidValue() {
		return $this->getRange() !== false;
	}



	/**
	 * Get filter template
	 *
	 * @return string
	 */
	protected function getTemplate() {
		return 'ext/reporting/view/filter/timerange.tmpl';
	}



	/**
	 * Get template data
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		$data	= parent::getTemplateData();

		$data['dates']	= $this->getDates();
		$data['range']	= $this->getRange();

		return $data;
	}



	/**
	 * Check whether the requirements for this filter are set
	 *
	 * @return	Boolean
	 */
	public function areRequirementsSet() {
		return $this->getRange() !== false && parent::areRequirementsSet();
	}



	/**
	 * Get selected start date
	 *
	 * @return	Integer
	 */
	public function getStart() {
		return intval($this->value['start']);
	}



	/**
	 * Get selected end date
	 *
	 * @return	Integer
	 */
	public function getEnd() {
		return intval($this->value['end']);
	}

}

?>