// Fix100vh
const fix100vh = () => {
	document.documentElement.style.setProperty(
		'--maxvh',
		`${window.innerHeight}px`
	);
};
export default fix100vh;
