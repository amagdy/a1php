<?
/**
@file useradmin_controller.php
@class useradmin_controller

@author Ahmed Magdy <a.magdy@a1works.com>
*/
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/user.php");

class useradmin_controller extends object implements controller
{
	/**
	Constructor
	*/
	public function __construct ()
	{
		global $__out;
		$group = new group();
                $__out['arr_group_id'] = $group->get_all_assoc("id", "name");
	}
//------------------------------------------------------------------------------------------------------	
	/**
	Adds a new user	*/
	public function add ()
	{
		global $__in, $__out;
		$user = new user();
		if ($__in['__is_form_submitted']) {		// if form is submitted
			try {
				$user->add($__in['user']);
				return dispatcher::redirect("getall", "added_successfully");
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
	Edits an existing user.
	*/
	public function edit ()
	{
		global $__in, $__out;
		$user = new user($__in['id']);
		if ($__in['__is_form_submitted']) {		// if form is submitted
			try {
				$user->edit($__in['user']);
				return dispatcher::redirect("getall", "updated_successfully");
			} catch (ValidationException $ex) {
				$ex->publish_errors();
				$__out['user'] = $__in['user'];
				$__out['user']['id'] = $__in['id'];
				return false;
			} catch (Exception $ex) {
				throw $ex;
			}
		} else {	// if form is not submitted
			$__out['user'] = $user->this_to_array();
			return true;
		}	// end if form submitted
	}	// end action edit
//------------------------------------------------------------------------------------------------------	
	/**
	Deletes one user	*/
	public function delete(){
		global $__in, $__out;
		try {
			$user = new user();
			$user->delete($__in['id']);
			return dispatcher::redirect("getall", "deleted_successfully");
		} catch (ValidationException $ex) {
			$ex->publish_errors();
			return dispatcher::redirect("getall");
		} catch (Exception $ex) {
			throw $ex;
		}
	}	// end action delete
//------------------------------------------------------------------------------------------------------	
	/**
	Deletes many users.
	*/
	public function delete_many ()
	{
		global $__in, $__out;
		try {
			$user = new user();
			$user->delete_many($__in['arr_ids']);
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} catch (ValidationException $ex) {
			$ex->publish_errors();
			return dispatcher::redirect(array("action"=>"getall"));
		} catch (Exception $ex) {
			throw $ex;
		}
		
	}	// end action delete_many	
//------------------------------------------------------------------------------------------------------
	/**
	Gets all users	*/
	public function getall ()
	{
		global $__in, $__out;
		try {
			$user = new user();
			$user->set_paging(25);
			$__out['arr_users'] = $user->get_all($__in['__orderby'], ($__in['__desc'] == 'yes' ? false : true));
		} catch (ValidationException $ex) {
			$ex->publish_errors();
		} catch (Exception $ex) {
			throw $ex;
		}
		$__out['user'] = $user->this_to_array();
		return true;
	}	// end action getall
//------------------------------------------------------------------------------------------------------
	/**
	Gets one user.
	*/
	public function getone () 
	{
		global $__in, $__out;
		try {
			$user = new user($__in['id']);
			$__out['user'] = $user->this_to_array();
                        $group = new group($user->group_id);
                        $__out['user']['group'] = $group;
		} catch (ValidationException $ex) {
			$ex->publish_errors();
		} catch (Exception $ex) {
			throw $ex;
		}
		return true;
	}
//------------------------------------------------------------------------------------------------------	
	/**
	Redirects to getall.
	*/
	public function index () 
	{
		return dispatcher::redirect("getall");
	}
//------------------------------------------------------------------------------------------------------
	/**
	Searches the database and returns the results in the same form as the getall form.
	*/
	public function search () 
	{
		global $__in, $__out;
		try {
			$user = new user();
			$user->set_paging(25);
			$__out['arr_users'] = $user->__search($__in['user_search'], $__in['__orderby'], ($__in['__desc'] == 'yes' ? false : true));
		} catch (ValidationException $ex) {
			$ex->publish_errors();
		} catch (Exception $ex) {
			throw $ex;
		}
		$__out['user_search_link'] = array("user_search" => $__in['user_search']);
		$__out['user'] = $user->this_to_array();
		return true;
	}
//------------------------------------------------------------------------------------------------------
	/*
	 * Resets the user password
	 * @global array $__in
	 * @global array $__out
	 * @return boolean
	 * @throws Exception
	 */
	function reset_password(){
		global $__in, $__out;
		if ($__in['__is_form_submitted']) {		// if form is submitted
			try {
				$user = new user();
				$user->reset_password($__in['id'], $__in['user']['new_password']);
				return dispatcher::redirect(array("action" => "getall"), "updated_successfully");
			} catch (ValidationException $ex) {
				$ex->publish_errors();
				$__out['user'] = $__in['user'];
				$__out['user']['id'] = $__in['id'];
				return true;
			} catch (Exception $ex) {
				throw $ex;
			}
		} else {	// if form is not submitted
			$__out['user'] = array("id" => $__in['id']);
			return true;
		}	// end if form submitted
	}	// end action edit
//------------------------------------------------------------------------------------------------------
	/**
	 * Dectivates a user account
	 * @throws Exception
	 * @global array $__in
	 * @global array $__out
	 * @return boolean
	 */
	function deactivate () {
		global $__in, $__out;
		try {
			$user = new user($__in['id']);
			$user->deactivate();
		} catch (ValidationException $ex) {
			$ex->publish_errors();
			return dispatcher::redirect("getall");
		} catch (Exception $ex) {
			throw $ex;
		}
		return dispatcher::redirect("getall", "updated_successfully");
	}
//------------------------------------------------------------------------------------------------------
	/**
	 * Activates a user account
	 * @throws Exception
	 * @global array $__in
	 * @global array $__out
	 * @return boolean
	 */
	public function activate () {
		global $__in, $__out;
		try {
			$user = new user($__in['id']);
			$user->activate();
		} catch (ValidationException $ex) {
			$ex->publish_errors();
			return dispatcher::redirect("getall");
		} catch (Exception $ex) {
			throw $ex;
		}
		return dispatcher::redirect("getall", "updated_successfully");
	}
//------------------------------------------------------------------------------------------------------
	/**
	 * Impersonates a user
	 * @global array $__in
	 * @global array $__out
	 * @return boolean
	 */
	public function impersonate () {
		global $__in, $__out;
		try {
			$user = new user($__in['id']);
			$user->impersonate();
		} catch (ValidationException $ex) {
			$ex->publish_errors();
		} catch (Exception $ex) {
			throw $ex;
		}
		return dispatcher::redirect(array("controller" => "user", "action" => "home"));
	}
}	// end class useradmin_controller
