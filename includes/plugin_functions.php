<?php
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

add_action('admin_init', 'rss_feed_reader_admin_init');
function rss_feed_reader_admin_init(){
	add_filter(	'plugin_row_meta',	'rss_feed_reader_init_row_meta',10,2);
	add_filter(	'plugin_action_links_' . plugin_basename( WPEMATICO_RSS_FEED_READER_ROOT_FILE ), 'rss_feed_reader_init_action_links');
}

function rss_feed_reader_tab($tabs) {
	$tabs['rss_feed_reader'] = __( 'RSS Feed Reader', WPeMatico::TEXTDOMAIN );
	return $tabs;
}
add_filter( 'wpematico_settings_tabs',  'rss_feed_reader_tab');

/*function rss_feed_reader_license_menu() {
	add_submenu_page(
				'edit.php?post_type=wpematico',
				'RSS Feed Reader Settings',
				'BoilerPlate <span class="dashicons-before dashicons-admin-plugins"></span>',
				'manage_options',
				'rss_feed_reader_license',
				'rss_feed_reader_license_page'
			);
	//add_plugins_page( 'Plugin License', 'Plugin License', 'manage_options', 'rss_feed_reader_license', 'rss_feed_reader_license_page' );
}
add_action('admin_menu', 'rss_feed_reader_license_menu');
*/


/** * Activate RSS Feed Reader on Activate Plugin */
//Does not work.  See on main file at bottom.
/*  register_activation_hook( plugin_basename( WPEMATICO_RSS_FEED_READER_ROOT_FILE ), 'rss_feed_reader_activate' );
function rss_feed_reader_activate() {
	if(class_exists('WPeMatico')) {
		$link= '<a href="' . admin_url("edit.php?post_type=wpematico&page=wpematico_settings&tab=rss_feed_reader") . '">'.__('RSS Feed Reader Plugin Settings.',  'rss_feed_reader')."</a>";
		$notice= __('RSS Feed Reader Activated.  Please check the fields on', 'rss_feed_reader').' '. $link;
		WPeMatico::add_wp_notice( array('text' => $notice , 'below-h2'=>false ) );
	}
}
*/

/** * Deactivate RSS Feed Reader on Deactivate Plugin  */
register_deactivation_hook( plugin_basename( WPEMATICO_RSS_FEED_READER_ROOT_FILE ), 'rss_feed_reader_deactivate' );
function rss_feed_reader_deactivate() {
	if(class_exists('WPeMatico')) {
		$notice = __('RSS Feed Reader DEACTIVATED.',  'rss_feed_reader');
		WPeMatico::add_wp_notice( array('text' => $notice , 'below-h2'=>false ) );
	}
}

/*
register_uninstall_hook( plugin_basename( __FILE__ ), 'rss_feed_reader_uninstall' );
function rss_feed_reader_uninstall() {
	
}
*/



/**
* Actions-Links del Plugin
*
* @param   array   $data  Original Links
* @return  array   $data  modified Links
*/
function rss_feed_reader_init_action_links($data)	{
	if ( !current_user_can('manage_options') ) {
		return $data;
	}
	return array_merge(
		$data,
		array(
			'<a href="'.  admin_url('edit.php?post_type=wpematico&page=wpematico_settings&tab=rss_feed_reader').'" title="' . __('Go to RSS Feed Reader Settings Page') . '">' . __('Settings') . '</a>',
		)
	);
}

/**
* Meta-Links del Plugin
*
* @param   array   $data  Original Links
* @param   string  $page  plugin actual
* @return  array   $data  modified Links
*/

function rss_feed_reader_init_row_meta($data, $page)	{
	if ( basename($page) != 'wpematico_rss_feed_reader.php' ) {
		return $data;
	}
	return array_merge(
		$data,
		array(
		'<a href="https://etruel.com/" target="_blank">' . __('etruel Store') . '</a>',
		'<a href="https://etruel.com/my-account/support/" target="_blank">' . __('Support') . '</a>',
		'<a href="https://wordpress.org/support/view/plugin-reviews/wpematico?filter=5&rate=5#postform" target="_Blank" title="Rate 5 stars on Wordpress.org">' . __('Rate Plugin' ) . '</a>'
		)
	);
}	

