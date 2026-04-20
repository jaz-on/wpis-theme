(function () {
	var root = document.documentElement;
	var key = 'wpis-theme';
	function apply(stored) {
		if (stored === 'dark' || stored === 'light') {
			root.setAttribute('data-theme', stored);
		} else {
			root.removeAttribute('data-theme');
		}
	}
	apply(localStorage.getItem(key));
	document.querySelectorAll('.wpis-theme-toggle').forEach(function (btn) {
		btn.addEventListener('click', function () {
			var dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
			var cur = root.getAttribute('data-theme');
			var effective = cur || (dark ? 'dark' : 'light');
			var next = effective === 'dark' ? 'light' : 'dark';
			root.setAttribute('data-theme', next);
			localStorage.setItem(key, next);
		});
	});
})();
