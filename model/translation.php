<?
require_once(dirname(__FILE__) . "/../lib/parent_model.php");
class translation extends parent_model
{
//----------------------------------------------------------------------------------
	function translation($arg_id=0) {
		$this->parent_model();
	}
//----------------------------------------------------------------------------------
	function humanize ($arg_str) {
		return ucfirst(str_replace("_", " ", $arg_str));
	}
//----------------------------------------------------------------------------------
	function delete($arg_key) {
		global $arr_AVAILABLE_LANGUAGES;
		$arr_available_languages = array_keys($arr_AVAILABLE_LANGUAGES);
		if (is_array($arr_available_languages)) {
			while (list(, $lang) = each($arr_available_languages)) {
				$arr_lang = array();
				require(PHP_ROOT . "uploads/translation/" . $lang . ".php");
				unset($arr_lang[$arg_key]);
				$str_lang = "<?\n" . $this->array_to_string($arr_lang, "\$arr_lang") . "\n";
				$this->string_to_file($str_lang, PHP_ROOT . "uploads/translation/" . $lang . ".php");
			}
		}
		return true;
	}
//----------------------------------------------------------------------------------
	function delete_many($arg_arr_keys) {
		global $arr_AVAILABLE_LANGUAGES;
		if (!is_array($arg_arr_keys) || count($arg_arr_keys) < 1) {
			add_error("no_entries_to_delete");
			return false;
		}
		$arr_available_languages = array_keys($arr_AVAILABLE_LANGUAGES);
		if (is_array($arr_available_languages)) {
			while (list(, $lang) = each($arr_available_languages)) {
				$arr_lang = array();
				require(PHP_ROOT . "uploads/translation/" . $lang . ".php");
				reset($arg_arr_keys);
				while (list(, $key) = each($arg_arr_keys)) {
					unset($arr_lang[$key]);
				}
				$str_lang = "<?\n" . $this->array_to_string($arr_lang, "\$arr_lang") . "\n";
				$this->string_to_file($str_lang, PHP_ROOT . "uploads/translation/" . $lang . ".php");
			}
		}
		return true;
	}
//----------------------------------------------------------------------------------
	function get_all () {
		require(PHP_ROOT . "uploads/translation/" . DEFAULT_LANGUAGE . ".php");
		return array_keys($arr_lang);
	}
//----------------------------------------------------------------------------------	
	function get_one_by_key ($arg_key) {
		global $arr_AVAILABLE_LANGUAGES;
		$arr_output = array();
		$arr_available_languages = array_keys($arr_AVAILABLE_LANGUAGES);
		if (is_array($arr_available_languages)) {
			while (list(, $lang) = each($arr_available_languages)) {
				$arr_lang = array();
				require(PHP_ROOT . "uploads/translation/" . $lang . ".php");
				$arr_output[$lang] = $arr_lang[$arg_key];
			}
		}
		if (!$arr_output['en']) $arr_output['en'] = $this->humanize($arg_key);
		return $arr_output;
	}
//----------------------------------------------------------------------------------
	function save ($arg_language_key, $arg_arr_languages) {
		global $arr_AVAILABLE_LANGUAGES;
		if (!$arg_language_key) {
			add_error('please_write_the_variable_name', array(), 'key');
			return false;
		}
		$arr_available_languages = array_keys($arr_AVAILABLE_LANGUAGES);
		if (is_array($arr_available_languages)) {
			while (list(, $lang) = each($arr_available_languages)) {
				$arr_lang = array();
				require(PHP_ROOT . 'uploads/translation/' . $lang . '.php');
				$arr_lang[$arg_language_key] = $arg_arr_languages[$lang];
				$str_lang = "<?\n" . $this->array_to_string($arr_lang, '$arr_lang') . "\n";
				echo $str_lang;
				$this->string_to_file($str_lang, PHP_ROOT . 'uploads/translation/' . $lang . '.php');
			}
		}
		return true;
	}
//----------------------------------------------------------------------------------
}	// end class 

