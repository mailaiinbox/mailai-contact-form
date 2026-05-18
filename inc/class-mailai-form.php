<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Mailai_Form_Handler {

	public function __construct() {
		add_shortcode( 'mailai_form', [ $this, 'render_form' ] );
		add_action( 'wp_ajax_mailai_submit_form', [ $this, 'handle_submission' ] );
		add_action( 'wp_ajax_nopriv_mailai_submit_form', [ $this, 'handle_submission' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
	}

	public function load_scripts() {
		wp_enqueue_script( 'mailai-js', MAILAI_PLUGIN_URL . 'js/mailai.js', [], MAILAI_VERSION, true );
	}

	public function render_form() {
		ob_start();
		?>
		<div class="mailai-form-container">
			<form id="mailai-contact-form" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="POST">
				<?php wp_nonce_field( 'mailai_form_nonce_action', 'mailai_form_nonce' ); ?>
				<input type="hidden" name="action" value="mailai_submit_form">

				<div class="mailai-field-group">
					<label for="mailai-name">Name</label><br/>
					<input type="text" id="mailai-name" name="mailai_name" required style="width:100%; margin-bottom:10px;">
				</div>

				<div class="mailai-field-group">
					<label for="mailai-email">Email</label><br/>
					<input type="email" id="mailai-email" name="mailai_email" required style="width:100%; margin-bottom:10px;">
				</div>

				<div class="mailai-field-group">
					<label for="mailai-subject">Subject</label><br/>
					<input type="text" id="mailai-subject" name="mailai_subject" required style="width:100%; margin-bottom:10px;">
				</div>

				<div class="mailai-field-group">
					<label for="mailai-message">Message</label><br/>
					<textarea id="mailai-message" name="mailai_message" rows="4" required style="width:100%; margin-bottom:10px;"></textarea>
				</div>

				<button type="submit" id="mailai-submit-btn" style="padding: 10px 20px; cursor: pointer;">Send Message</button>
				
				<div id="mailai-form-response" style="display:none; margin-top:15px;"></div>
			</form>
		</div>
		<?php
		return ob_get_clean();
	}

	public function handle_submission() {
		if ( ! isset( $_POST['mailai_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['mailai_form_nonce'] ) ), 'mailai_form_nonce_action' ) ) {
			wp_send_json_error( [ 'message' => 'Security check failed. Please refresh the page.' ] );
		}

		$name    = isset( $_POST['mailai_name'] ) ? sanitize_text_field( wp_unslash( $_POST['mailai_name'] ) ) : '';
		$email   = isset( $_POST['mailai_email'] ) ? sanitize_email( wp_unslash( $_POST['mailai_email'] ) ) : '';
		$subject = isset( $_POST['mailai_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['mailai_subject'] ) ) : 'New Submission';
		$message = isset( $_POST['mailai_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mailai_message'] ) ) : '';

		if ( empty( $name ) || empty( $email ) || empty( $subject ) || empty( $message ) || ! is_email( $email ) ) {
			wp_send_json_error( [ 'message' => 'Please fill in all fields correctly.' ] );
		}

		$recipients_setting = get_option( 'mailai_recipients' );
		$to = [];
		if ( ! empty( $recipients_setting ) ) {
			$raw_emails = explode( ',', $recipients_setting );
			foreach ( $raw_emails as $raw_email ) {
				$clean_email = sanitize_email( trim( $raw_email ) );
				if ( is_email( $clean_email ) ) {
					$to[] = $clean_email;
				}
			}
		}
		
		if ( empty( $to ) ) {
			$to = get_option( 'admin_email' );
		}

		$email_subject = 'Contact Form: ' . $subject;
		
		// THE FIX: Explicitly set HTML Content-Type and proper Reply-To formatting
		$headers = [ 
			'Content-Type: text/html; charset=UTF-8',
			'Reply-To: ' . sanitize_text_field( $name ) . ' <' . sanitize_email( $email ) . '>' 
		];

		// THE FIX: Format the email as a high-trust HTML table
		$body = "
		<html>
		<head>
			<title>New Contact Form Submission</title>
		</head>
		<body style='font-family: Arial, sans-serif; color: #333; line-height: 1.6;'>
			<div style='max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;'>
				<div style='background-color: #f8f9fa; padding: 20px; border-bottom: 1px solid #e0e0e0;'>
					<h2 style='margin: 0; color: #2271b1; font-size: 20px;'>New Website Submission</h2>
				</div>
				<div style='padding: 20px;'>
					<table style='width: 100%; border-collapse: collapse;'>
						<tr>
							<td style='padding: 10px 0; border-bottom: 1px solid #eee; width: 100px;'><strong>Name:</strong></td>
							<td style='padding: 10px 0; border-bottom: 1px solid #eee;'>" . esc_html( $name ) . "</td>
						</tr>
						<tr>
							<td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Email:</strong></td>
							<td style='padding: 10px 0; border-bottom: 1px solid #eee;'><a href='mailto:" . esc_attr( $email ) . "' style='color: #2271b1;'>" . esc_html( $email ) . "</a></td>
						</tr>
						<tr>
							<td style='padding: 10px 0; border-bottom: 1px solid #eee;'><strong>Subject:</strong></td>
							<td style='padding: 10px 0; border-bottom: 1px solid #eee;'>" . esc_html( $subject ) . "</td>
						</tr>
						<tr>
							<td style='padding: 20px 0 10px 0;' colspan='2'><strong>Message:</strong></td>
						</tr>
						<tr>
							<td colspan='2' style='background: #f4f4f4; padding: 15px; border-radius: 6px; white-space: pre-wrap;'>" . esc_html( $message ) . "</td>
						</tr>
					</table>
				</div>
			</div>
		</body>
		</html>";

		if ( wp_mail( $to, $email_subject, $body, $headers ) ) {
			wp_send_json_success( [ 'message' => 'Message sent successfully!' ] );
		} else {
			wp_send_json_error( [ 'message' => 'There was a problem sending your message.' ] );
		}
	}
}
