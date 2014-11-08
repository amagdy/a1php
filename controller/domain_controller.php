<?
require_once(PHP_ROOT . "lib/controller.php");
require_once(dirname(__FILE__) . "/../model/domain.php");
class domain_controller extends object implements controller
{
//------------------------------------------------------------------------------------------------------	
	/**
	Adds a new domain.
	*/
	function add(){
		global $__in, $__out;
		if ($__in['__is_form_submitted']) {		// if form is submitted
			$domain = new domain();
			if ($domain->add($__in['domain']['domain_name'])) {
				return dispatcher::redirect(array("action" => "getall"), "added_successfully");
			} else {
				$__out['domain'] = $__in['domain'];
				return false;
			}
		} else {	// if form is not submitted
			$__out['domain'] = array();
			return true;
		}	// end if form submitted
	}	// end function add
//------------------------------------------------------------------------------------------------------	
	/**
	Edits an existing domain.
	*/
	function edit(){
		global $__in, $__out;
		$domain = new domain($__in['id']);
		if ($__in['__is_form_submitted']) {		// if form is submitted
			if ($domain->edit($__in['domain']['domain_name'])) {	// editted successfully
				return dispatcher::redirect(array("action"=>"getall"), "updated_successfully");
			} else {	// could not edit
				$__out['domain'] = $__in['domain'];
				$__out['domain']['id'] = $__in['id'];
				return false;
			}	
		} else {	// if form is not submitted
			$__out['domain'] = $domain->this_to_array();
			return true;
		}	// end if form submitted
	}	// end action edit
//------------------------------------------------------------------------------------------------------	
	function delete(){
		global $__in, $__out;
		$domain = new domain($__in['id']);
		if ($domain->delete()) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the domain is not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the domain is deleted
	}	// end action delete
//------------------------------------------------------------------------------------------------------	
	function delete_many(){
		global $__in, $__out;
		$domain = new domain();
		if ($domain->delete_many($__in['arr_ids'])) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the array of ids are not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the array of ids are deleted	
	}	// end action delete_many	
//------------------------------------------------------------------------------------------------------
	function getall(){
		global $__in, $__out;
		$domain = new domain();
		$domain->set_paging(15);
		$__out['arr_domains'] = $domain->getall();
		$__out['domain'] = $domain->this_to_array();
		return true;
	}	// end action getall
//------------------------------------------------------------------------------------------------------
	function getone () {
		global $__in, $__out;
		$domain = new domain($__in['id']);
		$__out['domain'] = $domain->this_to_array();
		return true;
	}
//------------------------------------------------------------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "getall"));
	}
//------------------------------------------------------------------------------------------------------
}	// end class domain_controller
