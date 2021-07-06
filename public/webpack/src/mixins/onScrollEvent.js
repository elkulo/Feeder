// Vueでページスクロールイベント
export default {
	mounted() {
		if ( ! this.$options.onScrollEvent ) {
			return;
		}
		requestAnimationFrame( () => {

			// ページ遷移の際にVueRouterが実行するy=0へのスクロールを無視するための処理です
			/*
			if ( this._isDestroyed ) {
				return;
			}
			*/

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
