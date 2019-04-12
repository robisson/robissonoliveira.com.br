<?php
/*
Plugin Name: ManyContacts - Responsive Contact Form
Plugin URI: http://www.manycontacts.com
Description: A Contact Form to receive more calls, more subscribers and more contacts.
Version: 2.1.5
Author: woorank
Author URI: http://www.woorank.com/en/p/about
License: GPL2
*/

/*  Copyright 2011  manycontacts  (email : support@manycontacts.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Enable internationalisation
load_plugin_textdomain( 'manycontacts', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/');

//Create menu for configuration page
add_action('admin_menu', 'manycontacts_admin_actions');

/** Create menu for options page */
function manycontacts_admin_actions() {
	add_menu_page('ManyContacts', 'ManyContacts', 'manage_options', 'manycontacts-bar', 'manycontacts_admin', WP_PLUGIN_URL . "/". plugin_basename( dirname( __FILE__ ) ).'/img/manycontacts-icon.png');
}

/** To perform admin page functionality */
function manycontacts_admin() {
    if ( !current_user_can('manage_options') )
    	wp_die( __('You do not have sufficient permissions to access this page.','manycontacts') );
	include('manycontacts-bar-admin.php');
}

add_action( 'wp_footer', 'manycontacts_load_script' );

function manycontacts_load_script () {
	$code = get_option( 'manycontacts_code' );
	
	if( !empty( $code ) ) {
		echo "\n" . html_entity_decode( $code );
	}
}

function sanitize_data( $code="" ) {
	if ( !function_exists( 'wp_kses' ) ) {
		require_once( ABSPATH . 'wp-includes/kses.php' );
	}
	global $allowedposttags;
	global $allowedprotocols;
	
	if ( is_string( $code ) ) {
		$code = htmlentities( stripslashes( $code ), ENT_QUOTES, 'UTF-8' );
	}
	
	$code = wp_kses( $code, $allowedposttags, $allowedprotocols );
	return $code;
}
?>