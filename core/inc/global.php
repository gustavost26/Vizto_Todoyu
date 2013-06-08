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
@ini_set('memory_limit', '196M');
// Set session cookie HTTP only
@ini_set('session.cookie_httponly', 1);
// Force long session data lifetime (5 hours)
@ini_set('session.gc_maxlifetime', 3600 * 5);
// Ignore errors of type notice
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
// Set character encoding to utf-8
mb_internal_encoding('UTF-8');
// Set session lifetime to 5 hours
session_cache_expire(300);
// Set default dummy timezone
date_default_timezone_set('Europe/Paris');

// Start session
session_start();


// Define basic constants
include(dirname(dirname(__FILE__)).'/config/constants.php');

// Add todoyu include path
set_include_path(get_include_path().PATH_SEPARATOR.PATH);
// Add PEAR to include path
set_include_path(get_include_path().PATH_SEPARATOR.PATH_PEAR);

// Load dwoo
include(PATH_LIB.'/php/dwoo/dwooAutoload.php');

// Load basic classes
spl_autoload_register('__Core_Autoloader');
function __Core_Autoloader($class) {
	$file = PATH_CORE.'/model/'.$class.'.class.php';
	is_file($file) && include($file);
}

// Include basic person classes
include(PATH_EXT.'/contact/model/TodoyuContactPerson.class.php');
include(PATH_EXT.'/contact/model/TodoyuContactPersonManager.class.php');
include(PATH_EXT.'/contact/model/TodoyuContactPreferences.class.php');

// Load development classes
include(PATH_LIB.'/php/FirePHPCore/FirePHP.class.php');

// Register autoloader
spl_autoload_register(array('TodoyuAutoloader', 'load'));

// Register error handler
set_error_handler(array('TodoyuErrorHandler', 'handleError'));

// Load global functions
include(PATH_CORE.'/inc/version.php');

// Include strptime function if not defined on windows
if(!function_exists('strptime')) {
	require_once(PATH_CORE.'/inc/strptime.function.php');
}

// Load installed extension list
require_once(PATH_LOCALCONF.'/extensions.php');

// Load basic core config
require_once(PATH_CONFIG.'/config.php');
require_once(PATH_CONFIG.'/locales.php');
require_once(PATH_CONFIG.'/fe.php');
require_once(PATH_CONFIG.'/assets.php');
require_once(PATH_CONFIG.'/cache.php');
require_once(PATH_CONFIG.'/colors.php');


// Load local config
require_once(PATH_LOCALCONF.'/db.php');
require_once(PATH_LOCALCONF.'/system.php');
require_once(PATH_LOCALCONF.'/settings.php');
require_once(PATH_LOCALCONF.'/config.php');

// Load extconf
TodoyuExtensions::loadExtConf();

?>