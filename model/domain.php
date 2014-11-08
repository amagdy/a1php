<?
require_once(PHP_ROOT . "lib/parent_model.php");
require_once(PHP_ROOT . "model/link.php");
class domain extends parent_model
{
//----------------------------------------------------------------------------------
	function domain($arg_id=0) {
		$this->parent_model();
		$this->array_to_this($this->get_one_by_id($arg_id));
		
	}
//----------------------------------------------------------------------------------
	function add ($arg_domain_name) {
		$arg_domain_name = strtolower($arg_domain_name);
		if (!ereg("^[a-z0-9_\.-]+$", $arg_domain_name)) {
			add_error("please_write_a_valid_domain_name", array(), "domain_name");
			return false;
		}
		if ($this->db_get_one_value("SELECT id FROM domains WHERE domain_name='%s'", array($arg_domain_name))) {
			add_error("domain_already_exists", array(), "domain_name");
			return false;
		}
		$insert_id = $this->auto_insert(array("domain_name" => $arg_domain_name), "domains");

		if (!$insert_id) {
			add_error("could_not_add");
			return false;
		} else {
			return $insert_id;
		}
	}	// end function add
//----------------------------------------------------------------------------------
	function edit ($arg_domain_name) {
		$arg_domain_name = strtolower($arg_domain_name);
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_entry");
			return false;
		}
		if (!ereg("^[a-z0-9_\.-]+$", $arg_domain_name)) {
			add_error("please_write_a_valid_domain_name", array(), "domain_name");
			return false;
		}
		if ($this->db_get_one_value("SELECT id FROM domains WHERE id!=%d AND domain_name='%s'", array($this->id, $arg_domain_name))) {
			add_error("domain_already_exists", array(), "domain_name");
			return false;
		}
		$this->db_update("UPDATE domains SET domain_name='%s' WHERE id=%d", array($arg_domain_name, $this->id));
		//delete all links file
		$link = new link();
		$link->save_links_to_file();
		return true;
	}
//----------------------------------------------------------------------------------
	function delete() {
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_entry");
			return false;
		}
		if (!$this->db_delete("DELETE FROM domains WHERE id=%d", array($this->id))) {
			add_error("could_not_delete");
			return false;
		} else {
			//delete all domain links
			$link = new link();
			$link->delete_domain_links($this->id);
			return true;
		}
	}
//----------------------------------------------------------------------------------
	function delete_many($arg_arr_ids) {
		if (is_array($arg_arr_ids)) {
			while (list(,$id) = each($arg_arr_ids)) {
				$obj = new domain($id);
				$obj->delete();
			}
		}
	}
//----------------------------------------------------------------------------------
	function get_one_by_id ($arg_id) {
		if (!$this->is_id($arg_id)) return array();
		return $this->db_select_one_row("SELECT * FROM domains WHERE id=%d", array($arg_id));
	}
//----------------------------------------------------------------------------------
	function getall () {
		return $this->db_select("SELECT * FROM domains");
	}
//----------------------------------------------------------------------------------
}	// end class 

