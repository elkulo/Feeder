import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './app.scss';
import Vue from 'vue';
import axios from 'axios';

const API_PATH = '/api/v1/posts';

new Vue({
	el: '#app',
	data: {
		APIQuery: {}, // 元データオブジェクト
		feederQuery: {}, // フィードオブジェクト
		categories: [], // カテゴリーリスト
		isActiveTab: 1, // アクティブな記事ID
		isActiveCategory: '', // アクティブなカテゴリー名
		clock: {
			day: '', // 日付
			time: '' // 時間
		}
	},
	mounted() {
		this.currentDay();
		this.currentTime();
		axios.get( API_PATH ).then( ( response ) => {
			this.APIQuery = response.data;
			this.feederQuery = this.APIQuery.data;
			let categories = new Array();
			this.feederQuery.forEach( single => {
				categories = Array.from(
					new Set([ ...categories, ...single.category ])
				);
			});
			this.categories = categories;
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
		changeTab: function( tabID ) {
			this.isActiveTab = tabID;
		},
		changeCategory: function( requestCategory = '' ) {
			let taxonomy = [];
			let result;
			this.APIQuery.data.forEach( singleQuery => {
				result = singleQuery.category.filter( val => val === requestCategory );
				if ( result[0]) {
					taxonomy.push( singleQuery );
				}
			});
			if ( taxonomy.length ) {

				// カテゴリーに記事がある場合
				this.feederQuery = taxonomy;
				this.isActiveTab = taxonomy[0].id;
				this.isActiveCategory = requestCategory;
			} else {

				// 記事がなければ全記事表示
				this.feederQuery = this.APIQuery.data;
				this.isActiveTab = 1;
				this.isActiveCategory = '';
			}
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
