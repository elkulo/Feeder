/**
 * View.js
 */
import Vue from 'vue';
import axios from 'axios';

class View {

	/**
	 * constructor
	 *
	 */
	constructor() {
		this.run = this.run.bind( this );
		document.addEventListener(
			'DOMContentLoaded',
			() => {
				this.run();
			},
			false
		);
	}

	/**
	 * run
	 *
	 */
	run() {

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
					// eslint-disable-next-line no-console
					console.log( text );
				},
			},
			delimiters: [ '${', '}' ]
		});
	}
}
export default View;
