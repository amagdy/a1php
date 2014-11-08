function timepicker (field_id) {
	var field_obj = document.getElementById(field_id);
	var selection_position = 0;
	
	if (!field_obj.value) field_obj.value = "00:00:00";
	
	
	function get_allowed_values (position) {
		switch (position) {
			case 0:
				return [0,23];
			case 3:
				return [0,59];
			case 6:
				return [0,59];
		}
	}
	
	
	
	function set_selection(position) {
		if (window.event) {
			var oRange = field_obj.createTextRange();
			oRange.moveStart("character", position);
			oRange.moveEnd("character", (position + 2) - field_obj.value.length);
			oRange.select();
		} else {
			field_obj.selectionStart = position;
			field_obj.selectionEnd = position+2;
		}
		field_obj.focus();	
	}
	
	function get_selected_number () {
		var str_num = field_obj.value.substring(selection_position, selection_position+2);
		inta = parseInt(str_num, 10);
		return inta;
	}
	
	function set_selected_number (int_number) {
		var str_num = int_number + '';
		if (str_num.length == 1) str_num = "0" + str_num;
		var str1 = field_obj.value.substring(0, selection_position);
		var str2 = field_obj.value.substring(selection_position + 2);
		field_obj.value = str1 + str_num + str2;
	}

	field_obj.onfocus = function () {
							set_selection(0);
						}

	field_obj.onkeydown = function (evt){
								if (window.event) {
									keyCode = window.event.keyCode;
								} else {
									keyCode = evt.keyCode;
								}
								if (keyCode == 9) {
									return true;
								}
								if (keyCode == 37) {	// left
									if (selection_position == 0) {
										selection_position = 6;
									} else {
										selection_position -= 3;
									}
								} else if (keyCode == 39) {	/// right
									if (selection_position == 6) {
										selection_position = 0;
									} else {
										selection_position += 3;
									}
								} else if (keyCode == 38) {		// up
									range = get_allowed_values(selection_position);
									var sel_num = get_selected_number();
									if (sel_num-1 < range[0]) {
										set_selected_number(range[1]);
									} else {
										set_selected_number(sel_num-1);
									}
								} else if (keyCode == 40) {		// down
									range = get_allowed_values(selection_position);
									var sel_num = get_selected_number();
									if (sel_num+1 > range[1]) {
										set_selected_number(range[0]);
									} else {
										set_selected_number(sel_num+1);
									}
								}
								set_selection(selection_position);
								return false;

							}
								
	
}