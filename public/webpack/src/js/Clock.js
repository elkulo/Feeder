/*
 * 時計
 */
class Clock {

	/*
   * constructor
   *
   */
	constructor() {
		this.addCurrentDay = this.addCurrentDay.bind( this );
		this.addCurrentTime = this.addCurrentTime.bind( this );
		document.addEventListener(
			'DOMContentLoaded',
			() => {
				this.addCurrentDay();
				this.addCurrentTime();
			},
			false
		);
	}

	/*
   * currentDay
   *
   * 現在日時
   */
	addCurrentDay() {
		const $daysElement = document.querySelector( '[data-clock-days]' );
		if ( $daysElement ) {
			const DaysDate = new Date();
			const year = DaysDate.getFullYear();
			const month = ( '0' + ( DaysDate.getMonth() + 1 ) ).slice( -2 );
			const day = ( '0' + DaysDate.getDate() ).slice( -2 );

			const dayOfWeek = DaysDate.getDay();
			let dayOfWeekStr = [ '日', '月', '火', '水', '木', '金', '土' ][dayOfWeek];

			switch ( window.lang ) {
			case 'en':
				dayOfWeekStr = [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' ][
					dayOfWeek
				];
				break;
			case 'ko':
				dayOfWeekStr = [
					'일요일',
					'월요일',
					'화요일',
					'수요일',
					'목요일',
					'금요일',
					'토요일',
				][dayOfWeek];
				break;
			case 'zh-cn':
			case 'zh-tw':
				dayOfWeekStr = [
					'星期天',
					'星期一',
					'星期二',
					'星期三',
					'星期四',
					'星期五',
					'星期六',
				][dayOfWeek];
				break;
			}

			const dayFormat =
        year +
        '/' +
        month +
        '/' +
        day +
        '<span class="dayof">（' +
        dayOfWeekStr +
        '）</span>';

			$daysElement.innerHTML = dayFormat;
		}
	}

	/*
   * currentTime
   *
   * 現在時間
   */
	addCurrentTime() {
		let timer = 0;

		const timeLoop = () => {
			if ( 0 < timer ) {
				clearTimeout( timer );
			}
			const $timesElement = document.querySelector( '[data-clock-times]' );
			if ( $timesElement ) {
				const TimesDate = new Date();
				const hour = ( '0' + TimesDate.getHours() ).slice( -2 );
				const minute = ( '0' + TimesDate.getMinutes() ).slice( -2 );
				const second = ( '0' + TimesDate.getSeconds() ).slice( -2 );
				const timeFormat =
          hour + ':' + minute + ':<small>' + second + '</small>';
				$timesElement.innerHTML = timeFormat;

				timer = setTimeout( () => {
					timeLoop();
				}, 1000 );
			}
		};
		timeLoop();
	}
}
export default Clock;
