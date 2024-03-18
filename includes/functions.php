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
		self::wpematico_rss_feed_initiation();
	}

	public static function wpematico_rss_feed_initiation() {
		$campaigns = WpeMatico::get_campaigns();

		foreach ($campaigns as $campaign) {
			if ($campaign['campaign_type'] == 'rss_reader') {
				
				
				switch ($campaign['campaign_rss_feed_reader']) {
					case 'shortcode':
						$post_id = $campaign['ID']; //specify post id here
						$slug = get_post_field('post_name', $post_id); 

						var_dump($slug);
						die();
						add_shortcode($slug, array(__CLASS__, 'wpematico_rss_get_content'));
						break;
					default:
						add_action('the_content', array(__CLASS__, 'wpematico_rss_get_content'), 999);
						break;
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
				if (!empty($campaign['campaign_post_select'])) {
					$continue = ($campaign['campaign_post_select'] == $post->ID);
				} elseif (!empty($campaign['campaign_page_select'])) {
					$continue = ($campaign['campaign_page_select'] == $post->ID);
				}
				
				$campaign_id = $campaign['ID'];

				if($continue){
					if($campaign_id){
						$content = get_post_meta($campaign_id, 'feed_items');
						$content = implode('', $content);
					}
				}else{
					if(isset($campaign['campaign_rss_feed_reader']) && $campaign['campaign_rss_feed_reader'] == 'shortcode'){
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
		"<div id='wpe_rss-content' class='wpe_rss-content'>
		~~~BeginItemsRecord~~~  
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
		~~~EndItemsRecord~~~
		</div>";
	}
}