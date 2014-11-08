<?
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/configuration.php");
class configuration_controller extends object implements controller
{
//------------------------------------------------------------------------------------------------------	
	/**
	Adds a new configuration.
	*/
	function add(){
		global $__in, $__out;
		if ($__in['__is_form_submitted']) {		// if form is submitted
			$configuration = new configuration();
			if ($configuration->add($__in['configuration']['variable_type'], $__in['configuration']['key'], $__in['configuration']['value'], $__in['configuration']['description'])) {
				return dispatcher::redirect(array("action"=>"getall"), "added_successfully");
			} else {
				$__out['configuration'] = $__in['configuration'];
				return false;
			}
		} else {	// if form is not submitted
			$__out['configuration'] = array();
			return true;
		}	// end if form submitted
	}	// end function add
//------------------------------------------------------------------------------------------------------	
	/**
	Edits an existing configuration.
	*/
	function edit(){
		global $__in, $__out;
		$configuration = new configuration($__in['id']);
		if ($__in['__is_form_submitted']) {		// if form is submitted
			if ($configuration->edit($__in['configuration']['variable_type'], $__in['configuration']['key'], $__in['configuration']['value'], $__in['configuration']['description'])) {	// editted successfully
				return dispatcher::redirect(array("action"=>"getall"), "updated_successfully");
			} else {	// could not edit
				$__out['configuration'] = $__in['configuration'];
				$__out['configuration']['id'] = $__in['id'];
				return false;
			}	
		} else {	// if form is not submitted
			$__out['configuration'] = $configuration->this_to_array();
			return true;
		}	// end if form submitted
	}	// end action edit
//------------------------------------------------------------------------------------------------------
	function admin_edit () {
		global $__in, $__out;
		$configuration = new configuration($__in['id']);
		if ($__in['__is_form_submitted']) {		// if form is submitted
			if ($configuration->admin_edit($__in['configuration']['value'])) {	// editted successfully
				return dispatcher::redirect(array("action" => "getall"), "updated_successfully");
			} else {	// could not edit
				$__out['configuration'] = $__in['configuration'];
				$__out['configuration']['id'] = $__in['id'];
				return false;
			}	
		} else {	// if form is not submitted
			$__out['configuration'] = $configuration->this_to_array();
			return true;
		}	// end if form submitted		
	}
//------------------------------------------------------------------------------------------------------
	function delete(){
		global $__in, $__out;
		$configuration = new configuration($__in['id']);
		if ($configuration->delete()) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the configuration is not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the configuration is deleted
	}	// end action delete
//------------------------------------------------------------------------------------------------------
	function getall(){
		global $__in, $__out;
		$configuration = new configuration();
		$__out['arr_configurations'] = $configuration->getall();
		return true;
	}	// end action getall
//------------------------------------------------------------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "getall"));
	}
//------------------------------------------------------------------------------------------------------
}	// end class configuration_controller
