<?php

/**
 * Assets (JS, CSS) requirements for tutorial extension
 *
 * @package		Todoyu
 * @subpackage	Tutorial
 */

Todoyu::$CONFIG['EXT']['tutorial']['assets'] = array(
	'js' => array(
		array(
			'file'		=> 'ext/tutorial/asset/js/Ext.js',
			'position'	=> 150
		)
	),
	'css' => array(
		array(
			'file'		=> 'ext/tutorial/asset/css/ext.css',
			'media'		=> 'all'
		)
	)
);

?>