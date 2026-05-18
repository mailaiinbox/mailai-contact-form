<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Mailai_Admin_Settings {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_plugin_page' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_init', [ $this, 'handle_test_email' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( MAILAI_PLUGIN_DIR . 'mailai-contact-form.php' ), [ $this, 'add_settings_link' ] );
	}

	public function add_plugin_page() {
		add_options_page( 'Mailai SMTP', 'Mailai SMTP', 'manage_options', 'mailai-smtp', [ $this, 'render_admin_page' ] );
	}

	public function register_settings() {
		$option_group = 'mailai_settings_group';
		register_setting( $option_group, 'mailai_host', 'sanitize_text_field' );
		register_setting( $option_group, 'mailai_port', 'absint' );
		register_setting( $option_group, 'mailai_user', 'sanitize_text_field' );
		register_setting( $option_group, 'mailai_pass', 'sanitize_text_field' );
		register_setting( $option_group, 'mailai_enc', 'sanitize_text_field' );
		register_setting( $option_group, 'mailai_recipients', 'sanitize_text_field' ); 
		register_setting( $option_group, 'mailai_from_email', 'sanitize_email' ); 
	}

	public function handle_test_email() {
		if ( isset( $_POST['mailai_test_submit'] ) && current_user_can( 'manage_options' ) ) {
			check_admin_referer( 'mailai_test_email_nonce' );
			
			// FIX: Check if isset before sanitizing
			$test_email = isset( $_POST['mailai_test_email_address'] ) ? sanitize_email( wp_unslash( $_POST['mailai_test_email_address'] ) ) : '';
			
			if ( empty( $test_email ) ) {
				add_settings_error( 'mailai_messages', 'mailai_test_error', 'Please provide a valid test email address.', 'error' );
				return;
			}

			$subject = 'Mailai SMTP Test Successful';
			$message = 'If you are reading this, your Mailai Contact Form SMTP is configured perfectly!';

			if ( wp_mail( $test_email, $subject, $message ) ) {
				add_settings_error( 'mailai_messages', 'mailai_test_success', 'Test email sent successfully to ' . $test_email, 'success' );
			} else {
				add_settings_error( 'mailai_messages', 'mailai_test_error', 'Test email failed. Please check your SMTP settings.', 'error' );
			}
		}
	}

	public function render_admin_page() {
		if ( ! current_user_can( 'manage_options' ) ) return;

		$host = get_option( 'mailai_host', '' );
		$port = get_option( 'mailai_port', 587 );
		$user = get_option( 'mailai_user', '' );
		$pass = get_option( 'mailai_pass', '' );
		$enc  = get_option( 'mailai_enc', 'tls' );
		
		// Removed defaults so it shows placeholders instead
		$recs = get_option( 'mailai_recipients', '' );
		$from = get_option( 'mailai_from_email', '' );
		?>
		<div class="wrap">
			<h1>Mailai SMTP Configuration</h1>
			<?php settings_errors( 'mailai_messages' ); ?>

			<div style="background: #fff; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; max-width: 800px; margin-top: 20px;">
				<h2>1. Core Settings</h2>
				<form method="post" action="options.php">
					<?php settings_fields( 'mailai_settings_group' ); ?>
					<table class="form-table" role="presentation">
						<tbody>
							<tr>
								<th scope="row"><label for="mailai_host">SMTP Host</label></th>
								<td><input name="mailai_host" type="text" id="mailai_host" value="<?php echo esc_attr( $host ); ?>" class="regular-text" placeholder="smtp.example.com" /></td>
							</tr>
							<tr>
								<th scope="row"><label for="mailai_port">SMTP Port</label></th>
								<td><input name="mailai_port" type="number" id="mailai_port" value="<?php echo esc_attr( $port ); ?>" class="small-text" /></td>
							</tr>
							<tr>
								<th scope="row"><label for="mailai_enc">Encryption</label></th>
								<td>
									<select name="mailai_enc" id="mailai_enc">
										<option value="none" <?php selected( $enc, 'none' ); ?>>None</option>
										<option value="ssl" <?php selected( $enc, 'ssl' ); ?>>SSL</option>
										<option value="tls" <?php selected( $enc, 'tls' ); ?>>TLS</option>
									</select>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="mailai_user">SMTP Username</label></th>
								<td><input name="mailai_user" type="text" id="mailai_user" value="<?php echo esc_attr( $user ); ?>" class="regular-text" /></td>
							</tr>
							<tr>
								<th scope="row"><label for="mailai_pass">SMTP Password</label></th>
								<td><input name="mailai_pass" type="password" id="mailai_pass" value="<?php echo esc_attr( $pass ); ?>" class="regular-text" /></td>
							</tr>
                            <tr><td colspan="2"><hr></td></tr>
                            <tr>
								<th scope="row"><label for="mailai_from_email"><strong>Sender (From) Email</strong></label></th>
								<td>
									<input name="mailai_from_email" type="email" id="mailai_from_email" value="<?php echo esc_attr( $from ); ?>" class="regular-text" placeholder="mail@yoursite.com" />
									<p class="description" style="color: #d63638;"><strong>Crucial for avoiding Spam!</strong> This MUST be an email authorized by your SMTP provider.</p>
								</td>
							</tr>
							<tr>
								<th scope="row"><label for="mailai_recipients"><strong>Receiver Email IDs</strong></label></th>
								<td>
									<input name="mailai_recipients" type="text" id="mailai_recipients" value="<?php echo esc_attr( $recs ); ?>" class="large-text" placeholder="mail@yoursite.com, support@yoursite.com" />
									<p class="description">Where should submissions go? Separate multiple emails with a comma.</p>
								</td>
							</tr>
						</tbody>
					</table>
					<?php submit_button( 'Save Settings' ); ?>
				</form>
			</div>

			<div style="background: #f0f0f1; padding: 20px; border: 1px solid #ccd0d4; border-radius: 4px; max-width: 800px; margin-top: 20px;">
				<h2>2. Send Test Email</h2>
				<form method="post" action="">
					<input type="hidden" name="mailai_test_submit" value="1">
					<?php wp_nonce_field( 'mailai_test_email_nonce' ); ?>
					<input type="email" name="mailai_test_email_address" placeholder="Enter email to test" required class="regular-text">
					<?php submit_button( 'Send Test Email', 'secondary', 'submit', false ); ?>
				</form>
			</div>

            <div style="margin-top: 30px; max-width: 800px;">
				<h2>3. Display the Form</h2>
				<p>To display your contact form, paste this shortcode onto any page, post, or widget:</p>
				<code style="font-size: 16px; padding: 10px; display: inline-block; background: #fff; border: 1px solid #ccc;">[mailai_form]</code>
			</div>
		</div>
		<?php
	}

	public function add_settings_link( $links ) {
		array_unshift( $links, '<a href="options-general.php?page=mailai-smtp">Settings</a>' );
		return $links;
	}
}