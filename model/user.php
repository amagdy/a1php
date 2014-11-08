<?
/**
@file user.php
@class user
@author Ahmed Magdy <a.magdy@a1works.com>
*/
require_once(PHP_ROOT . "lib/parent_model.php");
class user extends parent_model 
{
//------------------------------------------------------------------------------	
	/**
	Constructor
	@param $arg_id [optional] default 0 primary key used to fetch a row from the database and wrap it into an object if it is not specified an empty object is returned
	*/
	public function __construct ($arg_id=0) 
	{
		$this->parent_model();
		$this->__table = "users";
		$this->__primary_key = "id";
		$this->__allowed_fields = array("email", "password", "name", "active", "group_id");

		// validation
		$this->add_validation ("email", "email", "invalid_email_address");
		$this->add_validation ("email", "uniq", "email_already_exists");
		$this->add_validation ("password", "presence", "please_enter_the_password");
		$this->add_validation ("group_id", "presence", "please_enter_the_group");
		
		// search criteria
		$this->__add_search("email", "like");
		$this->__add_search("name", "like");
		$this->__add_search("active", "eq");
		if ($arg_id) $this->array_to_this($this->get_one_by_id($arg_id), true);
		
	}	// end constructor
//------------------------------------------------------------------------------
	/**
	Adds a new user.
	@param arr_properties the array of all properties to be added
	@return the insert id or false on error
	*/
	public function add (array $arr_properties) 
	{
		$arr_properties = $this->filter_allowed_fields($arr_properties);
		$arr_properties = $this->filter_required_fields($arr_properties);
		$this->array_to_this($arr_properties);

		$this->registration_date = NOW();
		$this->registration_ip = ip2long($_SERVER['REMOTE_ADDR']);
		$this->activation_code = "000000000000";

		if ($this->is_error()) throw new ValidationException($this);
		return $this->__save();
	}
//------------------------------------------------------------------------------
	/**
	Edit user information
	@param arr_properties the array of all properties to be updated
	@return true if editted and false if not.
	*/
	public function edit (array $arr_properties) 
	{
		if (!$this->is_id($this->id)) {
			$this->add_validation_error("element_not_found");
			throw new ValidationException($this);
		}
		$arr_properties = $this->filter_allowed_fields($arr_properties);
		$this->array_to_this($arr_properties);
		if ($this->is_error()) throw new ValidationException($this);
		return $this->__save();
	}
//------------------------------------------------------------------------------
	/**
	Deletes a user from the users table.
	This object must be initialized and must have a value in the $this->id field.
	@return boolean true if deleted and false if not.
	
	public function delete($arg_id) 
	{
		if (!$this->is_id($arg_id)) {
			$this->add_validation_error("element_not_found");
			throw new ValidationException($this);
		}
		return $this->db_delete("DELETE FROM users WHERE id='%s'", array($arg_id));
	}*/
//------------------------------------------------------------------------------
	/**
	Deletes many users.	
	@param arg_arr_ids	array of user ids to be deleted.	
	@return true on success or false on failure if the the array if ids is not correct
	
	public function delete_many (array $arg_arr_ids) 
	{
		if (!is_array($arg_arr_ids)) return false;
		$user = new user();
		while (list (,$id) = each ($arg_arr_ids)) {
			try {
				$user->delete($id);
			} catch (Exception $ex) {}
		}
		return true;
	}*/
//------------------------------------------------------------------------------
	/**
	 * Register a new user
	 * @param array $arg_arr_properties the contents of the registration form
	 * @return boolean
	 * @throws ValidationException
	 */
	public function register (array $arg_arr_properties) {
		if ($arg_arr_properties['password'] == $arg_arr_properties['repassword']) $this->add_validation_error("please_retype_password_correctly", array(), "repassword");
		$arr_properties = $this->filter_allowed_fields($arr_properties);
		$this->array_to_this($arg_arr_properties);
		if ($this->is_error()) throw new ValidationException($this);
		if (!$this->__save()) return false;
		return false;
	}
//------------------------------------------------------------------------------
	/**
	Impersonates this user in other words it adds its id to the session and refreshes the page to make sure that the new request is done.
	@return boolean
	*/
	function impersonate() {
		$_SESSION['old_user_id'] = $_SESSION['user_id'];
		$_SESSION['user_id'] = $this->id;
		$_SESSION['group_id'] = $this->group_id;
		$group = new group($this->group_id);
		$_SESSION['layout'] = $group->layout;
		return true;
	}
//------------------------------------------------------------------------------
	/**
	User Login using email and password.
	returns true if logged in and false if not.
	@param String $arg_email
	@param String $arg_password
	@return boolean
	@throws ValidationException
	*/
	function login($arg_email, $arg_password){
		$this->skip_unique_validation();
		$this->email = $arg_email;
		$this->password = $arg_password;
		
		$user_info = $this->get_one_row_by(array("email" => $arg_email, "password" => $arg_password));
		if ($this->is_id($user_info['id'])) {
			if (!$user_info['active']) {
				$this->add_validation_error("user_inactive");
				throw new ValidationException($this);
			}
			
			$_SESSION['user_id'] = $user_info['id'];
			$_SESSION['group_id'] = $user_info['group_id'];
			$group = new group($user_info['group_id']);
			$_SESSION['layout'] = $group->layout;
			
			$this->__reset();
			$this->id = $user_info['id'];
			$this->session_id = session_id();
			$this->last_login_date = NOW();
			$this->last_login_ip = ip2long($_SERVER['REMOTE_ADDR']);
			$this->__save();
			
			return true;
		} else {
			$this->add_validation_error("wrong_email_or_password");
			throw new ValidationException($this);
		}
	}
//------------------------------------------------------------------------------	
	/**
	Logs out to be a visitor or return back to the user from which he made impersonation.
	@return true.
	*/
	function logout() {
		if (!$_SESSION['old_user_id']) {
			$_SESSION['user_id'] = 0;
			$_SESSION['group_id'] = 0;
			unset($_SESSION['layout']);
		} else {
			$arr_user_info = $this->get_one_by_id($_SESSION['old_user_id']);
			if ($arr_user_info['id']) {
				unset($_SESSION['old_user_id']);
				$_SESSION['user_id'] = $arr_user_info['id'];
				$_SESSION['group_id'] = $arr_user_info['group_id'];
				$group = new group($_SESSION['group_id']);
				$_SESSION['layout'] = $group->layout;
			}
		}
		return true;
	}
//------------------------------------------------------------------------------	
	/**
	The user changes his own password
	@param String $arg_old_password the old password.
	@param String $arg_new_password the new password.
	@param String $arg_renew_password retype new password.
	@return boolean
	@throws Exception
	@throws ValidationException
	*/
	public function change_password ($arg_old_password, $arg_new_password, $arg_renew_password) {
		if (!$this->is_id($this->id)) {
			throw new Exception("Invalid User");
		}

		if ($arg_new_password != $arg_renew_password) {
			$this->add_validation_error("please_make_sure_that_the_new_password_is_retyped_correctly", array(), "renew_password");
		}

		if (!$this->is_field_valid("password", $arg_new_password)) {
			$this->add_validation_error("please_write_the_new_password", array(), "new_password");
		}
		
		if ($this->password != $arg_old_password) {
			$this->add_validation_error("wrong_old_password", array(), "old_password");
		}
		
		if ($this->is_error()) throw new ValidationException($this);
		return $this->auto_update(array("password" => $arg_new_password), array("id" => $this->id));
	}
//------------------------------------------------------------------------------	
	/**
	The user changes his own information like the email and full name.
	@param email the email address of the user that should be unique and valid.
	@param name the full name of the user.
	@return changed successfully or not.
	*/
	public function change_info(array $arg_arr_user_info){
		if (!$this->is_id($this->id)) throw new Exception("Invalid User Account");
		$arg_arr_user_info = $this->filter_allowed_fields($arg_arr_user_info);
                $this->array_to_this($arg_arr_user_info);
		if ($this->is_error()) throw new ValidationException($this);
		unset($arg_arr_user_info['id']);
                return $this->auto_update($arg_arr_user_info, array('id' => $this->id));
	}	// end function change_info
//------------------------------------------------------------------------------
	public function reset_password ($arg_user_id, $arg_new_password) {
		if (!$this->is_id($arg_user_id)) throw new Exception("Invalid User");
                if (!$this->is_field_valid("password", $arg_new_password)) throw new ValidationException($this);
		return $this->auto_update(array("password" => $arg_new_password), array("id" => $arg_user_id));
	}	
//------------------------------------------------------------------------------
	public function get_group_users ($arg_group_id) {
		if (!$this->is_id($arg_group_id)) return array();
		return $this->db_select("SELECT * FROM users WHERE group_id=%d", array($arg_group_id));	
	}
//------------------------------------------------------------------------------
	/**
	The admin activates an inactive user.
	
	*/
	public function activate () {
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_user");
			return false;
		}
		if ($this->id == $_SESSION['user_id']) {
			return false;
		}
		return $this->db_update("UPDATE users SET active=1 WHERE id=%d", array($this->id));
	}
