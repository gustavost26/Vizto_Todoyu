<?php defined('WebFTP') || die('Forbidden Access');
/**
 * +----------------------------------------------------------------------
 * | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
 * +----------------------------------------------------------------------
 * | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
 * +----------------------------------------------------------------------
 * | Modify:   魔幻游戏    QQ:671064591 E-mail:671064591@qq.com
 * +----------------------------------------------------------------------
 */



class Cookie {

	// 判断Cookie是否存在
	static function is_set($name) {
		return isset($_COOKIE[C('COOKIE_PREFIX').$name]);
	}

	// 获取某个Cookie值
	static function get($name, $encode = FALSE) {
		$value = Cookie::is_set($name) ? $_COOKIE[C('COOKIE_PREFIX').$name] : NULL;
		$value = $encode ? unserialize(base64_decode($value)) : $value;

		return $value;
	}

	// 设置某个Cookie值
	static function set($name, $value, $encode = FALSE, $expire = '', $path = '', $domain = '') {
		$expire = empty($expire) ? C('COOKIE_EXPIRE') : $expire;
		$path   = empty($path) ? C('COOKIE_PATH') : $path;
		$domain = empty($domain) ? C('COOKIE_DOMAIN') : $domain;

		$expire = empty($expire) ? 0 : time() + $expire;
		$value  = $encode ? base64_encode(serialize($value)) : $value;
		setcookie(C('COOKIE_PREFIX').$name, $value, $expire, $path, $domain);
		$_COOKIE[C('COOKIE_PREFIX').$name] = $value;
	}

	// 删除某个Cookie值
	static function del($name) {
		Cookie::set($name, '', FALSE, -3600 * 8);
		unset($_COOKIE[C('COOKIE_PREFIX').$name]);
	}

	// 清空Cookie值
	static function clear() {
		unset($_COOKIE);
	}
}

