// ビューポート100vhの固定
export default {
	add() {
		document.documentElement.style.setProperty(
			'--maxvh',
			`${window.innerHeight}px`
		);
	},
	remove() {
		document.documentElement.style.removeProperty(
			'--maxvh'
		);
	},
};
