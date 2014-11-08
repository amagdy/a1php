<?
/**
 @file page_controller.
 */
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/page.php");
require_once(PHP_ROOT . "model/links_category.php");
require_once(PHP_ROOT . "model/link.php");
require_once(PHP_ROOT . "model/domain.php");
class page_controller extends object implements controller
{
//------------------------------------------------------------------------------------------------------	
	function index () {
		return dispatcher::redirect(array("action" => "getall"));
	}
//-----------------------------------------------------------------
	function getall() {
		global $__in, $__out;
		$page = new page();
		$page->set_paging(15);
		$__out['page'] = $page->this_to_array();
		$__out['arr_pages'] = $page->get_all();
		return true;
	}
//-----------------------------------------------------------------
	function add () {
		global $__in, $__out;
		$page = new page();
		$links_category = new links_category();
		$link = new link();
		$domain = new domain();
		$arr_result_links = $link->getall();

		if (is_array($arr_result_links)) {
			while (list(,$row) = each($arr_result_links)) {
				$__out['arr_parents'][$row['id']] = $row['domain_name'] . $row['friendly_url'];
			}
		}
		$__out['bool_add_link'] = false;
		$__out['page']['id'] = $page->add();
		$__out['arr_domains'] = $domain->assoc_array_from_result_array($domain->getall(), "id", "domain_name");
		$__out['arr_categories'] = $links_category->assoc_array_from_result_array($links_category->getall(), "id", "name");
		$__out['arr_langs'] = array("en" => "English", "ar"=> "Arabic");
		$__out['adding'] = "yes";
		$__out['count_pages'] = $page->count() + 1;
		$__out['action'] = "edit";
		return true;
	}
//-----------------------------------------------------------------
	function edit (){
		global $__in, $__out;
		/* this flag to indicate that there was an error occured in the check of the data validation or the page_edit method so execute specific code
		 because the page_edit method or link_valid method have the same code to execute when 'else' is occured */
		$flag = true;
		$page = new page($__in['id']);
		if($__in['page']['link_id']) {
			$link = new link($__in['page']['link_id']);
		} else {
			$link = new link();
		}
		if ($__in['__is_form_submitted']) {
			// check the urls and their names
			$flag = true;
			$link_id = 0;
			if ($__in['add_link']) {	// if the add link check box was checked then validate the link info
				if (!$link->validate($__in['link']['domain_id'],$__in['link']['friendly_url'],$__in['link']['real_url'],$__in['link']['title'],$__in['link']['description'],$__in['link']['keywords'],$__in['link']['enable_social_bookmarking'],$__in['link']['parent_id'],$__in['link']['category_id'],$__in['link']['lang'])) {
					$flag = false;
				} else {
					// if the page has a previous link_id then edit else add the link_id
					if($__in['page']['link_id']) {
						// if the data is valid then edit this data in the links table
						$link->edit($__in['link']['domain_id'],$__in['link']['friendly_url'],$__in['link']['real_url'],$__in['link']['title'],$__in['link']['description'],$__in['link']['keywords'],$__in['link']['enable_social_bookmarking'],$__in['link']['parent_id'],$__in['link']['category_id'],$__in['link']['lang'],$__in['link']['names'],$__in['link']['urls'],"false");
						$link_id = $__in['page']['link_id'];
					} else { // if the page has no previous link_id then add a new link_id
						$link_id = $link->add($__in['link']['domain_id'],$__in['link']['friendly_url'],$__in['link']['real_url'],$__in['link']['title'],$__in['link']['description'],$__in['link']['keywords'],$__in['link']['enable_social_bookmarking'],$__in['link']['parent_id'],$__in['link']['category_id'],$__in['link']['lang'],$__in['link']['names'],$__in['link']['urls'],"false");
					}
				}
			}
			if ($flag == true) {
				// edit the page data
				if ($page->edit($__in['page']['title'], $__in['page']['body'], $__in['page']['contact_email'], $link_id)) {
					return dispatcher::redirect(array("action"=>"getall"), ($__in['adding'] ? "added_successfully" : 'updated_successfully'), array("page"), "info");
				} else { // if the page could not be edited
					$flag = false;
				}
			}
			
			if($flag==false) { // if the link data is not valid or could_not_edit the page
				$__out['page'] = $__in['page'];
				$__out['page']['id'] = $__in['id'];
				$__out['add_link'] = $__in['add_link'];
				$__out['arr_domains'] = $__in['arr_domains'];
				$__out['arr_langs'] = $__in['arr_langs'];
				$__out['arr_categories'] = $__in['arr_categories'];
				return false;
			}
		} else { // if the form is not submitted
			// get all the categories
			$links_category = new links_category();
			$__out['arr_categories'] = $links_category->assoc_array_from_result_array($links_category->getall(), "id", "name");
			$__out['page'] = $page->get_one_by_id($__in['id']);
			$__out['add_link'] = false; // set the default value of the add_link checkbox
			$domain = new domain();
			// get all the domains
			$__out['arr_domains'] = $domain->assoc_array_from_result_array($domain->getall(), "id", "domain_name");
			$__out['count_pages'] = $page->count();
			$link = new link($__out['page']['link_id']);
			// get all the parent links
			$arr_result_links = $link->getall();
			if (is_array($arr_result_links)) {
				while (list(,$row) = each($arr_result_links)) {
					$__out['arr_parents'][$row['id']] = $row['domain_name'] . $row['friendly_url'];
				}
			}
			$__out['link'] = $link->get_one_by_id($__out['page']['link_id']);
			$__out['arr_see_also_links'] = $link->getall_see_also_links($__out['page']['link_id']);
			$__out['arr_langs'] = array("en" => "English", "ar"=> "Arabic");
			return true;
		}
	}
//-----------------------------------------------------------------
	function delete () {
		global $__in, $__out;
		$page = new page($__in['id']);
		if ($page->delete()) {
			return dispatcher::redirect(array("action"=>"getall"), 'deleted_successfully', array("page"), "info");
		} else {
			return dispatcher::redirect(array("action"=>"getall"));
		}
	}	// end function delete().
//-----------------------------------------------------------------
	function delete_many () {
		global $__in, $__out;
		$page = new page();
		if ($page->delete_many($__in['arr_ids'])) {
			return dispatcher::redirect(array("action"=>"getall"), 'deleted_successfully', array("page"), "info");
		} else {
			return dispatcher::redirect(array("action"=>"getall"));
		}
	}	// end function delete().
//-----------------------------------------------------------------
}	// end class
