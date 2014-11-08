<?php
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Creates an AJAX Select control that is populated with the output of an AJAX request fired by an event on another control like the onchange event of another select
for example when the user selects a country from a list all cities in this country is shown in an ajax select control without the page being reloaded
This control is a <select> tag that is populated with the output of an AJAX request
The AJAX request is fired on an event (changer event) of another control (changer control)
@param controller the controller of the AJAX request target
@param action the action of the ajax request target
@param id the id of the ajax_select control
@param changer_id the id of the controls that fires the event that populates this ajax_select control
@param changer_event [optional] default value is change. the event that when fired on the changer control populates the AJAX select control with the data provided by the ajax response
@param data_array_name [optional] default value is data. the name of the array provided by the action method
@param changer_value_name [optional] default value is id. the name of the variable sent to the ajax request that gets its value from the changer value
@param hide_if_empty [optional] default false if the control is empty then hide the control
@param empty_entry_text [optional] default "" if left empty there is no first empty option
@param extra  any extra params are added as attributes to the <select> tag like name, id, class, onchange
@return the <select> control to show on the page
*/
function smarty_compiler_ajax_select($tag_attrs, &$compiler)
{
        $_params = $compiler->_parse_attrs($tag_attrs);
        if (!isset($_params['controller'])) {
	        $compiler->_syntax_error("ajax_select: missing 'controller' parameter", E_USER_WARNING);
                return;
        }

        if (!isset($_params['action'])) {
	        $compiler->_syntax_error("ajax_select: missing 'action' parameter", E_USER_WARNING);
                return;
        }

        if (!isset($_params['id'])) {
	        $compiler->_syntax_error("ajax_select: missing 'id' parameter", E_USER_WARNING);
                return;
        }

        if (!isset($_params['changer_id'])) {
	        $compiler->_syntax_error("ajax_select: missing 'changer_id' parameter", E_USER_WARNING);
                return;
        }

        if (!isset($_params['changer_event'])) {
	        $_params['changer_event'] = "change";
        }

        if (is_string($_params['hide_if_empty'])) $_params['hide_if_empty'] = strtolower($_params['hide_if_empty']);
        if ($_params['hide_if_empty'] == true || $_params['hide_if_empty'] == "yes" || $_params['hide_if_empty'] = "true") {
	    $_params['hide_if_empty'] = true;
        } else {
            $_params['hide_if_empty'] = false;
        }

        if (!isset($_params['empty_entry_text'])) {
	        $empty_entry_text = "";
        } else {
        	$empty_entry_text = ereg_replace("['\"]", "", $_params['empty_entry_text']);
        	unset($_params['empty_entry_text']);
        }
        
        if (!isset($_params['data_array_name'])) {
	        $data_array_name = "data";
        } else {
        	$data_array_name = ereg_replace("['\"]", "", $_params['data_array_name']);
        	unset($_params['data_array_name']);
        }
        
        if (!isset($_params['changer_value_name'])) {
	        $changer_value_name = "id";
        } else {
        	$changer_value_name = ereg_replace("['\"]", "", $_params['changer_value_name']);
        	unset($_params['changer_value_name']);
        }
        
        $controller = $_params['controller'];
        $action = $_params['action'];
        $hide_if_empty = $_params['hide_if_empty'];
        $id = ereg_replace("['\"]", "", $_params['id']);
        $changer_id = ereg_replace("['\"]", "", $_params['changer_id']);
        $changer_event = ereg_replace("['\"]", "", $_params['changer_event']);
        
        unset($_params['controller']);
        unset($_params['action']);
        unset($_params['changer_id']);
        unset($_params['changer_event']);
        unset($_params['hide_if_empty']);
        
        $output = "<select";
        if (is_array($_params)) {
        	reset($_params);
                while (list($k, $v) = each($_params)) {
                	$output .= " " . $k . "=" . $v;
                }
        }
        $output .= ">";
        $output .= "</select>";
        $output .= "<script language=\"javascript\">
        function " . $changer_id . "_" . $changer_event . "() {";
        $output .= "\n\t\tvar HIDE_IF_EMPTY = " . ($hide_if_empty ? "true" : "false") . ";\n";
        $output .= "\nvar id = this.value;
       		var params = {\"controller\": $controller, \"action\": $action, \"$changer_value_name\": id};
       		FW.simple_ajax_request(function (original_req) {
       			// clear list
       			for (var i in \$('$id').options) {
       				\$('$id').remove(\$('$id').options[i]);
       			}
                        var option;
       			";
        if ($empty_entry_text) {
            $output .= "
                        option = document.createElement('option');
                        option.text = '" . $empty_entry_text . "';
                        \$('$id').appendChild(option);";
        }
        $output .= "
       			// populate list
       			var __in = original_req.responseJSON;
                        var count_options = 0;
       			for (var x in __in.$data_array_name) {
       				option = document.createElement('option');
       				option.value = x;
       				option.text = __in." . $data_array_name . "[x];
       				\$('$id').appendChild(option);
                                count_options++;
       			}
                        if (HIDE_IF_EMPTY == true) {
                            if (count_options == 0) {
                                \$('$id').hide();
                            } else {
                                \$('$id').show();
                            }
                        }
       		}, params, true);
       	}
        Event.observe(window, 'load', function(){Event.observe(\$('$changer_id'), '$changer_event', " . $changer_id . "_" . $changer_event . ");});
</script>";
	$output = "?>" . $output . "<?";
        return $output;
}

