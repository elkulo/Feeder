// Vueでページスクロールイベント
export default {
	mounted() {
		if ( ! this.$options.onScrollEvent ) {
			return;
		}
		requestAnimationFrame( () => {
			this.$options.onScrollEvent = this.$options.onScrollEvent.bind( this );
			window.addEventListener( 'scroll', this.$options.onScrollEvent );
		});
	},
	destroyed() {
		if ( ! this.$options.onScrollEvent ) {
			return;
		}
		window.removeEventListener( 'scroll', this.$options.onScrollEvent );
	},
};
