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
 * Manager for dev extension
 *
 * @package		Todoyu
 * @subpackage	Dev
 */
class TodoyuDevManager {

	/*
	 * Path in session where real user ID is stored
	 */
	const switchBackPath = 'dev/switchBackPerson';

	/*
	 * Path in session where real user clipboard is stored
	 */
	const switchBackClipboardPath = 'dev/switchBackClipboard';



	/**
	 * Save (real) user data to switch-back later: user ID, clipboard
	 */
	public static function saveSwitchBackData() {
		TodoyuSession::set(self::switchBackPath, TodoyuAuth::getPersonID());

		$clipboardData	= TodoyuClipboard::get('task');
		if( ! is_array($clipboardData) ) {
			$clipboardData	= array();
		}
		TodoyuSession::set(self::switchBackClipboardPath, $clipboardData);
	}



	/**
	 * Get ID of switch-back user (real identity)
	 *
	 * @return	Integer
	 */
	public static function getSwitchBackPersonID() {
		return intval(TodoyuSession::get(self::switchBackPath));
	}



	/**
	 * Check if switch-back ID is set in session
	 *
	 * @return	Boolean
	 */
	public static function hasSwitchBackPerson() {
		return self::getSwitchBackPersonID() > 0;
	}



	/**
	 * Remove switch-back ID
	 */
	public static function removeSwitchBackPerson() {
		TodoyuSession::remove(self::switchBackPath);
	}



	/**
	 * Empty clipboard
	 */
	public static function emptyClipboard() {
		TodoyuClipboard::remove('task');
	}



	/**
	 * Get switch-back clipboard data
	 *
	 * @return	Array
	 */
	public static function getSwitchBackClipboard() {
		$data = TodoyuSession::get(self::switchBackClipboardPath);
		if( ! is_array($data) ) {
			$data	= array();
		}

		return $data;
	}



	/**
	 * Restore real user's clipboard content
	 */
	public static function switchBackClipboard() {
		$data	= self::getSwitchBackClipboard();

		if( empty($data) ) {
			self::emptyClipboard();
		} else {
			TodoyuClipboard::set('task', $data);
		}
	}



	/**
	 * Remove switch-back clipboard
	 */
	public static function removeSwitchBackClipboard() {
		TodoyuSession::remove(self::switchBackClipboardPath);
	}



	/**
	 * Login as another (given) person
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuContactPerson
	 */
	public static function loginAsPerson($idPerson) {
		if( ! self::hasSwitchBackPerson() ) {
			self::saveSwitchBackData();
		}

		self::emptyClipboard();

		$idPerson	= intval($idPerson);

		TodoyuAuth::login($idPerson);

		return Todoyu::person();
	}



	/**
	 * Login as switch-back person (return to real user identity)
	 */
	public static function loginAsSwitchBackPerson() {
		$idPerson	= self::getSwitchBackPersonID();

		self::removeSwitchBackPerson();

		self::switchBackClipboard();
		self::removeSwitchBackClipboard();

		TodoyuAuth::login($idPerson);
	}



	/**
	 * Add switch-back info banner to page
	 */
	public static function addSwitchBackPersonInfoToPage() {
		$tmpl	= 'ext/dev/view/switchback-info.tmpl';
		$data	= array(
			'currentName'	=> Todoyu::person()->getFullName(),
			'isAdmin'		=> TodoyuAuth::isAdmin(),
			'currentRoles'	=> implode(',', TodoyuContactPersonManager::getPersonRoleLabels()),
			'realName'		=> TodoyuContactPersonManager::getPerson(self::getSwitchBackPersonID())->getFullName()
		);

		$html	= Todoyu::render($tmpl, $data);

		TodoyuPage::addBodyElement($html);
	}



	/**
	 * Check if installation is marked as dev
	 *
	 * @return	Boolean
	 */
	public static function isDevInstallation() {
		$extConf	= TodoyuSysmanagerExtConfManager::getExtConf('dev');

		return intval($extConf['is_dev']) === 1;
	}



	/**
	 * Mark page with devInstallation class
	 */
	public static function markAsDevInstallation() {
		TodoyuPage::addBodyClass('devInstallation');
		TodoyuFrontend::addMenuEntry('dev', 'dev.ext.tab', 'javascript:void(0)', 1000);
	}



	/**
	 * Get IDs of all (installed) extensions
	 *
	 * @return	Array
	 */
	public static function getExtensionsInfo() {
		$extKeys	= Todoyu::$CONFIG['EXT']['installed'];

		$extensions	= array();
		foreach($extKeys as $extKey) {
			$extID	= TodoyuExtensions::getExtID($extKey);

			$extensions[$extID]	= array(
				'extID'		=> $extID,
				'extKey'	=> $extKey,
				'version'	=> TodoyuExtensions::getExtVersion($extKey),
			);
		}

			// Sort by ID
		ksort($extensions);

		return $extensions;
	}



	/**
	 * Create ENABLE file
	 *
	 * @return	Boolean		success
	 */
	public static function activateInstaller() {
		return TodoyuFileManager::touch('install/ENABLE');
	}



	/**
	 * Get rerouting email address (if any) of user
	 *
	 * @return	String|Boolean		Rerouting email address or false if none set
	 */
	public static function getReroutingEmailAddress() {
		return TodoyuPreferenceManager::getPreference(EXTID_DEV, 'rerouteEmail');
	}



	/**
	 * Check whether rerouting of emails is active
	 *
	 * @return	Boolean
	 */
	public static function isMailReroutingActive() {
		$address	= self::getReroutingEmailAddress();

		return !empty($address);
	}



	/**
	 * Check whether a redirect for simulating a person is required
	 * Redirect is required if area or use right exist and is not set
	 *
	 * @param	String		$areaExt
	 * @return	Boolean
	 */
	public static function isSimulateUserRedirectRequired($areaExt) {
		$isAreaRightRequired	= TodoyuSysmanagerRightsEditorManager::hasRight($areaExt, 'general:area');
		$isUseRightRequired		= TodoyuSysmanagerRightsEditorManager::hasRight($areaExt, 'general:use');

		if( $isAreaRightRequired && !Todoyu::allowed($areaExt, 'general:area') ) {
			return true;
		}
		if( $isUseRightRequired && !Todoyu::allowed($areaExt, 'general:use') ) {
			return true;
		}

		return false;
	}



	/**
	 * Get last error messages of log
	 *
	 * @param	Integer		$entries
	 * @return	String
	 */
	public static function getLastErrorLogEntries($entries = 100) {
		$log	= TodoyuFileManager::getFileContent(PATH_ERRORLOG);
		$lines	= explode("\n", $log);
		$start	= TodoyuNumeric::intInRange(sizeof($lines) - $entries);
		$last	= array_slice($lines, $start);

		return implode("\n", $last);
	}

}

?>