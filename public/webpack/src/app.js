import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import Clock from './js/Clock';
import './scss/global.scss';

// 時計.
new Clock();

// タブ.
( function( Vue ) {
	new Vue({
		el: '#app',
		data: {
			isActive: '1'
		},
		methods: {
			change: function( num ) {
				this.isActive = num;
			}
		}
	});
})( window.Vue );
