<?php

/** 
 *  @package WPeMatico RSS Feed Reader
 *	functions to add metaboxes in campaign editing
 **/
if (!defined('ABSPATH')) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

add_action('init', array('Wpematico_feed_reader_edit', 'init'));

class Wpematico_feed_reader_edit {
	public static function init() {
		new self();
	}

	function __construct() {
		add_action('admin_print_scripts-post.php', array(__CLASS__, 'admin_scripts'));
		add_action('admin_print_scripts-post-new.php', array(__CLASS__, 'admin_scripts'));
		add_action('wpematico_print_addicional_options', array(__CLASS__, 'wpematico_rss_print_addicional'));
		// add_action('admin_print_styles-post.php', array(__CLASS__, 'admin_styles'));
		// add_action('admin_print_styles-post-new.php', array(__CLASS__, 'admin_styles'));
	}

	public static function hooks() {
		// Metabox campaigns 
		add_filter('wpematico_campaign_type_options', array(__CLASS__, 'campaign_type_options'), 16);
		// Saving campaigns (just check fields, saved in Free version
		add_filter('pro_check_campaigndata',  array(__CLASS__,'wpematico_rss_feed_reader_check_campaigndata'), 15, 2);
		add_action('wpematico_create_metaboxes_before',  array(__CLASS__, 'wpematico_rss_feed_reader_metaboxes'), 15, 2);
	}
	public static function campaign_type_options($options) {
		$options[] = array('value' => 'rss_reader', 'text' => __('RSS Feed Reader', 'wpematico_rss_feed_reader'), "show" => array('feeds-box', 'rss-page-feed-url-save'), 'hide' => array('images-box'));

		return $options;
	}

	public static function wpematico_rss_feed_reader_metaboxes() {  // chequea y agrega campos a campaign y graba en free
		global $pagenow, $post;
		if (!(($pagenow == 'post.php' || $pagenow == 'post-new.php') && $post->post_type == 'wpematico')) return false;

		add_meta_box('rss-page-feed-url-save', __('RSS Contents', 'wpematico_rss_feed_reader'), array(__CLASS__, 'wpematico_rss_feed_reader_box'), 'wpematico', 'normal', 'default');
	}

	public static function wpematico_rss_feed_reader_styles() {
		global $post;
		if ($post->post_type != 'wpematico') return $post->ID;
		//	wp_enqueue_style('thickbox');
?>
		<style type="text/css">
			#rss_feed_reader {
				margin-left: 20px;
			}

