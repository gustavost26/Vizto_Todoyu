<?php
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
 * Dev info headlet
 *
 * @package		Todoyu
 * @subpackage	Dev
 */
class TodoyuDevHeadletDevinfo extends TodoyuHeadletTypeOverlay {

	/**
	 * Initialize headlet
	 */
	protected function init() {
		$this->setJsHeadlet('Todoyu.Ext.dev.Headlet.DevInfo');
	}



	/**
	 * Get array of user status infos and option links for dev headlet content
	 *
	 * @return	Array
	 */
	protected function getInfos() {
		$items	= array();

			// Real name of current user
		$items[] = array(
			'label'	=> 'dev.ext.headlet.info.name',
			'value'	=> Todoyu::person()->getFullName()
		);

				// Username of current user
		$items[] = array(
			'label'	=> 'dev.ext.headlet.info.user',
			'value'	=> Todoyu::person()->getUsername()
		);

				// Render time
		$items[] = array(
			'label'	=> 'dev.ext.headlet.info.rendertime',
			'value'	=> date('Y-m-d H:i:s')
		);

			// Is current user admin?
		$items[] = array(
			'label'	=> 'dev.ext.headlet.info.is_admin',
			'value'	=> Todoyu::person()->isAdmin() ? Todoyu::Label('core.global.yes') : Todoyu::Label('core.global.no')
		);

			// Roles of current user
		$items[] = array(
			'label'	=> 'core.global.roles',
			'value'	=> implode(', ', TodoyuContactPersonManager::getPersonRoleLabels())
		);

			// "Show" dropdown
		if( $this->allowed('basic:showInfos') ) {
			$items[] = array(
				'label'	=> 'dev.ext.headlet.info.show',
				'value'	=> $this->getShowForm()
			);
		}

			// Enable and run installer button
		if( $this->allowed('basic:activateInstaller') ) {
			$items[] = array(
				'label'	=> 'dev.ext.headlet.info.installer',
				'value'	=> Dwoo_Plugin_Button(Todoyu::tmpl(), 'dev.ext.headlet.info.installer.activate', 'Todoyu.Ext.dev.activateInstaller()', 'activateinstaller')			);
		}

			// Simulate session timeout and provoke re-login popup
		if( $this->allowed('basic:sessionTimeout') ) {
			$items[] = array(
				'label'	=> 'Session',
				'value'	=> Dwoo_Plugin_Button(Todoyu::tmpl(), 'dev.ext.headlet.simulatesessiontimeout.button', 'Todoyu.Ext.dev.simulateSessionTimeout()', 'simulatesessiontimeout')
			);
		}

			// Input for email-rerouting address
		if( $this->allowed('basic:rerouteEmail') ) {
			$items[] = array(
				'label'	=> 'dev.ext.rerouteEmail',
				'value'	=> $this->renderRerouteEmailInput()
			);
		}


			// Login as another user option
		if( $this->allowed('user:loginAs') || TodoyuDevManager::hasSwitchBackPerson() ) {
			$items[] = array(
				'label'	=> 'dev.ext.headlet.info.login',
				'value'	=> $this->renderLoginAsUserSelect()
			);
		}

			// Delete all prefs (of selectable user)
		if( $this->allowed('user:deletePreferences') ) {
			$items[] = array(
				'label'	=> 'dev.ext.headlet.info.prefs',
				'value'	=> $this->renderDeletePrefsOfUserSelect()
			);
		}

			// Delete all cache button
		if( $this->allowed('basic:clearCache') ) {
			$items[] = array(
				'label'	=> 'dev.ext.headlet.info.cache',
				'value'	=> Dwoo_Plugin_Button(Todoyu::tmpl(), 'dev.ext.headlet.info.cache.clear', 'Todoyu.Ext.dev.clearCache()', 'clearcache')
			);
		}

			// Add Option to switch-back to real user
		if( TodoyuDevManager::hasSwitchBackPerson() ) {
			$items[] = array(
				'id'	=> 'dev-info-switchback',
				'label'	=> 'dev.ext.headlet.info.switchBack',
				'value'	=> Dwoo_Plugin_Button(Todoyu::tmpl(), 'dev.ext.headlet.info.switchBack.button', 'Todoyu.Ext.dev.switchBack()', 'switchback')
			);
		}

		$items	= TodoyuHookManager::callHookDataModifier('dev', 'headlet.infos', $items);

		return $items;
	}



	/**
	 * Check right shortcut
	 *
	 * @param	String		$right
	 * @return	Boolean
	 */
	protected function allowed($right) {
		return Todoyu::allowed('dev', $right);
	}



