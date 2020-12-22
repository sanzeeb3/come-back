<?php

namespace ComeBack\Emails;

defined( 'ABSPATH' ) || exit;

/**
 * Come Back Email.
 *
 * @since 1.3.0
 */
class Email {

	/**
	 * Emails to be sent from.
	 *
	 * @since 1.3.0
	 * @var string.
	 */
	private $from;

	/**
	 * Subject of the email.
	 */
	private $subject;

	/**
	 * Email Message
	 * @var string.
	 */
	private $message;

	/**
	 * ComeBack Email Contructor.
	 */
	public function __construct() {

		$this->header  = array( 'Content-Type: text/html; charset=UTF-8' );
		$this->subject = get_option( 'come_back_email_subject', esc_html__( 'Come Back!', 'come-back' ) );

		$message       = 'Howdy {user_first_name}, <br/><br/>We haven\'t seen you in a while. Things are a lot different since the last time you logged into {site_name}. I\'m {name}, CEO of {site_name}. I wanted to send you a note since you have been inactive for a while. You can come back and continue your awesome works at {site_name}.<br/><br/>Please come back!';
		
		$this->message = get_option( 'come-back-email-editor', $message );
	}

	/**
	 * Send the email.
	 *
	 * @since 1.3.0
	 *
	 * @param Object $user User object.
	 *
	 * @return void.
	 */
	public function send( $user ) {

		$message = apply_filters( 'come_back_process_smart_tags', nl2br( $this->message ), $user );

		do_action( 'come_back_before_email_sent', $this );
		
		ob_start();
		
		$this->come_back_get_template( 'template.php' );

		$email = ob_get_clean();

		wp_mail( $user->user_email, $this->subject, $email, $this->header );

		do_action( 'come_back_after_email_sent', $this );

		update_user_meta( $user->ID, 'come_back_email_sent', time() );
	}

	/**
	 * Get email template passing attributes and including the file.
	 *
	 * @param string $template_name
	 * @param array  $args          (default: array())
	 * @param string $template_path (default: '')
	 * @param string $default_path  (default: '')
	 */
	public function come_back_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args );
		}

		$located = $this->come_back_locate_template( $template_name, $template_path, $default_path );

		// Allow 3rd party plugin filter template file from their plugin.
		$located = apply_filters( 'come_back_get_template', $located, $template_name, $args, $template_path, $default_path );

		if ( ! file_exists( $located ) ) {
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.0' );

			return;
		}

		do_action( 'come_backbefore_template_part', $template_name, $template_path, $located, $args );

		include $located;

		do_action( 'come_backafter_template_part', $template_name, $template_path, $located, $args );
	}

	/**
	 * Locate a template and return the path for inclusion.
	 *
	 * This is the load order:
	 *
	 *        yourtheme        /    $template_path    /    $template_name
	 *        yourtheme        /    $template_name
	 *        $default_path    /    $template_name
	 *
	 * @param string $template_name
	 * @param string $template_path (default: '')
	 * @param string $default_path  (default: '')
	 *
	 * @return string
	 */
	public function come_back_locate_template( $template_name, $template_path = '', $default_path = '' ) {
		if ( ! $template_path ) {
			$template_path = COME_BACK . '/src/Emails/templates/';
		}

		if ( ! $default_path ) {
			$default_path = COME_BACK . '/src/Emails/templates/';
		}

		// Look within passed path within the theme - this is priority.
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// Get default template/
		if ( ! $template ) {
			$template = $default_path . $template_name;
		}

		// Return what we found.
		return apply_filters( 'come_back_locate_template', $template, $template_name, $template_path );
	}
}
