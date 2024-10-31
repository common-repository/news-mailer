<?php
/*
Plugin Name: News Mailer
Plugin URI: http://edvindev.wordpress.com/news-mailer/
Description: Simple plugin for mailing news or updates to members registered on the blog.
Author: Joel Bergroth
Version: 0.5.4
Author URI: http://edvindev.wordpress.com
*/

/*  Copyright 2010 Joel Bergroth (email: joel.bergroth@gmail.com)

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

/*
TODO

small fixes to come
- proper <p></p> with line breaks in articles
- space after inputs on option page (iow. css cleanup on settings page)
- fix the plain mail when second article is empty.
- fix the logging output on page when sending

0.6
- slänga in id i det som skickas till cron
- log to file
- handle all notices when debugging

0.7
- preview mail before sending
- proper multiple articles

0.8
- don't overwrite template files when upgrading

0.9
- multiple templates support

1.0
- send bug reports?
- information about mailing on help pages

*/


//Constants
define( 'NEWSMAILER_URLPATH', trailingslashit( WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) ) );
define( 'NEWSMAILER_DIRPATH', trailingslashit( WP_PLUGIN_DIR . '/' . plugin_basename( dirname(__FILE__) ) ) );
define( 'NEWSMAILER_MAIL_CRON_TABLE','mail_cron' );

//Includes
include( "options-mail.php" );
include( "newsletter.php" );
include( "send-mail.php" );

//Actions
add_action( 'init', 'news_mailer_load_textdomain' );
add_action( 'init', 'news_mailer_send_mail' );
add_action( 'admin_menu', 'news_mailer_create_main_menu' );
add_action( 'admin_menu', 'news_mailer_create_options_menu' );
add_action( 'admin_print_styles', 'news_mailer_stylesheet' );
add_action( 'news_mailer_mail_cron', 'news_mailer_send_mail_wp_cron' );

//Filters
add_filter( 'wp_mail_from','news_mailer_mail_from' );
add_filter( 'wp_mail_from_name','news_mailer_mail_from_name' );
add_filter( 'plugin_action_links', 'news_mailer_settings_link', 10, 2 );


//Localization
function news_mailer_load_textdomain() {
	$plugin_dir = dirname( plugin_basename( __FILE__ ) );
	load_plugin_textdomain( 'news-mailer', false, $plugin_dir.'/lang' );
}

// ------------------------------------------------------------------
// News Mailer Activation
// ------------------------------------------------------------------
//
// This is executed when News Mailer is activated from the 
// Plugins page
//

register_activation_hook(__FILE__, 'news_mailer_activation');

function news_mailer_activation() {
	
	//Set standard options
	if ( !get_option('mail_from_name') ) {
		$from_name = 'WordPress';
		
		update_option( 'mail_from_name', $from_name );
	}
		
	if ( !get_option('mail_from_mail') ) {
		// Get the site domain and get rid of www.
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;
		
		update_option( 'mail_from_mail', $from_email );
	}
	
	if ( !get_option('mail_transport') ) {
		update_option( 'mail_transport', 'wp_mail' );
	}
	
	if ( !get_option('mail_smtp_server_url') ) {
		$php_ini_SMTP = ini_get('SMTP');
		
		update_option( 'mail_smtp_server_url', $php_ini_SMTP );
	}
	
	if ( !get_option('mail_smtp_server_port') ) {
		$php_ini_smtp_port = ini_get('smtp_port');
		
		update_option( 'mail_smtp_server_port', $php_ini_smtp_port );
	}
	
	if ( !get_option('mail_server_mta') ) {
		$php_ini_sendmail_path = ini_get('sendmail_path');
		$php_ini_sendmail = explode(" ", $php_ini_sendmail_path);
		
		update_option( 'mail_server_mta', $php_ini_sendmail[0] );
	}
	
	if ( !get_option('mail_server_mta_flag') ) {
		update_option( 'mail_server_mta_flag', '-bs' );
	}
	
	if ( !get_option('mail_anti_flood_enable') ) {
		update_option( 'mail_anti_flood_enable', '1' );
	}
	
	if ( !get_option('mail_anti_flood_mails') ) {
		update_option( 'mail_anti_flood_mails', '40' );
	}
	
	if ( !get_option('mail_anti_flood_seconds') ) {
		update_option( 'mail_anti_flood_seconds', '5' );
	}
	
	if ( !get_option('mail_anti_flood_type') ) {
		update_option( 'mail_anti_flood_type', 'swiftmailer' );
	}
	
	if ( !get_option('mail_beginner_message') ) {
		update_option( 'mail_beginner_message', '1' );
	}
	
	if ( !get_option('mail_format') ) {
		update_option( 'mail_format', 'both' );
	}
	
	
	//Table
	news_mailer_create_mail_cron_table();
	
}


// ------------------------------------------------------------------
// News Mailer Database Table Creation And Deletion
// ------------------------------------------------------------------
//
// Triggers the creation and deletion of News Mailer table:
//  - {prefix}mail_cron
//  