//------------------------------------------------------------------------------
	/**
	User activates himself using a link sent to his inbox.
	*/
	public function user_activate ($arg_username, $arg_code) {
		if (!$this->is_username($arg_username)) {
			add_error("couldnot_activate_user");
			return false;
		}
		if (!$arg_code) {
			add_error("couldnot_activate_user");
			return false;
		}
		if (!$this->db_update("UPDATE users SET active=1, activation_code='' WHERE username='%s' AND activation_code='%s'", array($arg_username, $arg_code))) {
			add_error("couldnot_activate_user");
			return false;
		}
		return true;
	}
//------------------------------------------------------------------------------
	/**
	
	*/
	public function deactivate () {
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_user");
			return false;
		}
		if ($this->id == $_SESSION['user_id']) {
			add_error("you_cannot_deactivate_your_own_account");
			return false;
		}
		return $this->db_update("UPDATE users SET active=0 WHERE id=%d", array($this->id));
	}
//------------------------------------------------------------------------------
	/**
	
	*/
	public function generate_activation_code () {
		$code = "";
		$str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
		$i = 1;
		while ($i < 11) {
			$n = rand(0, strlen($str)-1);
			$code .= substr($str, $n, 1);
			$i++;
		}
		return $code;
	}
//------------------------------------------------------------------------------
}		// end class user.
