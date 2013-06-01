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
 * Reporting filters
 *
 * @module	Reporting
 */
Todoyu.Ext.reporting.Filter = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.reporting,

	/**
	 * Autocompleter instances
	 *
	 * @property	AC
	 * @type		Object
	 */
	AC: {},

	/**
	 * Timerange instances
	 *
	 * @property	TR
	 * @type		Object
	 */
	TR: {},

	/**
	 * Filter configuration
	 *
	 * @property	filterConfig
	 * @type		Object
	 */
	filterConfig: {},

	/**
	 * ID of last element which had the focus
	 *
	 * @property	lastFocus
	 * @type		String
	 */
	lastFocus: null,



	/**
	 * Initialize filters
	 *
	 * @method	init
	 * @param	{String}	[idReport]
	 */
	init: function(idReport) {
		idReport	= idReport || this.ext.getActive();

		if( this.ext.getForm(idReport) ) {
			this.initSelectMulti(idReport);
			this.initAutocompleters(idReport);
			this.disableInactiveFilters(idReport);
			this.installLastFocusObserver(idReport);
		}

			// If last focus is set, restore focus
		if( this.lastFocus !== null ) {
			$(this.lastFocus).focus();
		}
	},



	/**
	 * Observe for focus and blur on all text fields in the filter form
	 *
	 * @method	installLastFocusObserver
	 * @param	{Number}	idReport
	 */
	installLastFocusObserver: function(idReport) {
		var fields	= this.ext.getForm(idReport).select(':text');

		fields.invoke('on', 'focus', this.onTextFieldFocus.bind(this));
		fields.invoke('on', 'blur', this.onTextFieldBlur.bind(this));
	},



	/**
	 * Save last focus element
	 *
	 * @method	onTextFieldFocus
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onTextFieldFocus: function(event, element) {
		this.lastFocus = element.id;
	},



	/**
	 * Remove last focus element
	 *
	 * @method	onTextFieldBlur
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onTextFieldBlur: function(event, element) {
		this.lastFocus = null;
	},



	/**
	 * Add config for a report
	 *
	 * @method	addConfig
	 * @param	{String}	idReport
	 * @param	{String}	filter
	 * @param	{Object}	config
	 */
	addConfig: function(idReport, filter, config) {
		this.filterConfig[idReport + '-' + filter] = config;
	},



	/**
	 * Get custom filter config
	 *
	 * @method	getConfig
	 * @param	{String|Number}	idReport
	 * @param	{String}		filter
	 * @return	{Object}
	 */
	getConfig: function(idReport, filter) {
		return this.filterConfig[idReport + '-' + filter] || {};
	},



	/**
	 * Initialize multi select fields
	 *
	 * @method	initSelectMulti
	 * @param	{Number}	idReport
	 */
	initSelectMulti: function(idReport) {
		this.ext.getForm(idReport).select('.typeSelectMulti select').each(function(select){
			new Todoyu.SelectMulti(select, this.onSelectMultiAdd.bind(this), this.onSelectMultiRemove.bind(this));
		}, this);
	},



	/**
	 * Handler when a multi select element was added
	 *
	 * @method	onSelectMultiAdd
	 * @param	{Object}		items
	 */
	onSelectMultiAdd: function(items) {
		this.ext.refreshReport();
	},



	/**
	 * Handler when a multi select element was removed
	 *
	 * @method	onSelectMultiRemove
	 */
	onSelectMultiRemove: function() {
		this.ext.refreshReport();
	},



	/**
	 * Install autocompleters for report
	 *
	 * @method	initAutocompleters
	 * @param	{String|Number}	idReport
	 */
	initAutocompleters: function(idReport) {
		this.ext.getForm(idReport).select('input.textAC').each(function(idReport, element) {
			var filterName = element.id.split('-')[3];
			this.addAutoCompleter(idReport, filterName, element, this.getConfig(idReport, filterName));
		}.bind(this, idReport));
	},



	/**
	 * Add autocompleter event handlers
	 *
	 * @method	addAutoCompleter
	 * @param	{String}	idReport
	 * @param	{String}	name
	 * @param	{Element}	field
	 * @param	{Object}	config
	 */
	addAutoCompleter: function(idReport, name, field, config) {
		field			= $(field);
		var reportType	= this.ext.getReportType(idReport);

		var url		= Todoyu.getUrl('reporting', 'report', {
			action:		'autocomplete',
			report:		idReport,
			reporttype:	reportType,
			filtername:	field.id.split('-').last()
		});

		var options	= {
			paramName:			'search',
			minChars:			2
		};
		var suggestID= field.id + '-suggestions';

			// Override config with specialConfig if available
		if( config.acOptions ) {
			options = $H(options).merge(config.acOptions).toObject();
		}

		if( field.up('.typeAutocompleteMulti') ) {
			Todoyu.Autocomplete.AC[name] = new Todoyu.AutocompleterMulti(field, suggestID, url, options, this.onAcMultiAdd.bind(this), this.onAcMultiRemove.bind(this));
		} else {
			options.afterUpdateElement = Todoyu.getFunction(options.afterUpdateElement).wrap(this.onAutocompleteSelect.bind(this, idReport));
			Todoyu.Autocomplete.AC[name] = new Todoyu.Autocompleter(field, suggestID, url, options);
		}
	},



	/**
	 * Handler when an element was added from a multi AC
	 *
	 * @method	onAcMultiAdd
	 * @param	{Todoyu.AutocompleterMulti}		acMulti
	 * @param	{String}						idItem
	 * @param	{String}						label
	 */
	onAcMultiAdd: function(acMulti, idItem, label) {
		this.ext.refreshReport();
	},



	/**
	 * Handler when an element was removed from a multi AC
	 *
	 * @method	onAcMultiRemove
	 * @param	{Todoyu.AutocompleterMulti}		acMulti
	 * @param	{String}						idItem
	 */
	onAcMultiRemove: function(acMulti, idItem) {
		this.ext.refreshReport();
	},



	/**
	 * Handler when item from AC was selected
	 *
	 * @method	onAutocompleteSelect
	 * @param	{String}	idReport
	 * @param	{Function}	callOriginal
	 * @param	{Element}	inputField
	 * @param	{Element}	selectedListElement
	 */
	onAutocompleteSelect: function(idReport, callOriginal, inputField, selectedListElement) {
		$(inputField.id + '-value').value = selectedListElement.id;

		var fieldName = inputField.id.split('-').last();

		callOriginal(inputField, selectedListElement);

		this.onFilterChanged(idReport, fieldName);
	},



	/**
	 * Disable inactive filters
	 *
	 * @method	disableInactiveFilters
	 * @param	{String}	idReport
	 */
	disableInactiveFilters: function(idReport) {
		this.ext.getForm(idReport).select('div.filter.inactive :input[type!=hidden]').invoke('disable');
	},



	/**
	 * Handler called when a filter has updated itself
	 *
	 * @method	onFilterChanged
	 * @param	{Number}	idReport
	 */
	onFilterChanged: function(idReport) {
		this.ext.refreshReport(idReport);
	},



	/**
	 * Add a timerange
	 *
	 * @method	addTimerange
	 * @param	{String}	idReport
	 * @param	{String}	name
	 * @param	{Array}		validDates
	 * @param	{Array}		selectedDates
	 * @param	{Object}	timerangeOptions
	 */
	addTimerange: function(idReport, name, validDates, selectedDates, timerangeOptions) {
		var sliderOptions = {
			onSlide: this.onTimerangeSlide.bind(this, idReport, name),
			onChange: this.onTimerangeChange.bind(this, idReport, name)
		};

			// Add timerange reference
		this.TR[name] = new Todoyu.Timerange(name, validDates, selectedDates, sliderOptions, timerangeOptions);
	},



	/**
	 * Handler when time range slides
	 *
	 * @method	onTimerangeSlide
	 * @param	{String}	idReport
	 * @param	{String}	name
	 * @param	{Array}		values
	 */
	onTimerangeSlide: function(idReport, name, values) {

	},



	/**
	 * Handler when timerange changes
	 *
	 * @method	onTimerangeChange
	 * @param	{String}	idReport
	 * @param	{String}	name
	 * @param	{Array}		values
	 */
	onTimerangeChange: function(idReport, name, values) {
		this.onFilterChanged(idReport);
	},



	/**
	 * Get a time range
	 *
	 * @method	getTimerange
	 * @param	{String}	name
	 * @return	{Todoyu.Timerange}
	 */
	getTimerange: function(name) {
		return this.TR[name];
	}

};