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
	 *
	 * @var string.
	 */
	private $message;

	/**
	 * ComeBack Email Contructor.
	 */
	public function __construct() {

		$this->header  = array( 'Content-Type: text/html; charset=UTF-8' );
		$this->subject = get_option( 'come_back_email_subject', esc_html__( 'Come Back!', 'come-back' ) );

		$message = 'Howdy {user_first_name}, <br/><br/>We haven\'t seen you in a while. Things are a lot different since the last time you logged into {site_name}. I\'m {name}, CEO of {site_name}. I wanted to send you a note since you have been inactive for a while. You can come back and continue your awesome works at {site_name}.<br/><br/>Please come back!';

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
	public function send( $email_address, $user ) {

		$message = apply_filters( 'come_back_process_smart_tags', nl2br( $this->message ), $user );

		do_action( 'come_back_before_email_sent', $this );

		ob_start();

		// Allow themes to override the template.
		$template = locate_template(
			'come-back/template.php'
		);

	    // Themes's template should be given the priority.
		if ( ! file_exists( $template ) ) {
			$template = 'templates/template.php';
		}

		include $template;

		$email = ob_get_clean();

		$sent = wp_mail( $email_address, $this->subject, $email, $this->header );

		do_action( 'come_back_after_email_sent', $this );

		update_user_meta( $user->ID, 'come_back_email_sent', time() );

		return $sent;
	}
}