function news_mailer_create_mail_cron_table() {
	global $wpdb;
	
	$table_name = $wpdb->prefix.NEWSMAILER_MAIL_CRON_TABLE;
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
	
		$sql = "CREATE TABLE ".$table_name." (
			cron_id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			mail_to longtext NOT NULL,
			mail_subject text NOT NULL,
			mail_html longtext NOT NULL,
			mail_plain longtext NOT NULL,
			UNIQUE KEY (cron_id)
			) DEFAULT CHARSET=utf8 ;";
		
		$wpdb->query($sql);
	}
}

function news_mailer_drop_mail_cron_table() {
	global $wpdb;
	
	$table_name = $wpdb->prefix.NEWSMAILER_MAIL_CRON_TABLE;
	
	$sql = "DROP TABLE ".$table_name."";
		
	$wpdb->query($sql);
}


// ------------------------------------------------------------------
// News Mailer Uninstallation
// ------------------------------------------------------------------
//
// This is executed when News Mailer is uninstalled 
// (not deactivated) from the Plugins page
//

register_uninstall_hook(__FILE__, 'news_mailer_uninstall');

function news_mailer_uninstall() {

	//Delete all plugin created options in database
	delete_option( 'mail_transport' );
	delete_option( 'mail_from_name' );
	delete_option( 'mail_from_mail' );
	delete_option( 'mail_smtp_server_url' );
	delete_option( 'mail_smtp_server_port' );
	delete_option( 'mail_smtp_server_username' );
	delete_option( 'mail_smtp_server_password');
	delete_option( 'mail_smtp_server_encryption');
	delete_option( 'mail_server_mta' );
	delete_option( 'mail_server_mta_flag' );
	delete_option( 'mail_anti_flood_enable' );
	delete_option( 'mail_anti_flood_mails' );
	delete_option( 'mail_anti_flood_seconds' );
	delete_option( 'mail_anti_flood_type' );
	delete_option( 'mail_beginner_message' );
	delete_option( 'mail_current_cronjob' );		//is set in send-mail.php
	delete_option( 'mail_format' );
	
	// DELETE TABLES 
	news_mailer_drop_mail_cron_table();
}


// ------------------------------------------------------------------
// News Mailer Menu
// ------------------------------------------------------------------
//
// These functions are used to initialize the top level and
// options menu
//

function news_mailer_create_main_menu() {
	add_menu_page( __('Mail', 'news-mailer'), __('Mail', 'news-mailer'), 'manage_options', 'news-mailer', 'news_mailer_mail_page');
	add_submenu_page( 'news-mailer', __('Compose Newsletter', 'news-mailer'), __('Newsletter', 'news-mailer'), 'manage_options', 'news-mailer', 'news_mailer_mail_page');
}

function news_mailer_create_options_menu() {
	add_options_page( __('Mail Settings', 'news-mailer'), __('Mail', 'news-mailer'), 'manage_options', 'news_mailer_options', 'news_mailer_options_page');
	add_action( 'admin_init', 'register_news_mailer_settings' );
}

// ------------------------------------------------------------------
// News Mailer Settings Link
// ------------------------------------------------------------------
//
// Add a 'Settings' link to plugin list
// 
//

function news_mailer_settings_link($links, $file) {
	static $this_plugin;
	
	if (!$this_plugin) {
		$this_plugin = plugin_basename(__FILE__);
	}
 
	if ($file == $this_plugin) {
		$settings_link = '<a href="options-general.php?page=news_mailer_options">'.__("Settings", "news-mailer").'</a>';
 		array_unshift($links, $settings_link);
	}
	
	return $links;
}


// ------------------------------------------------------------------
// News Mailer Stylesheet
// ------------------------------------------------------------------
//
// Stylesheet for the News Mailer admin page
//

function news_mailer_stylesheet() {
	$css = "
	<style type='text/css'>
	#nm_sender .form-field input {
		width:25em;
	}
	#nm_sender .form-table input.small-text {
		width:50px;
	}
	</style>";
	echo $css;
}


// ------------------------------------------------------------------
// News Mailer Mail From (in wp_mail)
// ------------------------------------------------------------------
//
// Defines the FROM name and email in mails sent with wp_mail 
// from the WordPress installation, defaults to:
// WordPress <wordpress@thedomain.com>
//

function news_mailer_mail_from() {
	if ( get_option('mail_from_mail') ) {
		return get_option('mail_from_mail');
	}
	else {
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}

		return 'wordpress@' . $sitename;
	}
}

function news_mailer_mail_from_name() {
	if ( get_option('mail_from_name') ) {
		return get_option('mail_from_name');
	}
	else {
		return 'WordPress';
	}
}


// ------------------------------------------------------------------
// News Mailer Functions
// ------------------------------------------------------------------
//
// Different functions that News Mailer uses.
//

// Originally used the nl2br() function with the false statement on is_xhtml, but it turned out people used pre 5.3.0 php installations.
function nl2br2( $input ) {
	return preg_replace("/\r\n|\n|\r/", "<br>", $input);
}
?>