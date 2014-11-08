<?
/**

controller, action, extra_params, allow, description
$arg_controller, $arg_action, $arg_extra_params, $arg_allow, $arg_description

*/
require_once(dirname(__FILE__) . "/../lib/parent_model.php");
class permission extends parent_model		// start class permission
{
//------------------------------------------------------------------------------	
	function permission ($arg_id=0) {
		$this->parent_model();
		$this->array_to_this($this->get_one_by_id($arg_id));
	}
//------------------------------------------------------------------------------
	function get_one_by_id ($arg_id) {
		if (!$arg_id) return array();
		return $this->db_select_one_row("SELECT * FROM permissions WHERE id=%d", array($arg_id));
	}	// end function get_one_by_id
//------------------------------------------------------------------------------
	/**
	Add a new user permission.
	*/
	function add ($arg_controller, $arg_action, $arg_extra_params, $arg_allow, $arg_description) {
		if (!$arg_controller) {
			add_error("please_write_the_controller", array(), "controller");
			return false;
		}
		if (!$arg_action) {
			add_error("please_write_the_action", array(), "action");
			return false;			
		}
		$arg_allow = ($arg_allow ? 1 : 0);
		if (!$arg_description) {
			add_error("please_write_the_permission_description", array(), "description");
			return false;		
		}
		if ($this->db_get_one_value("SELECT id FROM permissions WHERE controller='%s' AND action='%s' AND extra_params='%s' AND allow=%d", array($arg_controller, $arg_action, $arg_extra_params, $arg_allow))) {
			add_error("permission_already_exists");
			return false;
		}
		$insert_id = $this->auto_insert(array("controller" => $arg_controller, "action" => $arg_action, "extra_params" => $arg_extra_params, "allow" => $arg_allow, "description" => $arg_description), "permissions");	
		if ($insert_id) {
			return $insert_id;
		} else {
			add_error("could_not_add_permission");
			return false;
		}
	}	// end function add
//------------------------------------------------------------------------------
	/**
	Edits a permission
	*/
	function edit ($arg_controller, $arg_action, $arg_extra_params, $arg_allow, $arg_description) {
		if (!$this->is_id($this->id)) {
			add_error ("could_not_find_permission");
			return false;
		}
		if (!$arg_controller) {
			add_error("please_write_the_controller", array(), "controller");
			return false;
		}
		if (!$arg_action) {
			add_error("please_write_the_action", array(), "action");
			return false;			
		}
		$arg_allow = ($arg_allow ? 1 : 0);
		if (!$arg_description) {
			add_error("please_write_the_permission_description", array(), "description");
			return false;		
		}
		if ($this->db_get_one_value("SELECT id FROM permissions WHERE controller='%s' AND action='%s' AND extra_params='%s' AND allow=%d AND id!=%d", array($arg_controller, $arg_action, $arg_extra_params, $arg_allow, $this->id))) {
			add_error("permission_already_exists");
			return false;
		}
		$this->db_update("UPDATE permissions SET controller='%s', action='%s', extra_params='%s', allow=%d, description='%s' WHERE id=%d", array($arg_controller, $arg_action, $arg_extra_params, $arg_allow, $arg_description, $this->id));
		return true;
	}
//------------------------------------------------------------------------------
	function delete () {
		if (!$this->is_id($this->id)) {
			add_error ("could_not_find_permission");
			return false;
		}
		$this->db_delete("DELETE FROM groups_permissions WHERE permission_id=%d", array($this->id));
		if ($this->db_delete("DELETE FROM permissions WHERE id=%d", array($this->id))) {
			return true;
		} else {
			add_error("could_not_delete");
			return false;
		}
	}	// end function delete
//------------------------------------------------------------------------------
	/**
	Get all permissions
	*/
	function getall () {
		return $this->db_select("SELECT * FROM permissions");
	}	// end function getall
//------------------------------------------------------------------------------
	/**
	
	*/
	function group_permissions ($arg_group_id) {
		if (!$this->is_id($arg_group_id)) {
			add_error("could_not_find_group");
			return false;
		}
		return $this->db_select("SELECT p.* FROM permissions AS p, groups_permissions AS gp WHERE p.id=gp.permission_id AND gp.group_id=%d", array($arg_group_id));
	}	// end function group_permissions
//------------------------------------------------------------------------------
}		// end class permission
