<?
/**
 @file controller.php
 @class controller
 This class main function is request that takes the request array from any type of client and responds with the array of response after calling the appropriate subcontroller and action.
 */
require_once(PHP_ROOT . "uploads/permission/permissions.php");
require_once(PHP_ROOT . "model/user.php");
require_once(PHP_ROOT . "model/configuration.php");
class dispatcher {
//--------------------------------------------------------------------------------
/**
 Initiates a request and returns the output.
 Calls the right controller and calls the function needed from it.
 It uses the global __in array of request params containing the controler and the action and any other parameters.
 It also uses the global __out an array to add to then return in the output of the request.
 @return tru or false and changes the input and output arrays.
 @access Public.
 */
    public static function request () {
        global $__in, $__out;
        $__in['controller'] = strtolower($__in['controller']);
        $__in['action'] = strtolower($__in['action']);

        //-------------------------------------------------------------
        if (DEBUG == true) {
            if ($_GET['noview']=="yes") {
                print "### Input:\n---------------\n";
                print_r($__in);
            }
        }
        //-------------------------------------------------------------

        $__out = array_merge($__out, $__in);
        if (dispatcher::is_request_found($__in)) {
        // check permissions
            if ($__in['controller'] != "errors") {
                if (!dispatcher::is_request_allowed($__in)) {
                    throw new PermissionDeniedException($__in);
                }
            }
            // make a new instance of the ubcontroller needed.
            eval("\$subcontroller = new " . $__in['controller'] . "_controller();");
            // calling the method.
            call_user_func(array(&$subcontroller, $__in['action']));
        } else {
            throw new PageNotFoundException($__in);
        }
        return true;
    }	// end function request().
    //------------------------------------------------------------------------------
    /**
     Checks if the requested url is found or not
     It checks the requested controller and see if it is a class
     Then checks if there is a method in that class with the same name as the requested action
     */
    public static function is_request_found ($arg_arr_request) {
    // include the class file from the same directory of the main controller.
        if (!file_exists(dirname(__FILE__) . "/../controller/" . $arg_arr_request['controller'] . "_controller.php")) 
            throw new PageNotFoundException($arg_arr_request, "Could not find file : " . dirname(__FILE__) . "/../controller/" . $arg_arr_request['controller'] . "_controller.php", 256);

        require_once(dirname(__FILE__) . "/../controller/" . $arg_arr_request['controller'] . "_controller.php");

        if (!class_exists($arg_arr_request['controller'] . "_controller"))
            throw new PageNotFoundException($arg_arr_request, "Could not find class : " . $arg_arr_request['controller'] . "_controller", 256);

        if (!method_exists($arg_arr_request['controller'] . "_controller", $arg_arr_request['action']))
            throw new PageNotFoundException($arg_arr_request, "Cannot call method " . $arg_arr_request['controller'] . "_controller::" . $arg_arr_request['action'] . "().", 256);

        return true;
    }
    //------------------------------------------------------------------------------
    /**
     Filters an array of request arrays to return the allowed requests only for the current user.
     @param arg_arr_requests array of all available requests.
     @return an array of allowed requests only for the current user.
     @see is_request_allowed().
     @see get_allowed_links().
     @see is_request_allowed().
     @access Public.
     */
    public static function get_allowed_requests ($arg_arr_requests) {
        global $__in, $__out;
        $arr_allowed_requests = array();
        if(!is_array($arg_arr_requests)) return array();
        while (list(,$request) = each($arg_arr_requests)) {
            if (!$request) {
                $arr_allowed_requests[] = array();
            } else {
                if (dispatcher::is_request_allowed($request)) $arr_allowed_requests[] = $request;	// if request is allowed then add it to the array of requests
            }
        }
    }
    //------------------------------------------------------------------------------
    /**
     Check if the request array is allowed for the current user or not.
     @param arg_arr_request the array of request params.
     @return boolean true or false.
     @access Public.
     */
    public static function is_request_allowed($arg_arr_request) {
        global $__in, $__out, $arr_groups_permissions;
        if (!$_SESSION["user_id"]) {
            $_SESSION['referrer'] = $_SERVER['HTTP_REFERRER'];
            $_SESSION['user_id'] = 0;
            $_SESSION['group_id'] = 0;
        }
        $bool_allowed = false;
        if (!is_array($arr_groups_permissions[$_SESSION['group_id']])) return false;
        $arr_group_perms = $arr_groups_permissions[$_SESSION['group_id']];
        // loop on allow permissions
        while (list(,$row) = each($arr_group_perms)) {
            if ($row['allow'] == 1) {
                if ($row['controller']==$arg_arr_request['controller'] || $row['controller']=="*") {
                    if ($row['action']==$arg_arr_request['action'] || $row['action']=="*") {
                        if ($row['extra_params']) {
                            reset($row['extra_params']);
                            while (list($x_k, $x_v) = each($row['extra_params'])) {
                                if($row['extra_params'][$x_k] != $arg_arr_request[$x_k]) {
                                    $bool_allowed = false;
                                    break;
                                }
                                $bool_allowed = true;
                            }
                        } else {
                            $bool_allowed = true;
                        }
                    }
                }
            }
        }

        if ($bool_allowed == false) return false;

        reset($arr_group_perms);
        // loop on deny permissions
        while (list(,$row) = each($arr_group_perms)) {
            if ($row['allow'] == 0) {
                if ($row['controller']==$arg_arr_request['controller'] || $row['controller']=="*") {
                    if ($row['action']==$arg_arr_request['action'] || $row['action']=="*") {
                        if ($row['extra_params']) {
                            reset($row['extra_params']);
                            while (list($x_k, $x_v) = each($row['extra_params'])) {
                                if($row['extra_params'][$x_k] != $arg_arr_request[$x_k]) {
                                    $bool_allowed = true;
                                    break;
                                }
                                $bool_allowed = false;
                            }
                        } else {
                            $bool_allowed = false;
                        }
                    }
                }
            }
        }
        return $bool_allowed;
    }
    //------------------------------------------------------------------------------
    /**
     Sets the language in session.
     Decides the current language by checking the request and the available and default language.
     @return String the current session name.
     @access Public.
     */
    public static function set_language() {
        global $__in, $__out, $arr_AVAILABLE_LANGUAGES;
        $arr_available_languages = array_keys($arr_AVAILABLE_LANGUAGES);
        if ($_SESSION['lang']) {	// there was session before
            if ($__in['lang']) {
                if ($__in['lang'] != $_SESSION['lang']) {
                    if (in_array($__in['lang'], $arr_available_languages)) {
                        $_SESSION['lang'] = $__in['lang'];
                    } else {
                        $_SESSION['lang'] = DEFAULT_LANGUAGE;
                    }
                }
            }
        } else {	// first time
            if ($__in['lang']) {
                if (in_array($__in['lang'], $arr_available_languages)) {
                    $_SESSION['lang'] = $__in['lang'];
                } else {
                    $_SESSION['lang'] = DEFAULT_LANGUAGE;
                }
            } else {
                $_SESSION['lang'] = DEFAULT_LANGUAGE;
            }
        }

        if(!in_array($_SESSION['lang'], $arr_available_languages)) {
            $_SESSION['lang'] = DEFAULT_LANGUAGE;
        }
        $__in['lang'] = $_SESSION['lang'];
        return $_SESSION['lang'];
    }	// end function set_language
    //-----------------------------------------------------------------------------------
    /**
     Redirects to the given request and Adds an information message to the global array of information $__info.
     @param arg_arr_request the array that contains the request parameteres (Controller, action ...).
     @param arg_str_info_msg [Optional] the message that is shown to the user.
     @param arg_arr_info_params [Optional] any extra parameters added to the info message if the message text has placeholders (Like %s or %d).
     @param arg_type [Optional] The type of the message that can be ("info" or "warning") which decides the style of the information shown.
     @return the output of the new request.
     */
    public static function redirect ($arg_arr_request, $arg_str_info_msg="", array $arg_arr_info_params=array(), $arg_type="info") {
        global $__in;
        if (!is_array($arg_arr_request)) {
            $arr_req = split("/", $arg_arr_request);
            $arg_arr_request = array();
            if (count($arr_req) == 1) {
                $arg_arr_request['action'] = $arr_req[0];
            } else if (count($arr_req) == 2) {
                    $arg_arr_request['controller'] = $arr_req[0];
                    $arg_arr_request['action'] = $arr_req[1];
                }
        }
        if ($arg_str_info_msg) add_info($arg_str_info_msg, $arg_arr_info_params, $arg_type);
        if (!$arg_arr_request['controller']) $arg_arr_request['controller'] = $__in['controller'];
        $_SESSION['__GET_REPITITION_STOPPER_OLD'] = $__in;
        $_SESSION['__GET_REPITITION_STOPPER_NEW'] = $arg_arr_request;
        $__in = $arg_arr_request;
        return dispatcher::request();
    }
}	// end class controller.
