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
 * @module	Dev
 */

Todoyu.Ext.dev.Show = {

	/**
	 * Popup window configuration
	 *
	 * @property	winConfig
	 * @type		Object
	 */
	winConfig: {
		id:		'session',
		width:	800,
		url:	Todoyu.getUrl('dev', 'ext'),
		options: {
			parameters: {
				action:	'session'
			}
		}
	},

	/**
	 * Window
	 */
	win: null,



	/**
	 * Show session info window
	 *
	 * @method	showPopup
	 * @param	{String}	showType		'session', 'extensions', ...
	 */
	showPopup: function(showType) {
		if( showType !== '0') {
			this.winConfig.id							= showType;
			this.winConfig.options.parameters.action	= showType;

			this.win = new Todoyu.OverflowWindow(this.winConfig);
		}
	}

};