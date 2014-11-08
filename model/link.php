<?
require_once(PHP_ROOT . "lib/parent_model.php");
class link extends parent_model {
//----------------------------------------------------------------------------------
	function link($arg_id=0) {
		$this->parent_model();
		$this->array_to_this($this->get_one_by_id($arg_id));
	}
//----------------------------------------------------------------------------------
	/**
	@access private
	*/
	function is_url (&$arg_url) {
		$arg_url = strtolower($arg_url);
		if ($arg_url == "/") return true;
		return ereg("^(\/[a-z0-9_\.\?&=%]+)+\/?$", $arg_url);
	}
//----------------------------------------------------------------------------------
	/**
	@access private
	*/
	function url_to_array ($arg_url) {
		$arr_result = array();
		$arg_url = substr($arg_url, 1);	// escape the first slash
		$arg_url = HTML_ROOT . $arg_url;
		$arr_result = route($arg_url, false);
		$index = strpos($arg_url, "?");
		if ($index > 0) {
			$str = substr($arg_url, $index+1);
			$arr_params = split("&", $str);
			if (is_array($arr_params)) {
				while (list(,$param) = each($arr_params)) {
					$arr_one_param = split("=", $param);
					$arr_result[urldecode($arr_one_param[0])] = urldecode($arr_one_param[1]);
				}
			}
		}
		return $arr_result;
	}
//-----------------------------------------------------------------------
	function validate ($arg_domain_id, $arg_friendly_url, $arg_real_url, $arg_title, $arg_description, $arg_keywords, $arg_enable_social_bookmarking, $arg_parent_id, $arg_category_id, $arg_lang) {
		if (!$this->is_id($arg_domain_id)) {
			$arg_domain_id = 1;
		}
		if (!$this->is_url($arg_friendly_url)) {
			add_error("please_write_the_friendly_url", array(), "friendly_url");
			return false;
		}
		if (!$this->is_url($arg_real_url)) {
			add_error("please_write_the_real_url", array(), "real_url");
			return false;
		}
		if (!$arg_title) {
			add_error("please_write_the_title", array(), "title");
			return false;
		}
		if (!$arg_lang) {
			add_error("please_write_the_lang", array(), "lang");
			return false;
		}

		if ($this->is_id($this->id)) {	// edit
			if ($this->db_get_one_value("SELECT id FROM links WHERE domain_id=%d AND friendly_url='%s' AND id!=%d", array($arg_domain_id, $arg_friendly_url, $this->id))) {
				add_error("link_already_exists", array(), "friendly_url");
				return false;
			}
		} else {	// add
			if ($this->db_get_one_value("SELECT id FROM links WHERE domain_id=%d AND friendly_url='%s'", array($arg_domain_id, $arg_friendly_url))) {
				add_error("link_already_exists", array(), "friendly_url");
				return false;
			}
		}
		return true;
	}
//----------------------------------------------------------------------------------
	function add ($arg_domain_id, $arg_friendly_url, $arg_real_url, $arg_title, $arg_description, $arg_keywords, $arg_enable_social_bookmarking, $arg_parent_id, $arg_category_id, $arg_lang, $arg_names, $arg_urls, $arg_validate=true) {
		if ($arg_validate) {
			if (!$this->validate($arg_domain_id, $arg_friendly_url, $arg_real_url, $arg_title, $arg_description, $arg_keywords, $arg_enable_social_bookmarking, $arg_parent_id, $arg_category_id, $arg_lang)) {
				return false;
			}
		}
		$insert_id = $this->auto_insert(array("domain_id" => $arg_domain_id, "friendly_url" => strtolower($arg_friendly_url), "real_url" => strtolower($arg_real_url), "title" => $arg_title, "description" => $arg_description, "keywords" => $arg_keywords, "enable_social_bookmarking" => ($arg_enable_social_bookmarking ? 1 : 0), "parent_id" => ($arg_parent_id ? $arg_parent_id : 0), "category_id" => ($arg_category_id ? $arg_category_id : 0), "lang" => $arg_lang), "links");
		if (!$insert_id) {
			add_error("could_not_add");
			return false;
		}
		$this->save_links_to_file();
		if (is_array($arg_names) && is_array($arg_urls)) {
			$this->set_see_also_links($insert_id, $arg_names, $arg_urls);
		}
		return $insert_id;
	}
	// end function add
//----------------------------------------------------------------------------------
	function edit ($arg_domain_id, $arg_friendly_url, $arg_real_url, $arg_title, $arg_description, $arg_keywords, $arg_enable_social_bookmarking,$arg_parent_id,$arg_category_id, $arg_lang, $arg_names, $arg_urls, $arg_validate=true) {
		if ($arg_validate) {
			if (!$this->validate($arg_domain_id, $arg_friendly_url, $arg_real_url,$arg_title,$arg_description, $arg_keywords,$arg_enable_social_bookmarking,$arg_parent_id,$arg_category_id,$arg_lang)) {
				return false;
			}
		}
		
		$this->db_update("UPDATE links SET domain_id=%d, friendly_url='%s', real_url='%s', title='%s', description='%s', keywords='%s', enable_social_bookmarking=%d, parent_id='%s',category_id='%s', lang='%s' WHERE id=%d", array($arg_domain_id, strtolower($arg_friendly_url), strtolower($arg_real_url), $arg_title, $arg_description, $arg_keywords, ($arg_enable_social_bookmarking ? 1 : 0), ($arg_parent_id ? $arg_parent_id : 0), ($arg_category_id ? $arg_category_id : 0), $arg_lang, $this->id));
		$this->save_links_to_file();
		$this->delete_see_also_links($this->id);
		if (is_array($arg_names) && is_array($arg_urls)) {
			$this->set_see_also_links($this->id, $arg_names, $arg_urls);
		}
		return true;
	}
//----------------------------------------------------------------------------------
	function delete($arg_rebuild_file=true) {
		if (!$this->is_id($this->id)) {
			add_error("could_not_find_entry");
			return false;
		}
		$this->delete_see_also_links($this->id);
		if (!$this->db_delete("DELETE FROM links WHERE id=%d", array($this->id))) {
			add_error("could_not_delete");
			return false;
		} else {
			if ($arg_rebuild_file) $this->save_links_to_file();
			return true;
		}
	}
//----------------------------------------------------------------------------------
	function delete_many($arg_arr_ids) {
		if (is_array($arg_arr_ids)) {
			while (list(,$id) = each($arg_arr_ids)) {
				$obj = new link($id);
				$obj->delete(false);
			}
			$this->save_links_to_file();
		}
	}
//----------------------------------------------------------------------------------
	function delete_domain_links($arg_domain_id) {
		if (!$this->is_id($arg_domain_id)) return false;
		if(!$this->db_delete("DELETE FROM links WHERE domain_id=%d", array($arg_domain_id))) {
			return false;
		} else {
			$this->save_links_to_file();
			return true;
		}
	}
//------------------------------------------------------------------------
	/**
	@access private
	*/
	function save_links_to_file () {
		// prepare array
		$arr_urls = $this->getall();
		$arr_result = array();
		if (is_array($arr_urls)) {
			while (list(,$row) = each($arr_urls)) {
				$arr_result[$row['domain_name'] . $row['friendly_url']] = $this->url_to_array($row['real_url']);
			}
		}
		// save array to file
		$str = $this->array_to_string($arr_result, '$__links');
		$str = "<?\n" . $str . "\n";
		$this->string_to_file($str, PHP_ROOT . "uploads/link/links.php");
		return true;
	}
//----------------------------------------------------------------------------------
	function get_one_by_id ($arg_id) {
		if (!$this->is_id($arg_id)) return array();
		return $this->db_select_one_row("SELECT * FROM links WHERE id=%d", array($arg_id));
	}
//----------------------------------------------------------------------------------
	function get_one_by_domain_url ($arg_domain_name, $arg_friendly_url) {
		$domain_id = $this->db_get_one_value("SELECT id FROM domains WHERE domain_name='%s'", array($arg_domain_name));
		$link_id = $this->db_get_one_value("SELECT id FROM links WHERE domain_id='%d' AND friendly_url='%s'", array($domain_id, $arg_friendly_url));
		$link_data = $this->db_select_one_row("SELECT * FROM links WHERE domain_id=%d AND friendly_url='%s'", array($domain_id, $arg_friendly_url));
		$link_data['domain_name'] = $arg_domain_name;
		$link_data['arr_see_also_links'] = $this->getall_see_also_links($link_id); 
		return $link_data;
	}
//----------------------------------------------------------------------------------
	function getall () {
		return $this->db_select("SELECT l.*, d.domain_name AS domain_name ,l.friendly_url AS friendly_url FROM links AS l, domains AS d WHERE d.id=l.domain_id");
	}
//----------------------------------------------------------------------------------

