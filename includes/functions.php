<?php

/**
 * Helper Functions
 *
 * @package     WPeMatico\PluginName\Functions
 * @since       1.0.0
 */


// Exit if accessed directly
if (!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

class wpematico_rss_feed_functions {
	public static function init(){
		add_action('template_redirect', array(__CLASS__, 'wpematico_rss_feed_initiation'), 999999);
		add_filter('theme_page_templates', array(__CLASS__,'wpematico_add_custom_template'));
		add_action('admin_action_wpematico_reset_campaign', array(__CLASS__, 'wpematico_reset_campaign'), 1);
	}

	public static function wpematico_rss_feed_initiation() {
		global $stopwith;
		$campaigns = WpeMatico::get_campaigns();
		
		$stopwith = array();
		
		foreach ($campaigns as $campaign) {
			if ($campaign['campaign_type'] == 'rss_reader') {

				if (!empty($campaign['campaign_rss_feed_reader'])) {
					switch ($campaign['campaign_rss_feed_reader']) {
						case 'shortcode':
							add_shortcode('wpe-' . $campaign['wpematico_shortcode_name'], array(__CLASS__, 'wpematico_rss_get_content'));
							break;
						
						case 'page_template':
							add_action('the_content', array(__CLASS__, 'wpematico_rss_get_content'), 999);
							break;

						case 'the_content':
							add_action('the_content', array(__CLASS__, 'wpematico_rss_get_content'), 999);
							break;

						default:
							break;
					}
				}
			}
		}
	}

	public static function wpematico_rss_get_content($content= ''){
		global $post, $stopwith;
		
		$campaigns = WpeMatico::get_campaigns();
		
		foreach($campaigns as $campaign){
			if ($campaign['campaign_type'] == 'rss_reader' ) {
				$continue = false;
				if (!empty($campaign['campaign_post_select']) && $post->post_type == 'post') {
					$continue = ($campaign['campaign_post_select'] == $post->ID);
				} elseif (!empty($campaign['campaign_page_select']) && $post->post_type == 'page') {
					$continue = ($campaign['campaign_page_select'] == $post->ID);
				}
				$campaign_id = $campaign['ID'];
				if($continue){
					if($campaign_id){
						$content = get_post_meta($campaign_id, 'feed_items');
						$content = array_reverse($content);
						$recent_content = array_slice($content, 0, $campaign['campaign_max_to_show']); // Get the most recent items
						$content = implode('', $recent_content);
					}
				}else{
					if(isset($campaign['campaign_rss_feed_reader']) && $campaign['campaign_rss_feed_reader'] == 'shortcode' && has_shortcode($post->post_content, "wpe-" . $campaign['wpematico_shortcode_name']) && !in_array($campaign['wpematico_shortcode_name'], $stopwith)){
						$currentshortcode = array($campaign['wpematico_shortcode_name']);
						$stopwith = array_merge($stopwith, $currentshortcode);

						$content_rss = get_post_meta($campaign_id, 'feed_items');
						$content_rss = array_reverse($content_rss);
						$recent_content = array_slice($content_rss, 0, $campaign['campaign_max_to_show']); // Get the most recent items
						$recent_content = implode('', $recent_content);
						
						$content = $recent_content;
					}
				}
			}
		}

		return $content;
	}

	public static function wpematico_rss_get_default_template(){

		return 
		"~~~BeginItemsRecord~~~  
		  <div class='wpe_rss-item'>
			<div class='wpe_rss-title'>
			  <h2><a href='~~~ItemLink~~~' target='_blank'>~~~ItemTitle~~~</a></h2>
			</div>
			<div class='wpe_rss-metadata'>
			  <div class='wpe_rss-metadata-item'><span class='dashicons dashicons-calendar'></span> <span>~~~ItemPubShortDate~~~ ~~~ItemPubShortTime~~~</span></div>
			</div>
			<div class='wpe_rss-description'>
			  ~~~ItemDescription~~~
			  <br /> 
			  <a href='~~~ItemSourceUrl~~~' class='wpe_rss-btn'>Go to source</a>
			</div>
		  </div>
		~~~EndItemsRecord~~~";
	}

	public static function wpematico_add_custom_template($templates){
		$templates[WPEMATICO_RSS_FEED_READER_DIR . 'templates/wpematico-rss-template.php'] = __('Feed reader template', 'wpematico_rss_feed_reader');

		return $templates;
	}

	public static function wpematico_reset_campaign($status = '') {
		if (!( isset($_GET['post']) || isset($_POST['post']) || ( isset($_REQUEST['action']) && 'wpematico_reset_campaign' == $_REQUEST['action'] ) )) {
			wp_die( esc_html(__('No campaign ID has been supplied!', 'wpematico')) );
		}
		$nonce = '';
		if (isset($_REQUEST['nonce'])) {
			$nonce = sanitize_text_field($_REQUEST['nonce']);
		}
		if (!wp_verify_nonce($nonce, 'wpe-action-nonce')) {
			wp_die('Are you sure?');
		}
		// Get the original post
		$id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']) );

		delete_post_meta($id, 'feed_items');
	}

}