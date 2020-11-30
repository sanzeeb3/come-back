<?php

namespace ComeBack;

defined( 'ABSPATH' ) || exit;	// Exit if accessed directly.

/**
 * Plugin Class.
 *
 * @since 1.0.0
 */
final class Plugin {

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * WP Inactive Delete Account Constructor.
	 */
	public function __construct() {

		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'update_last_login' ) );
		add_action( 'init', array( $this, 'schedule_notification' ) );
		add_action( 'cb_schedule_notification', array( $this, 'send' ) );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Locales found in:
	 *      - WP_LANG_DIR/come-back/come-back-LOCALE.mo
	 *      - WP_LANG_DIR/plugins/come-back-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'come-back' );

		load_textdomain( 'come-back', WP_LANG_DIR . '/come-back/come-back-' . $locale . '.mo' );
		load_plugin_textdomain( 'come-back', false, plugin_basename( dirname( COME_BACK ) ) . '/languages' );
	}

	/**
	 * Store last login info in usermeta table.
	 *
	 * @since  1.0.0
	 *
	 * @return void.
	 */
	public function update_last_login() {

		$user_id = get_current_user_id();

		if ( $user_id ) {
			update_user_meta( $user_id, 'last_login', time() ); 
		}
	}

	/**
	 * Schedule to send inactive notification.
	 *
	 * @since  1.0.0
	 * 
	 * @return String
	 */
	public function schedule_notification( ) {

		if ( false === as_next_scheduled_action( 'cb_schedule_notification' ) ) { 
			as_schedule_recurring_action( strtotime( '+ 1 day' ), DAY_IN_SECONDS, 'cb_schedule_notification', array(), 'come_back' );
		}
	}

	/**
	 * Delete Inactive Accounts.
	 *
	 * @since  1.0.0
	 * 
	 * @return string
	 */
	public function send() {

	}
}
