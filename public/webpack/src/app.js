import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './scss/global.scss';
import axios from 'axios';
import Clock from './js/Clock';

// 時計.
new Clock();

// タブ.
( function( Vue ) {

	new Vue({
		el: '#app',
		data: {
			isActive: 1,
			feeds: []
		},
		mounted() {
			axios.get( '/api/v1/posts' ).then( ( response ) => {
				this.feeds = response.data;
			});
		},
		methods: {
			change: function( num ) {
				this.isActive = num;
			},
			debug: function( text ) {
				console.log( text );
			},
		},
		delimiters: [ '${', '}' ]
	});
})( window.Vue );
