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
 * Report shows relation between estimated and tracked time of tasks
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingReportType_EstimatedTracked extends TodoyuReportingReport {

	/**
	 * @var null|Array
	 */
	protected $cachedMatchingData = null;



	/**
	 * Initialize report
	 */
	protected function init() {

	}



	/**
	 * Initialize filters
	 */
	protected function initFilters() {
		$this->addFilter('project', 'TodoyuReportingFilter_Project', 10, array(
			'disable'	=> array(
				'filterset'
			)
		));
		$this->addFilter('filterset', 'TodoyuReportingFilter_FiltersetTask', 20, array(
			'disable'	=> array(
				'project'
			)
		));
		$this->addFilter('persons', 'TodoyuReportingFilter_Persons');
		$this->addFilter('status', 'TodoyuReportingFilter_TaskStatus');
		$this->addFilter('activity', 'TodoyuReportingFilter_TaskActivity');

		$this->addFilter('range', 'TodoyuReportingFilter_Timerange', 100, array(
			'require' => array(
				array('project', 'filterset')
			)
		));
	}



	/**
	 * Initialize result view
	 */
	protected function initResultViews() {
		$this->addResultView_Chart_OvertimeBars();
		$this->addResultView_ChartPie_SummaryPie();
		$this->addResultView_Table_TaskDetails();
	}



	/**
	 * Check whether report filter have enough data to show views
	 *
	 * @return	Boolean
	 */
	protected function isReadyForView() {
		return $this->hasFilterValidValue('project') || $this->hasFilterValidValue('filterset');
	}



	/**
	 * Add chart which shows tracked, estimated, over- and left over time
	 */
	protected function addResultView_Chart_OvertimeBars() {
			// Get chart data
		$taskTrackings	= $this->getTaskTimeRatio();

			// Don't render chart if no data is available
		if( sizeof($taskTrackings) === 0 ) {
			$this->addResultViewNoData('reporting.basicreports.estimatedTracked.view.OvertimeBars');
			return ;
		}

			// Default chart config
		$chartConfig	= array(
			'legend' => array(
				'reversed' => true
			),
			'plotOptions' => array(
				'series' => array(
					'stacking' => 'normal'
				)
			),
			'colors' => array(
				'#aa4643', // Red - over-timed
				'#89a54e', // Green - under-timed
				'#4572a7', // Blue - tracked in range
			)
		);

			// Create chart
		$chart	= new TodoyuReportingResultView_Chart($this, 'overtimebars', 'reporting.basicreports.estimatedTracked.view.OvertimeBars', $chartConfig);
		$chart->data['xAxis'] = array();

			// Define chart labels
		$chart->setType('bar');
		$chart->setXTitle('project.task.tasks');
		$chart->setYTitle('core.date.time.hours');
		$chart->setTooltipFormatter('Todoyu.Ext.reporting.Formatter.tooltipBarTask');

			// Get data for the three groups
		$base		= TodoyuArray::getColumn($taskTrackings, 'base');
		$under		= TodoyuArray::getColumn($taskTrackings, 'under');
		$over		= TodoyuArray::getColumn($taskTrackings, 'over');

			// Add groups (the order matters, to set fixed positions and colors)
		$chart->addSerie('reporting.basicreports.estimatedTracked.view.OvertimeBars.overtime', $over);
		$chart->addSerie('reporting.basicreports.estimatedTracked.view.OvertimeBars.timeLeftOver', $under);
		$chart->addSerie('reporting.basicreports.estimatedTracked.view.OvertimeBars.timeInRange', $base);

			// Get all task numbers
		$taskNumbers = array_keys($taskTrackings);
			// Set task numbers as labels (reversed means x = horizontal)
		$chart->setXLabels($taskNumbers);

			// Calculate height of graph
		$height	= sizeof($taskNumbers) * 19 + 100;
		$chart->setHeight($height);

		$this->addResultView($chart);
	}



	/**
	 * Add pie chart with the two values tracked and estimated
	 */
	protected function addResultView_ChartPie_SummaryPie() {
		$taskInfos	= $this->getMatchingTaskTimeTrackedEstimated();

		if( sizeof($taskInfos) === 0 ) {
			$this->addResultViewNoData('reporting.basicreports.estimatedTracked.view.summaryPie');
			return ;
		}

		$chart		= new TodoyuReportingResultView_ChartPie($this, 'summary', 'reporting.basicreports.estimatedTracked.view.summaryPie');

		$estimated	= array_sum(TodoyuArray::getColumn($taskInfos, 'estimated'));
		$tracked	= array_sum(TodoyuArray::getColumn($taskInfos, 'tracked'));

		$chart->addSerie('project.task.attr.estimated_workload', $estimated/3600);
		$chart->addSerie('timetracking.ext.attr.workload_tracked', $tracked/3600);

		$chart->setTooltipFormatter('Todoyu.Ext.reporting.Formatter.tooltipPieHours');

		$this->addResultView($chart);
	}



	/**
	 * Add table with details about the tasks
	 */
	protected function addResultView_Table_TaskDetails() {
		$taskInfos	= $this->getMatchingTaskTimeTrackedEstimated();

		if( sizeof($taskInfos) === 0 ) {
			$this->addResultViewNoData('reporting.basicreports.estimatedTracked.view.TaskDetails');
			return ;
		}

		$table		= new TodoyuReportingResultView_Table($this, 'taskdetails', 'reporting.basicreports.estimatedTracked.view.TaskDetails');

		$table->addColumnHeader('number', 'project.task.number');
		$table->addColumnHeader('task', 'project.task.task');
		$table->addColumnHeader('estimated', 'reporting.ext.estimated');
		$table->addColumnHeader('tracked', 'reporting.ext.tracked');
		$table->addColumnHeader('ratio', 'reporting.ext.ratio');
		$table->addColumnHeader('diff', 'reporting.basicreports.estimatedTracked.view.TaskDetails.diff');

		foreach($taskInfos as $task) {
			$diff		= $task['estimated']-$task['tracked'];
			$diffSign	= $diff > 0 ? '-' : '+';

			$table->addRow(array(
				'number'	=> $task['id_project'] . '.' . $task['tasknumber'],
				'task'		=> $task['title'],
				'estimated'	=> TodoyuTime::formatTime($task['estimated']),
				'tracked'	=> TodoyuTime::formatTime($task['tracked']),
				'ratio'		=> TodoyuNumeric::ratio($task['tracked'], $task['estimated'], true, 0) . '%',
				'diff'		=> $diffSign . ' ' . TodoyuTime::formatTime(abs($diff))
			));
		}

		$table->addCallback(array($this, 'callbackTableTaskDetail'));

		$this->addResultView($table);
	}



	/**
	 * Callback for detail table
	 *
	 * @param	TodoyuReportingReportType_EstimatedTracked	$view
	 * @param	String										$value
	 * @param	Array										$rowData
	 * @param	String										$columnName
	 * @param	Array										$options
	 * @return	String
	 */
	public function callbackTableTaskDetail($view, $value, array $rowData, $columnName, array $options) {
		if( $columnName === 'diff' ) {
			$class	= strstr($value, '+') !== false ? 'pos' : 'neg';
			$value	= '<span class="' . $class . '">' . $value . '</span>';
		}

		return $value;
	}



	/**
	 * Get data for the task which match the filter
	 *  base: Tracked time in the estimated time
	 *  under: Not used estimated time
	 *  over: Too much tracked time
	 *
	 * @return	Array
	 */
	protected function getTaskTimeRatio() {
		$taskInfos	= $this->getMatchingTaskTimeTrackedEstimated();
		$data		= array();

		foreach($taskInfos as $task) {
			$taskNr	= $task['id_project'] . '.' . $task['tasknumber'];
			$data[$taskNr] = array(
				'base'	=> round(($task['tracked'] < $task['estimated'] ? $task['tracked'] : $task['estimated'])/3600, 2),
				'under'	=> round(($task['tracked'] < $task['estimated'] ? $task['estimated'] - $task['tracked'] : 0)/3600, 2),
				'over'	=> round(($task['tracked'] < $task['estimated'] ? 0 : $task['tracked'] - $task['estimated'])/3600, 2)
			);
		}

		return $data;
	}



	/**
	 * Get matching tasks with trackings
	 *
	 * @return	Array
	 */
	protected function getMatchingTaskTimeTrackedEstimated() {
		if( is_null($this->cachedMatchingData) ) {
			$range		= $this->getFilterValue('range');
			$dateStart	= intval($range['start']);
			$dateEnd	= intval($range['end']);
			$personIDs	= $this->getSelectPersons();
			$idProject	= $this->getProjectID();
			$statusIDs	= $this->getFilterValue('status');
			$activityIDs= $this->getFilterValue('activity');

				// If filterset is selected, select those
			if( $idProject === 0 && $this->hasFilterValidValue('filterset') ) {
				$idProject	= 0;
				$taskIDs	= $this->getFiltersetTaskIDs();
			} else {
				$taskIDs	= array();
			}

			$this->cachedMatchingData = $this->getTaskTimeTrackedEstimated($dateStart, $dateEnd, $idProject, $taskIDs, $personIDs, $statusIDs, $activityIDs);
		}

		return $this->cachedMatchingData;
	}



	/**
	 * Get tracked and estimated time of matching tasks
	 *
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @param	Integer		$idProject
	 * @param	Array		$taskIDs
	 * @param	Array		$personIDs
	 * @param	Array		$statusIDs
	 * @param	Array		$activityIDs
	 * @return	Array
	 */
	protected function getTaskTimeTrackedEstimated($dateStart, $dateEnd, $idProject, array $taskIDs, array $personIDs, array $statusIDs, array $activityIDs) {
		$dateStart	= intval($dateStart);
		$dateEnd	= intval($dateEnd);
		$idProject	= intval($idProject);
		$taskIDs	= TodoyuArray::intval($taskIDs);
		$personIDs	= TodoyuArray::intval($personIDs);
		$statusIDs	= TodoyuArray::intval($statusIDs);
		$activityIDs= TodoyuArray::intval($activityIDs);

		$fields	= '	t.id,
					t.id_project,
					t.tasknumber,
					t.title,
					t.estimated_workload as estimated,
					SUM(tr.workload_tracked) as tracked';
		$tables	= '	ext_project_task t,
					ext_timetracking_track tr';
		$where	= '	tr.id_task	= t.id '
				. ' AND t.deleted = 0 '
				. ' AND ('
				. '		t.date_start	BETWEEN ' . $dateStart . ' AND ' . $dateEnd
				. ' OR	t.date_end		BETWEEN ' . $dateStart . ' AND ' . $dateEnd
				. ' OR	t.date_deadline BETWEEN ' . $dateStart . ' AND ' . $dateEnd
				. ' )';


		if( $idProject !== 0 ) {
			$where .= ' AND	t.id_project = ' . $idProject;
		}

		if( $idProject === 0 ) {
			if( sizeof($taskIDs) > 0 ) {
				$where .= ' AND t.id IN(' . implode(',', $taskIDs) . ')';
			} else {
				$where .= ' AND 0';
			}
		}

		if( sizeof($personIDs) > 0 ) {
			$where .= '	AND t.id_person_assigned IN(' . implode(',', $personIDs) . ')';
		}
		if( sizeof($statusIDs) > 0 ) {
			$where .= '	AND t.status IN(' . implode(',', $statusIDs) . ')';
		}
		if( sizeof($activityIDs) > 0 ) {
			$where .= '	AND t.id_activity IN(' . implode(',', $activityIDs) . ')';
		}

		$group	= '	t.id';
		$order	= '	t.date_start,
					t.date_end,
					t.date_deadline';

		return Todoyu::db()->getArray($fields, $tables, $where, $group, $order);
	}



	/**
	 * Get selected project ID
	 *
	 * @return	Integer
	 */
	protected function getProjectID() {
		return intval($this->getFilterValue('project'));
	}



	/**
	 * Get task IDs which are in the selected filterset
	 *
	 * @return	Array
	 */
	protected function getFiltersetTaskIDs() {
		if( $this->hasFilterValidValue('filterset') ) {
			return $this->getFilter('filterset')->getItemIDs();
		} else {
			return array();
		}
	}



	/**
	 * Get selected persons
	 *
	 * @return	Array
	 */
	protected function getSelectPersons() {
		return TodoyuArray::intval($this->getFilter('persons')->getValue());
	}



	/**
	 * Get range from elements which match the filterset
	 *
	 * @return	Array|Boolean		[start,end] or FALSE
	 */
	protected function getFiltersetRange() {
		$taskIDs	= $this->getFilter('filterset')->getItemIDs();

		if( sizeof($taskIDs) === 0 ) {
			return false;
		}

		$fields	= '	MIN(NULLIF(date_start, 0)) as start,
					MAX(date_end) as end,
					MAX(date_deadline) as deadline';
		$table	= '	ext_project_task';
		$where	= '	id IN(' . implode(',', $taskIDs) . ')';

		$result	= Todoyu::db()->getRecordByQuery($fields, $table, $where);

		return array(
			'start'	=> $result['start'],
			'end'	=> max($result['end'], $result['deadline'])
		);
	}



	/**
	 * Get timerange
	 * timerange is defined by selected project
	 *
	 * @return	Array|Boolean
	 */
	protected function calcTimerange() {
		if( $this->hasFilterValidValue('filterset') ) {
				// If filterset is set, use this
			$timerange = $this->getFiltersetRange();
		} elseif( $this->hasFilterValidValue('project') ) {
				// If project is set, use this range
			$project	= TodoyuProjectProjectManager::getProject($this->getProjectID());

			$timerange = $project->getRangeDates();
		} else {
			$timerange = false;
		}

		return $timerange;
	}
}

?>