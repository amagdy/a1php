// user_controller.js
window.user_controller = function () {
	this.user_info = {};
	
	this.home = function (__in) {
		this.user_info = __in['user'];
		$('login_div').hide();
		$('welcome_span').show();
		$('welcome_span').innerHTML = "<b>Welcome " + __in['user']['name'] + "</b>";
		$('logout_span').show();
                FW.add_row_to_table('mytable', 1, __in['user']);
	}
	
	this.login = function (__in) {
		$('login_div').show();
		$('logout_span').hide();
		$('welcome_span').hide();
	}

	// local request
	this.alert_name = function (__in) {
		alert("The Name is: " + this.user_info['name']);
	}
	
}
