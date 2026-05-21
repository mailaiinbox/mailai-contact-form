=== Mailai Contact Form ===
Contributors: mailaiinbox
Donate link: https://mailai.in
Tags: contact form, smtp, email, mailer, forms
Requires at least: 5.8
Tested up to: 7.0
Stable tag: 1.0.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A professional-grade, lightweight contact form that allows you to bring your own SMTP to ensure 100% email deliverability.

== Description ==

Mailai Contact Form was built for founders and business owners who need a reliable way to receive leads without the bloat of traditional form builders. By allowing you to **Bring Your Own SMTP**, this plugin bypasses the often-unreliable native WordPress mail function and connects directly to your preferred provider (Amazon SES, Google Workspace, SendGrid, or private mail servers).

Built with a performance-first mindset, the form uses **Modern Vanilla JavaScript (AJAX)** to submit data without refreshing the page, providing a seamless experience for your users.

**Key Features:**
* **BYO SMTP:** Total control over your email infrastructure.
* **Zero Bloat:** No heavy CSS libraries or tracking scripts.
* **AJAX Submission:** Fast, no-refresh form processing.
* **Security Focused:** Uses WordPress Nonces and strict sanitization to protect your site.
* **SaaS Ready:** Designed to work perfectly with modern business environments.

== Installation ==

1. Upload the `mailai-contact-form` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Settings > Mailai SMTP** to enter your credentials.
4. Use the shortcode `[mailai_form]` on any page or post.

== Frequently Asked Questions ==

= Does this plugin store my SMTP password securely? =
Yes. Credentials are saved locally in your WordPress database and are never transmitted to any external servers other than your chosen SMTP host.

= Do I need a specific SMTP provider? =
No. You can use any provider that supports standard SMTP protocols (Host, Port, Username, Password).

= Can I customize the look of the form? =
To keep the plugin lightweight, the form inherits your theme's default styles. You can easily add custom CSS to your theme's stylesheet to tweak the appearance.

== Screenshots ==

1. The Mailai SMTP Configuration dashboard.
2. The sleek, AJAX-powered contact form in action.

== Changelog ==
= 1.0.1 =
* Tested and optimized for WordPress 7.0 compatibility.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
This is the initial release. No upgrade action is required.
