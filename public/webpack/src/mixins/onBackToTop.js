// トップへ戻る
import smoothscroll from 'smoothscroll-polyfill';

export default {
	methods: {
		onBackToTop: function() {
			smoothscroll.polyfill(); // Safari Polyfill.
			window.scrollTo({
				behavior: 'smooth',
				top: 0,
			});
		},
	}
};
