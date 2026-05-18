<?php
/**
 * Plugin Name:       Mailai Contact Form
 * Description:       A professional, lightweight contact form allowing you to bring your own SMTP. Built for performance and reliability.
 * Version:           1.0.0
 * Author:            Sanjeev
 * Author URI:        https://mailai.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mailai-contact-form
 */

if ( ! defined( 'WPINC' ) ) {
	die( 'Direct access is not permitted.' );
}

define( 'MAILAI_VERSION', '1.0.0' );
define( 'MAILAI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MAILAI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

function mailai_load_dependencies() {
	require_once MAILAI_PLUGIN_DIR . 'inc/class-mailai-smtp.php';
	require_once MAILAI_PLUGIN_DIR . 'inc/class-mailai-form.php';
	if ( is_admin() ) {
		require_once MAILAI_PLUGIN_DIR . 'admin/class-mailai-settings.php';
	}
}

function mailai_init() {
	new Mailai_SMTP_Handler();
	new Mailai_Form_Handler();
	if ( is_admin() ) {
		new Mailai_Admin_Settings();
	}
}
add_action( 'plugins_loaded', 'mailai_init' );

function mailai_activate() {
	if ( ! get_option( 'mailai_port' ) ) {
		add_option( 'mailai_port', 587 );
	}
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'mailai_activate' );

function mailai_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'mailai_deactivate' );

mailai_load_dependencies();