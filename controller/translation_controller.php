<?
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/translation.php");
class translation_controller extends object implements controller
{
//------------------------------------------------------------------------------------------------------	
	/**
	Adds a new translation.
	*/
	function add_edit(){
		global $__in, $__out, $arr_AVAILABLE_LANGUAGES;
		$__out['available_languages'] = $arr_AVAILABLE_LANGUAGES;
		$translation = new translation();
		if ($__in['__is_form_submitted']) {		// if form is submitted
			if ($translation->save($__in['translation']['key'], $__in['translation']['text'])) {
				return dispatcher::redirect(array("action"=>"getall"), "added_successfully");
			} else {
				$__out['translation'] = $__in['translation'];
				return false;
			}
		} else {
			if ($__in['key']) {	// edit or prepared key
				$__out['translation']['key'] = $__in['key'];
				$__out['translation']['text'] = $translation->get_one_by_key($__in['key']);
			} else {	// add new empty
				$__out['translation'] = array();
			}
		}
		return true;
	}	// end function add
//------------------------------------------------------------------------------------------------------	
	function delete(){
		global $__in, $__out;
		$translation = new translation();
		if ($translation->delete($__in['key'])) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the translation is not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the translation is deleted
	}	// end action delete
//------------------------------------------------------------------------------------------------------	
	function delete_many(){
		global $__in, $__out;
		$translation = new translation();
		if ($translation->delete_many($__in['arr_keys'])) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the array of ids are not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the array of ids are deleted	
	}	// end action delete_many	
//------------------------------------------------------------------------------------------------------
	function getall(){
		global $__in, $__out;
		$translation = new translation();
		$__out['arr_translations'] = $translation->get_all();
		return true;
	}	// end action getall
//------------------------------------------------------------------------------------------------------
	function getone () {
		global $__in, $__out;
		$translation = new translation();
		$__out['translation'] = $translation->get_one_by_key($__in['key']);
		return true;
	}
//------------------------------------------------------------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "getall"));
	}
//------------------------------------------------------------------------------------------------------
}	// end class translation_controller
