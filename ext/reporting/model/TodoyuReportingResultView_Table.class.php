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
 * Result view table
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
class TodoyuReportingResultView_Table extends TodoyuReportingResultView {

	/**
	 * Row data
	 *
	 * @var	Array
	 */
	protected $rows		= array();

	/**
	 * Column headers
	 *
	 * @var	Array
	 */
	protected $columnHeaders = array();

	/**
	 * Cell callback functions
	 *
	 * @var	Array
	 */
	protected $callbacks = array();

	/**
	 * Flag whether callbacks have already processed the cells
	 *
	 * @var	Boolean
	 */
	protected $callbacksProcessed = false;

	/**
	 * Flag whether table should be sortable
	 *
	 * @var	Boolean
	 */
	protected $sortable = true;


	/**
	 * Initialize table
	 */
	protected function init() {
		$this->rows	= TodoyuArray::assure($this->data);
	}



	/**
	 * Get table row data
	 *
	 * @return	Array
	 */
	protected function getTableData() {
		if( ! $this->callbacksProcessed ) {
			$this->applyCallbacks();
		}

		return $this->rows;
	}



	/**
	 * Set sortable flag which enabled/disables sorting
	 *
	 * @param	Boolean		$sortable
	 */
	public function makeSortable($sortable = true) {
		$this->sortable = (boolean)$sortable;
	}



	/**
	 * Get column headers config
	 *
	 * @return	Array
	 */
	protected function getColumnHeaders() {
		return $this->columnHeaders;
	}



	/**
	 * Get names of the columns
	 *
	 * @return	Array
	 */
	protected function getColumnNames() {
		return TodoyuArray::getColumn($this->columnHeaders, 'name');
	}



	/**
	 * Set all column headers at once
	 *
	 * @param	Array		$columns
	 */
	public function setColumnHeaders(array $columns) {
		$this->columnHeaders = $columns;
	}



	/**
	 * Add a column header
	 *
	 * @param	String		$name
	 * @param	String		$label
	 * @param	String		$class
	 */
	public function addColumnHeader($name, $label, $class = '') {
		$this->columnHeaders[$name] = array(
			'name'	=> $name,
			'label'	=> Todoyu::Label($label),
			'class'	=> $class
		);
	}



	/**
	 * Add a row
	 * The row array has to be an associative array where the keys match the column names
	 *
	 * @param	Array		$row
	 */
	public function addRow(array $row) {
		$this->rows[] = $row;
	}



	/**
	 * Add a column
	 *
	 * @param	String		$name
	 * @param	Array		$cells
	 * @param	Array		$header		If set, use 'label' and 'class' key to add a header column
	 */
	public function addColumn($name, array $cells, array $header = null) {
		if( is_array($header) ) {
			$this->addColumnHeader($name, $header['label'], $header['class']);
		}

			// Prevent adding extra cells if first column hasn't that much rows
		if( sizeof($this->rows) !== 0 && sizeof($cells) > sizeof($this->rows) ) {
			$cells	= array_slice($cells, 0, sizeof($this->rows));
		}
			// Add column cells
		foreach($cells as $index => $cell) {
			$this->rows[$index][$name] = $cell;
		}
	}



	/**
	 * Add a new cell callback
	 * Callback parameters:
	 * $view, $value, $rowData, $columnName, $options
	 *
	 * @param	Array|String	$callback		Array for call_user_func() or a todoyu func ref string
	 * @param	Array			$options
	 */
	public function addCallback($callback, array $options = array()) {
		$this->callbacks[] = array(
			'callback'	=> $callback,
			'options'	=> $options
		);
	}



	/**
	 * Apply registered callbacks to every cell
	 */
	protected function applyCallbacks() {
		foreach($this->rows as $rowIndex => $row) {
			foreach($row as $colName => $value) {
				foreach($this->callbacks as $callback) {
					$this->rows[$rowIndex][$colName] = call_user_func($callback['callback'], $this, $value, $row, $colName, $callback['options']);
				}
			}
		}
	}



	/**
	 * Get template data to render table
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		return array(
			'header'			=> $this->getColumnHeaders(),
			'columnNames'		=> $this->getColumnNames(),
			'data'				=> $this->getTableData(),
			'name'				=> $this->getName(),
			'idReport'			=> $this->getReportID(),
			'tableKitOptions'	=> $this->getTableKitOptions(),
			'sortable'			=> $this->sortable
		);
	}



	/**
	 * Get options for tableKit sorting script
	 *
	 * @return	Array
	 */
	protected function getTableKitOptions() {
		return array(
			'autoLoad'		=> false,
			'editable'		=> false,
			'resizable'		=> false,
			'rowEvenClass'	=> 'even',
			'rowOddClass'	=> 'odd',
		);
	}



	/**
	 * Render table
	 *
	 * @return	String
	 */
	public function render() {
		$tmpl	= 'ext/reporting/view/result/table.tmpl';
		$data	= $this->getTemplateData();

		return Todoyu::render($tmpl, $data);
	}

}

?>