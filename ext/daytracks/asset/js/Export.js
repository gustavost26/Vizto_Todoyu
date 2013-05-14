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
 * @module	Daytracks
 */

/**
 * Daytracks export
 *
 * @class		Export
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.daytracks.Export = {

	/**
	 * @property	popup
	 * @type		Object
	 */
	popup: {},



	/**
	 * Open the export popup
	 *
	 * @method	openExportPopup
	 */
	openExportPopup: function() {
		var url 	= Todoyu.getUrl('daytracks', 'export');
		var options = {
			parameters: {
				action: 'renderpopup'
			}
		};

		this.popup = Todoyu.Popups.open('time-export', '[LLL:daytracks.ext.export.popup.title]', 460, url, options);
		this.popup.show();
	},



	/**
	 * Close the export popup
	 *
	 * @method	closePopup
	 */
	closePopup: function() {
		this.popup.close();
	},



	/**
	 * Send download request
	 *
	 * @method	download
	 * @param	{String}	formID
	 */
	download: function(formID) {
		if( this.isAnyFieldFilledOut() ) {
			var formValues	= $(formID).serialize(true);
			formValues.action = 'download';

			Todoyu.goTo('daytracks', 'export', formValues);
		} else {
			Todoyu.notifyError('[LLL:daytracks.ext.export.popup.error.allFieldsEmpty]', 'daytracks.export');
		}
	},



	/**
	 * Check whether any field of the export form is filled-out
	 *
	 * @method	isAnyFieldFilledOut
	 * @return	{Boolean}
	 */
	isAnyFieldFilledOut: function() {
		var fields	= ['employee', 'project', 'company', 'date-start', 'date-end'];

		return $A(fields).any(function(field){
			return $F('export-field-' + field) !== '';
		});
	}

};