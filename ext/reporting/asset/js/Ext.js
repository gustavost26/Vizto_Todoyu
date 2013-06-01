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
 *  @module		Reporting
 */

/**
 * Reporting script container
 *
 * @class		Reporting
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.reporting = {

	PanelWidget: {},

	/**
	 * Highcharts instances
	 */
	charts: {},

	/**
	 * Config options of the highcharts instances (for later use only)
	 */
	chartOptions: {},

	/**
	 * Function timeout reference for delayed refresh
	 */
	refreshDelay: null,

	/**
	 * Delay before the results are refreshed after a fitler change
	 */
	refreshTime: 1.0,

	/**
	 * Todoyu.LoaderBox
	 */
	loaderBox: {},



	/**
	 * Initialize extension JS
	 *
	 * @method	init
	 */
	init: function() {
		if( Todoyu.isInArea('reporting') ) {
			this.Filter.init();
		}
	},



	/**
	 * Open a report
	 *
	 * @method	openReport
	 * @param	{String}	idReport
	 */
	openReport: function(idReport){
		this.open(idReport);
	},



	/**
	 * Open a new empty report by type
	 *
	 * @method	openReportType
	 * @param	{String}	reportType
	 */
	openReportType: function(reportType) {
		this.open(0, reportType);
	},



	/**
	 * Open a report by type or ID
	 *
	 * @method	open
	 * @param	{Number}	idReport
	 * @param	{String}	reportType
	 */
	open: function(idReport, reportType) {
		if( idReport != 0 && this.isReportLoaded(idReport) ) {
			this.showReport(idReport);
			this.saveOpenReports();
		} else {
			this.load(idReport, reportType);
		}
	},



	/**
	 * Remove report bodies which have no tabs
	 *
	 * @method	removeUnaccessibleReports
	 */
	removeUnaccessibleReports: function() {
		var open	= this.Tab.getOpenReports();
		var loaded	= $('content-body').select('div.report').collect(function(report){
			return report.id.split('-').last();
		});

		var unaccessible = Array.prototype.without.apply(loaded, open);

		unaccessible.each(function(idReport){
			var x = $('report-' + idReport);
			x.remove();
		});
	},



	/**
	 * Load a report
	 * Can be done by ID or by type. Only one of them is required
	 *
	 * @method	load
	 * @param	{Number}	idReport
	 * @param	{String}	reportType
	 * @param	{Function}	onComplete
	 */
	load: function(idReport, reportType, onComplete) {
		var url		= Todoyu.getUrl('reporting', 'report');
		var options	= {
			parameters: {
				action:		'load',
				report:		idReport,
				reporttype: reportType
			},
			onComplete: this.onLoaded.bind(this, idReport, reportType, onComplete)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when report is loaded
	 *
	 * @method	onLoaded
	 * @param	{Number}		idReport
	 * @param	{String}		reportType
	 * @param	{Function}		onComplete
	 * @param	{Ajax.Response}	response
	 */
	onLoaded: function(idReport, reportType, onComplete, response) {
		var label	= response.getTodoyuHeader('tablabel');
		idReport	= response.getTodoyuHeader('report');

			// Add tab report
		this.Tab.addReportTab(idReport, label);

		this.insertReportBody(response.responseText);

		this.showReport(idReport);

		this.initReport(idReport);

		this.removeUnaccessibleReports();

		if( onComplete ) {
			onComplete(idReport, reportType, response);
		}
	},



	/**
	 * Remove a report from content (tab and body(
	 *
	 * @method	removeReport
	 * @param	{String}	idReport
	 */
	removeReport: function(idReport) {
		var idActiveReport = this.Tab.getActive();

		this.Tab.remove(idReport);
		this.removeUnaccessibleReports();

		if( this.Tab.areNoReportsOpen() ) {
			this.showNoReportOpened();
		} else if( idReport == idActiveReport) {
			this.showNextOpenedReport();
		}
	},



	/**
	 * Show next opened report
	 *
	 * @method	showNextOpenedReport
	 */
	showNextOpenedReport: function() {
		var idReport	= this.Tab.getOpenReports().first();

		this.openReport(idReport);
	},



	/**
	 * Show message in content when no reports are open
	 *
	 * @method	showNoReportOpened
	 */
	showNoReportOpened: function() {
		var container	= 'content-body';
		var url		= Todoyu.getUrl('reporting', 'ext');
		var options	= {
			parameters: {
				action: 'noreportopened'
			}
		};

		Todoyu.Ui.insert(container, url, options);
	},



	/**
	 * Get number of open reports
	 *
	 * @method	getNumOpenReports
	 * @return	{Number}
	 */
	getNumOpenReports: function() {
		return this.Tab.getNumTabs();
	},



	/**
	 * Save which reports are currently opened as tabs
	 *
	 * @method	saveOpenReports
	 */
	saveOpenReports: function() {
		var reports	= this.Tab.getOpenReports();

		var url		= Todoyu.getUrl('reporting', 'ext');
		var options	= {
			parameters: {
				action:		'openreports',
				reports:	reports.join(',')
			}
		};

		Todoyu.send(url, options);
	},



	/**
	 * Check whether a report is loaded (report content body)
	 *
	 * @method	isReportLoaded
	 * @param	{String}	idReport
	 * @return	{Boolean}
	 */
	isReportLoaded: function(idReport) {
		return Todoyu.exists('report-' + idReport);
	},



	/**
	 * Initialize a report
	 *
	 * @method	initReport
	 * @param	{String}	[idReport]
	 */
	initReport: function(idReport) {
		idReport	= idReport || this.getActive();

		this.Filter.init(idReport);
	},



	/**
	 * Show a report which is already loaded
	 * Move tab to front and show content
	 *
	 * @method	showReport
	 * @param	{String}	idReport
	 */
	showReport: function(idReport) {
		this.Tab.moveAsFirst(idReport);
		this.showReportContent(idReport);
	},



	/**
	 * Check whether any report is currently shown
	 *
	 * @method	isAnyReportShown
	 * @return	{Boolean}
	 */
	isAnyReportShown: function() {
		return $$('.report[id!="report-noid"]').length > 0;
	},



	/**
	 * Show the content of a report. Hide currently active report
	 *
	 * @method	showReportContent
	 * @param	{String}	idReport
	 */
	showReportContent: function(idReport) {
		$('content').select('.report').invoke('hide');
		$('report-' + idReport).show();
	},



	/**
	 * Save filters as report
	 * If idReport is zero, a new report will be created
	 *
	 * @method	saveAsReport
	 * @param	{String}	idReport
	 * @param	{String}	newTitle
	 */
	saveAsReport: function(idReport, newTitle) {
		if( idReport === 0 && ! this.isActiveReportSaved() ) {
			idReport = this.getActive();
		}

		this.getActiveForm().request({
			parameters: {
				action:		'save',
				report:		idReport,
				title:		newTitle,
				reporttype: this.getReportType(idReport),
				area:		Todoyu.getArea()
			},
			onComplete: this.onSaved.bind(this, idReport, newTitle)
		});
	},



	/**
	 * Handler when report was saved
	 *
	 * @method	onSaved
	 * @param	{String}		idOldReport
	 * @param	{String}		title
	 * @param	{Ajax.Response}	response
	 */
	onSaved: function(idOldReport, title, response) {
		var idReport	= response.getTodoyuHeader('report');

		if( idReport != idOldReport ) {
				// Remove temporary report if saved as new
			if( ! this.isSavedReport(idOldReport) ) {
				this.removeReportElements(idOldReport);
			}
				// Load new saved report
			this.load(idReport, '', this.onNewSaveLoaded.bind(this, idOldReport));
		} else {
			Todoyu.Hook.exec('reporting.report.saved', idReport);
		}
	},



	/**
	 * Callback when a new report was saved and loaded again with the new id
	 *
	 * @method	onNewSaveLoaded
	 * @param	{Number}		idOldReport		ID of the old report (from bind() in onSaved())
	 * @param	{Number}		idReport		Current report id
	 * @param	{String}		reportType
	 * @param	{Ajax.Response}	response
	 */
	onNewSaveLoaded: function(idOldReport, idReport, reportType, response) {
		Todoyu.Hook.exec('reporting.report.saved', idReport);
	},



	/**
	 * Remove report body and tab
	 *
	 * @method	removeReportElements
	 * @param	{String|Number}	idReport
	 */
	removeReportElements: function(idReport) {
		this.removeReportBody(idReport);
		this.Tab.remove(idReport);
	},



	/**
	 * Remove body container of report
	 *
	 * @method	removeReportBody
	 * @param	{String|Number}	idReport
	 */
	removeReportBody: function(idReport) {
		$('report-' + idReport).remove();
	},



	/**
	 * Insert a new report body container into content-body
	 *
	 * @method	insertReportBody
	 * @param	{String}	content
	 */
	insertReportBody: function(content) {
		$('content-body').insert({
			bottom: content
		});
	},



	/**
	 * Get filter values of active form
	 *
	 * @method	getFilterValues
	 * @return	{Object}
	 */
	getFilterValues: function(idReport) {
		var data	= this.getForm(idReport).serialize(true);

		delete data.report;
		delete data.reporttype;

		return data;
	},



	/**
	 * Get form element
	 *
	 * @method	getForm
	 * @param	{String}	[idReport]
	 * @return	{Element}
	 */
	getForm: function(idReport) {
		idReport	= idReport || this.getActive();

		return $('report-' + idReport).down('form');
	},



	/**
	 * Get active form
	 *
	 * @method	getActiveForm
	 * @return	{Form}
	 */
	getActiveForm: function() {
		return this.getForm();
	},



	/**
	 * Toggle filter area, so only the results are visible
	 *
	 * @method	toggleFilters
	 * @param	{String}	idReport
	 */
	toggleFilters: function(idReport) {
		var form	= this.getForm(idReport);
		var effect	= form.visible() ? 'SlideUp' : 'SlideDown';

			// If report is already saved, save status
		if( this.isSavedReport(idReport) ) {
			var url		= Todoyu.getUrl('reporting', 'ext');
			var options	= {
				parameters: {
					action:		'filtertoggle',
					report:		idReport,
					toggled:	form.visible() ? 1 : 0
				}
			};

			Todoyu.send(url, options);
		}

		Effect[effect](form, {
			duration: 0.3
		});
	},



	/**
	 * Check if numeric id (of saved report)
	 *
	 * @method	isSavedReport
	 * @param	{String}	idReport
	 * @return	{Boolean}
	 */
	isSavedReport: function(idReport) {
		return parseInt(idReport) == idReport;
	},



	/**
	 * @method	isActiveReportSaved
	 * @return	{Boolean}
	 */
	isActiveReportSaved: function() {
		return this.isSavedReport(this.getActive());
	},



	/**
	 * Refresh the whole report (filter and views) with the current filters
	 *
	 * @method	refreshReport
	 * @param	{String}	[idReport]		Report to refresh (or active)
	 * @param	{Function}	[onComplete]		Custom callback function
	 * @param	{Boolean}	[noDelay]
	 */
	refreshReport: function(idReport, onComplete, noDelay) {
		idReport	= idReport || this.getActive();
		onComplete	= onComplete || Prototype.emptyFunction;

		if( noDelay !== true ) {
			this.delayRefresh(idReport, onComplete);
			return ;
		}
		this.showLoaderBox();
		var form	= this.getForm(idReport);

			// Set action URL
		form.action	= Todoyu.getUrl('reporting', 'report', {
			action: 'refresh'
		});

			// Submit form
		form.request({
			onComplete: this.onRefreshed.bind(this, idReport, onComplete)
		});
	},



	/**
	 * Start a delayed refresh
	 *
	 * @method	delayRefresh
	 * @param	{Number}	idReport
	 * @param	{Function}	onComplete
	 * @param	{Number}	[refreshTime]			Custom refresh delay time
	 */
	delayRefresh: function(idReport, onComplete, refreshTime) {
			// Get custom time or default
		refreshTime	= refreshTime || this.refreshTime;

			// Reset existing timeouts
		clearTimeout(this.refreshDelay);
		this.refreshDelay = null;

			// Create a new timeout
		this.refreshDelay = this.refreshReport.bind(this, idReport, onComplete, true).delay(refreshTime);
	},



	/**
	 * Only delay a refresh if an timeout already exists
	 *
	 * @method	delayRefreshIfActive
	 * @param	{Number}	refreshTime		Custom refresh time
	 * @param	{String}	idReport
	 * @param	{Function}	onComplete
	 */
	delayRefreshIfActive: function(refreshTime, idReport, onComplete) {
		if( this.refreshDelay !== null ) {
			this.delayRefresh(idReport, onComplete, refreshTime);
		}
	},



	/**
	 * Handler when report was refreshed
	 *
	 * @method	onRefreshed
	 * @param	{Number}		idReportOld
	 * @param	{Function}		onComplete
	 * @param	{Ajax.Response}	response
	 */
	onRefreshed: function(idReportOld, onComplete, response) {
		var idReport	= response.getTodoyuHeader('report');
		this.removeReportBody(idReportOld);

		this.insertReportBody(response.responseText);

		this.Filter.init();

		this.hideLoaderBox();

		onComplete(idReport, response);
	},



	/**
	 * Set content of results of a report
	 *
	 * @method	setResultViewContent
	 * @param	{String|Number}	idReport
	 * @param	{String}		content
	 */
	setResultViewContent: function(idReport, content) {
		$('report-' + idReport + '-results').update(content);
	},



	/**
	 * Add a new chart
	 *
	 * @method	addChart
	 * @param	{String}	name
	 * @param	{Object}	options
	 * @return	{Highcharts.Chart}
	 */
	addChart: function(name, options) {
		this.destroyChart(name);

		this.chartOptions[name] = options;

		var chartOptions	= Todoyu.Helper.cloneObject(options);
		this.charts[name] 	= new Highcharts.Chart(chartOptions);

		return this.charts[name];
	},



	/**
	 * Get options of a chart
	 *
	 * @method	getChartOptions
	 * @param	{String}	name
	 */
	getChartOptions: function(name) {
		return this.chartOptions[name];
	},



	/**
	 * Get a chart
	 *
	 * @method	getChart
	 * @param	{String}	name
	 * @return	{Highcharts.Chart}
	 */
	getChart: function(name) {
		return this.charts[name];
	},



	/**
	 * Destroy a chart to free memory
	 *
	 * @method	destroyChart
	 * @param	{String}	name
	 */
	destroyChart: function(name) {
		if( this.getChart(name) instanceof Highcharts.Chart ) {
			this.getChart(name).destroy();
			delete this.charts[name];
		}
	},



	/**
	 * Remove a chart from page and memory a chart
	 *
	 * @method	removeChart
	 * @param	{String}	name
	 */
	removeChart: function(name) {
		if( this.charts[name] ) {
			var container	= this.charts[name].options.chart.renderTo;

			this.charts[name].destroy();
			delete this.charts[name];

			if( Todoyu.exists(container) ) {
				$(container).update('');
			}
		}
	},



	/**
	 * Get active report
	 *
	 * @method	getActive
	 * @return	{String}		Report ID or temp key
	 */
	getActive: function() {
		return this.Tab.getActive();
	},



	/**
	 * Get report type
	 *
	 * @method	getReportType
	 * @param	{String}	[idReport]
	 * @return	{String}
	 */
	getReportType: function(idReport) {
		idReport	= idReport || this.getActive();

		return Todoyu.String.getClassKey($('report-' + idReport), 'reportType');
	},



	/**
	 * @method	showLoaderBox
	 */
	showLoaderBox: function() {
		if( ! (this.loaderBox instanceof Todoyu.LoaderBox) ) {
			this.loaderBox = new Todoyu.LoaderBox('report', {
					block:	true,
					text:	'[LLL:reporting.ext.pleaseWait]',
					show:	false
			});
		}
		
		this.loaderBox.show();
	},



	/**
	 * @method	hideLoaderBox
	 */
	hideLoaderBox: function() {
		this.loaderBox.hide();
	}

};