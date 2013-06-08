<?php
function dwooAutoload($class) {
	if($class==='Dwoo') {
		include dirname(__FILE__).DIRECTORY_SEPARATOR.'Dwoo.compiled.php';
		include(PATH_CORE.'/model/dwoo/plugins.php');
	}
	if(substr($class, 0, 20) === 'Dwoo_Plugin_restrict') {
		$file = PATH_CORE.'/model/dwoo/'.$class.'.php';
		is_file($file) && include($file);
	}
	if(substr($class, 0, 5) === 'Dwoo_') {
		include DWOO_DIRECTORY.strtr($class, '_', DIRECTORY_SEPARATOR).'.php';
	}
}

spl_autoload_register('dwooAutoload');
