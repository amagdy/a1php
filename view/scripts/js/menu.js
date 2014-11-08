	var arr_divs = {};
	function hide_div (divname) {
		document.getElementById(divname).style.display = "none";
		document.getElementById(divname+"_span").innerHTML = "+";
		arr_divs[divname] = "0";
		update_cookie();
	}

	function show_div (divname) {
		document.getElementById(divname).style.display = "";
		document.getElementById(divname+"_span").innerHTML = "-";
		arr_divs[divname] = "1";
		update_cookie();
	}
	
	function hide_show(divname) {
		if (document.getElementById(divname).style.display == "none") {
			show_div(divname);
		} else {
			hide_div(divname);
		}
		return false;
	}
	
	function update_cookie () 
	{
		var str = "";
		var i = 0;
		for (var x in arr_divs) 
		{
			if (i > 0) str += "#";
			str += x + "|" + arr_divs[x];
			i++;
		}
		document.cookie = "admin_panels=" + escape(str) + ";expires=" + (24*60*60*1000*7) + ";path=/";
	}
	
	function loop_on_divs () {
		var arr_cookies = document.cookie.split(";");
		for (var x in arr_cookies) {
			if (arr_cookies[x].indexOf('admin_panels=') > -1) {
				var arr_1 = arr_cookies[x].split("=");
				var str_content = unescape(arr_1[1]);
				var arr_temp_divs = str_content.split("#");
				if (arr_temp_divs) {
					for (var i in arr_temp_divs) {
						var tmp_div = arr_temp_divs[i].split("|");
						arr_divs[tmp_div[0]] = tmp_div[1];
						if (tmp_div[1] == 1) {
							show_div(tmp_div[0]);
						}
					}
				}
			}
		}
	}