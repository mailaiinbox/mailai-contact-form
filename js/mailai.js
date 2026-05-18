/**
 * Mailai Contact Form - AJAX Handler
 * Handles form submissions without reloading the page.
 */
document.addEventListener('DOMContentLoaded', function() {
    // 1. Select the form elements
	const form = document.getElementById('mailai-contact-form');
	const responseDiv = document.getElementById('mailai-form-response');
	const submitBtn = document.getElementById('mailai-submit-btn');

    // If the form isn't on this page, stop running the script to save memory.
	if (!form) return;

    // 2. Listen for the submit event
	form.addEventListener('submit', function(e) {
		e.preventDefault(); // Stop the default page reload

	// 3. UI Updates: Disable button and show loading state to prevent double-clicks
		responseDiv.style.display = 'none';
		responseDiv.className = '';
		submitBtn.disabled = true;

		const originalBtnText = submitBtn.innerText;
		submitBtn.innerText = 'Sending...';

	// 4. Gather the form data and the target URL (admin-ajax.php)
		const formData = new FormData(form);
		const actionUrl = form.getAttribute('action');

	// 5. Send the AJAX request securely
		fetch(actionUrl, {
			method: 'POST',
				body: formData,
				headers: {
					'Accept': 'application/json' // Tell the server we expect a JSON response
				}
		})
				.then(response => response.json())
				.then(data => {
			responseDiv.style.display = 'block';

			if (data.success) {
		// Success: Green stylish message and clear the form
				responseDiv.innerHTML = `<div style="color: #155724; background-color: #d4edda; padding: 12px; border-radius: 6px; border: 1px solid #c3e6cb; margin-top: 10px;">${data.data.message}</div>`;
				form.reset(); 
			} else {
		// Error: Red stylish message (e.g., failed Nonce or missing fields)
				responseDiv.innerHTML = `<div style="color: #721c24; background-color: #f8d7da; padding: 12px; border-radius: 6px; border: 1px solid #f5c6cb; margin-top: 10px;">${data.data.message}</div>`;
			}
		})
				.catch(error => {
	    // Network Failure Catch
			responseDiv.style.display = 'block';
			responseDiv.innerHTML = `<div style="color: #721c24; background-color: #f8d7da; padding: 12px; border-radius: 6px; border: 1px solid #f5c6cb; margin-top: 10px;">A network error occurred. Please check your connection and try again.</div>`;
		})
				.finally(() => {
	    // 6. Reset UI: Re-enable the button regardless of success or failure
			submitBtn.disabled = false;
			submitBtn.innerText = originalBtnText;
		});
	});
});