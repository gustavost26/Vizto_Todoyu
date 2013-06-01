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
 * Reporting tab handling
 *
 * @module	Reporting
 */
Todoyu.Ext.reporting.Tab = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.reporting,

	/**
	 * Maximum amount of opened tabs. If a new tabs gets added, the last one will be removed
	 *
	 * @property	maxTabs
	 * @type		Number
	 */
	maxTabs: 3,



	/**
	 * Handler when clicked on a tab
	 *
	 * @method	onSelect
	 * @param	{Event}		event
	 * @param	{String}	idReport
	 */
	onSelect: function(event, idReport) {
		Todoyu.Tabs.moveAsFirst('reports', idReport);

		this.ext.open(idReport);
	},



	/**
	 * Check whether a tab is open (in the tab group)
	 *
	 * @method	isOpen
	 * @param	{String}	idReport
	 */
	isOpen: function(idReport) {
		return Todoyu.Tabs.hasTab('reports', idReport);
	},



	/**
	 * Add a new report tab (or move to front if already exists)
	 *
	 * @method	addReportTab
	 * @param	{Number}	idReport
	 * @param	{String}	label
	 */
	addReportTab: function(idReport, label) {
		if( this.isOpen(idReport) ) {
			this.moveAsFirst(idReport);
		} else {
			label	= Todoyu.String.cropText(label, 25);
			Todoyu.Tabs.addTab('reports', idReport, '', label, true, true);
			this.removeNoReportTab();
			Todoyu.Tabs.removeSurplus('reports', this.maxTabs);
			if( isNaN(idReport) ) {
				this.markAsUnsaved(idReport);
			}
		}
	},



	/**
	 * Get tab element
	 *
	 * @method	getTab
	 * @param	{Number}	idReport
	 * @return	{Element}
	 */
	getTab: function(idReport) {
		return $('reports-tab-' + idReport);
	},



	/**
	 * Add unsaved class to report tab
	 *
	 * @method	markAsUnsaved
	 * @param	{}	idReport
	 */
	markAsUnsaved: function(idReport) {
		this.getTab(idReport).addClassName('unsaved');
	},



	/**
	 * Get active tab
	 *
	 * @method	getActive
	 * @return	{Element}
	 */
	getActive: function() {
		return Todoyu.Tabs.getActiveKey('reports');
	},



	/**
	 * Get open report keys
	 *
	 * @method	getOpenReports
	 * @return	{Array}
	 */
	getOpenReports: function() {
		return Todoyu.Tabs.getTabNames('reports');
	},



	/**
	 * Move tab to the first position and set active
	 *
	 * @method	moveAsFirst
	 * @param	{String}	idReport
	 */
	moveAsFirst: function(idReport) {
		Todoyu.Tabs.moveAsFirst('reports', idReport);
		Todoyu.Tabs.setActive('reports', idReport);
	},



	/**
	 * Rename a tab
	 *
	 * @method	rename
	 * @param	{String}	idReport
	 * @param	{String}	label
	 */
	rename: function(idReport, label) {
		Todoyu.Tabs.setLabel('reports', idReport, label);
	},



	/**
	 * Remove a tab
	 *
	 * @method	remove
	 * @param	{String}	idReport
	 */
	remove: function(idReport) {
		var tab = this.getTab(idReport);

		if( tab ) {
			tab.remove();
		}

		if( this.getNumTabs() === 0 ) {
			this.addNoReportTab();
		}
	},



	/**
	 * Get number of open tabs
	 *
	 * @method	getNumTabs
	 * @return	{Number}
	 */
	getNumTabs: function() {
		return Todoyu.Tabs.getNumTabs('reports');
	},



	/**
	 * Check whether the "noReports" tab is here
	 *
	 * @method	areNoReportsOpen
	 * @return	{Boolean}
	 */
	areNoReportsOpen: function() {
		return this.getOpenReports().first() === 'noid';
	},



	/**
	 * Remove the "noReports" tab
	 *
	 * @method	removeNoReportTab
	 */
	removeNoReportTab: function() {
		Todoyu.Tabs.removeTab('reports', 'noid');
	},



	/**
	 * Add the "noReports" tab
	 *
	 * @method	addNoReportTab
	 */
	addNoReportTab: function() {
		Todoyu.Tabs.addTab('reports', 'noid', '', 'No Reports opened', true, true);
	}

};