<?
/**
@author Ahmed Magdy Ezzeldin <admin@a1works.com>
Create a file field that can be used to upload a file or an image with no effort from the side of the developer
@param field String
@param image boolean [optional] default false
@param value String [optional] default ""
@param replace [optional] default false
@param uniq [optional] default true (prefixes filename with a uniqid with timestamp)
@param optional [optional] default true
@param upload_folder [optional] default value is like this example "uploads/user/" where user is the irst part of the field parameter
@param delete_word [optional] default "Delete"
@param image_attr array or string [optional] html attributes for the image if any.
@param ... [optional] any extra paramters are added as HTML attributes for the input file field
@return file input field
*/
function smarty_function_file ($arg_arr_params, &$smarty) {
	$arr_copy = $arg_arr_params;
	if (!$arg_arr_params['field']) {
		$smarty->_trigger_fatal_error("Please add the [field] attribute.");
		return;
	}
	// defaults
	if (!isset($arg_arr_params['value'])) $arg_arr_params['value'] = "";
	$arg_arr_params['image'] = (isset($arg_arr_params['image']) ? ($arg_arr_params['image'] ? true : false) : false);
	$arg_arr_params['replace'] = (isset($arg_arr_params['replace']) ? ($arg_arr_params['replace'] ? true : false) : false);
	$arg_arr_params['optional'] = (isset($arg_arr_params['optional']) ? ($arg_arr_params['optional'] ? true : false) : true);
	$arg_arr_params['uniq'] = (isset($arg_arr_params['uniq']) ? ($arg_arr_params['uniq'] ? true : false) : true);
	$arg_arr_params['upload_folder'] = (isset($arg_arr_params['upload_folder']) ? $arg_arr_params['upload_folder'] : "");
	$arg_arr_params['delete_word'] = (isset($arg_arr_params['delete_word']) ? $arg_arr_params['delete_word'] : "Delete");
	
	// html attributes
	unset($arr_copy['field']);
	unset($arr_copy['value']);
	unset($arr_copy['image']);
	unset($arr_copy['replace']);
	unset($arr_copy['image_attr']);
	unset($arr_copy['optional']);
	unset($arr_copy['uniq']);
	unset($arr_copy['upload_folder']);
	$html_attr = "";
	if (is_array($arr_copy)) {
		while (list($k, $v) = each($arr_copy)) {
			if (!is_array($v)) $html_attr .= " " . $k . "=\"" . htmlspecialchars($v) . "\"";
		}
		reset($arr_copy);
	}
	
	
	// image or link html attributes
	$image_attr = "";
	if (is_array($arg_arr_params['image_attr'])) {
		while (list($k, $v) = each($arg_arr_params['image_attr'])) {
			if (!is_array($v)) $image_attr .= " " . $k . "=\"" . htmlspecialchars($v) . "\"";
		}
		reset($arg_arr_params['image_attr']);
	} elseif (isset($arg_arr_params['image_attr'])) {
		$image_attr = ' ' . $arg_arr_params['image_attr'];
	}
	
	// get field name without the array parts
	$field = str_replace("]", "", $arg_arr_params['field']);
	$field = str_replace("[", " ", $field);
	$field = trim($field);
	$field = split(" ", $field);
	$model = $field[0];
	$field = end($field);
	if (!$arg_arr_params['upload_folder']) $arg_arr_params['upload_folder'] = "uploads/" . $model . "/";
	
	// get flags
	$flags = 0x0;
	if ($arg_arr_params['replace']) $flags |= 0x1;
	if ($arg_arr_params['image']) $flags |= 0x2;
	if ($arg_arr_params['optional']) $flags |= 0x4;
	if (strpos($arg_arr_params['field'], "[]") !== false) $flags |= 0x8;	// multiple files
	if ($arg_arr_params['uniq']) $flags |= 0x10;
	
	// prepare output
	$output = '<span id="' . $field . '_span">';
	$output .= '<input type="file" name="' . $arg_arr_params['field'] . '"' . $html_attr . "/>\n";
	$output .= '<input type="hidden" id="' . $field . '_id" name="__files_flags[' . $field . ']' . ($flags & 0x8 ? '[]' : '') . '" value="' . $flags . "\"/>\n";
	$output .= '</span>';
	$output .= '<input type="hidden" name="__files_old[' . $field . ']' . ($flags & 0x8 ? '[]' : '') . '" value="' . $arg_arr_params['value'] . "\"/>\n";
	$output .= "<script>document.getElementById('" . $field . "_id').form.enctype=\"multipart/form-data\";</script>\n";

	if ($arg_arr_params['value'] && !is_array($arg_arr_params['value'])) {
		if ($arg_arr_params['image']) {
			$output .= "<img src=\"" . HTML_ROOT . $arg_arr_params['upload_folder'] . $arg_arr_params['value'] . "\"" . $image_attr . "/>";
		} else {
			$output .= "<a href=\"" . HTML_ROOT . $arg_arr_params['upload_folder'] . $arg_arr_params['value'] . "\"" . $image_attr . ">" . $arg_arr_params['value'] . "</a>";
		}
		// add delete checkbox
		if ($arg_arr_params['optional']) {
			$output .= "<input type=\"checkbox\" name=\"__files_to_be_deleted[" . $field . "]" . ($flags & 0x8 ? "[]" : "") . "\" value=\"" . $arg_arr_params['value'] . "\"/>" . $arg_arr_params['delete_word'];
		}
	}
	return $output;
}

