<?php

namespace ComeBack;

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
	 * WP Inactive Delete Account Constructor.
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
		$email_subject          = get_option( 'come_back_email_subject', esc_html__( 'Come Back!', 'come-back' ) );
		$inactivity_period      = get_option( 'come_back_inactivity_period', 90 );


		$message       = 'We haven\'t seen you in a while. Things are a lot different since the last time you logged into {site_name}. I\'m {name}, CEO of {site_name}. I wanted to send you a note since you have been inactive for a while. You can come back and continue your awesome works at {site_name}.<br/><br/>Please come back!';

		$users = get_users();   // @TODO:: Improve query based on results.

		foreach ( $users as $user ) {
		
			$last_login           = get_user_meta( $user->ID, 'last_login', true );
			$come_back_email_sent = get_user_meta( $user->ID, 'come_back_email_sent', true );
			
			$email_message = get_option( 'come-back-email-editor', $message );
			$email_message = apply_filters( 'come_back_process_smart_tags', $email_message, $user );

			// Condition 1: Last login time is less than the current time minus the inactivity days to send emails.
			// Condition 2: Come Back email is already sent. Send it again after 30 days, if the user do not log in again after the email is sent.
			// Condition 3: If there is no last_login, send email based on plugin activation date. For inactive users before Come Back Installation.

			if ( ! empty( $last_login ) && $last_login < ( strtotime( '-' . $inactivity_period . 'day' ) )
				|| ( ! empty( $come_back_email_sent ) && $come_back_email_sent < ( strtotime( '- 30 day' ) ) ) && ( ! empty( $last_login ) && $last_login < ( strtotime( '- 30 day' ) ) )
				|| empty( $last_login ) && $plugin_activation_date < ( strtotime( '-' . $inactivity_period . 'day' ) )
			) {

				update_user_meta( $user->ID, 'come_back_email_sent', time() );

				$headers = array( 'Content-Type: text/html; charset=UTF-8' );

				wp_mail( $user->user_email, $email_subject, nl2br( $email_message ), $headers );
			}
		}
	}

	/**
	 * Process smart tags.
	 *
	 * @todo :: test and optimize the performance wherever possible.
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