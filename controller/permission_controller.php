<?
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/permission.php");
class permission_controller extends object implements controller
{
//------------------------------------------------------------------------------------------------------	
	/**
	Adds a new permission.
	*/
	function add(){
		global $__in, $__out;
		if ($__in['__is_form_submitted']) {		// if form is submitted
			$permission = new permission();
			if ($permission->add($__in['permission']['controller'], $__in['permission']['action'], $__in['permission']['extra_params'], $__in['permission']['allow'], $__in['permission']['description'])) {
				return dispatcher::redirect(array("action"=>"getall"), "added_successfully");
			} else {
				$__out['permission'] = $__in['permission'];
				return false;
			}
		} else {	// if form is not submitted
			$__out['permission'] = array();
			return true;
		}	// end if form submitted
	}	// end function add
//------------------------------------------------------------------------------------------------------	
	/**
	Edits an existing permission.
	*/
	function edit(){
		global $__in, $__out;
		$permission = new permission($__in['id']);
		if ($__in['__is_form_submitted']) {		// if form is submitted
			if ($permission->edit($__in['permission']['controller'], $__in['permission']['action'], $__in['permission']['extra_params'], $__in['permission']['allow'], $__in['permission']['description'])) {	// editted successfully
				return dispatcher::redirect(array("action"=>"getall"), "updated_successfully");
			} else {	// could not edit
				$__out['permission'] = $__in['permission'];
				$__in['permission']['id'] = $__in['id'];
				return false;
			}	
		} else {	// if form is not submitted
			$__out['permission'] = $permission->this_to_array();
			return true;
		}	// end if form submitted
	}	// end action edit
//------------------------------------------------------------------------------------------------------	
	function delete(){
		global $__in, $__out;
		$permission = new permission($__in['id']);
		if ($permission->delete()) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the permission is not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the permission is deleted
	}	// end action delete
//------------------------------------------------------------------------------------------------------	
	function delete_many(){
		global $__in, $__out;
		$permission = new permission();
		if ($permission->delete_many($__in['arr_ids'])) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the array of ids are not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the array of ids are deleted	
	}	// end action delete_many	
//------------------------------------------------------------------------------------------------------
	function getall(){
		global $__in, $__out;
		$permission = new permission();
		$__out['arr_permissions'] = $permission->getall();
	}	// end action getall
//------------------------------------------------------------------------------------------------------
	function getone () {
		global $__in, $__out;
		$permission = new permission($__in['id']);
		$__out['permission'] = $permission->this_to_array();
		return true;
	}
//------------------------------------------------------------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "getall"));
	}
//------------------------------------------------------------------------------------------------------
}	// end class permission_controller
