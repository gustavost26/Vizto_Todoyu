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
 * @module	Reporting
 */

Todoyu.Ext.reporting.ResultView = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.reporting,



	/**
	 * Initialize result view
	 *
	 * @method	init
	 */
	init: function() {

	},



	/**
	 * Initialize table sort
	 *
	 * @method	initTableSort
	 * @param	{String}	idTable
	 * @param	{Object}	options
	 */
	initTableSort: function(idTable, options) {
		TableKit.unloadTable(idTable);

		TableKit.Sortable.init(idTable, options);
	},



	/**
	 * Show chart in popup
	 *
	 * @method	showInPopup
	 * @param	{Element}	container		Container where original chart is located
	 * @param	{String}	name			Name of the (original) chart. To read the options
	 */
	showInPopup: function(container, name) {
		container	= $(container);
		var hcContainer		= container.down('div.highcharts-container');
		var idPopup			= container.id + '-popup';

			// Get dimensions
		var hcDim		= hcContainer.getDimensions();
		var viewportDim	= document.viewport.getDimensions();

			// Calculate new dimensions
		var factor		= Math.min((viewportDim.width-50)/hcDim.width, (viewportDim.height-50)/hcDim.height);
		var newWidth	= (hcDim.width * factor).floor();
		var newHeight	= (hcDim.height * factor).floor();

			// Get title
		var title	= container.up('div.result').down('.label .text').innerHTML;

			// Open popup window
		Todoyu.Popups.open(idPopup, title, newWidth);

			// Insert a new container into the popup window for the new chart
		var hcContainerPopup = new Element('div', {
			id: container.id + '-popup-container'
		}).setStyle({
			height: newHeight + 'px',
			width: newWidth + 'px'
		});
		$(idPopup + '_content').update(hcContainerPopup);

			// Create a new chart with the same options
		var options	= this.ext.getChartOptions(name);
		options.chart.renderTo = hcContainerPopup.id;
		this.ext.addChart(hcContainerPopup.id, options);

			// Observe close event
		$(idPopup).down('div.dialog_close').on('click', this.onClose.bind(this, hcContainerPopup.id));
	},



	/**
	 * Handler when popup is closed
	 * Destroy temporary popup highcharts element
	 *
	 * @method	onClose
	 * @param	{String}	chartName
	 * @para	{Event}		event
	 */
	onClose: function(chartName, event) {
		this.ext.destroyChart(chartName);
	},



	/**
	 * Scroll to result view
	 *
	 * @method	scrollTo
	 * @param	{String}	idReport
	 * @param	{String}	name
	 */
	scrollTo: function(idReport, name) {
		Effect.ScrollTo('report-' + idReport + '-result-' + name, {
			duration: 0.7,
			offset: -($('header').getHeight() + 5)
		});
	}

};