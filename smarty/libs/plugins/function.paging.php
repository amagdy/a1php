<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Create a paging area that shows the pages of the current result set
@param controller
@param action
@param model
@param link_array [optional]
@param prev [optional]
@param next [optional]
@return paging links
*/
function smarty_function_paging ($arg_arr_params, &$smarty) {
	$link_array = array();
	if (!$arg_arr_params['controller']) {
		$smarty->_trigger_fatal_error("Please add the [controller] attribute.");
		return;
	}
	$link_array['controller'] = $arg_arr_params['controller'];
	unset($arg_arr_params['controller']);
	
	if (!$arg_arr_params['action']) {
		$smarty->_trigger_fatal_error("Please add the [action] attribute.");
		return;
	}
	
	$link_array['action'] = $arg_arr_params['action'];
	unset($arg_arr_params['action']);
	
	if (!$arg_arr_params['model']) {
		$smarty->_trigger_fatal_error("Please add the [model] attribute.");
		return;
	}
	$model = $arg_arr_params['model'];
	unset($arg_arr_params['model']);
	
	if (!$arg_arr_params['class']) {
		$arg_arr_params['class'] = "mylink";
	}
	
	if ($arg_arr_params['prev']) {
		$prev = $arg_arr_params['prev'];
		unset($arg_arr_params['prev']);
	}
	
	if ($arg_arr_params['next']) {
		$next = $arg_arr_params['next'];
		unset($arg_arr_params['next']);
	}
	
	if ($arg_arr_params['link_array']) {
		$link_array['array'] = $arg_arr_params['link_array'];
		unset($arg_arr_params['link_array']);
	} else {
		$link_array['array'] = array();
	}

	
	$html_attr = "";
	if (is_array($arg_arr_params)) {
		while (list($k, $v) = each($arg_arr_params)) {
			if (!is_array($v)) $html_attr .= " " . $k . "=\"" . htmlspecialchars($v) . "\"";
		}
		reset($arg_arr_params);
	}

	$output = "";
	if ($prev) {
		if ($model['__previous_link']) {
			$link_array['array'] = array_merge($link_array['array'], $model['__previous_link']);
			$output .= "<a href=\"" . smarty_function_make_link($link_array, $smarty) . "\"" . $html_attr . ">" . $prev . "</a> &nbsp; &nbsp; &nbsp; \n";
		}
	}
	if (is_array($model['__paging_pages'])) {
		while (list($k, $v) = each($model['__paging_pages'])) {
			if ($v) {
				$link_array['array'] = array_merge($link_array['array'], $v);
				$output .= "<a href=\"" . smarty_function_make_link($link_array, $smarty) . "\"" . $html_attr . ">" . $k . "</a> \n";
			} else {
				$output .= "<b><u><font size=\"2\">" . $k . "</font></u></b> \n";
			}
		}
		reset($model['__paging_pages']);
	}
	if ($next) {
		if ($model['__next_link']) {
			$link_array['array'] = array_merge($link_array['array'], $model['__next_link']);
			$output .= "&nbsp; &nbsp; &nbsp; <a href=\"" . smarty_function_make_link($link_array, $smarty) . "\"" . $html_attr . ">" . $next . "</a>";
		}
	}
	return $output;
}

