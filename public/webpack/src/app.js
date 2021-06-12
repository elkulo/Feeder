import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './app.scss';
import Vue from 'vue';
import axios from 'axios';

// APIのパス.
const API_PATH = '/api/v1/posts';

// 1ページの表示上限.
const POSTS_PER_PAGE = 50;

new Vue({
	el: '#app',
	data: {
		APIQuery: [], // 元データオブジェクト
		feederQuery: [], // フィードオブジェクト
		categories: [], // カテゴリーリスト
		isActiveTab: 0, // アクティブな記事ID
		isActiveCategory: '', // アクティブなカテゴリー名
		clock: {
			day: '', // 日付
			time: '', // 時間
		},
	},
	created() {
		this.currentDay();
		this.currentTime();
		axios
			.get( API_PATH )
			.then( ( response ) => {

				// クエリーを生成
				this.APIQuery = response.data;
				this.feederQuery = [ ...this.getQueryAll(), ...this.APIQuery.data ];

				// カテゴリーを生成
				let categories = [];
				this.APIQuery.data.forEach( ( single ) => {
					categories = Array.from( new Set([ ...categories, ...single.category ]) );
				});
				this.categories = categories;
			})
			.catch( ( error ) => {
				// eslint-disable-next-line no-console
				console.error( '読み込み失敗', error );
			})
			.finally( () => {
				// eslint-disable-next-line no-console
				console.info( 'ローディング完了' );
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
			this.APIQuery.data.forEach( ( singleQuery ) => {
				result = singleQuery.category.filter( ( val ) => val === requestCategory );
				if ( result[0]) {
					taxonomy.push( singleQuery );
				}
			});

			if ( taxonomy.length ) {

				// カテゴリーに記事がある場合
				this.feederQuery = [ ...this.getQueryAll( requestCategory ), ...taxonomy ];
			} else {

				// ホームでは全記事表示
				this.feederQuery = [ ...this.getQueryAll(), ...this.APIQuery.data ];
			}
			this.isActiveTab = 0;
			this.isActiveCategory = requestCategory;
		},
		getQueryAll: function( requestCategory='' ) {
			let all = [];
			let result;

			// サイトから記事の配列のみを抜き取り結合
			this.APIQuery.data.forEach( ( singleQuery ) => {
				if ( requestCategory ) {
					result = singleQuery.category.filter( ( val ) => val === requestCategory );
					if ( result[0]) {
						all = Array.from( new Set([ ...all, ...singleQuery.feeder ]) );
					}
				} else {
					all = Array.from( new Set([ ...all, ...singleQuery.feeder ]) );
				}
			});

			// 更新順にソート
			all.sort( ( a, b ) => {
				if ( a.date > b.date ) {
					return -1;
				} else {
					return 1;
				}
			});

			// 50件までの一つのサイトとして出力
			return [{
				'id': 0,
				'name': '新着',
				'src': '',
				'url': '',
				'category': [],
				'feeder': all.filter( ( val, index ) => index < POSTS_PER_PAGE )
			}];
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
		},
	},
	delimiters: [ '${', '}' ],
});
