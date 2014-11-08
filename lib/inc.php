<?
/**
__autoload is a magic function from PHP 5 used to auto require one classes that are called without being required before
This function is called when a new instance of a class is needed to be created when its code is not included already by require_once
It searches the controller, model and lib directories for the needed class and includes it if it exists or throws an exception if the class was not found
@param $arg_class_name the class name to be loaded
*/
function __autoload($arg_class_name) {
	if (strpos($arg_class_name, "_controller") !== false) {		// controller
		if (file_exists(PHP_ROOT . "controller/" . $arg_class_name . ".php")) {
			require_once(PHP_ROOT . "controller/" . $arg_class_name . ".php");
			return;
		}	
	} else {	// model or lib
		if (file_exists(PHP_ROOT . "model/" . $arg_class_name . ".php")) {
			require_once(PHP_ROOT . "model/" . $arg_class_name . ".php");
			return;
		} else if (file_exists(PHP_ROOT . "lib/" . $arg_class_name . ".php")) {
			require_once(PHP_ROOT . "lib/" . $arg_class_name . ".php");
			return;
		}
	}
	return false;
    	//throw new Exception("Unable to load class : " . $arg_class_name);
}

/**
Adds an error to the array of errors that is shown to the user.
This function is not used for adding debugging errors.
@param arg_str_error_msg The error message to be shown to the user.
@param arg_arr_error_params	[Optional] Any extra parameters to be added to the error message if the error message has any place holders (Like %s and %d).
@param arg_str_field_name [Optional] The field name of the field that generated this error in case this error is generated by a validation function.
*/
function add_error ($arg_str_error_msg, $arg_arr_error_params=array(), $arg_str_field_name="") {
	global $__errors;
	if (!$arg_arr_error_params) $arg_arr_error_params = array();
	$arr_error = array();
	$arr_error['error_msg'] = $arg_str_error_msg;
	if ($arg_arr_error_params) $arr_error['error_params'] = $arg_arr_error_params;
	if ($arg_str_field_name) $arr_error['field_name'] = $arg_str_field_name;
	$__errors[] = $arr_error;
}
//------------------------------------------------------------------------
/**
Adds an information message to the global array of information.
@param String $arg_str_info_msg the message that is shown to the user.
@param array $arg_arr_info_params [Optional] any extra parameters added to the info message if the message text has placeholders (Like %s or %d).
@param String $arg_type [Optional] default "info" The type of the message that can be ("info" or "warning") which decides the style of the information shown.
*/
function add_info ($arg_str_info_msg, array $arg_arr_info_params=array(), $arg_type="info") {
	global $__info;
	$arr_info = array();
	$arr_info['info_msg'] = $arg_str_info_msg;
	if ($arg_arr_info_params) $arr_info['info_params'] = $arg_arr_info_params;
	$arr_info['type'] = $arg_type;
	$__info[] = $arr_info;
}


