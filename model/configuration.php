<?
require_once(dirname(__FILE__) . "/../lib/parent_model.php");
class configuration extends parent_model
{
//----------------------------------------------------------------------------------
	function configuration($arg_id=0) {
		$this->parent_model();
		$this->array_to_this($this->get_one_by_id($arg_id));
	}
//------------------------------------------------------------------------------------------------------	
	function generate_config_file () {
		$arr_config = $this->db_select("SELECT `key`, `value` FROM configurations");
		$arr_config = $this->assoc_array_from_result_array($arr_config, "key", "value");
		$str_config = "<?\n" . $this->array_to_string($arr_config, "\$__config") . "\n";
		$this->string_to_file($str_config, PHP_ROOT . "uploads/configuration/config.php");
		return true;
	}
//----------------------------------------------------------------------------------
	function add ($arg_variable_type, $arg_key, $arg_value, $arg_description) {
		$insert_id = $this->auto_insert(array("variable_type" => $arg_variable_type, "key" => $arg_key, "value" => $arg_value, "description" => $arg_description), "configurations");
		if (!$insert_id) {
			add_error("could_not_add");
			return false;
		} else {
			$this->generate_config_file();
			return $insert_id;
		}
	}	// end function add
//----------------------------------------------------------------------------------
	function edit ($arg_variable_type, $arg_key, $arg_value, $arg_description) {
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_entry");
			return false;
		}
		$bool_right = true;
		if (!isset($arg_variable_type)) {
			add_error("please_select_a_variable_type", array(), "variable_type");
			$bool_right = false;
		}
		if (!isset($arg_variable_type)) {
			add_error("please_write_a_valid_key", array(), "key");
			$bool_right = false;
		}
		if (!isset($arg_description)) {
			add_error("please_write_the_description", array(), "description");
			$bool_right = false;
		}
		if (!$bool_right) return false;
		$this->db_update("UPDATE configurations SET `variable_type`='%s', `key`='%s', `value`='%s', `description`='%s' WHERE `id`=%d", array($arg_variable_type, $arg_key, $arg_value, $arg_description, $this->id));
		$this->generate_config_file();
		return true;
	}
//----------------------------------------------------------------------------------
	function admin_edit ($arg_value) {
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_entry");
			return false;
		}
		$this->db_update("UPDATE configurations SET `value`='%s' WHERE `id`=%d", array($arg_value, $this->id));
		$this->generate_config_file();
		return true;
	}
//----------------------------------------------------------------------------------
	function delete() {
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_entry");
			return false;
		}
		if (!$this->db_delete("DELETE FROM configurations WHERE id=%d", array($this->id))) {
			add_error("could_not_delete");
			return false;
		} else {
			$this->generate_config_file();
			return true;
		}
	}
//----------------------------------------------------------------------------------
	function delete_many($arg_arr_ids) {
		if (is_array($arg_arr_ids)) {
			while (list(,$id) = each($arg_arr_ids)) {
				$obj = new configuration($id);
				$obj->delete();
			}
		}
	}
//----------------------------------------------------------------------------------
	function get_one_by_id ($arg_id) {
		if (!$this->is_id($arg_id)) return array();
		return $this->db_select_one_row("SELECT * FROM configurations WHERE id=%d", array($arg_id));
	}
//----------------------------------------------------------------------------------
	function getall () {
		return $this->db_select("SELECT * FROM configurations");
	}
//----------------------------------------------------------------------------------
}	// end class 

