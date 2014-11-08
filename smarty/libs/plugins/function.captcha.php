<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Create a captcha that saves a variable in the session and shows the picture
@param name: the name of the session variable and the text field name
@return the picture and the text field
*/
function smarty_function_captcha ($arg_arr_params, &$smarty) {
	if (!$arg_arr_params['name']) {
		$smarty->_trigger_fatal_error("Please add the [name] attribute.");
		return;
	}
	
	$output = "<img id=\"__CAPTCHA\" src=\"" . HTML_ROOT . "view/scripts/captcha/captcha.php?" . rand(0, 32768) . "\"/><br/>\n";
	$output .= "<input id=\"" . $arg_arr_params['name'] . "\" name=\"" . $arg_arr_params['name'] . "\" type=\"text\"/>";
	return $output;
}

