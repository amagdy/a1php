FW.debug_enabled = true;
// @author Ahmed Magdy Ezzeldin <admin@a1works.com>
// hash of pending responses holds responses that came from the server and do not have a controller in memory it is rerun later when the controller is retrieved
// key is controller_name and value is any array of pending responses
FW.hash_pending_responses = {};
FW.hash_pending_requests = {};
FW.hash_controller_objects = {};
FW.interactive_ajax_singleton = null;
//--------------------------------------------------------------
FW.timer = function (timeout_function, timeout_seconds, bool_timer_reset) {
	this.reset = function () {
		bool_timer_reset = true;	// reset timer
		return false;
	}
	
	new PeriodicalExecuter(
		function(pe) {
			if (!bool_timer_reset) {	// if timer is not reset then 
				if (timeout_function()) pe.stop();
			} 
			bool_timer_reset = false;
		}
	, timeout_seconds);
}
//--------------------------------------------------------------
FW.AjaxQueue = function () {
	var bool_working = false;
	var prv_arr = new Array();
	
	this.ajax_response = function (originalRequest) {
		var __in = originalRequest.responseJSON;
		__in['__ajax'] = true;
		__in['__in_queue'] = true;
		return FW.handle_response(__in);
	}

	this.ajax_error = function (originalRequest) {
		alert('Error: ' + originalRequest.statusText);
		this.process();
	}

	this.add = function (params, is_post) {
		var obj = {};
		obj.pars = $H(params).toQueryString();
		obj.is_post = is_post;
		prv_arr.push(obj);
		if (!this.bool_working)	this.process();
	};
	
	this.process = function () {
		this.bool_working = false;
		if ((obj = prv_arr.shift()) != undefined) {
			// do something with obj
			this.bool_working = true;
			var myAjax = new Ajax.Request(HTML_ROOT + "json.php", { method: (obj.is_post ? 'post' : 'get'), parameters: obj.pars, onComplete: this.ajax_response, onFailure: this.ajax_error });
		}
	};
}
//--------------------------------------------------------------
FW.ajax_queue = new FW.AjaxQueue();
//--------------------------------------------------------------
FW.handle_input = function (__in) {
	var subcontroller;
	if (FW.hash_controller_objects[__in['controller']]) {
		subcontroller = FW.hash_controller_objects[__in['controller']];
	} else {
		eval("subcontroller = new " + __in['controller'] + "_controller();");
		FW.hash_controller_objects[__in['controller']] = subcontroller;
	}
	eval("var myreturn = subcontroller." + __in['action'] + "(__in);");
	if (__in['__in_queue']) FW.ajax_queue.process();	// if this input was from the ajax queue then continue processing the queue
	return myreturn;
}
//--------------------------------------------------------------
FW.handle_response = function (__in) {
	if (!FW[__in['controller'] + "_controller"]) {
		// add response to hash of pending responses so that when its controller is downloaded it is handled
		if(!FW.hash_pending_responses[__in['controller']]) {
			FW.hash_pending_responses[__in['controller']] = [];
		}
		FW.hash_pending_responses[__in['controller']][FW.hash_pending_responses[__in['controller']].length] = __in;
		FW.load_js(__in['controller']);
		return false;
	} else {
		// handle the response right away if its controller exists
		return FW.handle_input(__in);
	}
}
//--------------------------------------------------------------
FW.handle_request = function (__in) {
	if (!FW[__in['controller'] + "_controller"]) {
		// add request to hash of pending requests so that when its controller is downloaded it is handled
		if(!FW.hash_pending_requests[__in['controller']]) {
			FW.hash_pending_requests[__in['controller']] = [];
		}
		FW.hash_pending_requests[__in['controller']][FW.hash_pending_requests[__in['controller']].length] = __in;
		FW.load_js(__in['controller']);
		return false;
	} else {
		// handle the request right away if its controller exists
		return FW.handle_input(__in);
	}
}
//--------------------------------------------------------------
FW.handle_pending_responses = function (controller_name) {
	var arr_responses = FW.hash_pending_responses[controller_name];
	for (var i = 0; i < arr_responses.length; i++) {
		FW.handle_input(arr_responses[i]);
	}
	delete FW.hash_pending_responses[controller_name];
	return true;
}
//--------------------------------------------------------------
FW.handle_pending_requests = function (controller_name) {
	var arr_requests = FW.hash_pending_requests[controller_name];
	for (var i = 0; i < arr_requests.length; i++) {
		FW.handle_input(arr_requests[i]);
	}
	delete FW.hash_pending_requests[controller_name];
	return true;
}
//--------------------------------------------------------------
FW.ajax_response = function (originalRequest) {
	var __in = originalRequest.responseJSON;
	__in['__ajax'] = true;
	if (FW.debug_enabled) FW.log("got response " + FW.debug(__in));
	return FW.handle_response(__in);
}
//--------------------------------------------------------------
FW.ajax_error = function (originalRequest) {
	alert('Error: ' + originalRequest.statusText);
}
//--------------------------------------------------------------
FW.ajax_request = function (params, is_post) {
	var pars = $H(params).toQueryString();
	var myAjax = new Ajax.Request(HTML_ROOT + "json.php", { method: (is_post ? 'post' : 'get'), parameters: pars, onComplete: FW.ajax_response, onFailure: FW.ajax_error });	
	return true;
}
//--------------------------------------------------------------
FW.simple_ajax_request = function (callback, params, is_post) {
	var pars = $H(params).toQueryString();
	var myAjax = new Ajax.Request(HTML_ROOT + "json.php", { method: (is_post ? 'post' : 'get'), parameters: pars, onComplete: callback, onFailure: FW.ajax_error });	
	return true;
}
//--------------------------------------------------------------
FW.js_ajax_response = function (originalRequest) {
	var controller = originalRequest.getResponseHeader('X-FILENAME');
	if (!originalRequest.responseText) return;
	eval(originalRequest.responseText);
	if (FW.hash_pending_responses[controller]) {
		FW.handle_pending_responses(controller);
	}
	if (FW.hash_pending_requests[controller]) {
		FW.handle_pending_requests(controller);
	}
}
//--------------------------------------------------------------
FW.js_ajax_error = function (originalRequest) {
	alert('Error: ' + originalRequest.statusText);
}
//--------------------------------------------------------------
/**
load a js file
*/
FW.load_js = function (controller_name) {
	var pars = $H({"file": controller_name}).toQueryString();
	var myAjax = new Ajax.Request(HTML_ROOT + "ajax_php/download_controller_js.php", { method: 'post', parameters: pars, onComplete: FW.js_ajax_response, onFailure: FW.js_ajax_error });	
}
//--------------------------------------------------------------
FW.serialize_form = function (form) {
	if (!form) form = this;
	return FW.link_to_object($H($(form).serialize(true)).merge(FW.link_to_object(form.action)).toQueryString());
}
//--------------------------------------------------------------
FW.on_form_submit = function (form) {
	if (!form.action) form = this;
	var obj_request_params = FW.serialize_form(form);
	if (form.readAttribute('queue')) {
		FW.ajax_queue.add(obj_request_params, true);
	} else {
		FW.ajax_request(obj_request_params, true);
	}
	return false;
}
//--------------------------------------------------------------
FW.on_local_form_submit = function (form) {
	if (!form.action) form = this;
	var obj_request_params = FW.serialize_form(form);
	FW.handle_request(obj_request_params);
	return false;
}
//--------------------------------------------------------------
/**
takes the params in any link and form a JSON object from it
*/
FW.link_to_object = function (str_link) {
	return str_link.toQueryParams();
}
//--------------------------------------------------------------
/**
used only if we want to create a link on the fly and set its href
*/
FW.object_to_link = function (params) {
	return HTML_ROOT + "?" + $H(params).toQueryString();
}
//--------------------------------------------------------------
FW.on_link_click = function (link) {
	if (!link.href) link = this;
	var __in = FW.link_to_object(link.href);
	if (link.readAttribute('queue')) {
		if (FW.debug_enabled) FW.log("Ajax link click in queue " + link.href);
		FW.ajax_queue.add(__in, false);
	} else {
		if (FW.debug_enabled) FW.log("Ajax link click async " + link.href);
		FW.ajax_request(__in, false);
	}
	return false;
}
//--------------------------------------------------------------
FW.on_local_link_click = function (link) {
	if (!link.href) link = this;
	if (FW.debug_enabled) FW.log("local link click " + link.href);
	var __in = FW.link_to_object(link.href);
	FW.handle_request(__in);
	return false;
}
//--------------------------------------------------------------
FW.make_ajax_links_and_forms = function () {
	var i;
	var arr_links = $$('a[ajax=yes]');
	var arr_local_links = $$('a[local=yes]');
	var arr_forms = $$('form[ajax=yes]');
	var arr_local_forms = $$('form[local=yes]');
	// loop on links
	for (i = 0; i < arr_links.length; i++) {
		// if this is a link then assign the onclick function for that link
		arr_links[i].onclick = FW.on_link_click;
	}

	for (i = 0; i < arr_local_links.length; i++) {
		// if this is a local link then assign the onclick function for that link
		arr_local_links[i].onclick = FW.on_local_link_click;
	}
	if (FW.debug_enabled) FW.log("attached links onclick functions");
	
	// loop on forms
	for (i = 0; i < arr_forms.length; i++) {
		// if this is a form then assign the onsubmit function for that form
		arr_forms[i].onsubmit = FW.on_form_submit;
	}
	
	// loop on forms
	for (i = 0; i < arr_local_forms.length; i++) {
		// if this is a local form then assign the onsubmit function for that form
		arr_local_forms[i].onsubmit = FW.on_local_form_submit;
	}
	if (FW.debug_enabled) FW.log("attached forms onsubmit functions");
	
	// handle events for elements that has a handler
	var item_iterator = function (item) {
		item = $(item);
		var handler = item.readAttribute('handler');
		for (var event_name in FW.EVENTS[item_name]) {
			item["on" + event_name] = FW.EVENTS[handler][event_name];
		}
	}
	
	for (var item_name in FW.EVENTS) {
		var arr_items = $$('[handler="' + item_name + '"]');
		arr_items.each(item_iterator);
	}
	if (FW.debug_enabled) FW.log("attached events");
	
}
//--------------------------------------------------------------
/**
creates an interactive ajax connection (HTTP streaming)
@param received_command a reference to a function [function (cmd)] where cmd is the command received from the interactive ajax connection
@param params the parameters sent to the json.php file
@param interactive_offset defaults to 0 (not used)
*/
FW.ajax_interactive = function (received_command, params, connection_lost_request, interactive_offset) {
	//this.timer = null;
	
	this.req = function (params) {
		if (FW.interactive_ajax_singleton != null) FW.interactive_ajax_singleton.transport.abort();
		var pars = $H(params).toQueryString();
		FW.interactive_ajax_singleton = new Ajax.Request(HTML_ROOT + "json.php", { method: 'post', parameters: pars, onInteractive: this.resp, onFailure: this.resp });
		//this.timer = new FW.timer(function (){FW.handle_input(connection_lost_request); return true;}, 3);
		return true;
	}

	this.resp = function (originalRequest) {
		//this.timer.reset();
		var new_text = originalRequest.responseText.substr(interactive_offset, originalRequest.responseText.length);
		interactive_offset = originalRequest.responseText.length;
		new_text = new_text.trim();
		if (new_text != "") {
			var arr_cmds = new_text.split(/\}\s*\{/);
			var cmds_length = arr_cmds.length;
			if (cmds_length > 1) {
				for (var i = 0; i < cmds_length; i++) {
					if (i > 0) arr_cmds[i] = "{" + arr_cmds[i];
					if (i < cmds_length-1) arr_cmds[i] = arr_cmds[i] + "}";
					received_command(arr_cmds[i]);
				}
			} else {
				received_command(new_text);
			}
		}
	}
	this.req(params);
}
//--------------------------------------------------------------
FW.process_template = function (html, data) {
	return html.sub(/(\{|%7B)\$([a-zA-Z0-9_\.]+)(\}|%7D)/, function (match) {return eval("data." + match[2]);}, 1000);
}
//--------------------------------------------------------------
FW.add_row_to_table = function (table_id, row_index, data) {
	var html = FW.process_template($(table_id + '_tr_tpl').innerHTML, data);
	if (row_index == null) {	// add to end
		row_index = $(table_id).rows.length;
	} else if (row_index < 0) {		// insert from the end
		row_index = $(table_id).rows.length + row_index;
	}
	var tr = $(table_id).insertRow(row_index);
	tr.innerHTML = html;
}
//--------------------------------------------------------------
FW.debug = function (obj) {
	var str = "";
	for (var x in obj) {
		str += x + " = " + obj[x] + "\n";
	}
	return str;
}
//--------------------------------------------------------------
FW.log = function (str) {
	$("debug").innerHTML += "### " + str + "\n";
}
//--------------------------------------------------------------
Event.observe(window, 'load', FW.make_ajax_links_and_forms);
//--------------------------------------------------------------
