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

Todoyu.Ext.dev.Headlet.DevInfo = Class.create(Todoyu.Headlet, {

	/**
	 * @property	contentLoaded
	 * @type		{Boolean}
	 */
	contentLoaded: false,

	/**
	 * Initialize headlet
	 *
	 * @method	initialize
	 * @param	{Function}	$super		Parent constructor: Todoyu.Headlet.initialize
	 * @param	{String}	name
	 */
	initialize: function($super, name) {
		$super(name);

		if( this.isContentLoaded() ) {
			this.contentLoaded = true;
			this.addObservers();
		}
	},



	/**
	 * @method	isContentLoaded
	 * @return	{Boolean}
	 */
	isContentLoaded: function() {
		return this.getContent().select('li').size() > 1;
	},



	/**
	 * Observe login form for changes
	 *
	 * @method	observeLoginAsForm
	 */
	addObservers: function() {
		$('dev-simulate-person').on('change', 'select', this.onLoginPersonSelect.bind(this));

		var selectDeletePrefPerson	= $('dev-deletepref-person');
		if( selectDeletePrefPerson ) {
			selectDeletePrefPerson.on('change', 'select', this.onDeletePrefPersonSelected.bind(this));
		}

		var inputRerouteEmail	= $('dev-reroute-email');
		if( inputRerouteEmail ) {
			inputRerouteEmail.on('blur', 'input', this.onRerouteEmailChanged.bind(this));
		}
	},



	/**
	 * Handler when a person is selected
	 *
	 * @method	onPersonSelect
	 * @param	{Event}			event
	 * @param	{Element}		field
	 */
	onLoginPersonSelect: function(event, field) {
		var idPerson= $F(field);
		var name	= field.options[field.selectedIndex].text;

		field.selectedIndex = 0;

		if( idPerson != 0 ) {
			if( confirm('Login as "' + name + '"?') ) {
				this.loginAsPerson(idPerson);
			}
		}
	},



	/**
	 * Login as another person
	 *
	 * @method	loginAsPerson
	 * @param	{Number}	idPerson
	 */
	loginAsPerson: function(idPerson) {
		var url		= Todoyu.getUrl('dev', 'ext');
		var options	= {
			parameters: {
				action:	'simulatePerson',
				person:	idPerson
			},
			onComplete:	this.onLoggedInAsPerson.bind(this, idPerson)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when logged in as another person
	 *
	 * @method	onLoggedInAsPerson
	 * @param	{Number}		idPerson
	 * @param	{Ajax.Response}	response
	 */
	onLoggedInAsPerson: function(idPerson, response) {
		var name	= response.getTodoyuHeader('name');
		var redirect= response.getTodoyuHeader('redirect');

		Todoyu.notifyInfo('[LLL:dev.ext.notify.simulateReload]'.replace('%s', name));

			// Check whether user should be redirected to portal (no access to current view)
		if( redirect ) {
			Todoyu.notifyInfo("[LLL:dev.ext.notify.simulateNoAccessRedirect]");
			Todoyu.goTo('portal');
		} else {
			location.reload();
		}
	},



	/**
	 * Handle selection of user to delete preferences
	 *
	 * @method	onDeletePrefPersonSelected
	 * @param	{Event}		event
	 * @param	{Element}	field
	 */
	onDeletePrefPersonSelected: function(event, field) {
		var idPerson= $F(field);
		var name	= field.options[field.selectedIndex].text;

		field.selectedIndex = 0;

		if( idPerson != 0 ) {
			if( confirm('Delete all preferences of "' + name + '"?') ) {
				this.deletePersonPreferences(idPerson, name);
			}
		}
	},



	/**
	 * Store user pref of rerouting email address
	 *
	 * @method	onRerouteEmailChanged
	 * @param	{Event}		event
	 * @param	{Element}	field
	 */
	onRerouteEmailChanged: function(event, field) {
		var url		= Todoyu.getUrl('dev', 'ext');

		var email	= $F(field);
		var options	= {
			parameters: {
				action:	'storeRerouteEmail',
				email:	email
			},
			onComplete:	this.onRerouteEmailPrefStored.bind(this, email)
		};

		Todoyu.send(url, options);
	},



	/**
	 * @method	onRerouteEmailPrefStored
	 * @param	{String}			email
	 * @param	{Ajax.Response}		response
	 */
	onRerouteEmailPrefStored: function(email, response) {
		if( email === '' ) {
				// Empty email = rerouting deativated
			Todoyu.Notification.notifyInfo('[LLL:dev.ext.rerouteEmail.info.cleared]', 'dev.rerouteemail');
		} else {
			if( response.hasTodoyuError() ) {
					// Invalid email = rerouting deactivated
				Todoyu.Notification.notifyError('[LLL:dev.ext.rerouteEmail.error]', 'dev.rerouteemail');
				$('dev-reroute-email').value	= '';
			} else {
					// Valid email = rerouting set
				Todoyu.Notification.notifySuccess('[LLL:dev.ext.rerouteEmail.success]', 'dev.rerouteemail');
			}
		}
	},



	/**
	 * Delete user preferences
	 *
	 * @method	deletePersonPreferences
	 * @param	{Number}	idPerson
	 * @param	{String}	name
	 */
	deletePersonPreferences: function(idPerson, name) {
		var url		= Todoyu.getUrl('dev', 'ext');
		var options	= {
			parameters: {
				action:	'deletePreferences',
				person:	idPerson
			},
			onComplete:	this.onPersonPreferencesDeleted.bind(this, idPerson, name)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Notify about deletion of preferences
	 *
	 * @method	onPersonPreferencesDeleted
	 * @param	{Number}		idPerson
	 * @param	{String}		name
	 * @param	{Ajax.Response}	response
	 */
	onPersonPreferencesDeleted: function(idPerson, name, response) {
		Todoyu.notifyInfo("[LLL:dev.ext.notify.personPreferencesDeleted]: " + name);
	},



	/**
	 * Called when content is showed
	 *
	 * @method	onContentShow
	 * @param	{Function}	$super
	 */
	onContentShow: function($super) {
		if( !this.contentLoaded ) {
			this.loadContent();
			this.contentLoaded = true;
		}
	},



	/**
	 * Load content for dev headlet
	 */
	loadContent: function() {
		var url		= Todoyu.getUrl('dev', 'ext');
		var options	= {
			parameters: {
				action:	'headletContent'
			},
			onComplete: this.onContentLoaded.bind(this)
		};
		var target	= 'todoyudevheadletdevinfo-content';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Handle content loaded
	 *
	 * @method	onContentLoaded
	 * @param	{Ajax.Response}		response
	 */
	onContentLoaded: function(response) {
		this.addObservers();
	}

});