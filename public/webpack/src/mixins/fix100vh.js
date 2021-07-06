// ビューポート100vhの固定
export default {
	mounted() {
		document.documentElement.style.setProperty(
			'--maxvh',
			`${window.innerHeight}px`
		);
	},
	destroyed() {
		document.documentElement.style.removeProperty(
			'--maxvh'
		);
	},
};
