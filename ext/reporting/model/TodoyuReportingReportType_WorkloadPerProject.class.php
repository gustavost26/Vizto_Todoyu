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
 * Show workload per project
 * See how much time was tracked on which projects in a period
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingReportType_WorkloadPerProject extends TodoyuReportingReport {

	/**
	 * Initialize report
	 */
	protected function init() {

	}



	/**
	 * Initialize filters
	 */
	protected function initFilters() {
		$this->addFilter('persons', 'TodoyuReportingFilter_Persons');
		$this->addFilter('jobtypes', 'TodoyuReportingFilter_Jobtypes');

		$this->addFilter('range', 'TodoyuReportingFilter_Timerange', 100, array(
			'require' => array(
				array('persons', 'jobtypes')
			)
		));
	}



	/**
	 * Initialize result view
	 */
	protected function initResultViews() {
		$this->addResultView_Chart_Area_Percentage();
		$this->addResultView_Chart_SummaryPie();
		$this->addResultView_Table_ProjectDetails();
	}



	/**
	 * Check whether the report is ready for view rendering
	 *
	 * @return	Boolean
	 */
	protected function isReadyForView() {
		return $this->hasFilterValidValue('range');
	}



	/**
	 * Add chart view for percentage workload per project
	 */
	protected function addResultView_Chart_Area_Percentage() {
		$start	= $this->getFilter('range')->getStart();
		$end	= $this->getFilter('range')->getEnd();

			// Get tracking data per project
		$trackedProjectsTime	= $this->getMatchingTrackedProjectTimeForMonths();

		if( sizeof($trackedProjectsTime) === 0 ) {
			$this->addResultViewNoData('reporting.basicreports.workloadPerProject.view.Percentage');
			return ;
		}

			// Default chart config
		$chartConfig	= array(
			'plotOptions' => array(
				'area'	=> array(
					'stacking'	=> 'percent'
				)
			)
		);

			// Create chart
		$chart	= new TodoyuReportingResultView_ChartArea($this, 'trackedtime', 'reporting.basicreports.workloadPerProject.view.Percentage', $chartConfig);

			// Define chart labels
		$chart->setXTitle('core.date.date');
		$chart->setYTitle('reporting.ext.percent');

		$chart->setTooltipFormatter('Todoyu.Ext.reporting.Formatter.tooltipLinePercent');

			// Set months as x-axis labels
		$monthLabels	= TodoyuReportingTime::getMonthLabels($start, $end);
		$chart->setXLabels($monthLabels);

			// Set chart height
		$numProjects= sizeof($trackedProjectsTime);
		$height		= $numProjects/3*14 + 16 + 400;
		$chart->setHeight($height);

			// Add series for all projects
		foreach($trackedProjectsTime as $idProject => $projectTime) {
			$project	= TodoyuProjectProjectManager::getProject($idProject);
			$chart->addSerie($project->getTitle(), $projectTime);
		}

		$this->addResultView($chart);
	}



	/**
	 * Add result view as pie chart with workload per project
	 */
	protected function addResultView_Chart_SummaryPie() {
		$personIDs	= $this->getSelectPersons();
		$start		= $this->getFilter('range')->getStart();
		$end		= $this->getFilter('range')->getEnd();

		$trackedProjectsTime	= $this->getTrackedTimePerProject($personIDs, $start, $end);

		if( sizeof($trackedProjectsTime) === 0 ) {
			$this->addResultViewNoData('reporting.basicreports.workloadPerProject.view.SummaryPie');
			return ;
		}

			// Create chart
		$chart	= new TodoyuReportingResultView_ChartPie($this, 'summaryPie', 'reporting.basicreports.workloadPerProject.view.SummaryPie');

			// Set custom tooltip formatter
		$chart->setTooltipFormatter('Todoyu.Ext.reporting.Formatter.tooltipPieHours');

		foreach($trackedProjectsTime as $projectTime) {
			$project	= TodoyuProjectProjectManager::getProject($projectTime['project']);
			$time		= round($projectTime['tracked']/3600, 1);

			$chart->addSerie($project->getTitle(), $time, true);
		}

		$this->addResultView($chart);
	}



	/**
	 * Add result view: Table with project details
	 */
	protected function addResultView_Table_ProjectDetails() {
		$trackedTime= $this->getMatchingTrackedProjectTimeForMonths();

		if( sizeof($trackedTime) === 0 ) {
			$this->addResultViewNoData('reporting.basicreports.workloadPerProject.view.detailsPerProject');
			return ;
		}

		$monthCols	= array_keys(current($trackedTime));
		$table		= new TodoyuReportingResultView_Table($this, 'projectdetails', 'reporting.basicreports.workloadPerProject.view.detailsPerProject');

			// Set table headers
		$table->addColumnHeader('number', 'reporting.basicreports.number');
		$table->addColumnHeader('customer', 'reporting.basicreports.customer');
		$table->addColumnHeader('project', 'reporting.basicreports.project');

			// Add a column (header) for each month
		foreach($monthCols as $month) {
			$table->addColumnHeader($month, $month);
		}

			// Add row per project
		foreach($trackedTime as $idProject => $projectTime) {
			$project	= TodoyuProjectProjectManager::getProject($idProject);

				// Add extra columns. The month column have already the correct index
			$projectTime['number']	= $idProject;
			$projectTime['project']	= $project->getTitle();
			$projectTime['customer']= $project->getCompany()->getTitle();

			$table->addRow($projectTime);
		}

		$this->addResultView($table);
	}



	/**
	 * Get tracked time per project for all persons
	 *
	 * @param	Array		$personIDs
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	protected function getTrackedTimePerProject(array $personIDs, $dateStart, $dateEnd) {
		$fields	= '	SUM(tr.workload_tracked) as tracked,
					t.id_project as project';
		$tables	= '	ext_timetracking_track tr,
					ext_project_task t';
		$where	= '		tr.id_task	= t.id'
				. ' AND	tr.id_person_create IN(' . implode(',', $personIDs) . ')'
				. ' AND tr.date_track BETWEEN ' . $dateStart . ' AND ' . $dateEnd;
		$group	= '	t.id_project';

		return Todoyu::db()->getArray($fields, $tables, $where, $group);
	}



	/**
	 * Get tracking time for filter config
	 *
	 * @return	Array
	 */
	protected function getMatchingTrackedProjectTimeForMonths() {
		$personIDs	= $this->getSelectPersons();
		$start		= $this->getFilter('range')->getStart();
		$end		= $this->getFilter('range')->getEnd();

		return $this->getTrackedProjectTimeForMonths($personIDs, $start, $end);
	}



	/**
	 * Get tracked time per month for for all projects the persons have tracked on
	 *
	 * @param	Array		$personIDs
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	protected function getTrackedProjectTimeForMonths(array $personIDs, $dateStart, $dateEnd) {
		$personIDs	= TodoyuArray::intval($personIDs);
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);

			// Get projects with trackings from persons in the range
		$projectIDs	= $this->getProjectIDs($personIDs, $dateStart, $dateEnd);
		$trackings	= array();

			// Prefilled array with month keys
		$monthMap	= TodoyuReportingTime::getMonthMapInRange($dateStart, $dateEnd);

			// Get tracked time per project per month
		foreach($projectIDs as $idProject) {
			$projectTrackings = $this->getTrackedProjectTime($idProject, $personIDs, $dateStart, $dateEnd);
			$months				= $monthMap;

			foreach($projectTrackings as $projectMonthTrack) {
				if( array_key_exists($projectMonthTrack['month'], $months) ) {
					$months[$projectMonthTrack['month']] = round($projectMonthTrack['tracked']/3600);
				}
			}

			$trackings[$idProject] = $months;
		}

		return $trackings;
	}



	/**
	 * Get tracked time of persons in a project in a range
	 *
	 * @param	Integer		$idProject
	 * @param	Array		$personIDs
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	protected function getTrackedProjectTime($idProject, array $personIDs, $dateStart, $dateEnd) {
		$fields	= '	SUM(tr.workload_tracked) as tracked,
					DATE_FORMAT(FROM_UNIXTIME(tr.date_track), \'%Y-%m\') as month';
		$tables	= '	ext_timetracking_track tr,
					ext_project_task t';
		$where	= '		tr.id_person_create IN(' . implode(',', $personIDs) . ')'
				. ' AND tr.id_task		= t.id'
				. ' AND t.id_project	= ' . $idProject
				. ' AND tr.date_track BETWEEN ' . $dateStart . ' AND ' . $dateEnd;
		$group	= '	month';
		$order	= '	month';

		return Todoyu::db()->getArray($fields, $tables, $where, $group, $order);
	}



	/**
	 * Get all projects on which the persons have tracked in the range
	 *
	 * @param	Array		$personIDs
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	protected function getProjectIDs(array $personIDs, $dateStart, $dateEnd) {
		$personIDs	= TodoyuArray::intval($personIDs, true, true);
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);

		$fields	= ' DISTINCT t.id_project';
		$tables	= '	ext_project_task t,
					ext_timetracking_track tr';
		$where	= '		tr.id_person_create IN(' . implode(',', $personIDs) . ')'
				. ' AND tr.date_track BETWEEN ' . $dateStart . ' AND ' . $dateEnd
				. ' AND tr.id_task	= t.id';
		$field	= 'id_project';

		return Todoyu::db()->getColumn($fields, $tables, $where, '', '', '', $field);
	}



	/**
	 * Get selected persons of the report
	 * Contains directly selected persons and persons with the selected jobtype
	 * If both types have values, only persons which are in both are returned
	 *
	 * @return	Array
	 */
	protected function getSelectPersons() {
		$selectedPersons	= $this->getFilter('persons')->getValue();
		$jobTypePersons		= $this->getFilter('jobtypes')->getPersonIDs();

		if( sizeof($selectedPersons) === 0 || sizeof($jobTypePersons) === 0 ) {
			$personIDs	= array_merge($selectedPersons, $jobTypePersons);
		} else {
			$personIDs	= array_intersect($selectedPersons, $jobTypePersons);
		}

		return array_unique($personIDs);
	}



	/**
	 * Get timerange for the report
	 * Based on the persons selected
	 *
	 * @return	Array
	 */
	protected function calcTimerange() {
		$personIDs	= $this->getSelectPersons();

		if( sizeof($personIDs) === 0 ) {
			$timerange = false;
		} else {
			$timerange = TodoyuReportingTime::getRangeOfPersonTrackings($personIDs);
			$timerange['end']	= TodoyuTime::getDayStart(NOW);
		}

		return $timerange;
	}



	/**
	 * Get default timerange for report
	 * Prevent hugh amount of data. Initialize for the last 6 months
	 *
	 * @param	TodoyuReportingFilter_Timerange	$filter
	 * @return	Array		[start,end]
	 */
	public function getTimerangeDefault(TodoyuReportingFilter_Timerange $filter) {
		$timerange		= $this->calcTimerange();
		$currentMonth	= date('n');
		$monthRange		= 6;
		$month			= $currentMonth - $monthRange + 1;
		$start			= mktime(0, 0, 0, $month, 1);

		if( $timerange['start'] < $start ) {
			$timerange['start'] = $start;
		}

		return $timerange;
	}

}

?>