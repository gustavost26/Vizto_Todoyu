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

/**
 * Dev main object
 *
 * @class		dev
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.dev = {

	/**
	 * @property	PanelWidget
	 * @type		{Object}
	 */
	PanelWidget: {},

	/**
	 * @property	Headlet
	 * @type		{Object}
	 */
	Headlet: {},



	/**
	 * Init dev extension
	 *
	 * @method	init
	 */
	init: function() {
		this.addHooks();
	},



	/**
	 * Add various JS hooks
	 *
	 * @method	addHooks
	 */
	addHooks: function() {
			// Add re-login request hook
		Todoyu.Hook.add('loginpage.relogin.onreloginresponse', this.onReloginResponse.bind(this));
	},



	/**
	 * Handle re-login response: remove switch-user info on success
	 *
	 * @method	onReloginResponse
	 * @param	{Ajax.Response}		response
	 */
	onReloginResponse: function(response) {
		var status	= response.responseJSON;
		var switchbackInfoElement	= $('dev-switchback-info');

		if( status.success && switchbackInfoElement ) {
			switchbackInfoElement.remove();
			$('dev-info-switchback').remove();
			Todoyu.notifyInfo('[LLL:dev.ext.info.switchedBack]');
		}
	},



	/**
	 * Send activate installer request
	 *
	 * @method	activateInstaller
	 */
	activateInstaller: function() {
		var url		= Todoyu.getUrl('dev', 'ext');
		var options	= {
			parameters: {
				action: 'activateInstaller'
			},
			onComplete: this.onInstallerActivated.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Open installer or show notification if enabling the installer failed
	 *
	 * @method	onInstallerActivated
	 * @param	{Ajax.Response}		response
	 */
	onInstallerActivated: function(response) {
		if( response.hasTodoyuError() ) {
			Todoyu.notifyError('...[LLL:dev.ext.notify.enableinstallerfailed]', 'dev.enableInstaller');
		} else {
			document.location.href = 'install/index.php';
		}
	},



	/**
	 * Send clear cache request
	 *
	 * @method	clearCache
	 */
	clearCache: function() {
		var url		= Todoyu.getUrl('dev', 'ext');
		var options	= {
			parameters: {
				action: 'clearCache'
			},
			onComplete: this.onCacheCleared.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Show notification when cache is cleared
	 *
	 * @method	onCacheCleared
	 * @param	{Ajax.Response}		response
	 */
	onCacheCleared: function(response) {
		Todoyu.notifySuccess('[LLL:dev.ext.notify.cachecleared]');
	},



	/**
	 * Simulate session timeout and provoke re-login popup
	 *
	 * @method	simulateSessionTimeout
	 */
	simulateSessionTimeout: function() {
		var url		= Todoyu.getUrl('dev', 'ext');
		var options	= {
			parameters: {
				action: 'sessionTimeout'
			}
		};

		Todoyu.send(url, options);
	},



	/**
	 * Switch Back to the real user
	 *
	 * @method	switchBack
	 */
	switchBack: function() {
		var url		= Todoyu.getUrl('dev', 'ext');
		var options	= {
			parameters: {
				action: 'switchBack'
			},
			onComplete: this.onSwitchedBack.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when switched back
	 *
	 * @method	onSwitchedBack
	 */
	onSwitchedBack: function() {
		location.reload();
	}

};