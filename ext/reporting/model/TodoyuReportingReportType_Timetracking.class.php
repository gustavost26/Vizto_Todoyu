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
 * Report for internal tracked time
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingReportType_Timetracking extends TodoyuReportingReport {

	/**
	 * Initialize report
	 */
	protected function init() {

	}



	/**
	 * Initialize filters
	 */
	protected function initFilters() {
		$this->addFilter('projects', 'TodoyuReportingFilter_Projects', 10, array(
			'disable' => array(
				'filterset',
				'company'
			)
		));
		$this->addFilter('filterset', 'TodoyuReportingFilter_FiltersetProject', 20, array(
			'disable' => array(
				'projects',
				'company'
			)
		));
		$this->addFilter('company', 'TodoyuReportingFilter_Company', 30, array(
			'disable' => array(
				'projects',
				'filterset'
			)
		));
		$this->addFilter('persons', 'TodoyuReportingFilter_Persons', 40, array(
			'disable' => array(
				'jobtypes'
			)
		));
		$this->addFilter('jobtypes', 'TodoyuReportingFilter_Jobtypes', 50, array(
			'disable' => array(
				'persons'
			)
		));

		$this->addFilter('range', 'TodoyuReportingFilter_Timerange', 100, array(
			'require' => array(
				array('projects', 'filterset', 'company')
			)
		));
	}



	/**
	 * Initialize result views
	 */
	protected function initResultViews() {
		$this->addResultView_Chart_TrackingsBySelection();
		$this->addResultView_MultiTable_Persons();
		$this->addResultView_Chart_AllPersonsRatio();
	}



	/**
	 * Check whether the report is ready to render the view
	 * This report can always be rendered
	 *
	 * @return	Boolean
	 */
	protected function isReadyForView() {
		return sizeof($this->getSelectedPersons()) > 0 && sizeof($this->getSelectedProjectIDs()) > 0;
	}



	/**
	 * Add tables with details for all persons
	 */
	protected function addResultView_MultiTable_Persons() {
		$projectIDs	= $this->getSelectedProjectIDs();
		$personIDs	= $this->getSelectedPersons();
		$dateStart	= $this->getFilter('range')->getStart();
		$dateEnd	= $this->getFilter('range')->getEnd();

		foreach($personIDs as $idPerson) {
			$this->addResultView_Table_Person($idPerson, $projectIDs, $dateStart, $dateEnd);
		}

		$this->addResultView_Table_AllPersons($personIDs, $projectIDs, $dateStart, $dateEnd);
	}



	/**
	 * Add table with detail for person
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$projectIDs
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 */
	protected function addResultView_Table_Person($idPerson, array $projectIDs, $dateStart, $dateEnd) {
		$monthTrackings	= $this->getPersonTrackingsPerMonth($idPerson, $projectIDs, $dateStart, $dateEnd);

		if( sizeof($monthTrackings) === 0 ) {
			$this->addResultViewNoData('reporting.basicreports.timetracking.view.PersonDetails');
			return ;
		}

			// Setup table config
		$person	= TodoyuContactPersonManager::getPerson($idPerson);
		$label	= Todoyu::Label('reporting.basicreports.timetracking.view.PersonDetails') . ': ' . $person->getFullName();

		$table	= new TodoyuReportingResultView_Table($this, 'details_' . $idPerson, $label);

		$table->addColumnHeader('month', 'core.date.month');
		$table->addColumnHeader('total', 'reporting.ext.total');
		$table->addColumnHeader('projects', 'reporting.ext.selection');
		$table->addColumnHeader('ratio', 'reporting.ext.ratio');

			// Add data
		foreach($monthTrackings as $monthTracking) {
			$table->addRow($monthTracking);
		}

			// Add summary as last row
		$totalCol		= TodoyuArray::getColumn($monthTrackings, 'total');
		$projectsCol	= TodoyuArray::getColumn($monthTrackings, 'projects');
		$totalTime		= array_sum($totalCol);
		$projectsTime	= array_sum($projectsCol);
		$ratio			= TodoyuNumeric::ratio($projectsTime, $totalTime, true, 1, 0);

			// Add total row
		$table->addRow(array(
			'month'		=> Todoyu::Label('reporting.ext.total'),
			'total'		=> $totalTime,
			'projects'	=> $projectsTime,
			'ratio'		=> $ratio,
			'summary'	=> true
		));

			// Format table values
		$table->addCallback(array($this, 'callbackTablePerson'));

		$this->addResultView($table);
	}



	/**
	 * Add table with detail for all persons
	 *
	 * @param	Array		$personIDs
	 * @param	Array		$projectIDs
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 */
	protected function addResultView_Table_AllPersons(array $personIDs, array $projectIDs, $dateStart, $dateEnd) {
		$ratioData	= $this->getRatioDataForAllPersons($personIDs, $projectIDs, $dateStart, $dateEnd);

			// Create table
		$label	= Todoyu::Label('reporting.basicreports.timetracking.view.AllPersonRatios');
		$table	= new TodoyuReportingResultView_Table($this, 'allpersonsratiotable', $label);

			// Add table columns
		$table->addColumnHeader('month', 'core.date.month');

		foreach($personIDs as $idPerson) {
			$person	= TodoyuContactPersonManager::getPerson($idPerson);

			$table->addColumnHeader('person_' . $idPerson, $person->getFullName());
		}

		$table->addColumnHeader('ratio', 'reporting.ext.ratio');


			// Add data
		foreach($ratioData['totalTrackings'] as $month => $monthTotalTracking) {
				// Row has the get data from different arrays
			$row	= array(
				'month'	=> $month
			);

			foreach($personIDs as $idPerson) {
				$row['person_' . $idPerson] = $ratioData['personRatios'][$idPerson][$month];
			}

			$row['ratio'] = $ratioData['summedRatios'][$month];


			$table->addRow($row);
		}

			// Format table values
		$table->addCallback(array($this, 'callbackTableAllPersons'));

		$this->addResultView($table);
	}


	protected function addResultView_Chart_AllPersonsRatio() {
		$projectIDs	= $this->getSelectedProjectIDs();
		$personIDs	= $this->getSelectedPersons();
		$dateStart	= $this->getFilter('range')->getStart();
		$dateEnd	= $this->getFilter('range')->getEnd();

		$ratioData	= $this->getRatioDataForAllPersons($personIDs, $projectIDs, $dateStart, $dateEnd);

			// Create chart
		$chart	= new TodoyuReportingResultView_ChartLine($this, 'allpersonsratiochart', 'reporting.basicreports.timetracking.view.AllPersonRatiosChart');

			// Set months as x-axis labels
		$monthLabels	= TodoyuReportingTime::getMonthLabels($dateStart, $dateEnd);

			// Define chart labels
		$chart->setXTitle('core.date.months');
		$chart->setYTitle('reporting.ext.ratio');
		$chart->setXLabels($monthLabels);
		$chart->setYAxisRange(0, 100);

		$chart->setTooltipFormatter('Todoyu.Ext.reporting.Formatter.tooltipLinePercent');
		$chart->setYAxisFormatter('Todoyu.Ext.reporting.Formatter.yAxisPercent');

			// Add series for all projects
		foreach($ratioData['personRatios'] as $idPerson => $montRatios) {
				// Only add persons with ratio not 0
			if( array_sum($montRatios) > 0 ) {
				$person	= TodoyuContactPersonManager::getPerson($idPerson);
				$chart->addSerie($person->getFullName(), $montRatios);
			}
		}

		$chart->addSerie('reporting.ext.ratio', $ratioData['summedRatios'], array(
			'dashStyle' => 'ShortDashDotDot',
			'lineWidth'	=> 5,
			'color'		=> '#FF8000'
		));

		$this->addResultView($chart);
	}



	/**
	 * Callback to format table values
	 *
	 * @param	TodoyuReportingResultView_Table		$view
	 * @param	Mixed								$value
	 * @param	Array								$rowData
	 * @param	String								$columnName
	 * @param	Array								$options
	 * @return	String
	 */
	public function callbackTableAllPersons($view, $value, array $rowData, $columnName, array $options) {
		if( $columnName === 'month' ) {
			if( strpos($value, '-') !== false ) {
				$value	= TodoyuReportingTime::formatMonth($value);
			}
		} elseif( $columnName === 'ratio' || strncmp('person_', $columnName, 7) === 0 ) {
			$value	= $value . '%';
		}

		return $value;
	}



	/**
	 * Get ratio data for all persons
	 *
	 * @param	Array		$personIDs
	 * @param	Array		$projectIDs
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	protected function getRatioDataForAllPersons(array $personIDs, array $projectIDs, $dateStart, $dateEnd) {
		$cacheID	= md5(serialize(func_get_args()));

		if( ! $this->cache[$cacheID] ) {
			$personRatios	= array();
			$totalTrackings	= array();
			$summedTrackings= array();
			$summedRatios	= array();

			foreach($personIDs as $idPerson) {
				$personTracking	= $this->getPersonTrackingsPerMonth($idPerson, $projectIDs, $dateStart, $dateEnd);

					// Cummulate for average and total
				foreach($personTracking as $monthTracking) {
					$personRatios[$idPerson][$monthTracking['month']]	= $monthTracking['ratio'];
					$totalTrackings[$monthTracking['month']]			+= $monthTracking['total'];
					$summedTrackings[$monthTracking['month']]			+= $monthTracking['projects'];
				}
			}

				// Calculate summed ratios
			foreach($totalTrackings as $month => $totalMonthTracking) {
				$summedRatios[$month] = TodoyuNumeric::ratio($summedTrackings[$month], $totalMonthTracking, true, 0, 0);
			}

			$this->cache[$cacheID] = array(
				'personRatios'		=> $personRatios,
				'totalTrackings'	=> $totalTrackings,
				'summedTrackings'	=> $summedTrackings,
				'summedRatios'		=> $summedRatios
			);
		}

		return $this->cache[$cacheID];
	}


	/**
	 * Callback to format table values
	 *
	 * @param	TodoyuReportingResultView_Table		$view
	 * @param	Mixed								$value
	 * @param	Array								$rowData
	 * @param	String								$columnName
	 * @param	Array								$options
	 * @return	String
	 */
	public function callbackTablePerson($view, $value, array $rowData, $columnName, array $options) {
		if( $columnName === 'month' ) {
			if( strpos($value, '-') !== false ) {
				$value	= TodoyuReportingTime::formatMonth($value);
			}
		} elseif( $columnName === 'total' || $columnName === 'projects' ) {
			$value	= TodoyuTime::formatTime($value) . 'h';
		} elseif( $columnName === 'ratio' ) {
			$value	= $value . '%';
		}

			// Mark summary row
		if( $rowData['summary'] ) {
			$value	= TodoyuString::wrapWithTag('strong', $value);
		}

		return $value;
	}



	/**
	 * Add chart result view
	 * Tracked hours in time range
	 */
	protected function addResultView_Chart_TrackingsBySelection() {
		$projectIDs	= $this->getSelectedProjectIDs();
		$personIDs	= $this->getSelectedPersons();
		$dateStart	= $this->getFilter('range')->getStart();
		$dateEnd	= $this->getFilter('range')->getEnd();

		$personTrackings	= array();
		$totalTrackings		= array();
		$averageTrackings	= array();

			// Get trackings per person
		foreach($personIDs as $idPerson) {
			$personTracking	= $this->getPersonTrackingsPerMonth($idPerson, $projectIDs, $dateStart, $dateEnd);

			$personTrackings[$idPerson] = TodoyuArray::getColumn($personTracking, 'projects');

				// Cummulate for average and total
			foreach($personTracking as $monthTracking) {
				$totalTrackings[$monthTracking['month']]	+= $monthTracking['total'];
				$averageTrackings[$monthTracking['month']]	+= $monthTracking['projects'];
			}
		}

			// Get average per month
		foreach($averageTrackings as $month => $monthTracking) {
			$averageTrackings[$month] = $monthTracking/sizeof($personIDs);
		}

			// Convert to hours
		$totalTrackings		= TodoyuReportingTime::secondsToHours($totalTrackings);
		$averageTrackings	= TodoyuReportingTime::secondsToHours($averageTrackings);

		foreach($personTrackings as $idPerson => $monthTrackings) {
			$personTrackings[$idPerson] = TodoyuReportingTime::secondsToHours($monthTrackings);
		}

		if( sizeof($totalTrackings) === 0 ) {
			$this->addResultViewNoData('reporting.basicreports.timetracking.view.TrackingsBySelection');
			return ;
		}


			// Create chart
		$chart	= new TodoyuReportingResultView_ChartLine($this, 'trackedtime', 'reporting.basicreports.timetracking.view.TrackingsBySelection');

			// Set months as x-axis labels
		$monthLabels	= TodoyuReportingTime::getMonthLabels($dateStart, $dateEnd);

			// Define chart labels
		$chart->setXTitle('core.date.months');
		$chart->setYTitle('core.date.time.hours');
		$chart->setXLabels($monthLabels);

		$chart->setTooltipFormatter('Todoyu.Ext.reporting.Formatter.tooltipLineHours');
		$chart->setYAxisFormatter('Todoyu.Ext.reporting.Formatter.yAxisHours');

			// Add series for all projects
		foreach($personTrackings as $idPerson => $trackedTime) {
				// Only add persons with tracked time
			if( array_sum($trackedTime) > 0 ) {
				$person	= TodoyuContactPersonManager::getPerson($idPerson);
				$chart->addSerie($person->getFullName(), $trackedTime);
			}
		}

		$chart->addSerie('reporting.basicreports.timetracking.view.TrackingsBySelection.totalForAll', $totalTrackings);

			// Average only makes sense if there are more then one person
		if( sizeof($personIDs) > 1 ) {
			$chart->addSerie('reporting.ext.average', $averageTrackings, array(
				'dashStyle' => 'ShortDashDotDot',
				'lineWidth'	=> 5,
				'color'		=> '#FF8000'
			));
		}

		$this->addResultView($chart);
	}



	/**
	 * Get selected project IDs
	 * If no projects are selected, automatically select all internal projects
	 *
	 * @return	Array
	 */
	protected function getProjectIDs() {
		return $this->getFilterValue('projects');
	}



	/**
	 * Get company ID
	 *
	 * @return	Integer
	 */
	protected function getCompanyID() {
		return $this->getFilterValue('company');
	}



	/**
	 * Get project IDs from current selection
	 * Get from:
	 * - projects
	 * - filtersets
	 * - company
	 *
	 * @return	Array
	 */
	protected function getSelectedProjectIDs() {
		if( $this->hasFilterValidValue('projects') ) {
			return $this->getFilterValue('projects');
		} elseif( $this->hasFilterValidValue('filterset') ) {
			return $this->getFilter('filterset')->getItemIDs();
		} elseif( $this->hasFilterValidValue('company') ) {
			return TodoyuProjectProjectManager::getCompanyProjectIDs($this->getCompanyID());
		} else {
			return array();
		}
	}



	/**
	 * Get project and total trackings with ratio for a person
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$projectIDs
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	protected function getPersonTrackingsPerMonth($idPerson, array $projectIDs, $dateStart, $dateEnd) {
		$cacheID	= md5(serialize(func_get_args()));

		if( ! $this->cache[$cacheID] ) {
			$totalTracked	= $this->getTrackingsPerMonth($idPerson, array(), $dateStart, $dateEnd);
			$projectsTracked= $this->getTrackingsPerMonth($idPerson, $projectIDs, $dateStart, $dateEnd);
			$monthMap		= TodoyuReportingTime::getMonthMapInRange($dateStart, $dateEnd);
			$months			= array_keys($monthMap);
			$trackings		= array();

			foreach($months as $month) {
				$timeTotal		= intval($totalTracked[$month]['tracked']);
				$timeProjects	= intval($projectsTracked[$month]['tracked']);
				$ratio			= TodoyuNumeric::ratio($timeProjects, $timeTotal, true, 1, 0);

				$trackings[] = array(
					'month'		=> $month,
					'total'		=> $timeTotal,
					'projects'	=> $timeProjects,
					'ratio'		=> $ratio
				);
			}

			$this->cache[$cacheID] = $trackings;
		}

		return $this->cache[$cacheID];
	}



	/**
	 * Get trackings per month. Limit to projects if IDs are provided
	 *
	 * @param	Integer		$idPerson
	 * @param	Array		$projectIDs
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	protected function getTrackingsPerMonth($idPerson, array $projectIDs, $dateStart, $dateEnd) {
		$fields	= '	SUM(tr.workload_tracked) as tracked,
					DATE_FORMAT(FROM_UNIXTIME(tr.date_track), \'%Y-%m\') AS month';
		$tables	= '	ext_timetracking_track tr';
		$where	= '		tr.date_track BETWEEN ' . $dateStart . ' AND ' . $dateEnd
				. ' AND tr.id_person_create	= ' . $idPerson;
		$group	= 'month';

		if( sizeof($projectIDs) > 0 ) {
			$tables	.= ', ext_project_task t';
			$where	.= ' AND tr.id_task = t.id'
					. ' AND t.id_project IN(' . implode(',', $projectIDs) . ')';
		}

		return Todoyu::db()->getArray($fields, $tables, $where, $group, '', '', 'month');
	}





	/**
	 * Get persons which are selected manually and by jobtype
	 *
	 * @return	Array
	 */
	protected function getSelectedPersons() {
		$personIDs			= $this->getFilter('persons')->getValue();
		$jobTypePersonIDs	= $this->getFilter('jobtypes')->getPersonIDs();

		return array_unique(array_merge($personIDs, $jobTypePersonIDs));
	}



	/**
	 * Get information why the results are not displayed
	 *
	 * @return	Array
	 */
	protected function getRequirementLabels() {
		return array(
			'reporting.basicreports.timetracking.requirement.person',
			'reporting.basicreports.timetracking.requirement.project'
		);
	}



	/**
	 * Get timerange for report
	 *
	 * @return	Array|Boolean
	 */
	protected function calcTimerange() {
		if( $this->hasFilterValidValue('projects') ) {
			$timerange	= TodoyuReportingTime::getRangeOfProjects($this->getProjectIDs());
		} elseif( $this->hasFilterValidValue('filterset') ) {
			$projectIDs	= $this->getFilter('filterset')->getItemIDs();
			$timerange	= TodoyuReportingTime::getRangeOfProjects($projectIDs);
		} elseif( $this->hasFilterValidValue('company') ) {
			$projectIDs	= TodoyuProjectProjectManager::getCompanyProjectIDs($this->getCompanyID());
			$timerange	= TodoyuReportingTime::getRangeOfProjects($projectIDs);
		} else {
			$timerange	= false;
		}

		if( $timerange !== false ) {
			$timerange['end'] = TodoyuTime::getDayStart(NOW);
		}

		return $timerange;
	}

}

?>