<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

$mailai_options = [
	'mailai_host',
	'mailai_port',
	'mailai_user',
	'mailai_pass',
	'mailai_enc',
	'mailai_recipients',
	'mailai_from_email'
];

foreach ( $mailai_options as $mailai_option ) {
	delete_option( $mailai_option );
}

wp_cache_flush();