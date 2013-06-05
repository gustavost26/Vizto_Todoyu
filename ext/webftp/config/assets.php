<?php

/**
 * Assets (JS, CSS) requirements for tutorial extension
 *
 * @package		Todoyu
 * @subpackage	Tutorial
 */

Todoyu::$CONFIG['EXT']['webftp']['assets'] = array(
	'js' => array(
		array(
			'file'		=> 'ext/webftp/asset/js/Ext.js',
			'position'	=> 150
		)
	),
	'css' => array(
		array(
			'file'		=> 'ext/webftp/asset/css/ext.css',
			'media'		=> 'all'
		),
		array(
			'file'		=> 'ext/webftp/asset/css/panelwidget-fileoperation.scss',
			'media'		=> 'all',
			'opsition'  => 110
		)
	)
);

?>