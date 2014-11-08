<?
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/link.php");
require_once(PHP_ROOT . "model/links_category.php");
require_once(PHP_ROOT . "model/domain.php");
class link_controller extends object implements controller
{
//------------------------------------------------------------------------------------------------------	
	/**
	Adds a new link.
	*/
	function add(){
		global $__in, $__out;
		$__out['arr_langs'] ['ar']="Arabic";
		$__out['arr_langs'] ['en']="English";
		$domain = new domain();
		$link = new link();
		$links_category = new links_category();
		$__out['arr_categories'] = $links_category->assoc_array_from_result_array($links_category->getall(), "id", "name");

		$arr_result_links = $link->getall();
		
		if (is_array($arr_result_links)) {
			while (list(,$row) = each($arr_result_links)) {
				$__out['arr_parents'][$row['id']] = $row['domain_name'] . $row['friendly_url'];
			}
		}
		$__out['arr_domains'] = $domain->assoc_array_from_result_array($domain->getall(), "id", "domain_name");
		$link->getall_category_links();
		if ($__in['__is_form_submitted']) {		// if form is submitted
			if ($link->add($__in['link']['domain_id'], $__in['link']['friendly_url'], 
				$__in['link']['real_url'],$__in['link']['title'],
				$__in['link']['description'],$__in['link']['keywords'],$__in['link']['enable_social_bookmarking'],
				$__in['link']['parent_id'],$__in['link']['category_id'],$__in['link']['lang'],$__in['link']['names'],$__in['link']['urls'])) {
				return dispatcher::redirect(array("action" => "getall"), "added_successfully");
			} else {
				$__out['link'] = $__in['link'];
				return false;
			}
		} else {	// if form is not submitted
			$__out['link'] = array();
			return true;
		}	// end if form submitted
	}	// end function add
//------------------------------------------------------------------------------------------------------	
	/**
	Edits an existing link.
	*/
	function edit(){
		global $__in, $__out;
		$domain = new domain();
		$__out['arr_langs'] ['ar']="Arabic";
		$__out['arr_langs'] ['en']="English";
		$link = new link();
		$links_category = new links_category();
		$__out['arr_categories'] = $links_category->assoc_array_from_result_array($links_category->getall(), "id", "name");
		$arr_result_links = $link->getall();
		
		if (is_array($arr_result_links)) {
			while (list(,$row) = each($arr_result_links)) {
				$__out['arr_parents'][$row['id']] = $row['domain_name'] . $row['friendly_url'];
			}
		}
		$__out['arr_domains'] = $domain->assoc_array_from_result_array($domain->getall(), "id", "domain_name");
		$link = new link($__in['id']);
		if ($__in['__is_form_submitted']) {		// if form is submitted
			if ($link->edit($__in['link']['domain_id'], $__in['link']['friendly_url'], $__in['link']['real_url'],$__in['link']['title'],
			$__in['link']['description'],$__in['link']['keywords'],$__in['link']['enable_social_bookmarking'],
			$__in['link']['parent_id'],$__in['link']['category_id'],$__in['link']['lang'],$__in['link']['names'],$__in['link']['urls'])) {	// editted successfully
				return dispatcher::redirect(array("action"=>"getall"), "updated_successfully");
			} else {	// could not edit
				$__out['link'] = $__in['link'];
				$__out['link']['id'] = $__in['id'];
				return false;
			}	
		} else {	// if form is not submitted
			$__out['link'] = $link->this_to_array();
			$__out['arr_see_also_links'] = $link->getall_see_also_links($__in['id']);
			return true;
		}	// end if form submitted
	}	// end action edit
//------------------------------------------------------------------------------------------------------	
	function delete(){
		global $__in, $__out;
		$link = new link($__in['id']);
		if ($link->delete()) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the link is not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the link is deleted
	}	// end action delete
//------------------------------------------------------------------------------------------------------	
	function delete_many(){
		global $__in, $__out;
		$link = new link();
		if ($link->delete_many($__in['arr_ids'])) {
			return dispatcher::redirect(array("action"=>"getall"), "deleted_successfully");
		} else {		// else if the array of ids are not deleted
			return dispatcher::redirect(array("action"=>"getall"));
		}	// end if the array of ids are deleted	
	}	// end action delete_many	
//------------------------------------------------------------------------------------------------------
	function getall(){
		global $__in, $__out;
		$link = new link();
		$link->set_paging(15);
		$__out['link'] = $link->this_to_array();
		$__out['arr_links'] = $link->getall();
		return true;
	}	// end action getall
//------------------------------------------------------------------------------------------------------
	function getone () {
		global $__in, $__out;
		$link = new link($__in['id']);
		$__out['link'] = $link->this_to_array();
		return true;
	}
//------------------------------------------------------------------------------------------------------
function getall_category_links(){
	global $__in, $__out;
	$link = new link();
	if($link->getall_category_links()){
		return dispatcher::redirect(array("action"=>"getall"), "generated_links_files_successfully");
	}
}
//------------------------------------------------------------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "getall"));
	}
//------------------------------------------------------------------------------------------------------
}	// end class link_controller

