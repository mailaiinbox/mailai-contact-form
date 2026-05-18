<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Mailai_SMTP_Handler {

	public function __construct() {
		add_action( 'phpmailer_init', [ $this, 'configure_smtp' ], 999 );
	}

	public function configure_smtp( $phpmailer ) {
		$host = get_option( 'mailai_host' );
		if ( empty( $host ) ) return;

		$phpmailer->isSMTP();
		$phpmailer->Host       = sanitize_text_field( $host );
		$phpmailer->SMTPAuth   = true;
		$phpmailer->Port       = absint( get_option( 'mailai_port', 587 ) );
		$phpmailer->Username   = sanitize_text_field( get_option( 'mailai_user' ) );
		$phpmailer->Password   = get_option( 'mailai_pass' ); 

		$encryption = sanitize_text_field( get_option( 'mailai_enc', 'tls' ) );
		if ( in_array( $encryption, [ 'tls', 'ssl' ], true ) ) {
			$phpmailer->SMTPSecure = $encryption;
		} else {
			$phpmailer->SMTPAutoTLS = false;
			$phpmailer->SMTPSecure  = '';
		}

		// FIX 1: Enforce the "From" email to prevent SPAM / Brevo overrides
		$from_email = sanitize_email( get_option( 'mailai_from_email' ) );
		
		if ( is_email( $from_email ) ) {
			$site_name = get_option( 'blogname' );
			$phpmailer->setFrom( $from_email, sanitize_text_field( $site_name ) );
		}

		$phpmailer->Timeout = 15; 
	}
}