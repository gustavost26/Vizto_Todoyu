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
 * Custom result view
 *
 * @package		Todoyu
 * @subpackage	Reporting
 */
abstract class TodoyuReportingResultView_Custom extends TodoyuReportingResultView {

	/**
	 * @var	String		Template path
	 */
	protected $template;



	/**
	 * Get path to template
	 *
	 * @param	String		$template
	 */
	public function setTemplate($template) {
		$this->template = TodoyuFileManager::pathAbsolute($template);
	}



	/**
	 * Get template data
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		return $this->data;
	}



	/**
	 * Get template path
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return $this->template;
	}



	/**
	 * Render no data message
	 *
	 * @return	String
	 */
	public function render() {
		$tmpl	= $this->getTemplate();
		$data	= $this->getTemplateData();

		return Todoyu::render($tmpl, $data);
	}

}

?>