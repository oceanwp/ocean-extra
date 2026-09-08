/**
 * OceanWP Current User shortcode.
 *
 */
(function () {
	'use strict';

	function oceanwpCurrentUserInit() {
		var elements = document.querySelectorAll(
			'.oceanwp-current-user-sensinfo[data-oceanwp-user-field]'
		);

		if (
			! elements.length ||
			typeof oceanwpCurrentUser === 'undefined'
		) {
			return;
		}

		var validElements = [];

		elements.forEach(function (element) {
			var field = element.getAttribute('data-oceanwp-user-field');

			if (
				field !== 'user_email' &&
				field !== 'user_login'
			) {
				return;
			}

			validElements.push({
				element: element,
				field: field
			});
		});

		if (!validElements.length) {
			return;
		}

		var body = new URLSearchParams();

		body.append('action', 'oceanwp_current_user_data');
		body.append('nonce', oceanwpCurrentUser.nonce);

		fetch(oceanwpCurrentUser.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: body.toString()
		})
			.then(function (response) {
				if (!response.ok) {
					throw new Error('Current user request failed.');
				}

				return response.json();
			})
			.then(function (response) {
				if (
					! response.success ||
					! response.data
				) {
					return;
				}

				validElements.forEach(function (item) {
					var value = response.data[item.field];

					if (typeof value !== 'string') {
						return;
					}

					/**
					 * Always insert returned account data as plain text.
					 *
					 * Never change this to innerHTML.
					 */
					item.element.textContent = value;
				});
			})
			.catch(function () {
				/**
				 * Fail silently.
				 */
			});
	}

	if (document.readyState === 'loading') {
		document.addEventListener(
			'DOMContentLoaded',
			oceanwpCurrentUserInit
		);
	} else {
		oceanwpCurrentUserInit();
	}
})();