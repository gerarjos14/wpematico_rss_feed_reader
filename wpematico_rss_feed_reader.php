<?php
/**
 * Plugin Name:     WPeMatico RSS Feed Reader
 * Plugin URI:      @todo
 * Description:     
 * Version:         1.0
 * Author:          etruel
 * Author URI:      http://www.netmdp.com
 * Text Domain:     wpematico_rss_feed_reader
 *
 * @package         etruel\RSS Feed Reader
 * @author          Esteban Truelsegaard
 * @copyright       Copyright (c) 2016
 *
 *
 * - Find all instances of @todo in the plugin and update the relevant
 *   areas as necessary.
 *
 * - All functions that are not class methods MUST be prefixed with the
 *   plugin name, replacing spaces with underscores. NOT PREFIXING YOUR
 *   FUNCTIONS CAN CAUSE PLUGIN CONFLICTS!
 */


// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

if( !class_exists( 'WpeMatico_RSS_Feed_Reader' ) ) {

	// Plugin version
	if(!defined('WPEMATICO_RSS_FEED_READER_VER')) {
		define('WPEMATICO_RSS_FEED_READER_VER', '1.0.2' );
	}
	
    /**
     * Main RSS Feed Reader class
     *
     * @since       1.0.0
     */
    class WpeMatico_RSS_Feed_Reader {

        /**
         * @var         WpeMatico_RSS_Feed_Reader $instance The one true RSS Feed Reader
         * @since       1.0.0
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      object self::$instance The one true RSS Feed Reader
         */
        public static function instance() {
            if( !self::$instance ) {
                self::$instance = new self();
                self::$instance->setup_constants();
                self::$instance->includes();
                self::$instance->load_textdomain();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
       public static function setup_constants() {
			// Plugin root file
			if(!defined('WPEMATICO_RSS_FEED_READER_ROOT_FILE')) {
				define('WPEMATICO_RSS_FEED_READER_ROOT_FILE', __FILE__ );
			}
            // Plugin path
			if(!defined('WPEMATICO_RSS_FEED_READER_DIR')) {
				define('WPEMATICO_RSS_FEED_READER_DIR', plugin_dir_path( __FILE__ ) );
			}
            // Plugin URL
			if(!defined('WPEMATICO_RSS_FEED_READER_URL')) {
				define('WPEMATICO_RSS_FEED_READER_URL', plugin_dir_url( __FILE__ ) );
			}
			if(!defined('WPEMATICO_RSS_FEED_READER_STORE_URL')) {
				define('WPEMATICO_RSS_FEED_READER_STORE_URL', 'https://etruel.com'); 
			} 
			if(!defined('WPEMATICO_RSS_FEED_READER_ITEM_NAME')) {
				define('WPEMATICO_RSS_FEED_READER_ITEM_NAME', 'WPeMatico RSS Feed Reader'); 
			} 
        }


        /**
         * Include necessary files
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
         public static function includes() {
            // Include scripts
			
			// require_once WPEMATICO_RSS_FEED_READER_DIR . 'includes/scripts.php';
			require_once WPEMATICO_RSS_FEED_READER_DIR . 'includes/settings.php';
            require_once WPEMATICO_RSS_FEED_READER_DIR . 'includes/plugin_functions.php';
            require_once WPEMATICO_RSS_FEED_READER_DIR . 'includes/functions.php';
            require_once WPEMATICO_RSS_FEED_READER_DIR . 'includes/campaign_edit.php';
            require_once WPEMATICO_RSS_FEED_READER_DIR . 'includes/processing.php';

            // require_once WPEMATICO_RSS_FEED_READER_DIR . 'includes/shortcodes.php';
            // require_once WPEMATICO_RSS_FEED_READER_DIR . 'includes/widgets.php';
        }


        /**
         * Run action and filter hooks
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         *
         */
         public static function hooks() {
            add_action('init', array('wpematico_rss_feed_functions', 'init'));
            // Register settings
            add_filter( 'wpematico_settings_extensions', array(__CLASS__, 'settings' ), 1 );
			add_filter( 'wpematico_plugins_updater_args', array(__CLASS__, 'add_updater'), 10, 1);
            // add_action('loop_start', 'wpematico_rss_feed_initiation');
        }
		
		public static function add_updater($args) {
			if (empty($args['rss_feed_reader'])) {
				$args['rss_feed_reader'] = array();
				$args['rss_feed_reader']['api_url'] = WPEMATICO_RSS_FEED_READER_STORE_URL;
				$args['rss_feed_reader']['plugin_file'] = WPEMATICO_RSS_FEED_READER_ROOT_FILE;
				$args['rss_feed_reader']['api_data'] = array(
														'version' 	=> WPEMATICO_RSS_FEED_READER_VER, 				// current version number
														'item_name' => WPEMATICO_RSS_FEED_READER_ITEM_NAME, 	// name of this plugin
														'author' 	=> 'Esteban Truelsegaard'  // author of this plugin
													);
					
			}
			return $args;
		}
        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
         public static function load_textdomain() {
            // Set filter for language directory
            $lang_dir = WPEMATICO_RSS_FEED_READER_DIR . '/languages/';
            $lang_dir = apply_filters( 'rss_feed_reader_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'rss_feed_reader' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'rss_feed_reader', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/rss_feed_reader/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/rss_feed_reader/ folder
                load_textdomain( 'rss_feed_reader', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/rss_feed_reader/languages/ folder
                load_textdomain( 'rss_feed_reader', $mofile_local );
            } else {
                // Load the default language files
                load_plugin_textdomain( 'rss_feed_reader', false, $lang_dir );
            }
        }


        /**
         * Add settings
         *
         * @access      public
         * @since       1.0.0
         * @param       array $settings The existing EDD settings array
         * @return      array The modified EDD settings array
         */
        public static function settings( $settings ) {
            $new_settings = array(
                array(
                    'id'    => 'rss_feed_reader_settings',
                    'name'  => '<strong>' . __( 'Plugin Name Settings', 'rss_feed_reader' ) . '</strong>',
                    'desc'  => __( 'Configure Plugin Name Settings', 'rss_feed_reader' ),
                    'type'  => 'header',
                )
            );

            return array_merge( $settings, $new_settings );
        }
    }
} // End if class_exists check


/**
 * The main function responsible for returning the one true RSS Feed Reader
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      \WpeMatico_RSS_Feed_Reader The one true RSS Feed Reader
 *
 * @todo        Inclusion of the activation code below isn't mandatory, but
 *              can prevent any number of errors, including fatal errors, in
 *              situations where your extension is activated but EDD is not
 *              present.
 */
function Wpematico_rss_feed_reader_load() {
    if( !class_exists( 'WPeMatico' ) ) {
        if( !class_exists( 'WPeMatico_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }

        $activation = new WPeMatico_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        return WpeMatico_RSS_Feed_Reader::instance();
    }
}
add_action( 'plugins_loaded', 'Wpematico_rss_feed_reader_load',999);

/**
 * The activation hook is called outside of the singleton because WordPress doesn't
 * register the call from within the class, since we are preferring the plugins_loaded
 * hook for compatibility, we also can't reference a function inside the plugin class
 * for the activation function. If you need an activation function, put it here.
 *
 * @since       1.0.0
 * @return      void
 */
function rss_feed_reader_activation() {
    /* Activation functions here */
	if(class_exists('WPeMatico')) {
		$link= '<a href="' . admin_url("edit.php?post_type=wpematico&page=wpematico_settings&tab=rss_feed_reader") . '">'.__('RSS Feed Reader Plugin Settings.',  'rss_feed_reader')."</a>";
		$notice= __('RSS Feed Reader Activated.  Please check the fields on', 'rss_feed_reader').' '. $link;
		WPeMatico::add_wp_notice( array('text' => $notice , 'below-h2'=>false ) );
	}
}
register_activation_hook( __FILE__, 'rss_feed_reader_activation' );
