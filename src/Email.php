<?php

namespace ComeBack;

defined( 'ABSPATH' ) || exit;

/**
 * Come Back Email.
 *
 * @since 1.3.0
 */
class Email {

	/**
	 * ComeBack Email Contructor.
	 */
	public function __construct() {

	}

	/**
	 * Send the email.
	 *
	 * @since 1.3.0
	 *
	 * @param string $to          The To address.
	 * @param string $subject     The subject line of the email.
	 * @param string $message     The body of the email.
	 * @param array  $attachments Attachments to the email.
	 *
	 * @return bool
	 */
	public function send() {

		do_action( 'come_back_before_email_sent', $this );

		wp_mail( $to, $subject, $message, $header, $attachments );

		do_action( 'come_back_after_email_sent', $this );
	}
}
