<?php

namespace ComeBack;

use ComeBack\Emails\Email;

defined( 'ABSPATH' ) || exit;   // Exit if accessed directly.

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
	 * Come Back Constructor.
	 */
	public function __construct() {

		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'init', array( $this, 'update_last_login' ) );
		add_action( 'init', array( $this, 'schedule_notification' ) );
		add_action( 'init', array( $this, 'register_admin_area' ) );
		add_action( 'come_back_process_smart_tags', array( $this, 'process_smart_tags' ), 10, 2 );
		add_action( 'cb_schedule_notification', array( $this, 'process_send' ) );
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
	 * Register admin area.
	 *
	 * @since 1.1.0
	 */
	public function register_admin_area() {

		$settings = new Settings();
		$settings->init();
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
	public function schedule_notification() {

		if ( false === as_next_scheduled_action( 'cb_schedule_notification' ) ) {
			as_schedule_recurring_action( strtotime( '+ 1 day' ), DAY_IN_SECONDS, 'cb_schedule_notification', array(), 'come_back' );
		}
	}

	/**
	 * Process Sending Inactive Notification.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function process_send() {

		$plugin_activation_date = get_option( 'come_back_activation_date' );
		$inactivity_period      = get_option( 'come_back_inactivity_period', 90 );

		$users = get_users();   // @TODO:: Improve query based on results.

		foreach ( $users as $user ) {

			$last_login           = get_user_meta( $user->ID, 'last_login', true );
			$come_back_email_sent = get_user_meta( $user->ID, 'come_back_email_sent', true );

			// Last login time is less than the current time minus the inactivity days to send emails.
			// Come Back email is already sent. Send it again after 30 days, if the user do not log in again after the email is sent.
			// If there is no last_login, send email based on plugin activation date. For inactive users before Come Back Installation.
			if ( ! empty( $last_login ) && $last_login < ( strtotime( '-' . $inactivity_period . 'day' ) )
				|| ( ! empty( $come_back_email_sent ) && $come_back_email_sent < ( strtotime( '- 30 day' ) ) ) && ( ! empty( $last_login ) && $last_login < ( strtotime( '- 30 day' ) ) )
				|| empty( $last_login ) && $plugin_activation_date < ( strtotime( '-' . $inactivity_period . 'day' ) )
			) {

				$email = new Email();
				$email->send( $user );
			}
		}
	}

	/**
	 * Process smart tags.
	 *
	 * @todo :: test and optimize the performance wherever possible.
	 *
	 * @param  string $content The raw subject or email message.
	 * @param  object $user The user object.
	 *
	 * @return Email Subject or Email message.
	 *
	 * @since 1.0.0
	 */
	public function process_smart_tags( $content, $user ) {

		$content = str_replace( '{site_name}', get_bloginfo(), $content );
		$content = str_replace( '{user_first_name}', get_user_meta( $user->ID, 'fist_name', true ), $content );
		$content = str_replace( '{user_last_name}', get_user_meta( $user->ID, 'last_name', true ), $content );

		return $content;
	}
}