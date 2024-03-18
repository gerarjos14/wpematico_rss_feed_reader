<?php
/** 
 *  @package WPeMatico rss_feed_reader
 *	functions to add a tab with custom options in wpematico settings 
**/

if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

// add_action('admin_init', 'wpematico_rss_feed_reader_help');
function wpematico_rss_feed_reader_help(){
	if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'wpematico_settings' ) && 
			( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'wpematico' ) &&
			( isset( $_GET['tab'] ) && $_GET['tab'] == 'rss_feed_reader' ) ) {
		
		$screen = WP_Screen::get('wpematico_page_wpematico_settings ');
		
		$content = '<h3>' . __( 'Using RSS Feed Reader for WPeMatico Campaigns.','wpematico_rss_feed_reader' ) . '</h3>';
		$content.= '<p>' . __( 'This Add-on allows to use WPeMatico from a Wordpress website and to send the read posts from each campaign to an email account.','wpematico_rss_feed_reader' ) . '</p>';
		$content.= '<p>' . __( 'Each campaign allows sending content to different email accounts.','wpematico_rss_feed_reader' ) . '</p>';
		$content.= '<p>' . __( 'Therefore, it can post from a unique website with WPeMatico plugin to many different Wordpress websites.','wpematico_rss_feed_reader' ) . '</p>';
//		$content.= '<p>' . __( '','wpematico_rss_feed_reader' ) . '</p>';
		
		$screen->add_help_tab( array(
			'id'	=> 'rss_feed_reader',
			'title'	=> __('RSS Feed Reader', 'wpematico_rss_feed_reader'),
			'content'=> $content,
//			'content'=> '<p>' . __( '.' ) . '</p>',
//			'callback'=> 'wpematico_rss_feed_reader_commandshelp',
		) );
	}
}