(() => {
	'use strict';

	let enabledInput;
	let isEnabled;
	let enabledWrapper;

	const noticeInit = () => {
		enabledInput = document.querySelector('#enabled');

		// find closest <tr> parent element
		enabledWrapper = enabledInput.closest('tr'); // assign enabledWrapper here

		// check if input is :checked, set as true or false
		isEnabled = enabledInput.checked;

		// noticeToggle
		noticeToggle(isEnabled );

		enabledInput.addEventListener('click', () => {
			// noticeToggle
			noticeToggle(isEnabled );
		});
	};

	const noticeToggle = ($enabled) => {
		if (enabledInput.checked) {
			enabledWrapper.classList.add('is-enabled');
		} else {
			enabledWrapper.classList.remove('is-enabled');
		}
	};

	document.addEventListener('DOMContentLoaded', () => {
		noticeInit();
	});
})();
