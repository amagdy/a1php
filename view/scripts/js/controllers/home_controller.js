// user_controller.js
window.home_controller = function () {
	this.showonepage = function (__in) {
		alert(__in['page']['title']);
		alert(__in['page']['body']);
	}
}