			#rss_feed_reader-box h2.hndle {
				background: #2ccbcb;
				color: maroon;
			}
		</style>
		<?php
	}
	public static function admin_scripts() { // load javascript 
		global $post;
		
		if ($post->post_type != 'wpematico') return $post->ID;

		wp_enqueue_script('wpematico_rss_feed_reader_campaign_edit', WPEMATICO_RSS_FEED_READER_URL . 'assets/js/campaign_edit.js', array('jquery'), WPEMATICO_RSS_FEED_READER_VER, true);
	}

	public static function wpematico_rss_feed_reader_box() {
		global $post, $campaign_data;

		$campaign_rss_feed_reader = empty($campaign_data['campaign_rss_feed_reader']) ? '' : $campaign_data['campaign_rss_feed_reader'];
		$campaign_rss_html_content = (!empty($campaign_data['campaign_rss_html_content'])) ? $campaign_data['campaign_rss_html_content'] : wpematico_rss_feed_functions::wpematico_rss_get_default_template();
		?>
			<b><?php _e('How to display the feed content:',  'wperss-page') ?></b><br />
			<label><input type="radio" name="campaign_rss_feed_reader" <?php echo checked('the_content', $campaign_rss_feed_reader, false); ?> value="the_content" /> <span style="background-color: lightblue; padding-left: 3px; padding-right: 3px;">get_the_content</span> <?php _e('Wordpress filter', 'wperss-page'); ?></label><br />
			<label><input type="radio" name="campaign_rss_feed_reader" <?php echo checked('page_template', $campaign_rss_feed_reader, false); ?> value="page_template" />
				<?php _e('RSS Page Template.', 'wperss-page'); ?><br /><?php _e('Must selected also on Page Attributes.', 'wperss-page'); ?></label><br />
			<label><input type="radio" name="campaign_rss_feed_reader" <?php echo checked('shortcode', $campaign_rss_feed_reader, false); ?> value="shortcode" /> <span style="background-color: lightblue; padding-left: 3px; padding-right: 3px;"><?php echo "[wpe-$post->post_name]" ?></span> <?php _e('Shortcode', 'wperss-page'); ?></label><br/><br>
			<label for="campaign_rss_html_content"><b><?php echo __('Template feed', 'wpematico') ?></b></label><br>
			
			<textarea id="campaign_rss_html_content" name="campaign_rss_html_content" rows="10" cols="100"><?php echo htmlspecialchars($campaign_rss_html_content); ?></textarea><br>
<?php
	}

	public static function wpematico_rss_print_addicional($campaign_data){

		
		// Dropdown for available posts
		$custom_posts = get_posts(array(
			'post_type'      => 'post',
			'posts_per_page' => -1,
			'orderby'        => 'title',
			'order'          => 'ASC',
		));

		
		if ($custom_posts) {
			echo '<div class="custom-dropdown hidden" id="custom-dropdown-posts">';
			echo '<select name="campaign_post_select">';
			echo '<option value="">Select a Post</option>';
			foreach ($custom_posts as $custom_post) {
				$selected = (!empty($campaign_data['campaign_post_select']) && $campaign_data['campaign_post_select'] == $custom_post->ID) ? 'selected' : '';
				echo '<option value="' . $custom_post->ID . '" ' . $selected . '>' . $custom_post->post_title . '</option>';
			}
			echo '</select>';
			echo '</div>';
		}

		$args = array(
			'name'             => 'campaign_page_select',
			'show_option_none' => 'Select a Page',
			'sort_column'      => 'menu_order',
			'echo'             => 1,
			'selected'         => !empty($campaign_data['campaign_page_select']) ? $campaign_data['campaign_page_select'] : '',
		);
		
		echo '<div class="custom-dropdown hidden" id="custom-dropdown-pages">';
			wp_dropdown_pages($args);
		echo '</div>';
	}
	public static function wpematico_rss_feed_reader_check_campaigndata($campaign_data = array(), $post_data = array()){  // chequea y agrega campos a campaign y graba en free
		if ($campaign_data['campaign_type'] == 'rss_reader') {
			$default_template = wpematico_rss_feed_functions::wpematico_rss_get_default_template();
			$campaign_data['campaign_rss_feed_reader'] = (!isset($post_data['campaign_rss_feed_reader']) || empty($post_data['campaign_rss_feed_reader'])) ? '' : (($post_data['campaign_rss_feed_reader'] != '') ? $post_data['campaign_rss_feed_reader'] : '');

			$campaign_data['campaign_rss_html_content'] = (!isset($post_data['campaign_rss_html_content']) || empty($post_data['campaign_rss_html_content'])) ? $default_template : (($post_data['campaign_rss_html_content'] != '') ? $post_data['campaign_rss_html_content'] : $default_template);

			if ( $post_data['campaign_customposttype'] == 'post') {
				if(!empty($post_data['campaign_post_select'])){
					// If 'campaign_post_select' has a value, set 'campaign_page_select' to empty
					$campaign_data['campaign_post_select'] = $post_data['campaign_post_select'];
					$campaign_data['campaign_page_select'] = '';
				}
			}else{
				if($post_data['campaign_customposttype'] == 'page'){
					if (!empty($post_data['campaign_page_select'])) {
						// If 'campaign_page_select' has a value, set 'campaign_post_select' to empty
						$campaign_data['campaign_page_select'] = $post_data['campaign_page_select'];
						$campaign_data['campaign_post_select'] = '';
					}
				}
			}
		}

		return $campaign_data;
	}
}

Wpematico_feed_reader_edit::hooks();
