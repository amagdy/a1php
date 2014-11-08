<?
require_once(PHP_ROOT . "lib/parent_model.php");
class links_category extends parent_model
{
//----------------------------------------------------------------------------------
	function links_category($arg_id=0) {
		$this->parent_model();
		$this->array_to_this($this->get_one_by_id($arg_id));
	}
//----------------------------------------------------------------------------------
	function add ($arg_name, $arg_lang) {
		if (!$arg_name) {
			add_error("category_name_doesnt_exist", array(), "name");
			return false;
		}
		if (!$arg_lang) {
			add_error("language_doesnt_exist", array(), "lang");
			return false;
		}
		$insert_id = $this->auto_insert(array("name" => $arg_name, "lang" => $arg_lang), "links_categories");

		if (!$insert_id) {
			add_error("could_not_add");
			return false;
		} else {
			return $insert_id;
		}
	}	// end function add
//----------------------------------------------------------------------------------
	function edit ($arg_name, $arg_lang) {
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_entry");
			return false;
		}
		if (!$arg_name) {
			add_error("category_name_doesnt_exist", array(), "name");
			return false;
		}
		if (!$arg_lang) {
			add_error("language_doesnt_exist", array(), "lang");
			return false;
		}
		$this->db_update("UPDATE links_categories SET name='%s', lang='%s' WHERE id=%d", array($arg_name, $arg_lang, $this->id));
		return true;
	}
//----------------------------------------------------------------------------------
	function delete() {
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_entry");
			return false;
		}
		if (!$this->db_delete("DELETE FROM links_categories WHERE id=%d", array($this->id))) {
			add_error("could_not_delete");
			return false;
		} else {
			return true;
		}
	}
//----------------------------------------------------------------------------------
	function delete_many($arg_arr_ids) {
		if (is_array($arg_arr_ids)) {
			while (list(,$id) = each($arg_arr_ids)) {
				$obj = new links_category($id);
				$obj->delete();
			}
		}
	}
//----------------------------------------------------------------------------------
	function get_one_by_id ($arg_id) {
		if (!$this->is_id($arg_id)) return array();
		return $this->db_select_one_row("SELECT * FROM links_categories WHERE id=%d", array($arg_id));
	}
//----------------------------------------------------------------------------------
	function getall () {
		return $this->db_select("SELECT * FROM links_categories");
	}
//----------------------------------------------------------------------------------
}	// end class 

