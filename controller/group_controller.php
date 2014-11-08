<?
/**
 @file group_controller.php
 @class group_controller

 @author Ahmed Magdy <a.magdy@a1works.com>
 */
require_once(PHP_ROOT . "lib/controller.php");
require_once(PHP_ROOT . "model/group.php");

class group_controller extends object implements controller
{
/**
 Constructor
 */
    public function __construct () {
        global $__out;
        $group = new group();
        $__out['arr_layout'] = $group->get_layouts();
    }
    //------------------------------------------------------------------------------------------------------
    /**
     Adds a new group        @return boolean
     @throws Exception
     */
    public function add () {
        global $__in, $__out;
        if ($__in['__is_form_submitted']) {		// if form is submitted
            try {
                $group = new group();
                $group->add($__in['group']);
                return dispatcher::redirect("getall", "added_successfully");
            } catch (ValidationException $ex) {
                $ex->publish_errors();
                $__out['group'] = $__in['group'];
                return false;
            } catch (Exception $ex) {
                throw $ex;
            }
        } else {	// if form is not submitted
            $__out['group'] = array();
            return true;
        }	// end if form submitted
    }	// end function add
    //------------------------------------------------------------------------------------------------------
    /**
     Edits an existing group.
     @return boolean
     @throws Exception
     */
    public function edit () {
        global $__in, $__out;
        if ($__in['__is_form_submitted']) {		// if form is submitted
            try {
                $group = new group($__in['id']);
                $group->edit($__in['group']);
                return dispatcher::redirect("getall", "updated_successfully");
            } catch (ValidationException $ex) {
                $ex->publish_errors();
                $__out['group'] = $__in['group'];
                $__out['group']['id'] = $__in['id'];
            } catch (Exception $ex) {
                throw $ex;
            }
        } else {	// if form is not submitted
            $group = new group($__in['id']);
            $__out['group'] = $group->this_to_array();
        }	// end if form submitted
        return true;
    }	// end action edit
    //------------------------------------------------------------------------------------------------------
    /**
     Deletes one group        @return boolean
     @throws Exception
     */
    public function delete () {
        global $__in, $__out;
        try {
            $group = new group();
            $group->delete($__in['id']);
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
     Deletes many groups.
     */
    public function delete_many () {
        global $__in, $__out;
        try {
            $group = new group();
            $group->delete_many($__in['arr_ids']);
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
     Gets all groups        @return boolean
     @throws Exception
     */
    public function getall () {
        global $__in, $__out;
        try {
            $group = new group();
            $group->set_paging(25);
            $__out['arr_groups'] = $group->get_all($__in['__orderby'], ($__in['__desc'] == 'yes' ? false : true));
            $__out['group'] = $group->this_to_array();
        } catch (ValidationException $ex) {
            $ex->publish_errors();
        } catch (Exception $ex) {
            throw $ex;
        }
        return true;
    }	// end action getall
    //------------------------------------------------------------------------------------------------------
    /**
     Gets one group.
     @return boolean
     @throws Exception
     */
    public function getone () {
        global $__in, $__out;
        try {
            $group = new group($__in['id']);
            $__out['group'] = $group->this_to_array();
        } catch (ValidationException $ex) {
            $ex->publish_errors();
        } catch (Exception $ex) {
            throw $ex;
        }
        return true;
    }
    //------------------------------------------------------------------------------------------------------
    /**
     Searches the database and returns the results in the same form as the getall form.
     @return boolean
     */
    public function search () {
        global $__in, $__out;
        try {
            $group = new group();
            $group->set_paging(25);
            $__out['arr_groups'] = $group->__search($__in['group_search'], $__in['__orderby'], ($__in['__desc'] == 'yes' ? false : true));
        } catch (ValidationException $ex) {
            $ex->publish_errors();
        } catch (Exception $ex) {
            throw $ex;
        }
        $__out['group_search_link'] = array("group_search" => $__in['group_search']);
        $__out['group'] = $group->this_to_array();
        return true;
    }
    //------------------------------------------------------------------------------------------------------
    /**
     Redirects to getall.
     @return boolean
     */
    public function index () {
        return dispatcher::redirect("getall");
    }
    //------------------------------------------------------------------------------------------------------
    /**
     Sets the permissions for a group.
     */
    function set_permissions () {
        global $__in, $__out;
        try {
            $group = new group($__in['id']);
            $__out['group'] = $group->this_to_array();
            $permission = new permission();
            $__out['arr_permissions'] = $permission->assoc_array_from_result_array($permission->getall(), "id", "description");
            $__out['selected_permission_ids'] = $group->get_selected_permission_ids();
            if ($__in['__is_form_submitted']) {		// if form is submitted
                $group->set_permissions($__in['group']['permissions']);
                return dispatcher::redirect(array("action"=>"getall"), "updated_successfully");
            }
        } catch (ValidationException $ex) {
            $ex->publish_errors();
        } catch (Exception $ex) {
            throw $ex;
        }
        return true;
    }	// end action set_permissions
//------------------------------------------------------------------------------------------------------
}	// end class group_controller