	/**
	 * @return	String
	 */
	protected  function getShowForm() {
		$tmpl	= 'ext/dev/view/show-form.tmpl';
		$data	= array(
			'options'	=> array(
				array(
					'value'	=> 'extensions',
					'label'	=> Todoyu::Label('dev.ext.show.installedExtensions')
				),
				array(
					'value'	=> 'session',
					'label'	=> Todoyu::Label('dev.ext.show.sessionData')
				),
				array(
					'value'	=> 'errorlog',
					'label'	=> Todoyu::Label('dev.ext.show.errorLog')
				),
				array(
					'value'	=> 'cronjobs',
					'label'	=> Todoyu::Label('dev.ext.show.cronjobs')
				),
				array(
					'value'	=> 'hooks',
					'label'	=> Todoyu::Label('dev.ext.show.hooks')
				)
			)
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render input field for rerouting email address
	 *
	 * @return	String
	 */
	protected function renderRerouteEmailInput() {
		$tmpl	= 'ext/dev/view/reroute.tmpl';
		$data	= array(
			'rerouteEmail'	=> TodoyuDevManager::getReroutingEmailAddress()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render form for "log-in as other user" option of dev headlet
	 *
	 * @return	String
	 */
	protected function renderLoginAsUserSelect() {
		$data	= array(
			'id'		=> 'dev-simulate-person',
			'size'		=> 1,
			'options'	=> $this->getLoginPersonOptions()
		);

		return TodoyuRenderer::renderSelectGrouped($data);
	}



	/**
	 * Render options for preference deletion
	 *
	 * @return	String
	 */
	protected function renderDeletePrefsOfUserSelect() {
		$data	= array(
			'id'		=> 'dev-deletepref-person',
			'size'		=> 1,
			'options'	=> $this->getLoginPersonOptions(true)
		);

		return TodoyuRenderer::renderSelectGrouped($data);
	}



	/**
	 * Get options for person selector of "log-in as other user" option of dev headlet
	 *
	 * @param	Boolean		$includeOwnPerson
	 * @return	Array
	 */
	protected function getLoginPersonOptions($includeOwnPerson = false) {
		$options	= array();

			// Internal persons
		$groupLabel	= Todoyu::Label('dev.ext.persons.internal');
		$options[$groupLabel]	= $this->getInternalPersonOptions(false, $includeOwnPerson, true);

			// External persons
		$groupLabel	= Todoyu::Label('dev.ext.persons.external');
		$options[$groupLabel]	= self::getExternalPersonOptions(true);

		return $options;
	}



	/**
	 * Get options for all internal persons (users) except the current user himself
	 *
	 * @param	Boolean		$addPleaseSelect
	 * @param	Boolean		$includeOwnPerson
	 * @param	Boolean		$requireRoles
	 * @return	Array
	 */
	protected function getInternalPersonOptions($addPleaseSelect = false, $includeOwnPerson = false, $requireRoles = false) {
		$options	= array();

		if( $addPleaseSelect ) {
			$options[]	= array(
				'value'		=> 0,
				'label'		=> Todoyu::Label('core.form.select.pleaseSelect')
			);
		}

		$internalPersonIDs	= TodoyuContactPersonManager::getInternalPersonIDs();
		$ownPersonID		= Todoyu::personid();

		foreach( $internalPersonIDs as $idPerson) {
			if( $includeOwnPerson || $idPerson != $ownPersonID ) {
				$person	= TodoyuContactPersonManager::getPerson($idPerson);

				$roles		= TodoyuContactPersonManager::getRoles($idPerson);
				if( $requireRoles && sizeof($roles) === 0 ) {
					continue;
				}

				$roles		= TodoyuArray::reform($roles, array('title' => 'label'));
				$roleLabels	= !empty($roles) ? implode(TodoyuArray::flatten($roles), ', ') : Todoyu::Label('dev.ext.noRole');

				$options[] = array(
					'value'	=> $idPerson,
					'label'	=> $person->getFullName(true) . ' - ' . $roleLabels
				);
			}
		}

		return $options;
	}



	/**
	 * Get options for external persons (users)
	 *
	 * @param	Boolean		$requireRole
	 * @return	Array
	 */
	public static function getExternalPersonOptions($requireRole = false) {
		$options	= array();

		$loginPersonIDs		= TodoyuArray::getColumn(TodoyuContactPersonManager::getAllActivePersons(array('id'), false), 'id');
		$internalPersonIDs	= TodoyuContactPersonManager::getInternalPersonIDs();

		foreach($loginPersonIDs as $idPerson) {
			if( ! in_array($idPerson, $internalPersonIDs) ) {
				$person	= TodoyuContactPersonManager::getPerson($idPerson);

				if( $requireRole ) {
					if( sizeof($person->getRoleIDs()) === 0 ) {
						continue;
					}
				}

				$companies		= $person->getCompanies();
				if( sizeof($companies) > 0 ) {
					$companyLabel	= self::renderCompaniesLabel($companies);
					$optionLabel	= $person->getFullName(true) . ' (' . $companyLabel . ')';
					$options[] = array(
						'value'	=> $idPerson,
						'label'	=> TodoyuString::crop($optionLabel, 65, '...', false)
					);
				} //else {
//					$companyLabel	= Todoyu::Label('dev.ext.noCompany');
//				}
			}
		}

		return $options;
	}



	/**
	 * Render label out of title(s) of one or more companies, comma-separated if multiple
	 *
	 * @param	TodoyuContactCompany[]	$companies
	 * @return	String
	 */
	private static function renderCompaniesLabel(array $companies) {
		$companiesTitles	= array();
		foreach($companies as $company) {
			/** @var TodoyuContactCompany	$company	 */
			$companiesTitles[] = $company->getTitle();
		}

		return implode(', ', $companiesTitles);
	}



	/**
	 * Render content (status infos and dev options) of dev headlet
	 *
	 * @return	String
	 */
	protected function renderOverlayContent() {
		if( $this->isOpen() ) {
			return $this->renderContent();
		} else {
			$labelLoading	= Todoyu::Label('dev.ext.loading');

			return TodoyuString::wrapWithTag('li', $labelLoading);
		}
	}



	/**
	 * @return	String
	 */
	public function renderContent() {
		$tmpl	= 'ext/dev/view/headlet-devinfo.tmpl';
		$data	= array(
			'infos'	=> $this->getInfos()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get dev headlet label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return 'Dev Info';
	}

}

?>