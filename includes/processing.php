<?php
/** 
 *  @package WPeMatico wpematico_googlo_news
 *	functions to add filters and parsers on campaign running
**/
if ( !defined('ABSPATH') ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

class Wpematico_feed_reader_process {
	
	function __construct() {
		add_action('wpematico_allow_insertpost', array(__CLASS__, 'allow_insertpost'), 10, 3); //hook to add actions and filter on init fetching
		add_filter('wpematico_custom_simplepie', array(__CLASS__,'wpematico_rss_feed_reader_process'), 10, 4);
	}

	public static function wpematico_rss_feed_reader_process($simplepie, $class, $feed, $kf){
		
		if ($class->campaign['campaign_type'] == 'rss_reader') {

			$wpe_url_feed = apply_filters('wpematico_simplepie_url', $feed, $kf, $class->campaign);
			/**
			 * @since 1.0.0
			 * Added @fetch_feed_params to change parameters values before fetch the feed.
			 */
			$fetch_feed_params = array(
				'url' => $wpe_url_feed,
				'stupidly_fast' => $class->cfg['set_stupidly_fast'],
				'max' => $class->campaign['campaign_max'],
				'order_by_date' => $class->campaign['campaign_feed_order_date'],
				'force_feed' => false,
			);
			$fetch_feed_params = apply_filters('wpematico_fetch_feed_params', $fetch_feed_params, $kf, $class->campaign);
			$simplepie = WPeMatico::fetchFeed($fetch_feed_params);
		}
		
		return $simplepie;
	}

	public static function allow_insertpost($allow, $fetch, $args){
		global $post;
		
		$campaign = $fetch->campaign;
		$current_item = $fetch->current_item;
		
		if ($campaign['campaign_type'] == 'rss_reader' ) {
			$campaign_rss_html_content = $campaign['campaign_rss_html_content'];
			$campaign_id = $campaign['ID'];
			if (self::wpematico_set_rss_data($campaign_id, $current_item, $campaign_rss_html_content)) {
				$allow = false;
				// get all posts
				$all_posts = get_post_meta($campaign_id, 'feed_items');

				if (count($all_posts) > $campaign['campaign_max_to_show']) {
					// erase the oldest posts
					delete_post_meta($campaign_id, 'feed_items', $all_posts[0]);
				}
				return $allow;
			}
		}

		return $allow;
	}

	public static function wpematico_set_rss_data($campaign_id, $item, $template = ''){

		if($campaign_id){
			//start the process to change the template to a feed
			$template = str_replace('~~~BeginItemsRecord~~~', '', $template);
			$template = str_replace('~~~ItemPubShortDate~~~', empty($item['date']) ? date_i18n('d-m-Y') : $item['date'], $template);
			$template = str_replace('~~~ItemPubShortTime~~~', strtotime('now') , $template);
			$template = str_replace('~~~ItemDescription~~~', $item['content'], $template);
			$template = str_replace('~~~ItemLink~~~', $item['permalink'], $template);
			$template = str_replace('~~~ItemTitle~~~', $item['title'], $template);
			$template = str_replace('~~~ItemSourceUrl~~~', $item['meta']['wpe_sourcepermalink'], $template);
			$template = str_replace('~~~EndItemsRecord~~~', '', $template);
			//finish the process
			
			
			
			//save data for the $campaign_id
			return add_post_meta($campaign_id, 'feed_items', $template);
		}

		return false;

	}
}

$wpematico_feed_reader_process = new Wpematico_feed_reader_process();