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

Todoyu.Ext.reporting.Formatter = {

	/**
	 * Format value as hour
	 *
	 * @method	yAxisHours
	 * @param	{Object}	that
	 * @return	{String}
	 */
	yAxisHours: function(that) {
		return this.value + 'h';
	},



	/**
	 * Format value as percent
	 *
	 * @method	yAxisPercent
	 * @param	{Object}	that
	 * @return	{String}
	 */
	yAxisPercent: function(that) {
		return this.value + '%';
	},



	/**
	 * Format value for a bar tooltip
	 *
	 * @method	tooltipBar
	 * @param	{Object}	that
	 * @return	{String}
	 */
	tooltipBar: function(that) {
		return this.series.name + ': ' + this.y.round(1);
	},

	/**
	 * Build tooltip for bar chart
	 *
	 * @method	bar
	 * @param	{Object}	that		Ref to this namespace
	 */
	tooltipBarTask: function(that) {
		return that.tooltipBar.call(this, that) + ' [LLL:core.date.time.hours] [<a href="javascript:Todoyu.Ext.project.goToTaskInProjectByTasknumber(' + this.x + ')">[LLL:project.task.contextmenu.showinproject]</a>]';
	},



	/**
	 * Format a pie tooltip
	 *
	 * @method	pie
	 * @param	{Object}	that
	 */
	tooltipPie: function(that) {
		return that._formatPie(this.point.name, this.y, this.total);
	},



	/**
	 * Build tooltip for pie charts which display hours
	 *
	 * @method	pieHours
	 * @param	{Object}	that		Ref to this namespace
	 */
	tooltipPieHours: function(that) {
		return that._formatPie(this.point.name, this.y.round(2) + ' [LLL:core.date.time.hours]', this.total.round(2) + ' [LLL:core.date.time.hours]');
	},



	/**
	 * Format tooltip for a pie chart
	 *
	 * @method	_formatPie
	 * @param	{String}	name
	 * @param	{Number}	y
	 * @param	{Number}	total
	 */
	_formatPie: function(name, y, total) {
		return '<strong>' + name + '</strong><br/>' + y + ' [LLL:core.global.of] ' + total;
	},



	/**
	 * Build tooltip for area chart with hours
	 *
	 * @method	areaHours
	 * @param	{Object}	that		Ref to this namespace
	 */
	tooltipAreaHours: function(that) {
		return that._formatArea(this.x, this.series.name, this.y.round(1) + ' [LLL:core.date.time.hours]');
	},



	/**
	 * Format tooltip for line with hours
	 *
	 * @method	tooltipLineHours
	 * @param	{Object}	that
	 */
	tooltipLineHours: function(that) {
		return that.tooltipAreaHours.call(this, that);
	},



	/**
	 * Format tooltip for line with percent
	 *
	 * @method	tooltipLinePercent
	 * @param	{Object}	that
	 */
	tooltipLinePercent: function(that) {
		return that._formatArea(this.x, this.series.name, this.y.round(1) + '%');
	},



	/**
	 * Format area tooltip
	 *
	 * @method	_formatArea
	 * @param	{String}	date
	 * @param	{String}	category
	 * @param	{Number}	value
	 */
	_formatArea: function(date, category, value) {
		return '<strong>' + date + '</strong><br/><span style="font-size:10px">' + category + '</span><br/><span>' + value + '</span>';
	}

};