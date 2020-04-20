<?php
/**
 * Plugin Name: Meth Assistant
 * Plugin URI: https://github.com/Codestag/meth-assistant
 * Description: A plugin to assist Meth theme in adding widgets.
 * Author: Codestag
 * Author URI: https://codestag.com
 * Version: 1.0
 * Text Domain: meth-assistant
 * License: GPL2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package Meth
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Meth_Assistant' ) ) :
	/**
	 * Meth_Assistant Base Plugin Class.
	 *
	 * @since 1.0
	 */
	class Meth_Assistant {

		/**
		 * Base instance property.
		 *
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * Registers a plugin instance & loads required methods.
		 *
		 * @since 1.0
		 */
		public static function register() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Meth_Assistant ) ) {
				self::$instance = new Meth_Assistant();
				self::$instance->init();
				self::$instance->define_constants();
				self::$instance->includes();
			}
		}

		/**
		 * Initialize plugin hooks.
		 *
		 * @since 1.0
		 */
		public function init() {
			add_action( 'enqueue_assets', 'plugin_assets' );
		}

		/**
		 * Registers constants.
		 *
		 * @since 1.0
		 */
		public function define_constants() {
			$this->define( 'MA_VERSION', '1.0' );
			$this->define( 'MA_DEBUG', true );
			$this->define( 'MA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			$this->define( 'MA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Checks & defines undefined constants.
		 *
		 * @param string $name
		 * @param string $value
		 * @since 1.0
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Loads required files.
		 *
		 * @since 1.0
		 */
		public function includes() {
			require_once MA_PLUGIN_PATH . 'includes/helpers.php';
			require_once MA_PLUGIN_PATH . 'includes/widgets/intro.php';
			require_once MA_PLUGIN_PATH . 'includes/widgets/features.php';
			require_once MA_PLUGIN_PATH . 'includes/widgets/feature-box.php';
			require_once MA_PLUGIN_PATH . 'includes/widgets/static-content.php';
			require_once MA_PLUGIN_PATH . 'includes/widgets/portfolio.php';
			require_once MA_PLUGIN_PATH . 'includes/widgets/team.php';
			require_once MA_PLUGIN_PATH . 'includes/widgets/blog.php';
			require_once MA_PLUGIN_PATH . 'includes/widgets/contact.php';
			require_once MA_PLUGIN_PATH . 'includes/widgets/testimonials.php';

			if ( is_admin() ) {
				require_once MA_PLUGIN_PATH . 'includes/stag-admin-metabox.php';
				require_once MA_PLUGIN_PATH . 'includes/meta/portfolio.php';
				require_once MA_PLUGIN_PATH . 'includes/meta/team.php';
			}
		}

		/**
		 * Enqueue required scripts and styles.
		 *
		 * @param string $hook Current page slug.
		 *
		 * @since 1.0
		 * @return void
		 */
		public function scripts_and_styles( $hook ) {
			if ( 'post.php' === $hook || 'post-new.php' === $hook ) {
				wp_enqueue_media();
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'stag-admin-metabox', MA_PLUGIN_URL . 'assets/css/stag-admin-metabox.css', array('wp-color-picker'), MA_VERSION, 'screen' );
			}
		}

	}
endif;


/**
 * Registers base class instance.
 *
 * @since 1.0
 */
function meth_assistant() {
	return Meth_Assistant::register();
}

/**
 * Plugin activation notice.
 *
 * @since 1.0
 */
function meth_assistant_activation_notice() {
	echo '<div class="error"><p>';
	echo esc_html__( 'Meth Assistant requires Meth WordPress Theme to be installed and activated.', 'meth-assistant' );
	echo '</p></div>';
}

/**
 * Plugin activation check.
 *
 * @since 1.0
 */
function meth_assistant_activation_check() {
	$theme = wp_get_theme(); // gets the current theme
	if ( 'Meth' === $theme->name || 'Meth' === $theme->parent_theme ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			add_action( 'after_setup_theme', 'meth_assistant' );
		} else {
			meth_assistant();
		}
	} else {
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		deactivate_plugins( plugin_basename( __FILE__ ) );
		add_action( 'admin_notices', 'meth_assistant_activation_notice' );
	}
}

// Plugin loads.
meth_assistant_activation_check();