	function set_see_also_links ($arg_id,$arg_names,$arg_urls) {
		for($i=0;$i<count($arg_names);$i++){
			$this->db_insert("insert into see_also_links(`name`,`url`,`link_id`) values('%s','%s','%d')",array($arg_names[$i],$arg_urls[$i],$arg_id));
		}
	}
//------------------------------------------------------------------------------------
	function delete_see_also_links ($arg_id) {
		return $this->db_delete("DELETE FROM see_also_links WHERE link_id=%d",array($arg_id));
	}
//-------------------------------------------------------------------------------------
	function getall_see_also_links ($arg_id) {
		return $this->db_select("SELECT * FROM see_also_links WHERE link_id=%d", array($arg_id));
	}
//-----------------------------------------------------------------------------------
	function getall_category_links () {
		$__cat_arr_en = $this->db_select("SELECT c.id, c.name, l.id AS link_id, l.title, l.friendly_url, l.domain_id, d.domain_name, l.lang FROM links AS l, links_categories AS c, domains AS d WHERE l.lang='en' AND c.id=l.category_id AND d.id=l.domain_id");
		$__cat_arr_ar = $this->db_select("SELECT	c.id, c.name, l.id AS link_id, l.title, l.friendly_url, l.domain_id, d.domain_name, l.lang FROM links AS l, links_categories AS c, domains AS d WHERE l.lang='ar' AND c.id=l.category_id AND d.id=l.domain_id");
		$cat_link_arr_en = array();
		for($i = 0; $i < count($__cat_arr_en); $i++)  {
			$cat_link_arr_en[strtolower($__cat_arr_en[$i]['id'])]['name'] = strtolower( $__cat_arr_en[$i]['name']);
			$cat_link_arr_en[strtolower($__cat_arr_en[$i]['id'])]['links']["http://" . strtolower($__cat_arr_en[$i]['domain_name'] . $__cat_arr_en[$i]['friendly_url'])] = $__cat_arr_en[$i]['title'];
		} 
		$cat_link_arr_ar=array();
		for($i = 0; $i < count($__cat_arr_ar); $i++)  {
			$cat_link_arr_ar[strtolower($__cat_arr_ar[$i]['id'])]['name'] = strtolower( $__cat_arr_ar[$i]['name']);
			$cat_link_arr_ar[strtolower($__cat_arr_ar[$i]['id'])]['links']["http://" . strtolower($__cat_arr_ar[$i]['domain_name'] . $__cat_arr_ar[$i]['friendly_url'])] = $__cat_arr_ar[$i]['title'];
		} 
		$this->save_categories_links_to_file ($cat_link_arr_en, "en") ;
		$this->save_categories_links_to_file ($cat_link_arr_ar, "ar") ;   
		return true;            
	}
//--------------------------------------------------------------------	
	function save_categories_links_to_file ($__cat_arr, $arg_lang) {
		// prepare array
		$arr_urls = $__cat_arr;
		$str = $this->array_to_string($arr_urls, '$__categories');
		$str = "<?\n" . $str . "\n";
		$this->string_to_file($str, PHP_ROOT ."uploads/category/".$arg_lang.".php");
		return true;
	}
//--------------------------------------------------------------------	
}	// end class

