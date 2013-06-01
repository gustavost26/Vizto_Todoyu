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
 * Default dev action controller
 *
 * @package		Todoyu
 * @subpackage	Dev
 */
class TodoyuDevExtActionController extends TodoyuActionController {

	/**
	 * Protect
	 *
	 * @param	Array		$params
	 */
	public function init(array $params) {
		if( !TodoyuDevManager::hasSwitchBackPerson() ) {
			Todoyu::restrict('dev', 'general:use');
		}
	}



	/**
	 * Load headlet content
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function headletContentAction(array $params) {
		$headlet	= new TodoyuDevHeadletDevinfo();

		return $headlet->renderContent();
	}



	/**
	 * Simulate another user, do real login
	 *
	 * @param	Array		$params
	 */
	public function simulatePersonAction(array $params) {
		if( !TodoyuDevManager::hasSwitchBackPerson() ) {
			Todoyu::restrict('dev', 'user:loginAs');
		}

			// Close dev headlet (close all headlets)
		TodoyuHeadletManager::saveOpenStatus('');

		$idPerson	= intval($params['person']);
		$person		= TodoyuDevManager::loginAsPerson($idPerson);
		$name		= $person->getFullName();

			// Send "noadmin" flag if current area is admin, but user is not an admin
		if( TodoyuDevManager::isSimulateUserRedirectRequired(AREAEXT) ) {
			TodoyuHeader::sendTodoyuHeader('redirect', 1);
		}

		TodoyuHeader::sendTodoyuHeader('name', $name);
	}



	/**
	 * Switch back to the real user
	 *
	 * @param	Array		$params
	 */
	public function switchBackAction(array $params) {
		if( TodoyuDevManager::hasSwitchBackPerson() ) {
			TodoyuDevManager::loginAsSwitchBackPerson();
		}
	}



	/**
	 * Debug all session data
	 *
	 * @param	Array			$params
	 * @return	mixed|string
	 */
	public function sessionAction(array $params) {
		Todoyu::restrict('dev', 'basic:showInfos');

		$content = "Todoyu Session\n";
		$content.= "=======================================\n\n";
		$content.= print_r(TodoyuSession::getAll(), true);

		return $content;
	}



	/**
	 * View hooks
	 *
	 * @param	Array			$params
	 * @return	mixed|string
	 */
	public function hooksAction(array $params) {
		Todoyu::restrict('dev', 'basic:showInfos');

		$extInfos	= TodoyuExtensions::getAllExtInfo();

		foreach($extInfos as $extKey => $extInfo) {
			$extInfos[$extKey]['hooks']	= TodoyuHookManager::getAllHooksOfExtension($extKey);
		}

		$tmpl	= 'ext/dev/view/hooks.tmpl';
		$data	= array(
			'extensions'	=> $extInfos
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Simulate session timeout: logout
	 *
	 * @param	Array	$params
	 */
	public function sessionTimeoutAction(array $params) {
		Todoyu::restrict('dev', 'basic:sessionTimeout');

		TodoyuSession::clear();
		session_regenerate_id(true);
	}



	/**
	 * Trace all registered cronjobs
	 *
	 * @param	Array			$params
	 * @return	String
	 * @todo	Cleanup and improve!
	 */
	public function cronjobsAction(array $params) {
		Todoyu::restrict('dev', 'basic:showInfos');

		$content = '<strong>Registered Cronjobs</strong><br />';
		$content.= '=======================================<br /><br />';

		$jobs		= TodoyuScheduler::getDueJobs();
		foreach($jobs as $job) {
			$content	.= '<br/>Class: <strong>' . $job['class'] . '</strong><br/>';
			$content	.= 'Crontime: ' . $job['crontime'] . '<br/>';
			$content	.= 'Options: <pre>' . print_r($job['options'], true) . '<pre>';
			$content	.= '<br/>';
		}

		return $content;
	}



	/**
	 * Debug all (installed and) registered extension IDs
	 *
	 * @param	Array			$params
	 * @return	mixed|string
	 */
	public function errorlogAction(array $params) {
		Todoyu::restrict('dev', 'basic:showInfos');

		$tmpl	= 'ext/dev/view/errorlog.tmpl';
		$data	= array(
			'errorlog'	=> TodoyuDevManager::getLastErrorLogEntries()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Debug all (installed and) registered extension IDs
	 *
	 * @param	Array			$params
	 * @return	mixed|string
	 */
	public function extensionsAction(array $params) {
		Todoyu::restrict('dev', 'basic:showInfos');

		$tmpl	= 'ext/dev/view/extensions.tmpl';
		$data	= array(
			'extensions'	=>	TodoyuDevManager::getExtensionsInfo()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Delete all user preferences
	 *
	 * @param	Array	$params
	 */
	public function deletePreferencesAction(array $params) {
		Todoyu::restrict('dev', 'user:deletePreferences');

		$idPerson	= intval($params['person']);

		TodoyuPreferenceManager::deleteUserPreferences($idPerson);
	}



	/**
	 * Activate installer
	 *
	 * @param	Array		$params
	 */
	public function activateInstallerAction(array $params) {
		Todoyu::restrict('dev', 'basic:activateInstaller');

		$success = TodoyuDevManager::activateInstaller();

		if( !$success ) {
			TodoyuHeader::sendTodoyuError('Enable Installer Failed.');
		}
	}



	/**
	 * Delete all cache files
	 *
	 * @param	Array		$params
	 */
	public function clearCacheAction(array $params) {
		Todoyu::restrict('dev', 'basic:clearCache');

		TodoyuCacheManager::clearAllCache();
	}



	/**
	 * @param	Array	$params
	 */
	public function storeRerouteEmailAction(array $params) {
		Todoyu::restrict('dev', 'basic:rerouteEmail');

		$email	= trim($params['email']);

		$preference	= 'rerouteEmail';

		if( TodoyuValidator::isEmail($email) ) {
				// Email is valid: store pref
			TodoyuPreferenceManager::savePreference(EXTID_DEV, $preference, $email);
		} else {
				// Invalid email detected: unset it
			TodoyuPreferenceManager::deletePreference(EXTID_DEV, $preference);
			TodoyuHeader::sendTodoyuErrorHeader();
		}
	}

}

?>