//------------------------------------------------------------------------
/**
custom error handler to show a full stack trace if DEBUG == true 
or saves the stack trace to a log file if DEBUG == false
@param errno the error number of the error
@param errmsg the error message to be printed
@param filename the file name of the file that produced the error
@param linenum the error line number
@param vars extra variables
*/
function error_handler($errno, $errmsg, $filename, $linenum, $vars)
{
	global $arr_dbprofiles;
	if (!in_array($errno, array(1, 2, 4, 16, 32, 64, 128, 256, 512))) return false;		// if the error is a notice then do nothing
	$str_error .= "<h3><font color='#990000'>" . $errmsg . "</font></h3>\n<h4>";
	$arr_trace = debug_backtrace();
	next($arr_trace);
	while (list(,$arr_function) = each($arr_trace)) {
		$args = array();
		if (is_array($arr_function['args'])) {
			while (list(, $arg) = each($arr_function['args'])) {
				if (is_numeric($arg)) {
					$args[] = $arg;
				} elseif (is_array($arg)) {
					$args[] = var_export($arg, true);
				} else {
					$args[] = "\"" . (is_object($arg) ? get_class($arg) : $arg) . "\"";
				}
			}
		}
		$str_error .= " - " . $arr_function["file"] . " - Line: " . $arr_function['line'] . " - " . ($arr_function['class'] ? $arr_function['class'] . $arr_function['type'] : "") . $arr_function['function'] . "(" . join(", ", $args) . ");<br/>\n";
	}
	$str_error .= "</h4>\n\n";
	if (ereg("mysql_connect", $str_error)) $str_error = "MySQL Connect Error: on DB Profile: " . $arr_dbprofiles[DEFAULT_DBPROFILE];
	// if debug = true or the client is on the same machine as the server then debug messages can be shown.
	if (DEBUG == true) {
		print $str_error;
		exit();
	} else {
		error_log($str_error);
		print INTERNAL_ERRORMSG;
		exit();
	}
}
//------------------------------------------------------------------------
/**
custom error handler to show a full stack trace if DEBUG == true 
or saves the stack trace to a log file if DEBUG == false
@param errno the error number of the error
@param errmsg the error message to be printed
@param filename the file name of the file that produced the error
@param linenum the error line number
@param vars extra variables
*/
function exception_handler($arg_exception)
{
	global $arr_dbprofiles;
	$str_error = $arg_exception;
	if (ereg("mysql_connect", $str_error)) $str_error = "MySQL Connect Error: on DB Profile: " . $arr_dbprofiles[DEFAULT_DBPROFILE];
	// if debug = true or the client is on the same machine as the server then debug messages can be shown.
	if (DEBUG == true) {
		print "<h3>" . nl2br($str_error) . "</h3>";
		exit();
	} else {
		error_log($str_error);
		print INTERNAL_ERRORMSG;
		exit();
	}
}
//------------------------------------------------------------------------
/**
NOW() gets the current date and time in a format that is mysql like 2009-01-31 16:50:33
used as a php replacement for the MySQL function NOW()
It can also be used to get a date before or after now.
If the $arg_plus_minus_seconds is negative then the date is in the past and if it is positive then the date is in the future leave it empty 
@param $arg_plus_minus_seconds is the number of seconds added to thor 0e current time if the number is negative it is subtracted from the current time
@return current date and time 
*/
function NOW($arg_plus_minus_seconds=0) {
	$arg_plus_minus_seconds = intval($arg_plus_minus_seconds);
	return date('Y-m-d H:i:s', time() + $arg_plus_minus_seconds);
}
//----------------------------------------------------
/**
Reorder the $_FILES array.
@autor jess@semlabs.co.uk
http://www.php.net/manual/en/features.file-upload.multiple.php
Uploading multiple files
jess at semlabs dot co dot uk
03-Mar-2009 08:32
*/
function reorder_files_array($files, $name=null, &$new=false, $path=false) {
	$names = array( 'name' => 'name', 'type' => 'type', 'tmp_name' => 'tmp_name', 'error' => 'error', 'size' => 'size' );
	foreach ($files as $key => &$part) {
		$key = ( string ) $key;
		if (in_array($key, $names)) $name = $key;
		if (!in_array($key, $names)) $path[] = $key;
		if (is_array($part)) {
			$part = reorder_files_array($part, $name, $new, $path);
		} elseif (!is_array($part)) {
			$current = &$new;
			foreach ($path as $p) {
				$current = &$current[$p];
			}
			$current[$name] = $part;
			unset($path);
			$name = null;
		}
	}
	return $new;
}
//------------------------------------------------------------------------
	/**
	Redirects the browser to another page.
	Shows a language specific message and redirects to the given url.
	@param arg_str_url the url to redirect to.
	@access Public.
	*/
	function go_to_url ($arg_str_url="") {
		if(!$arg_str_url) return false;
		header("Location: " . $arg_str_url);
		exit();
	}
//---------------------------------------------------

//------------------------------------------------------------------------
	/**
	check the value if it is equal to the __CAPTCHA value saved in session
	@param $arg_value the value to be checked
	@return true/false
	*/
	function check_captcha ($arg_value) {
		return ($arg_value == $_SESSION['__CAPTCHA']);
	}