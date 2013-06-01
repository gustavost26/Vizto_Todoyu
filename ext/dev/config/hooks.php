<?php
/****************************************************************************
* This extension is published under the snowflake
* Commercial Software License Agreement:
* http://www.todoyu.com/index.php?id=commercial-license
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Hooks config for Dev extension
 *
 * @package		Todoyu
 * @subpackage	Dev
 */

	// Before adding receiver email and fullname to mail
TodoyuHookManager::registerHook('core', 'mail.receiver', 'TodoyuDevMailManager::hookReceiver', 1000);
TodoyuHookManager::registerHook('core', 'mail.subject', 'TodoyuDevMailManager::hookSubject', 1000);

?>