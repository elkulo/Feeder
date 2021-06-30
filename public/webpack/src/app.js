import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import Vue from 'vue';
import axios from 'axios';
import smoothscroll from 'smoothscroll-polyfill';
import isStorageAvailable from './js/isStorageAvailable.js';
import fix100vh from './js/fix100vh.js';
import './app.scss';

// APIのパス.
const API_PATH = '/api/v1/posts';

// 1ページの表示上限.
const POSTS_PER_PAGE = 50;

// 自動更新の間隔.
const RELOAD_TIMER = {
	id: 0,
	interval: 15, // リロード間隔(分)
};

// Vueアプリケーション.
new Vue({
	el: '#app',
	data: {
		APIQuery: [], // APIデータオブジェクト
		feederQuery: [], // フィードオブジェクト
		categories: [], // カテゴリーリスト
		isActiveTab: 0, // アクティブな記事ID
		isActiveCategory: '', // アクティブなカテゴリー名
		hasPreLoad: true, // プリロードのフラグ
		autoload: {
			display: false, // 表示切替
			latest: '', // 時刻
		},
		clock: {
			day: '', // 日付
			time: '', // 時間
		},
		toast: {
			display: false, // 表示切替
			latest: '', // 時刻
			title: 'Alert', // タイトル
			content: 'Hello,world!', // 内容
		},
		setting: {
			autoload: true, // 自動更新
			darkmode: false, // ダークモード
		},
		backToTop: {
			display: false, // スクロールトップの表示
		},
	},
	computed: {

		// 連想配列のパラメーターの変更を取得.
		computedSetting() {
			return JSON.parse( JSON.stringify( this.setting ) );
		},
	},
	watch: {

		// APIの更新を監視.
		APIQuery: {
			handler: function() {
				if ( this.setting.autoload ) {
					clearTimeout( RELOAD_TIMER.id );
					RELOAD_TIMER.id = setTimeout( () => this.onAjaxReload(), 60 * 1000 * RELOAD_TIMER.interval );
				} else {
					clearTimeout( RELOAD_TIMER.id );
				}
			},
		},

		// 連想配列のパラメーターの変更を比較.
		computedSetting: {
			handler: function( newValue, oldValue ) {

				// オートロード.
				if ( oldValue.autoload !== newValue.autoload ) {
					if ( newValue.autoload ) {
						this.onAjaxReload(); // TrueならAPIを更新.
					} else {
						clearTimeout( RELOAD_TIMER.id ); // FalseならTimerを停止.
					}
				}

				// ダークモード.
				if ( oldValue.darkmode !== newValue.darkmode ) {
					if ( newValue.darkmode ) {
						document.body.classList.add( 'darkmode' );
					} else {
						document.body.classList.remove( 'darkmode' );
					}
				}

				if ( isStorageAvailable( 'localStorage' ) ) {
					localStorage.setItem( 'feeder-app-setting', JSON.stringify( this.setting ) );
				}
			},
			deep: true,
		},
	},
	methods: {
		onResetSetting: function() {
			if ( isStorageAvailable( 'localStorage' ) ) {
				localStorage.removeItem( 'feeder-app-setting' );
				this.setting = {
					autoload: true, // 自動更新
					darkmode: false, // ダークモード
				};
			}
		},
		toggleSwitchSetting: function( key ) {
			if ( this.setting[key] === true ) {
				this.setting[key] = false;
			} else {
				this.setting[key] = true;
			}
		},
		changeTab: function( dataSiteID = 0 ) {
			this.isActiveTab = dataSiteID;
		},
		chengeToastDisplay: function( show = false ) {
			this.toast.display = show;
		},
		changeCategory: function( requestCategory = '', dataSiteID = 0 ) {
			let inTaxonomy = [];
			let result = [];
			this.APIQuery.data.forEach( ( singleQuery ) => {
				result = singleQuery.category.filter( ( val ) => val === requestCategory );
				if ( result[0]) {
					inTaxonomy.push( singleQuery );
				}
			});

			if ( inTaxonomy.length ) {

				// カテゴリーに記事がある場合
				this.feederQuery = [ ...this.getQueryAll( requestCategory ), ...inTaxonomy ];
			} else {

				// ホームでは全記事表示
				this.feederQuery = [ ...this.getQueryAll(), ...this.APIQuery.data ];
			}
			this.isActiveTab = dataSiteID;
			this.isActiveCategory = requestCategory;
		},
		onAjaxReload: function() {
			let isLoadSuccess = false;
			axios.get( API_PATH )
				.then( ( response ) => {
					isLoadSuccess = true;

					// クエリーを生成
					this.APIQuery = response.data;
					this.APIQuery.data.forEach( ( singleQuery ) => {

						// feeder要素に掲載サイトの情報を追加.
						singleQuery.feeder.forEach( ( _, index ) => {
							const parentQuery = singleQuery;
							singleQuery.feeder[index].parent = {
								id: parentQuery.id,
								name: parentQuery.name,
							};
						});
					});

					// フィードリストを生成
					this.feederQuery = [ ...this.getQueryAll(), ...this.APIQuery.data ];

					// カテゴリーリストを生成
					let categories = [];
					this.APIQuery.data.forEach( ( single ) => {
						categories = Array.from( new Set([ ...categories, ...single.category ]) );
					});
					this.categories = categories;
					this.changeCategory( this.isActiveCategory, this.isActiveTab );

					// eslint-disable-next-line no-console
					console.info( 'API loading is complete, at ' + this.clock.time.replace( /(<([^>]+)>)/gi, '' ) );
				})
				.catch( () => {
					isLoadSuccess = false;

					// Toastでアラートを表示.
					this.toast = {
						display: true,
						latest: 'Latest ' + this.clock.time, // 時刻
						title: 'Failed to load API.', // タイトル
						content: '最新情報の取得に失敗しました。', // 内容
					};

					// eslint-disable-next-line no-console
					console.error( 'Failed to load API. at ' + this.clock.time.replace( /(<([^>]+)>)/gi, '' ) );
				})
				.finally( () => {
					this.onAjaxReloadAfter( isLoadSuccess );
				});
		},
		onAjaxReloadAfter: function( isLoadSuccess ) {

			// API読み込みが失敗した場合はスキップ
			if ( ! isLoadSuccess ) {
				return;
			}

			// 初回のAPI読み込みか、2回目以降か判定
			if ( this.hasPreLoad ) {

				// プリロード済みにフラグ変更
				setTimeout( () => ( this.hasPreLoad = false ), 1000 );

				// オートロード時間の更新
				this.autoload = {
					display: false,
					latest: 'Latest ' + this.clock.time,
				};
			} else {

				// オートロード時間の更新
				this.autoload = {
					display: true,
					latest: 'Latest ' + this.clock.time,
				};

				// オートロードを自動で閉じる
				setTimeout( () => ( this.autoload.display = false ), 20000 );
			}
		},
		getQueryAll: function( requestCategory = '' ) {
			let inPosts = [];
			let result = [];

			// サイトから記事の配列のみを抜き取り結合
			this.APIQuery.data.forEach( ( singleQuery ) => {
				if ( requestCategory ) {
					result = singleQuery.category.filter( ( val ) => val === requestCategory );
					if ( result[0]) {
						inPosts = Array.from( new Set([ ...inPosts, ...singleQuery.feeder ]) );
					}
				} else {
					inPosts = Array.from( new Set([ ...inPosts, ...singleQuery.feeder ]) );
				}
			});

			// 更新順にソート
			inPosts.sort( ( a, b ) => {
				if ( a.date > b.date ) {
					return -1;
				} else {
					return 1;
				}
			});

			// 50件までの一つのサイトとして出力
			return [
				{
					id: 0,
					name: '新着',
					src: '',
					url: '',
					category: [],
					feeder: inPosts.filter( ( val, index ) => index < POSTS_PER_PAGE ),
				},
			];
		},
		onCurrentDay: function() {
			const DaysDate = new Date();
			const year = DaysDate.getFullYear();
			const month = ( '0' + ( DaysDate.getMonth() + 1 ) ).slice( -2 );
			const day = ( '0' + DaysDate.getDate() ).slice( -2 );

			const dayOfWeek = DaysDate.getDay();
			const dayOfWeekStr = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ][ dayOfWeek ];

			const dayFormat = year + '/' + month + '/' + day + ' <small>' + dayOfWeekStr + '</small>';
			this.clock.day = dayFormat;
		},
		onCurrentTime: function() {
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

				timer = setTimeout( () => timeLoop(), 1000 );
			};
			timeLoop();
		},
		onShowScrollTopButton: function() {
			const $backToTop = document.querySelector( '#back-to-top' );
			const windowHeight = window.innerHeight;
			let ticking = false;

			window.addEventListener(
				'scroll',
				() => {
					if ( ! ticking && $backToTop ) {
						ticking = true;

						window.requestAnimationFrame( () => {
							if ( window.pageYOffset > windowHeight ) {
								this.backToTop.display = true;
							} else {
								this.backToTop.display = false;
							}
							ticking = false;
						});
					}
				},
				{ passive: true }
			);
		},
		onBackToTop: function() {

			// Safari Polyfill.
			smoothscroll.polyfill();

			window.scrollTo({
				behavior: 'smooth',
				top: 0,
			});
		},
		debug: function( text ) {
			// eslint-disable-next-line no-console
			console.log( text );
		},
	},
	created() {

		// 100vhを調整
		fix100vh();

		// ローカルストレージから設定を取得
		if ( isStorageAvailable( 'localStorage' ) ) {
			const data = localStorage.getItem( 'feeder-app-setting' );
			if ( data ) {
				this.setting = Object.assign( this.setting, JSON.parse( data ) );
			}
		}

		// 初回イベント
		this.onAjaxReload();
		this.onCurrentDay();
		this.onCurrentTime();
		this.onShowScrollTopButton();
	},
	destroyed() {
		clearTimeout( RELOAD_TIMER.id );
	},
	delimiters: [ '${', '}' ],
});
