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

Todoyu.Ext.reporting.PanelWidget.ReportsList = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.reporting,



	/**
	 * Initialize panel widget
	 *
	 * @method	init
	 */
	init: function() {
		this.addPanelWidgetObservers();

			// Listen to report.saved hook
		Todoyu.Hook.add('reporting.report.saved', this.hookReportSaved.bind(this));
	},



	/**
	 * Initialize observers
	 *
	 * @method	initObservers
	 */
	addPanelWidgetObservers: function() {
			// Initialize sorting of group elements
		this.initSortableList();

			// Observe select field for new reports
		$('panelwidget-reportslist-select').on('change', this.onReportTypeSelect.bind(this));
	},



	/**
	 * Initialize list as sortable
	 *
	 * @method	initSortableList
	 */
	initSortableList: function() {
		new Todoyu.SortablePanelList('reports-list', this.onListToggled.bind(this), this.onReportSorted.bind(this), true);
	},



	/**
	 * Save toggle status of group
	 *
	 * @method	onListToggled
	 * @param	{String}	group
	 * @param	{Boolean}	expanded
	 */
	onListToggled: function(group, expanded) {
		var url		= Todoyu.getUrl('reporting', 'panelwidgetreportslist');
		var options	= {
			parameters: {
				action:	'savetoggle',
				type: group,
				expanded: expanded ? 1 : 0
			}
		};

		Todoyu.send(url, options);
	},



	/**
	 * Save sorting order of reports in a type
	 *
	 * @method	onReportSorted
	 * @param	{String}	type
	 * @param	{Array}		items
	 */
	onReportSorted: function(type, items) {
		var url		= Todoyu.getUrl('reporting', 'panelwidgetreportslist');
		var options	= {
			parameters: {
				action:	'saveorder',
				type: type,
				items: items.join(',')
			}
		};

		Todoyu.send(url, options);
	},



	/**
	 * Callback for report.saved hook
	 *
	 * @method	hookReportSaved
	 * @param	{Number}	idReport
	 */
	hookReportSaved: function(idReport) {
		this.refresh();
	},



	/**
	 * Refresh panel widget
	 *
	 * @method	refresh
	 */
	refresh: function() {
		var url		= Todoyu.getUrl('reporting', 'panelwidgetreportslist');
		var options	= {
			parameters: {
				action: 'refresh'
			},
			onComplete: this.onRefreshed.bind(this)
		};
		var container= 'panelwidget-reportslist-content';

		Todoyu.Ui.update(container, url, options);
	},



	/**
	 * Handler when panelwidget was refreshed
	 *
	 * @method	onRefreshed
	 * @param	{Ajax.Response}		response
	 */
	onRefreshed: function(response) {
		this.addPanelWidgetObservers();
	},



	/**
	 * Handler when user selects a reporttype from dropdown
	 *
	 * @method	onReportTypeSelect
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onReportTypeSelect: function(event, element) {
		var type	= $F(element);
		element.selectedIndex = 0;

		if( type !== '0' ) {
			this.ext.openReportType(type);
		}
	},



	/**
	 * Open a report
	 *
	 * @method	openReport
	 * @param	{Number}	idReport
	 */
	openReport: function(idReport) {
		this.ext.openReport(idReport);
	},



	/**
	 * Rename a report
	 *
	 * @method	renameReport
	 * @param	{Number}	idReport
	 */
	renameReport: function(idReport) {
		var currentName	= this.getReportTitle(idReport);
		var newName		= prompt('[LLL:search.ext.filterset.rename]', currentName);

		if( newName !== null && newName.strip() !== '' ) {
			newName = newName.stripScripts().strip();

			$('report-' + idReport + '-label').update(newName.escapeHTML());
			$('report-' + idReport + '-label').title = newName.escapeHTML();

			this.saveReportRename(idReport, newName);

			if( this.ext.Tab.isOpen(idReport) ) {
				this.ext.Tab.rename(idReport, newName);
			}
		}
	},



	/**
	 * Get report title
	 *
	 * @method	getReportTitle
	 * @param	{Number}	idReport
	 * @return	{String}
	 */
	getReportTitle: function(idReport) {
		return $('report-' + idReport + '-label').title;
	},



	/**
	 * Save/overwrite a report
	 *
	 * @method	saveReport
	 * @param	{Number}	idReport
	 */
	saveReport: function(idReport) {
		if( this.hasReportSameTypeAsActive(idReport) ) {
			if( confirm('[LLL:reporting.panelwidget-reportslist.overwriteReport]') ) {
				this.ext.saveAsReport(idReport);
			}
		} else {
			alert('[LLL:search.ext.filterset.error.saveWrongType]');
		}
	},



	/**
	 * Delete a report
	 *
	 * @method	deleteReport
	 * @param	{Number}	idReport
	 */
	deleteReport: function(idReport) {
		var reportTitle = this.getReportTitle(idReport);
		reportTitle		= reportTitle.replace(/"/, '\\\"');
		var message		= "[LLL:reporting.ext.report]: \"" + reportTitle + "\"\r\n\r\n[LLL:reporting.panelwidget-reportslist.deleteReport]";

		if( confirm(message) ) {
			var url		= Todoyu.getUrl('reporting', 'report');
			var options	= {
				parameters: {
					action: 'delete',
					report: idReport
				},
				onComplete: this.onReportDeleted.bind(this, idReport)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler when a report was deleted
	 * Update panel widget and call extension handler
	 *
	 * @method	onReportDeleted
	 * @param	{String}	idReport
	 */
	onReportDeleted: function(idReport) {
		this.ext.removeReport(idReport);
		this.refresh();
	},



	/**
	 * Check whether the selected report has the same type as the active one
	 * Necessary to overwrite it with new config
	 *
	 * @method	hasReportSameTypeAsActive
	 * @param	{String}	idReport
	 */
	hasReportSameTypeAsActive: function(idReport) {
		return this.ext.getReportType() === this.getType(idReport);
	},



	/**
	 * Get type of report
	 *
	 * @method	getType
	 * @param	{Number}	idReport
	 * @return	{String}
	 */
	getType: function(idReport) {
		return $('report_' + idReport).up('ul').id.split('-').last();
	},



	/**
	 * Save new name of report
	 *
	 * @method	saveReportRename
	 * @param	{Number}	idReport
	 * @param	{String}	title
	 */
	saveReportRename: function(idReport, title) {
		var url		= Todoyu.getUrl('reporting', 'report');
		var options	= {
			parameters: {
				action:	'rename',
				report:	idReport,
				title: title
			},
			onComplete: this.onRenameSaved.bind(this, idReport, title)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when rename request is finisehd
	 *
	 * @method	onRenameSaved
	 * @param	{Number}		idReport
	 * @param	{String}		title
	 * @param	{Ajax.Response}	response
	 */
	onRenameSaved: function(idReport, title, response) {

	},



	/**
	 * Save active report as a new report
	 * Ask for a label
	 *
	 * @method	saveAsNewReport
	 */
	saveAsNewReport: function() {
		if( this.ext.isAnyReportShown() ) {
			var title = prompt("[LLL:reporting.panelwidget-reportslist.newReportName]", '');
			if( title ) {
				this.ext.saveAsReport(0, title.strip());
			}
		} else {
			alert("[LLL:reporting.panelwidget-reportslist.newReportName]");
		}
	},



	/**
	 * Save current report- if already saved overwrite, otherwise ask for name
	 *
	 * @method	saveCurrent
	 */
	saveCurrent: function() {
		if( this.ext.isAnyReportShown() ) {
			if( this.ext.isActiveReportSaved() ) {
				this.ext.saveAsReport(this.ext.getActive());
			} else {
				this.saveAsNewReport();
			}
		} else {
			alert("[LLL:reporting.panelwidget-reportslist.newReportName]");
		}
	}

};