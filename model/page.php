<?
/**
 @class page
 wraps the pages table.
 Selects, Adds, Edits and deletes rows from the pages table.
 */
require_once(dirname(__FILE__) . "/../lib/parent_model.php");
class page extends parent_model
{
	/**
	 Initializes an object of the page class and can retrieve a certain page from db.
	 @param arg_id the page id of the required page if 0 there is no page retrieved from db.
	 @see parent_model::db_select_one_row().
	 @see parent_model::array_to_this().
	 @access Public.
	 */
	function page ($arg_id=0) {
		$this->parent_model();
		$this->array_to_this($this->get_one_by_id($arg_id));
	}	// end __construct().
	//------------------------------------------------------------------
	function garbage_collector() {
		$this->db_delete("DELETE FROM pages WHERE last_updated=0");
	}
	//------------------------------------------------------------------
	/**
	 Adds a new page to DB.
	 @param ar_title the arabic title.
	 @param en_title the english title.
	 @param ar_body the arabic body.
	 @param en_body the english body.
	 @param contact_email the contact email if written a contact form is shown on the page.
	 @param rank the rank of the page if 0 then it is not shown in the main menu if more than 0 then it is shown with the specified order.
	 @return false or insert id.
	 @see parent_model::db_get_one_value().
	 @see parent_model::auto_insert().
	 @access Public.
	 */
	function add ($arg_title="", $arg_body="", $arg_contact_email="", $arg_last_updated=0, $arg_link_id=0) {
		$this->garbage_collector();
		return $this->auto_insert (array("title"=>$arg_title, "body"=>$arg_body, "contact_email"=>$arg_contact_email, "link_id"=>$arg_link_id, "last_updated"=>$arg_last_updated, "fixed"=>0), "pages");
	}	// end function add().
	//------------------------------------------------------------------
	/**
	 Edits a row in the pages table by id.
	 @param
	 @return false or true.
	 @see parent_model::db_get_one_value().
	 @see parent_model::db_update().
	 @access Public.
	 */
	function edit ($arg_title, $arg_body, $arg_contact_email="", $arg_link_id=0) {
		if (!$this->is_id($this->id)) {
			add_error("couldnot_edit_element");
			return false;
		}
		$bool_right = true;
		if (!$arg_title) {
			add_error("please_write_a_title", array(), "title");
			$bool_right = false;
		}
		if ($arg_contact_email) {
			if (!$this->is_email($arg_contact_email)) {
				add_error("invalid_email", array(), "contact_email");
				$bool_right = false;
			}
		}
		if (!$bool_right) return false;
		$this->db_update("UPDATE pages SET title='%s', body='%s', contact_email='%s', last_updated=%d, link_id=%d WHERE id=%d", array($arg_title, $arg_body, $arg_contact_email, time(), $arg_link_id, $this->id));
		return true;

	}	// end function edit().
	//------------------------------------------------------------------
	function add_picture ($arg_pic) {
		if (!$this->is_id($this->id)) {
			add_error("couldnot_edit_element");
			return false;
		}
		// if the `id` directory of the pictures is not exits then create the directory
		if (!file_exists(PHP_ROOT . "uploads/page/$this->id"))
		mkdir(PHP_ROOT . "uploads/page/$this->id");
		// send to the function the directory name with `id`, with no prefix to use the friendly name of the file
		return $this->upload_picture ($arg_pic, PHP_ROOT . "uploads/page/$this->id/", "", "", 400, 800);
	}
	//------------------------------------------------------------------
	function set_default_picture ($arg_pic_name) {
		if (!$this->is_id($this->id)) {
			add_error("couldnot_edit_element");
			return false;
		}
		$this->pic = $arg_pic_name;
		$this->make_thumbnail($arg_pic_name, PHP_ROOT . "uploads/page/", "", 100, 200);
		return $this->db_update("UPDATE pages SET pic='%s' WHERE id=%d", array($arg_pic_name, $this->id));
	}
	//------------------------------------------------------------------
	function unset_default_picture () {
		if (!$this->is_id($this->id)) {
			add_error("couldnot_edit_element");
			return false;
		}
		$this->pic = "";
		return $this->db_update("UPDATE pages SET pic='' WHERE id=%d", array($this->id));
	}
	//------------------------------------------------------------------
	function delete_picture ($arg_pic_name) {
		if (file_exists(PHP_ROOT . "uploads/page/" . $arg_pic_name)) unlink(PHP_ROOT . "uploads/page/" . $arg_pic_name);
		return true;
	}
	//------------------------------------------------------------------
	function get_all_pictures () {
		if (!$this->is_id($this->id)) {
			add_error("couldnot_edit_element");
			return false;
		}
		$arr_pix = $this->list_files_in_folder(PHP_ROOT . "uploads/page/$this->id/", "");
		if (!is_array($arr_pix)) return array();
		$arr_all_pix = array();
		while (list($k, $v) = each($arr_pix)) {
			if ($v == $this->pic) {
				$arr_all_pix[$v] = 1;
			} else {
				$arr_all_pix[$v] = 0;
			}
		}
		return $arr_all_pix;
	}
	//------------------------------------------------------------------
	/**
	 Deletes a page from database.
	 @returns true or false
	 @see parent_model::db_delete().
	 @access Public.
	 */
	function delete () {
		if ((!$this->is_id($this->id)) || $this->fixed) {
			add_error("couldnot_delete_element");
			return false;
		}
		$this->delete_files_in_folder(PHP_ROOT . "uploads/page/", $this->id . "_");
		return $this->db_delete("DELETE FROM pages WHERE id=%d", array($this->id));
	}	// end function delete().
	//----------------------------------------------------------------------------------
	function delete_many($arg_arr_ids) {
		if (is_array($arg_arr_ids)) {
			while (list(,$id) = each($arg_arr_ids)) {
				$obj = new page($id);
				$obj->delete();
			}
		}
	}
	//-------------------------------------------------------------------
	/**
	 Returns all pages in a multidimensional array.
	 @return multidimensional array of pages db rows.
	 @see parent_model::db_select().
	 @access Public.
	 */
	function get_all () {
		return $this->db_select("SELECT * FROM pages ORDER BY id ASC");
	}	// end function get_all().
	//------------------------------------------------------------------
	/**
	 Returns all pages titles that should appear in the main menu in a multidimensional array ordered by their rank.
	 @return multidimensional array of pages db rows.
	 @see parent_model::db_select().
	 @access Public.
	 */
	function get_menu_pages () {
		return $this->db_select("SELECT id, en_title, ar_title, rank, fixed FROM pages WHERE fixed=0 AND rank>0 ORDER BY rank ASC");
	}	// end function get_all().
	//------------------------------------------------------------------
	/**
	 Returns the number of pages in the table.
	 @return integer number of the count of pages in the pages table.
	 @see parent_model::db_select().
	 @access Public.
	 */
	function count () {
		return $this->db_get_one_value("SELECT COUNT(*) FROM pages WHERE last_updated>0");
	}	// end function get_all().
	//------------------------------------------------------------------
	/**
	 Returns an array of one row by id.
	 @param arg_id the page id of the required page if 0 there is no page retrieved from db.
	 @return an array of one row of the pages table.
	 @see parent_model::db_select_one_row().
	 @access Public.
	 */
	function get_one_by_id ($arg_id) {
		if(!$this->is_id($arg_id)) return false;
		return $this->db_select_one_row("SELECT * FROM pages WHERE id=%d", array($arg_id));
	}	// end function get_one_by_id().
	//------------------------------------------------------------------
	function get_home_pages_by_language ($arg_lang='ar') {
		if ($arg_lang == "en") {
			return $this->db_select("SELECT * FROM pages WHERE id IN (1,2,3,4) ORDER BY rank ASC");
		} else {
			return $this->db_select("SELECT * FROM pages WHERE id IN (5,6,7,8) ORDER BY rank ASC");
		}
	}
	//------------------------------------------------------------------
	function send_contact_email ($arg_name, $arg_email, $arg_subject, $arg_message) {
		if (!$this->is_id($this->id)) {
			add_error("page_not_found");
			return false;
		}
		$bool_right = true;
		if (!$arg_name) {
			add_error("please_write_your_name", array(), "name");
			$bool_right = false;
		}
		if (!$this->is_email($arg_email)) {
			add_error("invalid_email", array(), "email");
			$bool_right = false;
		}
		if (!$arg_subject) {
			add_error("please_write_the_message_subject", array(), "subject");
			$bool_right = false;
		}
		if (!$arg_message) {
			add_error("please_write_the_message_body", array(), "message");
			$bool_right = false;
		}
		if (!$bool_right) return false;
		$this->send_mail($this->contact_email, $arg_email, $arg_name, $arg_subject, $arg_message);
		return true;
	}
	//------------------------------------------------------------------
	function getall_links_for_language ($arg_lang) {
		return $this->db_select("SELECT id, title, rank FROM pages WHERE language='%s' AND rank > 0 ORDER BY rank ASC", array($arg_lang));
	}


}	// end class page

