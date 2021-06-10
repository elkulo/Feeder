import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './app.scss';
import Vue from 'vue';
import axios from 'axios';

const API_PATH = '/api/v1/posts';

new Vue({
	el: '#app',
	data: {
		isActiveTab: 1,
		feeds: [],
		clock: {
			day: '',
			time: ''
		}
	},
	mounted() {
		this.currentDay();
		this.currentTime();
		axios.get( API_PATH ).then( ( response ) => {
			this.feeds = response.data;
		}).catch( ( error ) => {
			// eslint-disable-next-line no-console
			console.error( '読み込み失敗', error );
		}).finally( () => {
			// eslint-disable-next-line no-console
			console.info( 'ローディング表示終了' );
			document.querySelector( '#app' ).classList.add( 'loaded' );
		});
	},
	methods: {
		debug: function( text ) {
			// eslint-disable-next-line no-console
			console.log( text );
		},
		changeTab: function( num ) {
			this.isActiveTab = num;
		},
		currentDay: function() {
			const DaysDate = new Date();
			const year = DaysDate.getFullYear();
			const month = ( '0' + ( DaysDate.getMonth() + 1 ) ).slice( -2 );
			const day = ( '0' + DaysDate.getDate() ).slice( -2 );

			const dayOfWeek = DaysDate.getDay();
			const dayOfWeekStr = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ][dayOfWeek];

			const dayFormat = year + '/' + month + '/' + day + ' <small>' + dayOfWeekStr + '</small>';
			this.clock.day = dayFormat;
		},
		currentTime: function() {
			let timer = 0;

			const timeLoop = () => {
				if ( 0 < timer ) {
					clearTimeout( timer );
				}
				const TimesDate = new Date();
				const hour = ( '0' + TimesDate.getHours() ).slice( -2 );
				const minute = ( '0' + TimesDate.getMinutes() ).slice( -2 );
				const second = ( '0' + TimesDate.getSeconds() ).slice( -2 );
				const timeFormat = hour + ':' + minute + ':<small>' + second + '</small>';
				this.clock.time = timeFormat;

				timer = setTimeout( () => {
					timeLoop();
				}, 1000 );
			};
			timeLoop();
		}
	},
	delimiters: [ '${', '}' ]
});
