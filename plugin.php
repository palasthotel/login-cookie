<?php

/**
 * Plugin Name: Login Cookies
 * Plugin URI: https://github.com/palasthotel/login-cookies
 * Description: Add some login cookies for caching optimization
 * Version: 1.0.0
 * Author: Palasthotel <rezeption@palasthotel.de> (in person: Edward Bock)
 * Author URI: http://www.palasthotel.de
 * Requires at least: 4.0
 * Tested up to: 5.4.2
 * License: http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 * @copyright Copyright (c) 2020, Palasthotel
 * @package Palasthotel\LoginCookies
 */

namespace Palasthotel\LoginCookies;

use WP_User;

const COOKIE_NAME = "special_login_cookie";
const COOKIE_VALUE = "special_cookie_value";

const FILTER_MIN_CAP = "login_cookies_min_cap";

/**
 * set cookie
 */
function setSpecialCookie(){
	setcookie(COOKIE_NAME, COOKIE_VALUE, 0 );
}

/**
 * delete cookie
 */
function unsetSpecialCookie(){
	setcookie(COOKIE_NAME, "", time() - 3600 );
}

/**
 * when user was successfully logged in
 * @param string $user_login
 * @param WP_User $user
 */
function wp_login($user_login, $user){
	if( user_can( $user,apply_filters(FILTER_MIN_CAP, "edit_posts")) ){
		setSpecialCookie();
	}
}
add_action('wp_login', __NAMESPACE__.'\wp_login', 10, 2);

/**
 * when logged out
 */
function wp_logout(){
	unsetSpecialCookie();
}
add_action('wp_logout', __NAMESPACE__.'\wp_logout');

/**
 * in case of unclean logout
 */
function clean(){
	if( isset($_COOKIE[COOKIE_NAME]) && !is_user_logged_in()){
		unsetSpecialCookie();
	}
}
add_action('init', __NAMESPACE__.'\clean');

/**
 * Disable admin bar
 */
function disable_admin_bar() {
	if(isset($_COOKIE[COOKIE_NAME])) return;
	if(is_admin()) return;

	add_filter( 'show_admin_bar', '__return_false', 999 );
}
add_action('init', __NAMESPACE__.'\disable_admin_bar');
