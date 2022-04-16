/*!
 * Feeder | el.kulo v2.0.0 (https://github.com/elkulo/Feeder/)
 * Copyright 2020-2022 A.Sudo
 * Licensed under LGPL-2.1-only (https://github.com/elkulo/Feeder/blob/main/LICENSE)
 */
import { onMounted, onBeforeUnmount, reactive, watch } from 'vue';
import axios from 'axios';
import smoothscroll from 'smoothscroll-polyfill';
import StorageAvailable from './utils/StorageAvailable.js';
import Fix100vh from './utils/Fix100vh.js';
import { API_PATH, POSTS_PER_PAGE, RELOAD_TIMER } from './config.js';

export default {
	name: 'App',
	setup() {
		const state = reactive({
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
			clock: {
				day: '', // 日付
				time: '', // 時間
			},
		});

		watch(
			() => state.APIQuery,
			() => {
				if ( state.setting.autoload ) {
					clearTimeout( RELOAD_TIMER.id );
					RELOAD_TIMER.id = setTimeout( () => onAjaxReload(), 60 * 1000 * RELOAD_TIMER.interval );
				} else {
					clearTimeout( RELOAD_TIMER.id );
				}
			},
			{ deep: true }
		);

		watch(
			() => state.setting,
			( value ) => {

				// オートロード.
				if ( value.autoload ) {
					onAjaxReload(); // TrueならAPIを更新.
				} else {
					clearTimeout( RELOAD_TIMER.id ); // FalseならTimerを停止.
				}

				// ダークモード.
				if ( value.darkmode ) {
					document.body.classList.add( 'darkmode' );
				} else {
					document.body.classList.remove( 'darkmode' );
				}

				if ( StorageAvailable.has( 'localStorage' ) ) {
					localStorage.setItem( 'feeder-app-setting', JSON.stringify( state.setting ) );
				}
			},
			{ deep: true }
		);

		const onCurrentDay = () => {
			const DaysDate = new Date();
			const year = DaysDate.getFullYear();
			const month = ( '0' + ( DaysDate.getMonth() + 1 ) ).slice( -2 );
			const day = ( '0' + DaysDate.getDate() ).slice( -2 );

			const dayOfWeek = DaysDate.getDay();
			const dayOfWeekStr = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ][ dayOfWeek ];

			const dayFormat = year + '/' + month + '/' + day + ' <small>' + dayOfWeekStr + '</small>';
			state.clock.day = dayFormat;
		};

		const onCurrentTime = () => {
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
				state.clock.time = timeFormat;

				timer = setTimeout( () => timeLoop(), 1000 );
			};
			timeLoop();
		};

		const onBackToTop = () => {
			smoothscroll.polyfill(); // Safari Polyfill.
			window.scrollTo({
				behavior: 'smooth',
				top: 0,
			});
		};

		const onResetSetting = () => {
			if ( StorageAvailable.has( 'localStorage' ) ) {
				localStorage.removeItem( 'feeder-app-setting' );
				state.setting = {
					autoload: true, // 自動更新
					darkmode: false, // ダークモード
				};
			}
		};

		const toggleSwitchSetting = ( key ) => {
			if ( state.setting[key] === true ) {
				state.setting[key] = false;
			} else {
				state.setting[key] = true;
			}
		};

		const changeTab = ( dataSiteID = 0 ) => {
			state.isActiveTab = dataSiteID;
		};

		const chengeToastDisplay = ( show = false ) => {
			state.toast.display = show;
		};

		const changeCategory = ( requestCategory = '', dataSiteID = 0 ) => {
			let inTaxonomy = [];
			let result = [];
			state.APIQuery.data.forEach( ( singleQuery ) => {
				result = singleQuery.category.filter( ( val ) => val === requestCategory );
				if ( result[0]) {
					inTaxonomy.push( singleQuery );
				}
			});

			if ( inTaxonomy.length ) {

				// カテゴリーに記事がある場合
				state.feederQuery = [ ...getQueryAll( requestCategory ), ...inTaxonomy ];
			} else {

				// ホームでは全記事表示
				state.feederQuery = [ ...getQueryAll(), ...state.APIQuery.data ];
			}
			state.isActiveTab = dataSiteID;
			state.isActiveCategory = requestCategory;
		};

		const onAjaxReload = () => {
			let isLoadSuccess = false;
			axios.get( API_PATH )
				.then( ( response ) => {
					isLoadSuccess = true;

					// クエリーを生成
					state.APIQuery = response.data;
					state.APIQuery.data.forEach( ( singleQuery ) => {

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
					state.feederQuery = [ ...getQueryAll(), ...state.APIQuery.data ];

					// カテゴリーリストを生成
					let categories = [];
					state.APIQuery.data.forEach( ( single ) => {
						categories = Array.from( new Set([ ...categories, ...single.category ]) );
					});
					state.categories = categories;
					changeCategory( state.isActiveCategory, state.isActiveTab );

					// eslint-disable-next-line no-console
					console.info( 'API loading is complete, at ' + state.clock.time.replace( /(<([^>]+)>)/gi, '' ) );
				})
				.catch( () => {
					isLoadSuccess = false;

					// Toastでアラートを表示.
					state.toast = {
						display: true,
						latest: 'at ' + state.clock.time, // 時刻
						title: 'Failed to load API.', // タイトル
						content: '最新情報の取得に失敗しました。', // 内容
					};

					// eslint-disable-next-line no-console
					console.error( 'Failed to load API. at ' + state.clock.time.replace( /(<([^>]+)>)/gi, '' ) );
				})
				.finally( () => {
					onAjaxReloadAfter( isLoadSuccess );
				});
		};

		const onAjaxReloadAfter = ( isLoadSuccess ) => {

			// API読み込みが失敗した場合はスキップ
			if ( ! isLoadSuccess ) {
				return;
			}

			// 初回のAPI読み込みか、2回目以降か判定
			if ( state.hasPreLoad ) {

				// プリロード済みにフラグ変更
				setTimeout( () => ( state.hasPreLoad = false ), 1000 );

				// オートロード時間の更新
				state.autoload = {
					display: false,
					latest: 'Latest ' + state.clock.time,
				};
			} else {

				// オートロード時間の更新
				state.autoload = {
					display: true,
					latest: 'Latest ' + state.clock.time,
				};

				// オートロードを自動で閉じる
				setTimeout( () => ( state.autoload.display = false ), 5600 );
			}
		};

		const getQueryAll = ( requestCategory = '' ) => {
			let inPosts = [];
			let result = [];

			// サイトから記事の配列のみを抜き取り結合
			state.APIQuery.data.forEach( ( singleQuery ) => {
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
		};

		// スクロールを監視.
		const ScrollEvent = {
			do: () => {
				const windowHeight = window.innerHeight;
				if ( window.scrollY > windowHeight ) {
					state.backToTop.display = true;
				} else {
					state.backToTop.display = false;
				}
			},
			add: () => {
				requestAnimationFrame( () => {
					window.addEventListener( 'scroll', ScrollEvent.do, false );
				});
			},
			remove: () => {
				window.removeEventListener( 'scroll', ScrollEvent.do, false );
			}
		};

		const debug = ( text ) => {
			// eslint-disable-next-line no-console
			console.log( text );
		};

		onMounted( () => {
			Fix100vh.add();
			ScrollEvent.add();
			onCurrentDay();
			onCurrentTime();

			// ローカルストレージから設定を取得
			if ( StorageAvailable.has( 'localStorage' ) ) {
				const data = localStorage.getItem( 'feeder-app-setting' );
				if ( data ) {
					state.setting = Object.assign( state.setting, JSON.parse( data ) );
				}
			}
			onAjaxReload();
		});

		onBeforeUnmount( () => {
			Fix100vh.remove();
			ScrollEvent.remove();
			clearTimeout( RELOAD_TIMER.id );
		});

		return {
			state,
			onBackToTop,
			onResetSetting,
			toggleSwitchSetting,
			changeTab,
			chengeToastDisplay,
			changeCategory,
			onAjaxReload,
			getQueryAll,
			debug,
		};
	},
};
