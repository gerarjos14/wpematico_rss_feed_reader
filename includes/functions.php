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
		add_action('template_redirect', array(__CLASS__, 'wpematico_rss_feed_initiation'));
	}

	public static function wpematico_rss_feed_initiation() {
		$campaigns = WpeMatico::get_campaigns();
		
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
		global $post;
		
		if ($post->post_type !== 'post' && $post->post_type !== 'page') return;

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
						$content = implode('', $content);

					}
				}else{
					if(isset($campaign['campaign_rss_feed_reader']) && $campaign['campaign_rss_feed_reader'] == 'shortcode' && has_shortcode($post->post_content, "wpe-" . $campaign['wpematico_shortcode_name'])){
						$content = get_post_meta($campaign_id, 'feed_items');
						$content = implode('', $content);
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
			  <div class='wpe_rss-metadata-item'><span class='dashicons dashicons-admin-users'></span> <span>~~~ItemAuthor~~~</span></div>
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
}