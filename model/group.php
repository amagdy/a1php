<?
/**
@file group.php
@class group
@author Ahmed Magdy <a.magdy@a1works.com>
*/
require_once(PHP_ROOT . "lib/parent_model.php");
class group extends parent_model 
{
//------------------------------------------------------------------------------	
	/**
	Constructor
	@param $arg_id [optional] default 0 primary key used to fetch a row from the database and wrap it into an object if it is not specified an empty object is returned
	*/
	public function __construct ($arg_id=0) 
	{
		$this->parent_model();
		$this->__table = "groups";
		$this->__primary_key = "id";
		$this->__allowed_fields = array("id", "name", "layout");
		
		if ($arg_id == 0) {
			$this->id = 0;
			$this->name = "Visitors";
			$this->layout = "index";
		}
		
		// validation
		$this->add_validation ("name", "presence", "please_enter_the_group_name");
		$this->add_validation ("name", "uniq", "group_name_already_exists");
		$this->add_validation ("layout", "presence", "please_enter_the_layout");
		
		// search criteria
		$this->__add_search("name", "like");
		if ($arg_id) $this->array_to_this($this->get_one_by_id($arg_id), true);
		
	}	// end constructor
//------------------------------------------------------------------------------
	/**
	gets the available layouts in the current language
	@return array
	@throws Exception
	*/
	public function get_layouts () {
		$arr_layouts = array();
		$folder_name = PHP_ROOT . "view/" . $_SESSION['lang'] . "/default/layouts/";
		$arr_files = $this->list_files_in_folder($folder_name);
		if (!is_array($arr_files) || !$arr_files) {
			throw new Exception("No Layouts Found");
		}
		while (list(,$file) = each($arr_files)) {
			if (is_file($folder_name . $file)) {
				if ($file != "errors.tpl") {
					list($layout, $ext) = split("\.", $file);
					$arr_layouts[$layout] = $layout;
				}
			}
		}
		
		return $arr_layouts;
	}
//----------------------------------------------------------------------------
	/**
	Updates the permission file with the permissions in the database table
	@return boolean
	@throws Exception
	*/
	public function update_permissions_file () {
		$permissions_file = PHP_ROOT . "uploads/permission/permissions.php";
		$arr_result = $this->db_select("SELECT gp.group_id, p.controller, p.action, p.extra_params, p.allow FROM permissions AS p, groups_permissions AS gp WHERE gp.permission_id=p.id ORDER BY gp.group_id ASC");
		if (!is_array($arr_result) || !$arr_result) {
			throw new Exception("No Permission to write to file");
		}
		$arr_groups_perms = array();
		while (list(,$row) = each($arr_result)) {
			$group_id = $row['group_id'];
			unset($row['group_id']);
			if ($row['extra_params']) {
				$arr_extra_params = array();
				$arr = split("&", $row['extra_params']);
				while (list(,$one_param) = each($arr)) {
					list($k, $v) = split("=", trim($one_param));
					$arr_extra_params[trim($k)] = trim($v);
				}
				$row['extra_params'] = $arr_extra_params;
			}
			$arr_groups_perms[$group_id][] = $row;
		}
		$GLOBALS['arr_groups_permissions'] = $arr_groups_perms;
		$this->string_to_file("<?\n" . $this->array_to_string($arr_groups_perms, '$arr_groups_permissions') . "\n", $permissions_file);
		return true;
	}
//------------------------------------------------------------------------------
	/**
         * Sets the permissions of the current group by deleting the old permissions and inserting the new permission array in the groups_permissions table
         * @param array $arg_permission_ids
         * @return boolean
         */
	function set_permissions (array $arg_permission_ids) {
		if (!$this->is_id($this->id)) {
			if ($this->id != 0) {
				$this->add_validation_error ("could_not_find_group");
				throw new ValidationException($this);
			}
		}
		$this->db_delete("DELETE FROM groups_permissions WHERE group_id=%d", array($this->id));
		if (is_array($arg_permission_ids)) {
			while (list(,$permid) = each($arg_permission_ids)) {
				if (!$this->is_id($permid)) continue;
				$this->db_insert("INSERT INTO groups_permissions (group_id, permission_id) VALUES (" . $this->id . ", " . $permid . ")");
			}
		}
		$this->update_permissions_file();
		return true;
	}	// end function set_permissions
//------------------------------------------------------------------------------
	/**
	Returns the Ids of permissions assigned to this group.
	@param long $arg_id
	@return array of selected permissions
	@throws ValidationException
	*/
	public function get_selected_permission_ids () {
		if (!$this->is_id($this->id)) {
			if ($this->id != 0) {
				$this->add_validation_error ("could_not_find_group");
				throw new ValidationException($this);
			}
		}	
		return $this->result_array_to_one_array($this->db_select("SELECT permission_id FROM groups_permissions WHERE group_id=%d", array($this->id)));
	}
//------------------------------------------------------------------------------
	/**
	Adds a new group.
	@param arr_properties the array of all properties to be added
	@return the insert id or false on error
	*/
	public function add (array $arr_properties) 
	{
		$this->array_to_this($arr_properties);

		if ($this->is_error()) throw new ValidationException($this);
		return $this->__save();
	}
//------------------------------------------------------------------------------
	/**
	Edit group information
	@param arr_properties the array of all properties to be updated
	@return true if editted and false if not.
	*/
	public function edit (array $arr_properties) 
	{
		if (!$this->is_id($this->id)) {
			$this->add_validation_error("element_not_found");
			throw new ValidationException($this);
		}
		$this->array_to_this($arr_properties);
		if ($this->is_error()) throw new ValidationException($this);
		return $this->__save();
	}
//------------------------------------------------------------------------------
	/**
	Deletes a group from the groups table.
	This object must be initialized and must have a value in the $this->id field.
	@return boolean true if deleted and false if not.
	*/
	public function delete($arg_id) 
	{
		if (!$this->is_id($arg_id)) {
			$this->add_validation_error("element_not_found");
			throw new ValidationException($this);
		}
		return $this->db_delete("DELETE FROM groups WHERE id='%s'", array($arg_id));
	}
//------------------------------------------------------------------------------
	/**
	Deletes many groups.	
	@param arg_arr_ids	array of group ids to be deleted.	
	@return true on success or false on failure if the the array if ids is not correct
	*/
	public function delete_many (array $arg_arr_ids) 
	{
		if (!is_array($arg_arr_ids)) return false;
		$group = new group();
		while (list (,$id) = each ($arg_arr_ids)) {
			try {
				$group->delete($id);
			} catch (Exception $ex) {}
		}
		return true;
	}
//------------------------------------------------------------------------------



}		// end class group.
