<?
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/links_category.php");

class links_category_controller extends object implements controller
{
//------------------------------------------------------------------------------------------------------	
	/**
	Adds a new links_category.
	*/
	function add(){
		global $__in, $__out;
		$links_category = new links_category();
		$__out['arr_langs'] ['ar']="Arabic";
		$__out['arr_langs'] ['en']="English";
		if ($__in['__is_form_submitted']) {		// if form is submitted
			
			if ($links_category->add($__in['links_category']['name'], $__in['links_category']['lang'])) {
				return dispatcher::redirect(array("action"=>"getall"), "added_successfully");
			} else {
				$__out['links_category'] = $__in['links_category'];
				return false;
			}
		} else {	// if form is not submitted
			$__out['links_category'] = array();
			return true;
		}	// end if form submitted
	}	// end function add
//------------------------------------------------------------------------------------------------------	
	/**
	Edits an existing links_category.
	*/
	function edit () {
		global $__in, $__out;
		$__out['arr_langs'] ['ar']="Arabic";
		$__out['arr_langs'] ['en']="English";

		$links_category = new links_category($__in['id']);
		if ($__in['__is_form_submitted']) {		// if form is submitted
			if ($links_category->edit($__in['links_category']['name'], $__in['links_category']['lang'])) {	// editted successfully
				return dispatcher::redirect(array("action"=>"getall"), "updated_successfully");
			} else {	// could not edit
				$__out['links_category'] = $__in['links_category'];
				$__out['links_category']['id'] = $__in['id'];
				return false;
			}	
		} else {	// if form is not submitted
			$__out['links_category'] = $links_category->this_to_array();
			return true;
		}	// end if form submitted
	}	// end action edit
//------------------------------------------------------------------------------------------------------	
	function delete () {
		global $__in, $__out;
		$links_category = new links_category($__in['id']);
		if ($links_category->delete()) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the links_category is not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the links_category is deleted
	}	// end action delete
//------------------------------------------------------------------------------------------------------	
	function delete_many(){
		global $__in, $__out;
		$links_category = new links_category();
		if ($links_category->delete_many($__in['arr_ids'])) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the array of ids are not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the array of ids are deleted	
	}	// end action delete_many	
//------------------------------------------------------------------------------------------------------
	function getall () {
		global $__in, $__out;
		$links_category = new links_category();
		$links_category->set_paging(15);
		$__out['links_category'] = $links_category->this_to_array();
		$__out['arr_links_categories'] = $links_category->getall();
		return true;
	}	// end action getall
//------------------------------------------------------------------------------------------------------
	function getone () {
		global $__in, $__out;
		$links_category = new links_category($__in['id']);
		$__out['links_category'] = $links_category->this_to_array();
		return true;
	}
//------------------------------------------------------------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "getall"));
	}
//------------------------------------------------------------------------------------------------------
}	// end class links_category_controller

