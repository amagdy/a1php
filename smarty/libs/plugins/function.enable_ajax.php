<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Enables ajax in the current page
@param ajax
@param no_ajax
@return html that enables AJAX.
*/
function smarty_function_enable_ajax ($arg_arr_params, &$smarty) {
	$str = "";
	$str .= "<script language=\"javascript\">var HTML_ROOT = \"" . HTML_ROOT . "\";</script>\n";
	$str .= "<script language=\"javascript\" src=\"" . HTML_ROOT . "view/scripts/js/prototype.js\"></script>\n";
	$str .= "<script language=\"javascript\" src=\"" . HTML_ROOT . "view/scripts/js/events.js\"></script>\n";
	$str .= "<script language=\"javascript\" src=\"" . HTML_ROOT . "view/scripts/js/ajax.js\"></script>\n";
	return $str;
}

