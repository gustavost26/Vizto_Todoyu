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
 * Dev extension mail manager
 * Hooked into todoyu core's mailing
 *
 * @package		Todoyu
 * @subpackage	Dev
 */
class TodoyuDevMailManager {

	/**
	 * Hook to modify the receiver if rerouting is active
	 *
	 * @param	TodoyuMailReceiverInterface		$mailReceiver
	 * @param	String							$receiverType			to, cc, replyto
	 * @return	TodoyuMailReceiverInterface
	 */
	public static function hookReceiver(TodoyuMailReceiverInterface $mailReceiver, $receiverType) {
		if( TodoyuDevManager::isMailReroutingActive() ) {
			$address	= TodoyuDevManager::getReroutingEmailAddress();
			$name		= 'Rerouted: ' . $mailReceiver->getName();

			$mailReceiver = new TodoyuMailReceiverSimple($address, $name);
		}

		return $mailReceiver;
	}



	/**
	 * Hook modify subject if rerouting is active
	 *
	 * @param	String		$subject
	 * @param	TodoyuMail	$mail
	 * @return	String
	 */
	public static function hookSubject($subject, TodoyuMail $mail) {
		if( TodoyuDevManager::isMailReroutingActive() ) {
			$subject = Todoyu::Label('dev.ext.rerouteEmail.subjectPrefix') . ' - ' . $subject;
		}

		return $subject;
	}

}

?>