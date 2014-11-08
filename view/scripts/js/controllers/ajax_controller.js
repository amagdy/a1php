// ajax_controller.js
window.ajax_controller = function () {
	this.test_slow = function (__in) {
		alert(__in['some_output']);
	}
	
	this.test_fast = function (__in) {
		alert(__in['some_output']);
	}
}
