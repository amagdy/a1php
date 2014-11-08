<?
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/user.php");
class user_controller extends object implements controller {
//------------------------------------------------------------------------------------------------------	
/**
 * Regsiters a new user
 * @global array $__in
 * @global array $__out
 * @return boolean
 */
    function register() {
        global $__in, $__out;
        if ($__in['__is_form_submitted']) {		// if form is submitted
            try {
                $user = new user();
                $user->register($__in['user']);
                return dispatcher::redirect("getall", "registered_successfully");
            } catch (ValidationException $ex) {
                $ex->publish_errors();
                $__out['user'] = $__in['user'];
                return false;
            } catch (Exception $ex) {
                throw $ex;
            }
        } else {	// if form is not submitted
            $__out['user'] = array();
            return true;
        }	// end if form submitted
    }	// end function add
    //------------------------------------------------------------------------------------------------------
    /**
     Update the information of the current user.
     */
    function change_info() {
        global $__in, $__out;
        $user = new user($_SESSION['user_id']);
	$user->delete_by(array("name" => "h.adel@birdict.com", "email" => '123'));
        if ($__in['__is_form_submitted']) {		// if form is submitted
            try {
                $user->change_info($__in['user']);
                return dispatcher::redirect(array("action"=>"home"), "updated_successfully");
            }catch (ValidationException $ex) {
                $ex->publish_errors();
                $__out['user'] = $__in['user'];
                return false;
            }
        } else {	// if form is not submitted
            $__out['user'] = $user->this_to_array();
            return true;
        }	// end if form submitted
    }	// end action edit
    //------------------------------------------------------------------------------------------------------
    /**
     Change user password.
     */
    function change_password() {
        global $__in, $__out;
        if ($__in['__is_form_submitted']) {
            if ($__in['new_password'] != $__in['renew_password']) {
                add_error("please_retype_password_correctly", array(), "renew_password");
                return false;
            }
            $user = new user($_SESSION['user_id']);
            if (!$user->change_password($__in['user']['old_password'], $__in['user']['new_password'], $__in['user']['renew_password'])) {
                return false;
            } else {
                return dispatcher::redirect(array("action" => "home"), "password_changed");
            }
        }
    }	// end action change_password
    //------------------------------------------------------------------------------------------------------

    //------------------------------------------------------------------------------------------------------
    function home () {
        global $__in, $__out;
        $user = new user($_SESSION['user_id']);
        $__out['user'] = $user->secure_output(array(), array("session_id", "password", "registration_ip", "flags", "group_id"));
        return true;
    }
    //------------------------------------------------------------------------------------------------------
    function login () {
        global $__in, $__out;
        if ($__in['__is_form_submitted']) {
            try {
                $user = new user();
                $user->login($__in['user']['email'], $__in['user']['password']);
                if ($_SESSION['__post_login_request']) return dispatcher::redirect($_SESSION['__post_login_request'], "welcome_user", array($user->name));
                return dispatcher::redirect(array("action" => "home"), "welcome_user", array($user->name));
            } catch (ValidationException $ex) {
                $ex->publish_errors();
                $__out['user'] = $__in['user'];
                return false;
            } catch (Exception $ex) {
                throw $ex;
            }
        }
        return true;
    }	// end action login
    //------------------------------------------------------------------------------------------------------
    function logout () {
        global $__in, $__out;
        $user = new user($_SESSION['user_id']);
        $user->logout();
        if ($_SESSION['user_id']) {
            return dispatcher::redirect(array("action" => "home"), "logged_out_successfully");
        } else {
            return dispatcher::redirect(array("action" => "login"), "logged_out_successfully");
        }
    }
    //------------------------------------------------------------------------------------------------------
    function index () {
        return dispatcher::redirect(array("action" => "home"));
    }
//------------------------------------------------------------------------------------------------------
}	// end class user_controller
