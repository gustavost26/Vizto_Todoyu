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
 * Multi select field with internal persons as options
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingFilter_PersonsInternal extends TodoyuReportingFilter_SelectMulti {

	/**
	 * Initialize as normal person auto-completer
	 */
	protected function init() {
		parent::init();
	}



	/**
	 * Get options (persons of internal company)
	 *
	 * @return	Array
	 */
	protected function getOptions() {
		$options	= array();
		$persons	= TodoyuContactPersonManager::getInternalPersons(true, false, false);

		$jobTypeLabels	= TodoyuContactJobTypeManager::getJobTypes();

		foreach($persons as $personData) {
			$jobTypeLabel	= intval($personData['id_jobtype']) !== 0 ? (' (' . $jobTypeLabels[$personData['id_jobtype']]['title'] . ')') : '';
			$options[]	= array(
				'value'	=> $personData['id'],
				'label'	=> TodoyuContactPersonManager::getPerson($personData['id'])->getLabel() . $jobTypeLabel
			);
		}

		return $options;
	}



	/**
	 * Get filter label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Todoyu::Label('reporting.ext.filter.internalPersons');
	}



	/**
	 * Get selected item labels
	 *
	 * @return	Array
	 */
	protected function getSelectedItems() {
		$personIDs	= $this->getValue();
		$persons	= array();

		foreach($personIDs as $idPerson) {
			$persons[]	= array(
				'id'	=> $idPerson,
				'label'	=> TodoyuContactPersonManager::getPerson($idPerson)->getLabel()
			);
		}


		return $persons;
	}

}

?>