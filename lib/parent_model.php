<?
/**
 @phpversion 5.1
 @file parent_model.php
 @class parent_model
 This class is the parent class of almost all model classes. It implements many functions that are used in the children classes.
 */
require_once(dirname(__FILE__) . '/db_factory.php');	// it requires object class too
require_once(dirname(__FILE__) . "/class.phpmailer.php"); // it is for mail server
class parent_model extends object {
	// Private
	private $__DB;					/**< Private Member: DB Object to connect to database and run queries. */
	private $__properties = array();		/**< Private Member: The internal array of properties, used to save database output for the current object. */
	private $__offset_var_name = 'offset';		/**< Private Member: The offset variable name as used in the url of the page. */
	private $__skip_unique_validation = false;	/**< Private Member: Whether to skip unique validation or not.*/
	// protected
	protected $__allowed_fields = array();	/**< Protected Member: The names of the allowed fields. if it is provided then no fields are allowed to be used that are not listed in this array. If left empty there will be no restrictions on the field names. */
	protected $__private_keys = array();	/**< Protected Member: The names of properties that are removed from the properties array when using the method secure_output(). */
	protected $__primary_key = 'id';	/**< Protected Member: The name of the table field that is the primary key of the table. Used in some functions. */
	protected $__table;			/**< Protected Member: The table name of the current class if any. */
	protected $__is_error = false;		/**< Protected Member: a variable set by the validation function to state that there was an error in validation. It is get and cleared by the method $this->is_error().*/
	protected $__validation_errors = array(); /**< Protected Member: an array of all validation errors that happened on this class. */

	/** Protected Member: An array of validation settings for the current table fields.
	 It should be filled on the constructor of the child (used) class. And it should contain the following fields.
	 'field_name' => array(array(
	 'type' => 'the type of validation can be any of the following (format, number, int, presence, uniq, email, username, function)'
	 'function' => 'The name of the custom method used for validation. Used only if type=function'
	 'format' => 'regular expression (used only if type=format)'
	 'error_msg' => 'The error message to be shown to the user.'
	 )),
	 'another_field_name' => array(...)
	 */
	protected $__validation = array();

	/**
	 Search settings are saved in $this->__search_array in this form:
	 $this->__search_array[field_name_operator] = array('field_name', 'operator');
	 operator can be : [ eq, lt, gt, le, ge, like ] whick stand for [ =, <, >, <=, >=, LIKE ]
	 e.g.	$this->__search_array[username_eq] = array('username', 'eq');
	 $this->__search_array[age_gt] = array('age', 'gt');
	 */
	protected $__search_array = array();

	/**
	 A list of methods that are defined in the current object $this and are used to rollback what __save() does if it fails
	 $this->__rollback_functions = array(
	 array(\'method1\', $arr_params1),
	 array(\'method2\', $arr_params2)
	 )
	 */
	protected $__rollback_functions = array();

	/**
	 $this->__relations = array(
	 "field_name" => array("get_function" => "function_name", "set_function" => "function_name")
	 );
	 */
	protected $__relations = array();

	// public
	public $__upload_folder;			/**< Public Member: The relative path of the upload folder e.g. \'uploads/page/\'. set by default to \'uploads/__CLASS__/\'*/
	public $__page_limit = 0;			/**< Public Member: The number of records shown per page. */
	public $__offset = 0;				/**< Public Member: Paging offset: is the record where this page starts at. */
	public $__next_link = array();			/**< Public Member: The array that forms the url of the next page when paging is enabled. */
	public $__previous_link = array();		/**< Public Member: The array that forms the url of the previous page when paging is enabled. */
	public $__paging_pages = array();		/**< Public Member: The array of paging pages filled when set_paging() is called and then db_select() is called. */
//--------------------------------------------------------------------------------------
	private function __set_file ($arg_value) {
		// validate file
		if (!$this->__is_file_valid($arg_value)) {
			$this->__is_error = true;
			return false;
		}
		$uploaded_file_name = '';
		if ($this->is_field_valid($arg_value['property_name'], $arg_value)) {	// if field valid
			// if the field is valid then upload the file
			if ($arg_value['old_file_name'] && !$arg_value['tmp_name']) {	// if keep old file
				$uploaded_file_name = $arg_value['old_file_name'];
			} elseif ($arg_value['tmp_name']) {
				$bool_uniq = (($arg_value['flags'] & 0x10) > 0);
				$uploaded_file_name = $this->upload_file($arg_value, $this->__upload_folder, ($bool_uniq ? uniqid(time() . '_') . '_' : ''), ($arg_value['old_file_name'] != $arg_value['name'] ? $arg_value['old_file_name'] : ''));
				// add rollback method
				if ($uploaded_file_name) $this->__add_rollback_method ('delete_files', array($uploaded_file_name, $this->__upload_folder));

			} // end if keep old file
		} else {
			$uploaded_file_name = false;
		}	// end if field valid
		// set property
		return $uploaded_file_name;
	}
//--------------------------------------------------------------------------------------
	private function __set_files ($arg_property_name, $arg_value) {
		global $__in;
		// prepare upload folder
		if (!$this->__upload_folder) $this->__upload_folder = 'uploads/' . get_class($this) . '/';
		// create upload folder if it does not exist
		if (!file_exists($this->__upload_folder)) @mkdir($this->__upload_folder);
		// delete unneeded files
		if (is_array($__in['__files_to_be_deleted']) && $__in['__files_to_be_deleted'][$arg_property_name]) {
			$this->delete_files($__in['__files_to_be_deleted'][$arg_property_name], $this->__upload_folder);
		}

		// file
		if (is_array($__in['__files_flags'][$arg_property_name])) {	// multi
			$arr_files = array();
			while (list($i, $file_info) = each($__in['__files_flags'][$arg_property_name])) {
				$arg_value[$i]['flags'] = $__in['__files_flags'][$arg_property_name][$i];
				if (is_array($__in['__files_old']) && is_array($__in['__files_old'][$arg_property_name])) {
					$arg_value[$i]['old_file_name'] = $__in['__files_old'][$arg_property_name][$i];
				}
				if (isset($__in['__files_to_be_deleted']) && is_array($__in['__files_to_be_deleted'][$arg_property_name]) && in_array($arg_value[$i]['old_file_name'], $__in['__files_to_be_deleted'][$arg_property_name])) $arg_value[$i]['old_file_name'] = '';
				$arg_value[$i]['property_name'] = $arg_property_name;
				$arr_files[] = $this->__set_file($arg_value[$i]);
			}
			reset($__in['__files_flags'][$arg_property_name]);
			return $arr_files;
		} else {
			$arg_value['flags'] = $__in['__files_flags'][$arg_property_name];
			if (is_array($__in['__files_old'])) {
				$arg_value['old_file_name'] = $__in['__files_old'][$arg_property_name];
			}
			if (isset($__in['__files_to_be_deleted']) && $__in['__files_to_be_deleted'][$arg_property_name] == $arg_value['old_file_name']) $arg_value['old_file_name'] = '';
			$arg_value['property_name'] = $arg_property_name;
			return $this->__set_file($arg_value);
		}
	}
//--------------------------------------------------------------------------------------
	/**
	 * Filters an array of fields (the keys of the array has the fields names) and returns only the allowed fields.
	 * @param array $arg_arr_fields The array of fields to be filtered
	 * @param array $arg_arr_allowed_fields [optional] default array() if empty defaults to $this->__allowed_fields
	 * @return array filtered array
	 */
	protected function filter_allowed_fields (array $arg_arr_fields, array $arg_arr_allowed_fields=array()) {
		if (!$arg_arr_allowed_fields) $arg_arr_allowed_fields = $this->__allowed_fields;
		if (!$arg_arr_allowed_fields) return $arg_arr_fields;
		$arr_return = array();
		while (list($k, $v) = each($arg_arr_fields)) {
			if (!in_array($k, $arg_arr_allowed_fields)) continue;
			$arr_return[$k] = $v;
		}
		return $arr_return;
	}
//--------------------------------------------------------------------------------------
	/**
	 * Makes sure that all required fields are added to the input array to secure against hacking
	 * @param array $arg_arr_fields The array of fields to be filtered
	 * @param array $arg_arr_allowed_fields [optional] default array() if empty defaults to $this->__allowed_fields
	 * @return array filtered array
	 */
	protected function filter_required_fields (array $arg_arr_fields, array $arg_arr_allowed_fields=array()) {
		if (!$arg_arr_allowed_fields) $arg_arr_allowed_fields = $this->__allowed_fields;
		if (!$arg_arr_allowed_fields) return $arg_arr_fields;
		$arr_return = array_fill_keys($arg_arr_allowed_fields, null);
		$arr_return = array_merge($arr_return, $arg_arr_fields);
		return $arr_return;
	}
//--------------------------------------------------------------------------------------
	/**
	 * Prepares an order by statement to be added to a select statement for sorting results
	 * @param String $arg_order_by_field field name
	 * @param boolean $arg_ascendingly [optiona] default true
	 * @return String where statement without the WHERE word
	 */
	protected function prepare_order_by ($arg_order_by_field, $arg_ascendingly=true)
	{
		$query = '';
		if ($arg_order_by_field != '') {
			$query = ' ORDER BY ' . $this->__DB->escape($arg_order_by_field) . ($arg_ascendingly ? ' ASC' : ' DESC');
		}
		return $query;
	}
//--------------------------------------------------------------------------------------
	/**
	 Adds an error to the array of validation errors in this instance.
	 This function is not used for adding debugging errors.
	 @param String $arg_error_msg The error message to be shown to the user.
	 @param array $arg_arr_error_params	[Optional] Any extra parameters to be added to the error message if the error message has any place holders (Like %s and %d).
	 @param String $arg_field_name [Optional] The field name of the field that generated this error in case this error is generated by a validation function.
	 */
	protected function add_validation_error ($arg_error_msg, array $arg_arr_error_params=array(), $arg_field_name="") {
		$arr_error = array();
		$arr_error['error_msg'] = $arg_error_msg;
		if ($arg_arr_error_params) $arr_error['error_params'] = $arg_arr_error_params;
		if ($arg_field_name) $arr_error['field_name'] = $arg_field_name;
		$this->__validation_errors[] = $arr_error;
		$this->__is_error = true;
		return true;
	}
//--------------------------------------------------------------------------------------
	/**
	 * Prepares a where statement from the where array
	 * @param array $arg_arr_where the array of fields and there values
	 * @param String $arg_separator [optional] default ' AND '
	 * @return String the where statement or an empty string if the array was empty
	 */
	protected function prepare_where_statement (array $arg_arr_where, $arg_separator=' AND ') {
		if (!$arg_arr_where) return "";
		$where = "";
		$i = 0;
		while (list($k, $v) = each($arg_arr_where)) {
			if ($i > 0) $where .= $arg_separator;
			$where .= "`" . $this->__DB->escape($k) . "`='" . $this->__DB->escape($v) . "'";
			$i++;
		}
		return $where;
	}
//--------------------------------------------------------------------------------------
	/**
	 Constructor
	 Creates the database object to be used later.
	 @param arg_str_dbprofile the database profile from which the database host, databasename, username and password are extracted.
	 @see db::__construct().
	 @access Public.
	 */
	public function parent_model ($arg_str_dbprofile=DEFAULT_DBPROFILE) {
		$this->object();
		$this->__DB = &db_factory::get_connection($arg_str_dbprofile);
	}	// end constructor
//--------------------------------------------------------------------------------------
	/**
	 Returns the array of validation errors that happened on this instance
	 @access public
	 @return array of validation errors
	 */
	public function get_and_clear_validation_errors () {
		$errors = $this->__validation_errors;
		$this->__validation_errors = array();
		return $errors;
	}
//--------------------------------------------------------------------------------------
	/**
	 * Creates an update statement from an associative array of fields
	 * @param array $arg_arr_fields the associative array of fields
	 * @param array $arg_arr_where where conditions
	 * @param String $arg_table_name [optional] default '' if empty defaults to $this->__table
	 @return boolean true if updated successfully.
	 */
	public function auto_update (array $arg_arr_fields, array $arg_arr_where, $arg_table_name='') {
		if (!$arg_arr_fields) return false;
		if (!$arg_table_name) $arg_table_name = $this->__table;
		if (!$arg_table_name) throw new Exception("Invalid Table Name");

		$str_update_query = 'UPDATE `' . $arg_table_name . '` SET ';
		$str_update_query .= $this->prepare_where_statement($arg_arr_fields, ', ');
		if ($arg_arr_where) $str_update_query .= ' WHERE ' . $this->prepare_where_statement($arg_arr_where);
		return $this->__DB->update($str_update_query);
	}
//--------------------------------------------------------------------------------------
	/**
	 * Deletes from table by certain conditions
	 * @param array $arg_arr_where the conditions of the where statement
	 * @param String $arg_table_name [optional] default '' if empty defaults to $this->__table
	 * @return boolean
	 */
	public function delete_by (array $arg_arr_where, $arg_table_name='') {
		if (!$arg_table_name) $arg_table_name = $this->__table;
		if (!$arg_table_name) throw new Exception("Invalid Table Name");
		return $this->db_delete("DELETE FROM " . $arg_table_name . ($arg_arr_where ? ' WHERE ' . $this->prepare_where_statement($arg_arr_where) : ''));
	}
//--------------------------------------------------------------------------------------
	/**
	 Deletes a record from the current table by its primary key field
	 @param [$arg_id] the value of the primary key field (if not specified then it should be specified as an attribute to the $this)
	 @return returns true on successful delete and false otherwise
	 */
	public function delete ($arg_id=0) {
		if (!$this->__table) throw new Exception('No table given');
		if (!$this->__primary_key) throw new Exception('No Primary Key given');
		$id = $this->__primary_key;
		$this->$id = ($arg_id ? $arg_id : $this->$id);

		if (!$this->is_id($this->$id)) {
			$this->add_validation_error("element_not_found");
			throw new ValidationException($this);
		}
		return $this->db_delete('DELETE FROM `' . $this->__table . '` WHERE `' . $id . '`=%d', array($this->$id));
	}
//--------------------------------------------------------------------------------------
	public function __save () {
		global $__in;
		if (!$this->__table) throw new Exception('Model table name is not specified please set the $this->__table variable in your model constructor.');

		$arr_fields = $this->__properties;
		$arr_relation_fields = array();
		if (is_array($arr_fields)) {
			while (list($key, $value) = each($arr_fields)) {
				if (array_key_exists($key, $this->__relations)) {
					$arr_relation_fields[$key] = $value;
					unset($this->__properties[$key]);
				} else if (is_array($value) && is_array($__in['__files_flags']) && isset($__in['__files_flags'][$key])) {
					$value = $this->__set_files($key, $value);
					if ($value !== false) {
						$this->__properties[$key] = $value;
					}
				}
			}
			reset($arr_fields);
		}
		// if the object has the primary key field set then
		if ($this->__properties[$this->__primary_key]) {	// update the record
			$fields = $this->__properties;
			unset($fields[$this->__primary_key]);
			$return = $this->auto_update($fields, array($this->__primary_key => $this->__properties[$this->__primary_key]));
		} else {		// else insert
			unset($this->__properties[$this->__primary_key]);
			$id = $this->auto_insert($this->__properties, $this->__table);
			$this->__properties[$this->__primary_key] = $id;
			$return = $id;
		}

		while (list($key, $value) = each($arr_relation_fields)) {
			$this->__properties[$key] = $value;
			if ($this->__relations[$key]['set_function']) {
				call_user_func(array(&$this, $this->__relations[$key]['set_function']), $value);
			}
		}
		return $return;
	}
//--------------------------------------------------------------------------------------
	/**
	 Overiding method
	 If a method starting with get_one_by_ or get_all_by_ and that is not found in the current class or any of its parents is called,
	 This method finds the field name at the end of the method and finds the required records from the current table
	 This Function only works for PHP 5.1 and later
	
	 @phpversion 5.1
	 @param $arg_function_name the function being called
	 @param $arg_arr_params the paramters sent to this function
	 @return the return value of the called method or false in case there is an error
	 */
	public function __call ($arg_function_name, $arg_arr_params) {
		$arr_matches = array();
		if (substr($arg_function_name, 0, 11) == 'get_one_by_') {
			$field_name = str_replace('get_one_by_', '', $arg_function_name);
			return $this->get_one_row_by(array($field_name, $arg_arr_params[0]));

		} elseif (substr($arg_function_name, 0, 11) == 'get_all_by_') {
			$field_name = str_replace('get_all_by_', '', $arg_function_name);
			return $this->get_all_by_field($field_name, $arg_arr_params[0]);

		} elseif (preg_match('/^get_([a-z0-9_]+)_by_([a-z0-9_]+)$/i', $arg_function_name, $arr_matches)) {
			// $arr_field1_values = get_<field1>_by_<field2>($field1_value, $field2_value)
			return $this->get_field_by(array($arr_matches[1] => $arr_matches[2]), $arg_arr_params[0]);

		} elseif (preg_match('/^filter_result_array_by_([a-z0-9_]+)$/i', $arg_function_name, $arr_matches)) {
			// $arr_filtered_result = $this->filter_result_array_<field_name>($arr_result, $field_value, $operator="eq");
			return $this->filter_result_array_by_field($arg_arr_params[0], $arr_matches[1], $arg_arr_params[1], $arg_arr_params[2]);

		} elseif (preg_match('/^set_all_([a-z0-9_]+)$/i', $arg_function_name, $arr_matches)) {
			return $this->set_all_field($arr_matches[1], $arg_arr_params[0]);

		} elseif (preg_match('/^set_([a-z0-9_]+)$/i', $arg_function_name, $arr_matches)) {
			return $this->set_field($arr_matches[1], $arg_arr_params[0]);

		} elseif (preg_match('/^get_assoc_([a-z0-9_]+)_and_([a-z0-9_]+)$/i', $arg_function_name, $arr_matches)) {
			return $this->get_all_assoc($arr_matches[1], $arr_matches[2]);

		} else {
			throw new Exception('Call to an Undefined function : ' . __CLASS__ . '->' . $arg_function_name);
		}
	}
//--------------------------------------------------------------------------------------
	/**
	 Returns whether there was an error in the fields that has been validated recently
	 and resets the error flag to false so that we can restart validation on other fields.
	 @return boolean true if there was an error and false otherwise.
	 */
	public function is_error () {
		$bool_error = $this->__is_error;
		$this->__is_error = false;
		if ($bool_error) {
			// rollback
			$this->__rollback();
		}
		return $bool_error;
	}
//--------------------------------------------------------------------------------------
	/**
	 Checks if a field value is valid or not
	 It validates the value against the field validator arrays.
	 @param $arg_field_name the name of the field to be validated
	 @param $arg_value the value to be validated
	 @return returns true if valid and false otherwise
	 */
	public function is_field_valid ($arg_field_name, $arg_value) {
		$arr_field_validations = $this->__validation[$arg_field_name];
		if (is_array($arr_field_validations)) {
			while (list(, $arr_validation) = each($arr_field_validations)) {
				if (!$this->run_validation ($arg_field_name, $arg_value, $arr_validation)) {
					$this->__is_error = true;
					$this->add_validation_error($arr_validation['error_msg'], array($arg_value), $arg_field_name);
					return false;
				}
			}	// end while listing arg_arr_fields
		}
		return true;
	}
//---------------------------------------------------
	/**
	 Magic function to set object properties
	 @phpversion 5.1
	 @param $arg_property_name the property name of the object
	 @param $arg_value the value to be put for the property
	 */
	public function __set ($arg_property_name, $arg_value) {
		if ($this->is_field_valid($arg_property_name, $arg_value)) {
			$this->__properties[$arg_property_name] = $arg_value;
		}
	}
//--------------------------------------------------------------------------------------
	/**
	 Magic function to get an object property value
	 @phpversion 5.1
	 @param $arg_property_name the property name of the object
	 */
	public function __get($arg_property_name) {
		if (array_key_exists($arg_property_name, $this->__relations) && $this->__relations[$arg_property_name]['get_function']) return call_user_function(array(&$this, $this->__relations[$arg_property_name]['get_function']));
		return $this->__properties[$arg_property_name];
	}
//--------------------------------------------------------------------------------------
	/**
	 Magic function to check if an object property is set
	 @phpversion 5.1
	 @param $arg_property_name the property name of the object
	 */
	public function __isset($arg_property_name) {
		return isset($this->__properties[$arg_property_name]);
	}
//--------------------------------------------------------------------------------------
	/**
	 Magic function to unset object properties
	 @phpversion 5.1
	 @param $arg_property_name the property name of the object
	 */
	public function __unset($arg_property_name) {
		unset($this->__properties[$arg_property_name]);
	}
//--------------------------------------------------------------------------------------
	/**
	 Changes an array to This Object.
	 Copies an array to __properties of the current object.
	 @param arg_arr_vars associative array of variables to be copied to __properties.
	 @param $arg_bool_skip_validation to skip validation
	 @see __set().
	 @see array_to_object().
	 @see this_to_array().
	 @see objects_to_array().
	 @see array_to_objects().
	 @access Public.
	 */
	public function array_to_this ($arg_arr_vars, $arg_bool_skip_validation=false) {
		if (is_array($arg_arr_vars)) {
			while (list($k, $v) = each($arg_arr_vars)) {
				if ($arg_bool_skip_validation) {
					// skip the validation phase for optimization
					$this->__properties[$k] = $v;
				} else {
					$this->$k = $v;
				}
			}
		}
	}	// end function array_to_this
//--------------------------------------------------------------------------------------
	/**
	 Changes Array to an Object.
	 Creates an object of the type of the current class and copies an array to __properties of the this object.
	 @param arg_arr_vars associative array of variables to be copied to __properties.
	 @return An object of the current class type.
	 @see array_to_this().
	 @see this_to_array().
	 @see objects_to_array().
	 @see array_to_objects().
	 @access Public.
	 */
	public function array_to_object ($arg_arr_vars) {
		if(is_array($arg_arr_vars)) {
			$obj = NULL;
			eval('$obj = new ' . get_class($this) . '();');
			$obj->array_to_this($arg_arr_vars);
			return $obj;
		}else {	// else if there is no array
			return false;
		}	// end if array
	}	// end function array_to_object
//--------------------------------------------------------------------------------------
	/**
	 Changes This object to an Array.
	 returns __properties and can also return all public properties in the same array.
	 @param arg_bool_all boolean value to indicate whether to return all public properties in the array or not.
	 @return Array of properties.
	 @see array_to_this().
	 @see array_to_object().
	 @see objects_to_array().
	 @see array_to_objects().
	 @access Public.
	 */
	public function this_to_array ($arg_bool_all=true) {
		$arr_vars = $this->__properties;
		if ($arg_bool_all) {
			$arr = get_object_vars($this);
			if (is_array($arr)) {
				while(list($k, $v) = each($arr)) {
					$arr_vars[$k] = $v;
				}
			}
		}
		return $arr_vars;
	} 	// end function this_to_array
//--------------------------------------------------------------------------------------
	public function __reset () {
		$this->__properties = array();
		$this->__is_error = false;
		$this->__skip_unique_validation = false;
		$this->__validation_errors = array();
		$this->__page_limit = 0;
		$this->__offset = 0;
		$this->__next_link = array();
		$this->__previous_link = array();
		$this->__paging_pages = array();
	}
//--------------------------------------------------------------------------------------
	/**
	 * Skip the Unique validation in this instance
	 * if you want to reenable unique validation call $this->__reset()
	 * @see reset()
	 */
	public function skip_unique_validation () {
		$this->__skip_unique_validation = true;
	}
//--------------------------------------------------------------------------------------
	/**
	 Changes An array of objects to a multidimensional array.
	 returns the properties of an object in an array for each object in the given array of objects.
	 @param arr_objects Array of objects.
	 @param arg_bool_all boolean value to indicate whether to return all public properties in the array or not.
	 @return Multidimentional array of properties of objects.
	 @see array_to_this().
	 @see array_to_object().
	 @see this_to_array().
	 @see array_to_objects().
	 @access Public.
	 */
	public function objects_to_array ($arr_objects, $arg_bool_all=false) {
		if(is_array($arr_objects)) {
			while(list($key, $obj) = each($arr_objects)) {
				$arr_arrays[$key] = $obj->this_to_array($arg_bool_all);
			}	// end while
		}	// end if array of objects
		return $arr_arrays;
	}	// end function objects_to_array
//--------------------------------------------------------------------------------------
	/**
	 Changes a multidimensional array to an array of objects.
	 Copies each row in a multidimensional array to __properties of an object of the same type of the current class.
	 @param arg_arr_vars multidimensional array of associative arrays of variables to be copied to __properties.
	 @return An array of objects.
	 @see array_to_this().
	 @see array_to_object().
	 @see this_to_array().
	 @see objects_to_array().
	 @access Public.
	 */
	public function array_to_objects ($arg_arr_vars) {
		if(!is_array($arg_arr_vars)) return false;
		while(list(,$arr_row) = each($arg_arr_vars)) {	// while array of vars
			$obj = $this->array_to_object($arr_row);
			$arr_objects[] = $obj;
		}	// end while array
		return (is_array($arr_objects) ? $arr_objects : false);
	}	// getAllElements
//--------------------------------------------------------------------------------------
	/**
	 Enables or Disables paging.
	 If the arg_int_page_limit is 0 then paging is disabled and if it is bigger than 0 paging is enabled and the page size is set to the value of arg_int_page_limit.
	 The offset variable name may be changed to facilitate paging in a page that has more than one paging context.
	 @param arg_int_page_limit the number of rows to show per page.
	 @param arg___offset_var_name the name of the REQUEST variable that holds the holds the current paging offset.
	 @see db_select().
	 @access Public.
	 */
	public function set_paging ($arg_int_page_limit=0, $arg_offset_var_name='offset') {
		if ($arg_int_page_limit < 0) $arg_int_page_limit = 0;
		$this->__page_limit = $arg_int_page_limit;
		$this->__offset_var_name = $arg_offset_var_name;
	}
//--------------------------------------------------------------------------------------
	/**
	 Builds a query.
	 Builds a query from a query string with place holders and an array of variables. It cleans the array of variables to secure it against SQL injection then puts the variables of the array in the place holders.
	 @param arg_str_query The string of the query with its place holders.
	 @param arg_arr_variables the array of variables that are cleaned then put in the place holders of the query.
	 @see db_select().
	 @see db_update().
	 @see db_delete().
	 @access Public.
	 */
	public function query_builder ($arg_str_query, $arg_arr_variables) {
		if(!$arg_str_query) {
			return '';
		}
		if(!$arg_arr_variables) {
			return $arg_str_query;
		}
		$this->array_to_db($arg_arr_variables);	// cleanup the array
		$arg_str_query = vsprintf($arg_str_query, $arg_arr_variables);	// build the query
		return $arg_str_query;
	}	// end function query_builder
//--------------------------------------------------------------------------------------
	/**
	 Automatic insert or replace.
	 Inserts or replaces a row by taking only the table name and the associative array of field names and field values.
	 It gets the types of table fields first then decides whether to put single quotes around the values or not.
	 @param arg_arr_variables
	 @param arg_str_table_name
	 @param arg_str_command
	 @return Last insert id or Boolean value.
	
	 @see db::insert().
	 @see auto_replace().
	 @see auto_insert().
	 @access Private.
	 */
	public function auto_insert_replace (array $arg_arr_variables, $arg_str_table_name='', $arg_str_command='INSERT') {	// returns insert id
		$arg_str_table_name = ($arg_str_table_name ? $arg_str_table_name : $this->__table);
		if(!$arg_str_table_name) throw new Exception('No Table to Use in Insert');	// if there is not table name passed to the function

		$this->array_to_db($arg_arr_variables);

		// create the INSERT statement
		if(!is_array($arg_arr_variables)) throw new Exception('Nothing to insert in table <' . $arg_str_table_name . '> ');

		$str_fields = '';	// the filed names
		$str_values = '';	// the field values
		$str_fields = '`' . join('`, `', array_keys($arg_arr_variables)) . '`';
		$str_values = '\'' . join('\', \'', array_values($arg_arr_variables)) . '\'';
		$str_sql = $arg_str_command . ' INTO ' . $arg_str_table_name . ' (' . $str_fields . ') VALUES (' . $str_values . ')';
		return $this->__DB->insert($str_sql);
	}	// end function insert
//--------------------------------------------------------------------------------------
	/**
	 Automatic replace in DB.
	 Calls auto_insert_replace() with the last parameter as 'REPLACE'.
	 @see auto_insert_replace().
	 @access Public.
	 */
	public function auto_replace (array $arg_arr_variables, $arg_str_table_name='') {
		return $this->auto_insert_replace($arg_arr_variables, $arg_str_table_name, 'REPLACE');
	}	// end function insert
//--------------------------------------------------------------------------------------
	/**
	 Automatic insert in DB.
	 Calls auto_insert_replace() with the last parameter as 'INSERT'.
	 @see auto_insert_replace().
	 @access Public.
	 */
	public function auto_insert (array $arg_arr_variables, $arg_str_table_name='') {
		return $this->auto_insert_replace($arg_arr_variables, $arg_str_table_name, 'INSERT');
	}	// end function insert
//--------------------------------------------------------------------------------------
	/**
	 Insert or replace in db using a normal query.
	 Takes a query with place holders and an array of variables to be put in the place holders.
	 Used for optimized environments because auto insert runs an extra query.
	 @param arg_str_query string select statement with %%s place holders.
	 @param arg_arr_variables array of variables to be put in the %%s place holders of the arg_str_query.
	 @return Last insert id or Boolean value.
	 @see query_builder().
	 @see db::insert().
	 @see auto_insert_replace().
	 @access Public.
	 */
	public function db_insert ($arg_str_query='', $arg_arr_variables=array()) {
		$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		return $this->__DB->insert($arg_str_query);
	}
//--------------------------------------------------------------------------------------
	/**
	 Selects rows from db.
	 Builds the select query and selects a number of rows from db and frees the result.
	 It also supports paging by reading the instance variables int_page_limit, int_offset,
	 __offset_var_name and filling the instance variables arr_next_link and arr_previous_link.
	 If int_page_limit instance variable is more than 0 paging is enabled. It also cleans up the
	 result by removing escape slashes from it.
	
	 @param $arg_str_query String select statement with %%s place holders.
	 @param $arg_arr_variables Array of variables to be put in the %%s place holders of the arg_str_query.
	 @param $arg_old_mysql_count_word String optional defaults to COUNT(*) and is only used when MYSQL4_OR_LATER equals false.
	 @param $arg_old_mysql_count_sql String optional the sql statement of the count query. To enable it you have to put the value of $arg_old_mysql_count_word as '' (only used in suphisticate queries that cannot be just treated with COUNT(*) or any other key words in its place or that needs editing to other places in the source query).
	 $arg_old_mysql_count_sql_params Array optional only specifies when $arg_old_mysql_count_word = '' and $arg_old_mysql_count_sql has a query in it.
	 @return Multidimensional array of database rows (we can call it result array).
	
	 @see db::select().
	 @see query_builder().
	 @access Public.
	 */
	public function db_select ($arg_str_query, array $arg_arr_variables=array(), $arg_old_mysql_count_word='COUNT(*)', $arg_old_mysql_count_sql='', $arg_old_mysql_count_sql_params=array()) {
		global $__in;
		$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		if ($this->__page_limit > 0) {        // if paging is enabled
			// paging part #################
			$this->__offset = $__in[$this->__offset_var_name];
			if(!$this->__offset) $this->__offset = 0;
			if($this->__offset < 0) $this->__offset = 0;


			if (MYSQL4_OR_LATER) {
				/**< mysql 4.1 and later. */
				$arg_str_query = preg_replace('/^select[\s]+/i', 'SELECT SQL_CALC_FOUND_ROWS ', $arg_str_query);
				$arg_str_query .= ' LIMIT ' . $this->__offset . ', ' . $this->__page_limit;
				$arr_result = $this->__DB->select($arg_str_query);
				$int_count = $this->db_get_one_value('SELECT FOUND_ROWS()');
			} else {
				/**< mysql 3.2 */
				if ($arg_old_mysql_count_word) {
					$arg_str_count_query = 'SELECT ' . $arg_old_mysql_count_word . stristr($arg_str_query, ' FROM ');
				} else if ($arg_old_mysql_count_sql) {
					$arg_str_count_query = $this->query_builder($arg_old_mysql_count_sql, $arg_old_mysql_count_sql_params);
				} else {
					throw new Exception('You did not specify a Count query for the sql statement when you specified that MySQL Version is less than 4.1');
				}
				$arg_str_query .= ' LIMIT ' . $this->__offset . ', ' . $this->__page_limit;
				$arr_result = $this->__DB->select($arg_str_query);
				$int_count = $this->db_get_one_value($arg_str_count_query);
			}
			// make the $arr_pages
			$pages_count = ceil($int_count / $this->__page_limit);
			if ($this->__offset < 1) {
				$current_page = 1;
			} else {
				$current_page = intval($this->__offset / $this->__page_limit) + 1;
			}
			for ($i=1; $i <= $pages_count; $i++) {
				if ($i == $current_page) {
					$this->__paging_pages[$i] = array();
				} else {
					$this->__paging_pages[$i] = array($this->__offset_var_name => (($i * $this->__page_limit)-$this->__page_limit));
				}
			}

			// next and previous
			if($int_count > ($this->__offset + $this->__page_limit)) {        // if there are still records in the database then there must be a next link
				$int_next_offset = $this->__offset + $this->__page_limit;
				$this->__next_link = array($this->__offset_var_name => $int_next_offset);
			}else {
				$this->__next_link = array();
			}    // end if next link

			if($this->__offset > 0) {    // ifthe offset is bigger than 0 this means that there is previous link
				$int_previous_offset = $this->__offset - $this->__page_limit;
				$this->__previous_link = array($this->__offset_var_name => $int_previous_offset);
			}else {
				$this->__previous_link = array();
			}    // end if previous link
			// end paging part #################
		} else {	// if paging is not enabled
			$arr_result = $this->__DB->select($arg_str_query);
		}    // end if paging enabled
		return $arr_result;
	}    // end function db_select
//--------------------------------------------------------------------------------------
	/**
	 * Returns an associative array from a query by specifying the key and value fields
	 * @param String $arg_query
	 * @param array $arg_arr_params
	 * @param String $arg_key_field
	 * @param String $arg_value_field
	 * @return array
	 */
	public function db_get_assoc_array($arg_query, array $arg_arr_params, $arg_key_field, $arg_value_field) {
		$query = $this->query_builder($arg_query, $arg_arr_params);
		return $this->assoc_array_from_result_array($this->__DB->select($query), $arg_key_field, $arg_value_field);
	}
//--------------------------------------------------------------------------------------
	/**
	 * Returns one array from a query like an array of ids
	 * @param String $arg_query
	 * @param array $arg_arr_params
	 * @param String $arg_field [optional] default ''
	 * @return array
	 */
	public function db_get_one_array($arg_query, array $arg_arr_params=array(), $arg_field='') {
		$query = $this->query_builder($arg_query, $arg_arr_params);
		return $this->result_array_to_one_array($this->__DB->select($query), $arg_field);
	}
//--------------------------------------------------------------------------------------
	/**
	 Selects one row from db.
	 Does the same like db_select but reutrns the first row only of the recordset.
	
	 @param arg_str_query string select statement with %%s place holders.
	 @param arg_arr_variables array of variables to be put in the %%s place holders of the arg_str_query.
	 @return An array of one rows (the first row of the result array).
	
	 @see db_select().
	
	 @access Public.
	 */
	public function db_select_one_row ($arg_str_query, array $arg_arr_variables=array()) {
		$arr_wanted_row = array();
		$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		return $this->__DB->select_one_row($arg_str_query);
	}
//--------------------------------------------------------------------------------------
	/**
	 Updates a row or more in db.
	 Builds the update query and updates the database and can return the affected rows and the matched rows of a sql statement. This function by default returns true or false.
	 @param arg_str_query string update statement with %%s place holders.
	 @param arg_arr_variables array of variables to be put in the %%s place holders of the arg_str_query.
	 @param arg_bool_return_affected_rows Boolean true/false whether to return the number of affected rows or not.
	 @param arg_return_matched_rows Boolean true/false whether to return the number of matched rows or not.
	 @return Mixed (Array(int_affected_rows, int_matched_rows) or int_affected_rows only, or int_matched_rows only, or true).
	 @see db::update().
	 @see query_builder().
	 @access Public.
	 */
	public function db_update ($arg_str_query='', $arg_arr_variables=array(), $arg_bool_return_affected_rows=false, $arg_return_matched_rows=false) {	// returns true or false
		$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		$int_rows_affected = $this->__DB->update($arg_str_query, $arg_bool_return_affected_rows);
		if ($arg_bool_return_affected_rows) {
			$arr_return[] = $int_rows_affected;
		}
		$int_matched_rows = $this->__DB->get_matched_rows();
		if ($arg_return_matched_rows == true) {
			$arr_return[] = $int_matched_rows;
		}
		if (count($arr_return) == 2) {
			return $arr_return;
		} elseif (count($arr_return) == 1) {
			return $arr_return[0];
		} else {
			if ($int_matched_rows) {
				return true;
			} else {
				return false;
			}
		}
	}	// end function dbUpdate
//--------------------------------------------------------------------------------------
	/**
	 Deletes a row or more from db.
	 Builds the delete query and deletes rows from the database and can return the affected rows. This function by default returns true or false.
	 @param String $arg_query string delete statement with %%s place holders.
	 @param array $arg_arr_variables [optional] default array() array of variables to be put in the %%s place holders of the arg_str_query.
	 @param boolean $arg_return_affected_rows [optional] default false Boolean true/false whether to return the number of affected rows or not.
	 @return Mixed int_affected_rows or boolean.
	 @see db::delete().
	 @see query_builder().
	 @access Public.
	 */
	public function db_delete ($arg_query, array $arg_arr_variables=array(), $arg_return_affected_rows=false) {	// return true or false
		$arg_query = $this->query_builder($arg_query, $arg_arr_variables);
		return $this->__DB->delete($arg_query, $arg_return_affected_rows);
	}	// end function delete
//--------------------------------------------------------------------------------------
	/**
	 Returns one value only.
	 Builds a select statement and selects the first field in the first row of the output.
	 @param arg_str_query string select statement with %%s place holders.
	 @param arg_arr_variables array of variables to be put in the %%s place holders of the arg_str_query.
	 @return Scalar value String or integer or float.
	 @see db::get_one_value().
	 @see query_builder().
	 @access Public.
	 */
	public function db_get_one_value ($arg_str_query='', $arg_arr_variables=array()) {	// return one value
		if($arg_arr_variables)$arg_str_query = $this->query_builder($arg_str_query, $arg_arr_variables);
		return $this->__DB->get_one_value($arg_str_query);
	}	// end function db_get_one_value
//--------------------------------------------------------------------------------------
	/**
	 Array to DB.
	 Cleans array values from sql injection.
	 @param arg_arr_vars an array of variables to be cleaned. It is passed by reference.
	 @return The cleaned array.
	 @see query_builder().
	 @see db::escape_array().
	 @access Public.
	 */
	public function array_to_db (&$arg_arr_vars) {	// cleans the array to be put in the db
		$arg_arr_vars = $this->__DB->escape_array($arg_arr_vars);
		return $arg_arr_vars;
	}	// end function array_to_db
//--------------------------------------------------------------------------------------
	/**
	 Sends mail using mail().
	 Uses mail() to send an email to arg_str_to_email email address from arg_str_from_email using SMTP with no authentication. This function makes sure that the sender and receiver emails are in a right format first. It can also send mails in html or text format.
	 @param arg_str_to_email The receiver email address.
	 @param arg_str_from_email the sender email address.
	 @param arg_str_from_name the sender full or nick name.
	 @param arg_str_subject the message subject.
	 @param arg_str_message the message body.
	 @param arg_bool_html boolean whether this message should be sent in html format or not.
	 @param arg_str_other_headers Any extra headers that the developer may need to add.
	 @return Boolean wheteher the mail has been sent or not.
	 @see is_email().
	 @access Public.
	 */
	public function send_mail ($arg_str_to_email, $arg_str_from_email, $arg_str_from_name='', $arg_str_subject='', $arg_str_message='', $arg_bool_html=false, $arg_str_other_headers='') {	// a function that sends mail with headers
		$return = true;
		if(!$this->is_email($arg_str_to_email)) {
			$this->add_validation_error('invalid_receiver_email');
			$return = false;
		}		// If the receiver email address is wrong return false
		if(!$this->is_email($arg_str_from_email)) {
			$this->add_validation_error('invalid_sender_email');
			$return = false;
		}		// if the sender email address is wrong return false
		if(!$return) return false;
		//	$arg_str_message = text_to_html($arg_str_message);
		// make up the header
		$header = "MIME-Version: 1.0\r\n";
		if($arg_bool_html) {	// if the mail is sent as html
			$header .= "Content-type: text/html; charset=UTF-8\r\n";
		}else {		// else if the email is sent as clear text
			$header .= "Content-type: text/plain; charset=UTF-8\r\n";
		}	// end if HTML
		$header .= 'Organization: ' . SITE_NAME . "\r\n";
		//$header .= "Content-Transfer-encoding: 8bit\r\n";
		$header .= 'To: ' . $arg_str_to_email . "\r\n";
		$header .= 'From: ' . $arg_str_from_name . ' <' . $arg_str_from_email . ">\r\n";
		$header .= 'Reply-To: ' . $arg_str_from_name . ' <' . $arg_str_from_email . ">\r\n";
		$header .= 'Message-ID: <' . md5(uniqid(time())) . '@' . $_SERVER['SERVER_NAME'] . ">\r\n";
		$header .= 'Return-Path: ' . $arg_str_from_email . "\r\n";
		$header .= "X-Priority: 1\r\n";
		$header .= "X-MSmail-Priority: High\r\n";
		$header .= "X-Mailer: Microsoft Office Outlook, Build 11.0.5510\r\n"; //hotmail and others dont like PHP mailer.
		$header .= "X-MimeOLE: Produced By Microsoft MimeOLE V6.00.2800.1441\r\n";
		$header .= 'X-Sender: ' . $arg_str_from_email . "\r\n";
		$header .= 'X-AntiAbuse: This is an email sent from - ' . $arg_str_from_name . ' - to ' . $arg_str_to_email . ".\r\n";
		$header .= 'X-AntiAbuse: Servername - ' . $_SERVER['SERVER_NAME'] . "\r\n";
		$header .= 'X-AntiAbuse: User - ' . $arg_str_from_email . "\r\n";
		$header .= $arg_str_other_headers;

		if ($_SERVER['REMOTE_ADDR'] != '127.0.0.1') $mail_sent = @mail($arg_str_to_email, $arg_str_subject, $arg_str_message, $header);
		if($mail_sent==1) {
			return true;
		}else {	// else if the mail is not sent
			$this->add_validation_error('couldnot_send_mail');
			return false;
		}	// end if mail is send
	}	// end function send_mail
//--------------------------------------------------------------------------------------
	/**
	Sends mail using mail().
	Uses mail() to send an email to arg_str_to_email email address from arg_str_from_email using SMTP with no authentication. This function makes sure that the sender and receiver emails are in a right format first. It can also send mails in html or text format.
	@param arg_str_to_email The receiver email address.
	@param arg_str_to_name the name of the receiver persone.
	@param arg_str_from_email the sender email address.
	@param arg_str_from_name the sender full or nick name.
	@param arg_str_subject the message subject.
	@param arg_str_message the message body.
	@param arg_server the mail server which will be used.
	@param arg_username the username of the acount in this mail server.
	@param arg_password the password for this acount.
	@param arg_bool_html boolean whether this message should be sent in html format or not.
	@return Boolean wheteher the mail has been sent or not.
	@see is_email().
	@access Public.
	*/
	function send_mail_using_phpmailer ($arg_to_email, $arg_to_name, $arg_from_email, $arg_from_name, $arg_subject, $arg_message, $arg_bool_html=false, $arg_bcc_email="", $arg_server=SMTP_SERVER, $arg_username=SMTP_USERNAME, $arg_password=SMTP_PASSWORD) {
		$return = true;
		if(!$this->is_email($arg_to_email)){add_error("invalid_receiver_email"); $return = false;}		// If the receiver email address is wrong return false
		if(!$this->is_email($arg_from_email)){add_error("invalid_sender_email"); $return = false;}		// if the sender email address is wrong return false
		if($arg_bcc_email!="" && !$this->is_email($arg_bcc_email)){add_error("invalid_bcc_email"); $return = false;}
		if(!$return) return false;
		error_reporting(E_STRICT);

		date_default_timezone_set('America/Toronto');

		$mail = new PHPMailer();
		//$mail->SMTPDebug = true;
		//$body = $mail->getFile('contents.html');
		//$body = eregi_replace("[\]",'',$body);

		$mail->IsSMTP();
		$mail->SMTPAuth = true;                  // enable SMTP authentication
		//$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mail->Host = $arg_server;
		//$mail->Port = 25;
		$mail->Port = 465;

		$mail->Username = $arg_username;
		$mail->Password = $arg_password;

		$mail->AddReplyTo($arg_from_email, $arg_from_name);

		$mail->From = $arg_from_email;
		$mail->FromName = $arg_from_name;

		$mail->Subject = $arg_subject;

		if ($arg_bool_html) {
			$mail->IsHTML(true); // send as HTML
			$mail->MsgHTML($arg_message);
			$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
		} else {
			$mail->Body = $arg_message;                      //HTML Body
			$mail->WordWrap = 50; // set word wrap
		}

		$mail->AddAddress($arg_to_email, $arg_to_name);
		if($arg_bcc_email!="") $mail->AddBCC($arg_bcc_email, $arg_bcc_email);

		//$mail->AddAttachment("images/phpmailer.gif");             // attachment

		if(!$mail->Send()) {
		  	//add_error("couldnot_send_mail");
			return false;
		} else {
		  	return true;
		}
	}
//--------------------------------------------------------------------------------------
	/**
	 HTML to Text.
	 Changes html encoded text to its original form of text by using html_entity_decode which is the oposite of htmlspecialchars(). for example changing &nbsp; to a space and &gt; to > and so on.
	 @param arg_str_text the text to be changed from html to normal text. This parameter is passed by reference to ease its use.
	 @return string the changed text.
	 @see text_to_html().
	 @access Public.
	 */
	public function html_to_text (&$arg_str_text) {		// to Change the HTML to Text
		$arg_str_text = str_replace(' &nbsp; ', '  ', $arg_str_text);
		$arg_str_text = trim(html_entity_decode($arg_str_text));
		return $arg_str_text;
	}	// end function html_to_text
//--------------------------------------------------------------------------------------
	/**
	 Text to DB.
	 Used to escape user input to secure the application against SQL Injection.
	 @param arg_str_text the text to be cleaned. it is passed by reference for convenience.
	 @return String the cleaned text.
	 @see array_to_db().
	 @access Public.
	 */
	public function text_to_db (&$arg_str_text) {
		$arg_str_text = $this->__DB->escape($arg_str_text);
		return $arg_str_text;
	}	// end function text_to_db
//--------------------------------------------------------------------------------------
	/**
	 Text to HTML.
	 Prepares text to be shown in the page as html by changing html tags using htmlspecialchars. This function is used to disable Cross platform attacks (XSS). So please try to use it whenever you want to show text on the html page.
	 It also replaces new lines with html line breaks and spaces to &nbsp;
	 @param arg_str_text the rte text that should be cleaned. It is passed by reference to make it easier to use.
	 @return string the cleaned rte text.
	 @access Public.
	 */
	public function text_to_html (&$arg_str_text) {	// to get text from DB and put it in an HTML page
		$arg_str_text = trim(htmlspecialchars($arg_str_text));
		$arg_str_text = str_replace("\n", '<br>' . "\n", $arg_str_text);
		$arg_str_text = str_replace('  ', ' &nbsp; ', $arg_str_text);
		return $arg_str_text;
	}	// end function text_to_html
//--------------------------------------------------------------------------------------
	/**
	 Checks if a number is a valid table id.
	 checks the id and if it is not valid returns false. It also makes sure that the id is an integer.
	 @param arg_int_id the id to be checked. it is passed by reference to change it to its integer value within the same function.
	 @return Boolean whether the id is valid or not.
	 @access Public.
	 */
	public function is_id (&$arg_int_id) {
		if(!is_numeric($arg_int_id)) {
			return false;
		}
		$arg_int_id = intval($arg_int_id);
		if($arg_int_id < 1) {
			return false;
		}
		return true;
	}	//	is_id
//--------------------------------------------------------------------------------------
	/**
	 Is Email address correct.
	 Checks an email then returns true if the email has the correct format and false if not
	 @param arg_str_email  an email address to be checked against a regular expression to check its format.
	 @return Boolean whether the email address is correct or not.
	 @see send_mail().
	 @access Public.
	 */
	public function is_email (&$arg_str_email) {
		$arg_str_email = trim(strtolower($arg_str_email));
		if(!$arg_str_email) {
			return false;
		}
		return (preg_match('/^([a-zA-Z0-9_\.\-]){2,}(\@){1}([a-zA-Z0-9_\-]){2,}(\.){1}([a-zA-Z0-9_\.\-]){2,}$/', $arg_str_email) == 1);
	}	// end function
//--------------------------------------------------------------------------------------
	/**
	 Checks the validity of a username.
	 Checks the username if it is empty or less than 3 characters or more than 50 characters or has invalid characters then it is invalid
	 @param arg_str_username the username to be checked.
	 @return Boolean whether the username is correct or not.
	 @access Public.
	 */
	public function is_username (&$arg_str_username) {
		$arg_str_username = trim(strtolower($arg_str_username));
		if(!$arg_str_username) {
			return false;
		}
		return (preg_match('/(^(([0-9a-zA-Z_-]){3,50})$)/', $arg_str_username) == 1);
	}     // end function
//--------------------------------------------------------------------------------------
	/**
	 List the files in a folder.
	 Lists all files found in a certain folder or lists only the files that start with a certain string.
	 @param arg_str_folder_name the folder name that will be searched.
	 @param arg_str_files_starting_with if sepecified lists only the files starting with its value.
	 @return Array of file names.
	 @see delete_files_in_folder().
	 @access Public.
	 */
	public function list_files_in_folder ($arg_str_folder_name, $arg_str_files_starting_with='') {	// a function that takes a folder name and returns an array of all the files in that folder (not recursive)
		$arr_files = array();
		if(!$arg_str_folder_name) {
			return false;
		}	// if the folder name is not valid then return false
		if (!file_exists($arg_str_folder_name)) {
			return false;
		}		// if the folder does not exists then return false
		if (!is_dir($arg_str_folder_name)) {
			return false;
		}		// if it is not a folder then return false
		$dir_folder = opendir($arg_str_folder_name);		// open the folder
		while ($file = readdir($dir_folder)) {		// while folder
			if (is_dir($file)) {
				continue;
			}	// if it is a directory then do nothing and continue to the next while loop
			if($arg_str_files_starting_with) {		// if a condition is put that the files start with a certain text
				if (preg_match('/^' . $arg_str_files_starting_with . '/', $file)) {
					$arr_files[] = $file;
				}		// if it is a file add it to the array of files
			}else {
				$arr_files[] = $file;		// if it is a file add it to the array of files
			}		// end if a condition is put
		}	// end while
		closedir($dir_folder);							// close the folder
		if(count($arr_files) < 1) {		// if array of files is empty
			return false;		// if the array is empty then return false
		}else {
			return $arr_files;		// return the array of files if it is not empty
		}	// end if array of files is empty
	}	// end function list_files_in_folder
//--------------------------------------------------------------------------------------
	/**
	 List the Folders in a folder.
	 Lists all folders found in a certain folder or lists only the folders that start with a certain string.
	 @param arg_str_folder_name the folder name that will be searched.
	 @param arg_str_folder_starting_with if sepecified lists only the folders starting with its value.
	 @return Array of folder names.
	 @see list_files_in_folder().
	 @access Public.
	 */
	public function list_folders_in_folder ($arg_str_folder_name, $arg_str_folder_starting_with='') {	// a function that takes a folder name and returns an array of all the folders in that folder (not recursive)
		$arr_folders = array();
		if(!$arg_str_folder_name) {
			return false;
		}	// if the folder name is not valid then return false
		if (!file_exists($arg_str_folder_name)) {
			return false;
		}		// if the folder does not exists then return false
		if (!is_dir($arg_str_folder_name)) {
			return false;
		}		// if it is not a folder then return false
		$dir_folder = opendir($arg_str_folder_name);		// open the folder
		while ($folder = readdir($dir_folder)) {		// while folder
			if(is_dir($folder)) {
				if(($folder == '.') && ($folder == '..')) continue;
				if($arg_str_folder_starting_with)	// if folders starting with
				{
					if(preg_match('/^' . $arg_str_folder_starting_with . '/', $folder)) {
						$arr_folders[] = $folder;
					}
				}
				else	// else if all folders
				{
					$arr_folders[] = $folder;
				}		// end if arg_str_folder_starting_with
			}	// end if dir
		}	// end while
		closedir($dir_folder);							// close the folder
		return $arr_folders;
	}	// end function list_folders_in_folder
//--------------------------------------------------------------------------------------
	/**
	 Delete the files in a folder.
	 Deletes all files found in a certain folder or deletes only the files that start with a certain string.
	 @param arg_str_folder_name the folder name that will be searched.
	 @param arg_str_files_starting_with if sepecified deletes only the files starting with its value.
	 @return Boolean whether the files are deleted or not.
	 @see list_files_in_folder().
	 @access Public.
	 */
	public function delete_files_in_folder ($arg_str_folder_name, $arg_str_files_starting_with='') {	// a function that takes a folder name and returns an array of all the files in that folder (not recursive)
		$this->edit_dir_name($arg_str_folder_name);
		$arr_files = $this->list_files_in_folder($arg_str_folder_name, $arg_str_files_starting_with);

		if(is_array($arr_files)) {
			while(list(, $str_fileName) = each($arr_files)) {
				@unlink($arg_str_folder_name . $str_fileName);
			}
		}
		return true;
	}	// end function delete_files_in_folder
//--------------------------------------------------------------------------------------
	/**
	 Makes sure the directory name ends with / and replaces any backslashes with normal slashes.
	 @param arg_str_dir_name the directory name to be checked. It is passed by reference for convenience.
	 @return the formatted directory name.
	 @access Public.
	 */
	public function edit_dir_name (&$arg_str_dir_name) {		// if the folder name does not end with a / the slash is adde
		$arg_str_dir_name = str_replace('\\', '/', $arg_str_dir_name);
		$arg_str_dir_name = trim($arg_str_dir_name);
		if ($arg_str_dir_name[strlen($arg_str_dir_name)-1] != '/') $arg_str_dir_name .= '/';
		return $arg_str_dir_name;
	}	// end 	function editFolderName
//--------------------------------------------------------------------------------------
	/**
	 Makes a thumbnail from a picture.
	 Makes a thumbnail image from the picture and gives it the new name or just resizes the picture if the arg_str_thumbnail_name is left empty.
	 @param arg_str_file_name the filename of the picture to be resized.
	 @param arg_str_picture_dir the foldername of the picture and the thumbnail.
	 @param arg_str_thumbnail_name the name of the file of the thumbnail picture if left empty the arg_str_file_name original picture will be resized with the same name.
	 @param arg_int_max_width The maximum width allowed for the thumbnail if left 0 this means width will not be resized.
	 @param arg_int_max_height The maximum height allowed for the thumbnail if left 0 this means height will not be resized.
	 @return Boolean whether the operation occured or not.
	 @see upload_picture().
	 @access Public.
	 */
	public function make_thumbnail ($arg_str_file_name, $arg_str_picture_dir, $arg_str_thumbnail_name='', $arg_int_max_width=0, $arg_int_max_height=0) {
		if(!$arg_str_file_name) {
			return false;
		}
		if(!$arg_str_picture_dir) {
			return false;
		}
		$this->edit_dir_name($arg_str_picture_dir);
		if(!$arg_str_thumbnail_name) {
			$arg_str_thumbnail_name = $arg_str_file_name;
		}

		// get the command suffix from the pic extension
		$path = pathinfo($arg_str_file_name);
		if((strtolower($path['extension']) == 'jpg') || (strtolower($path['extension']) == 'jpeg')) {
			$commandsuffix = 'jpeg';
		}elseif(strtolower($path['extension']) == 'png') {
			$commandsuffix = 'png';
		}elseif(strtolower($path['extension']) == 'gif') {
			$commandsuffix = 'gif';
		}else {	// else if not a supported format of pictures
			return false;
		}	// end if extension

		// get the size of thenew thumbnail
		$original_size = getimagesize($arg_str_picture_dir . $arg_str_file_name);

		$old_size = $original_size;
		$new_size = $old_size;

		if($arg_int_max_width!=0) {
			if($old_size[0] > $arg_int_max_width) {
				// resize width and height acording to the new width which is the maximum width
				$new_size[0] = $arg_int_max_width;
				$new_size[1] = (int)(($new_size[0]/$old_size[0]) * $old_size[1]);
			}	// check the width
		}
		$old_size = $new_size;		// so the new size now will be old because the picture may be resized again
		if($arg_int_max_height!=0) {
			if($old_size[1] > $arg_int_max_height) {
				// resize width and height acording to the new height which is the maximum height
				$new_size[1] = $arg_int_max_height;
				$new_size[0] = (int)(($new_size[1]/$old_size[1]) * $old_size[0]);
			}	// check the height
		}	// end if

		// make sure if there will be a change
		if(($original_size[0] == $new_size[0]) && ($original_size[1] == $new_size[1])) {
			return true;
		}
		$img_big_pic = NULL;
		eval('$img_big_pic = imagecreatefrom' . $commandsuffix . '(\'' . $arg_str_picture_dir . $arg_str_file_name . '\');');
		$img_small_pic = imagecreatetruecolor($new_size[0], $new_size[1]);
		imagecopyresampled($img_small_pic, $img_big_pic, 0, 0, 0, 0, $new_size[0], $new_size[1], $original_size[0], $original_size[1]);

		// save the thumbnail to disk
		eval('image' . $commandsuffix . '($img_small_pic, \'' . $arg_str_picture_dir . $arg_str_thumbnail_name . '\', 80);');

		// destroy the unwanted resources
		imagedestroy($img_big_pic);
		imagedestroy($img_small_pic);
		return true;
	}	// end function make_thumbnail
//--------------------------------------------------------------------------------------
	/**
	 Uploads a file to the server.
	 Uploads a file to a certain folder on the server. In most cases the folder should be given the permission 755. The file uploaded is moved and renamed to a new generated name that starts with arg_str_prefix.
	 If arg_str_replaced_file_name is specified the file that has the replaced file name will be removed.
	 arg_str_prefix can be used as id_ to differenciate files and is easily differenciated using list_files_in_folder().
	 @param arg_file the file input to be uploaded like $_FILES['myfile'].
	 @param arg_str_upload_dir the folder where the file will be moved (in most cases need to have 755 permissions rwx).
	 @param arg_str_prefix The string that the new filename should start with if any.
	 @param arg_str_replaced_file_name the filename of the file that we want to remove on this upload process if any.
	 @return the uploaded generated filename or false on failure.
	 @see list_files_in_folder().
	 @see upload_picture().
	 @access Public.
	 */
	public function upload_file ($arg_file, $arg_str_upload_dir, $arg_str_prefix='', $arg_str_replaced_file_name='') {
		$matches = array();
		if (!preg_match('/\.(exe|zip|rar|tar.bz2|tar|tar\.gz|gz|bz2|ace|iso|nrg|daa|dmg|pdf|psd|jpeg|jpg|gif|png|bmp|tiff|doc|docx|odt|rtf|txt|sql|xls|csv|ppt|pps|wav|mp3|rm|ram|avi|dat|wmv|mpg|mpeg|ra|a4v|a4m|arm|mp4|ape|ogg|flac|vqf)$/i', $arg_file['name'], $matches)) {
			$this->add_validation_error('extension_not_allowed');
			return false;
		}
		$ext = $matches[1];
		//$file_withno_ext = preg_replace('/' . $ext . '/', '', $arg_file['name']);
		if (!$arg_str_upload_dir) return false;
		$this->edit_dir_name($arg_str_upload_dir);
		if ($arg_file['name']) {	// if there is a file given to the function
			$str_new_file_name = $arg_str_prefix . $arg_file['name']; //preg_replace('/[^A-Za-z0-9\._]/', '_', $arg_file['name']);
			// $str_new_file_name = uniqid($arg_str_prefix) . '.' . $ext;	// combine the prefix and the unique id and extension to get the new file name
			if (move_uploaded_file($arg_file['tmp_name'], $arg_str_upload_dir . $str_new_file_name)) {	// rename and move the file to the right folder after it is uploaded
				chmod($arg_str_upload_dir . $str_new_file_name, 0644);	// change permissions if possible
				if($arg_str_replaced_file_name != '') {	// if the old file name is specified
					if (file_exists($arg_str_upload_dir . $arg_str_replaced_file_name)) {
						unlink($arg_str_upload_dir . $arg_str_replaced_file_name);
					} else {
						$this->add_validation_error('could_not_delete_file_because_it_is_not_found', array($arg_str_replaced_file_name, $arg_str_upload_dir));
					}
				}	// end if
				return $str_new_file_name;
			}else {		// if the file could not be uploaded
				$this->add_validation_error('couldnot_upload_file', array($arg_file['name'], $arg_str_upload_dir));
				return false;
			}	// end if
		}else {	//else if there is no file given to the function
			$this->add_validation_error('no_file_to_upload');
			return false;
		}	// end if
	}	// end function upload_file
//--------------------------------------------------------------------------------------
	/**
	 Uploads a picture and resizes it if needed.
	 It is a combination of the functions upload_file(). and make_thumbnail().
	 It first makes sure that the file extension is a known picture extension.
	 Then it uploads the picture with upload_file() and uses the new file name
	 returned by this function in make_thumbnail() to resize the uploaded picture.
	 @param arg_file the file input to be uploaded like $_FILES['mypicture'].
	 @param arg_str_upload_dir the folder where the file will be moved (in most cases need to have 755 permissions rwx).
	 @param arg_str_prefix The string that the new filename should start with if any.
	 @param arg_str_replaced_file_name the filename of the file that we want to remove on this upload process if any.
	 @param arg_int_max_width The maximum width allowed for the thumbnail if left 0 this means width will not be resized.
	 @param arg_int_max_height The maximum height allowed for the thumbnail if left 0 this means height will not be resized.
	 @return the uploaded generated filename or false on failure.
	 @see upload_file().
	 @see make_thumbnail().
	 @access Public.
	 */
	public function upload_picture ($arg_file, $arg_str_upload_dir, $arg_str_prefix='', $arg_str_replaced_file_name='', $arg_int_max_width=100, $arg_int_max_height=200) {
		// upload the fil of the picture
		$arr_pathinfo = pathinfo($arg_file['name']);
		if((strtolower($arr_pathinfo['extension']) != 'jpg') && (strtolower($arr_pathinfo['extension']) != 'jpeg') && (strtolower($arr_pathinfo['extension']) != 'png')) {
			$this->add_validation_error('not_a_picture');
			return false;
		}
		$str_new_file_name = $this->upload_file($arg_file, $arg_str_upload_dir, $arg_str_prefix, $arg_str_replaced_file_name);
		if(!$str_new_file_name) {
			return false;
		}
		// resize the uploaded image
		if($this->make_thumbnail($str_new_file_name, $arg_str_upload_dir, '', $arg_int_max_width, $arg_int_max_height)) {
			return $str_new_file_name;
		}else {
			return false;
		}
	}	// end function upload_picture
//--------------------------------------------------------------------------------------
	/**
	 Returns a one dimensional array from an array of arrays (result array).
	 Searches the arrays in the result array for the arg_str_key_field and puts its value in a one dimensional array. If there is no arg_str_key_field the first field is taken instead.
	 @param arg_arr_array The results array to loop on.
	 @param arg_str_key_field the key field name to put in the one dimensional array. If not supplied the first field is taken
	 @return one dimensional array.
	 @access Public.
	 */
	public function result_array_to_one_array ($arg_arr_array, $arg_str_key_field='') {		// one array like getting all the ids of a table
		if(!is_array($arg_arr_array))return array();
		$arr_output = array();
		while(list(,$row) = each($arg_arr_array)) {
			if ($arg_str_key_field) {
				$arr_output[] = $row[$arg_str_key_field];
			} else {
				list(,$value) = each($row);
				$arr_output[] = $value;
			}
		}
		return $arr_output;
	}
//--------------------------------------------------------------------------------------
	/**
	 Gets the real folder path from its relative path.
	 @param arg_str_relative_folder_name the relative path of the given directory.
	 @return string directory absolute path.
	 @access Public.
	 */
	public function get_real_folder ($arg_str_relative_folder_name='') {
		$arr_pathinfo = pathinfo($_SERVER['PHP_SELF']);
		$rootDir = $_SERVER['DOCUMENT_ROOT'] . $arr_pathinfo['dirname'];		// sepecify the root directory on the file system
		$this->edit_dir_name($rootDir);
		$mydir = $rootDir . $arg_str_relative_folder_name;
		$this->edit_dir_name($mydir);
		return $mydir;
	}
//--------------------------------------------------------------------------------------
	/**
	 Associative Array from Result Array (Array of Arrays).
	 Takes the keys from which it will get the key and value of the associative array and loops on an array of arrays (result array) to build the associative array.
	 @param arg_arr_objects_array the array of objects to loop on.
	 @param arg_str_key_field the property name of the key field of the result associative array.
	 @param arg_str_value_field the property name of the value field of the result associative array.
	 @return Associative Array (one dimensional) of key, value.
	 @see assoc_array_from_objects_array().
	 @access Public.
	 */
	public function assoc_array_from_result_array ($arg_arr_result_array, $arg_str_key_field, $arg_str_value_field) {
		if(!is_array($arg_arr_result_array)) {
			return array();
		}
		if(!$arg_str_key_field) {
			return array();
		}
		if(!$arg_str_value_field) {
			return array();
		}
		$arr_assoc = array();
		while(list(,$row) = each($arg_arr_result_array)) {
			if((array_key_exists($arg_str_key_field, $row)) && (array_key_exists($arg_str_value_field, $row))) {
				$arr_assoc[$row[$arg_str_key_field]] = $row[$arg_str_value_field];
			}
		}
		return $arr_assoc;
	}
//--------------------------------------------------------------------------------------
	/**
	 Associative Array from Objects Array.
	 Takes the names of the properties from which it will get the key and value of the associative array and loops on an objects array to build the associative array.
	 @param arg_arr_objects_array the array of objects to loop on.
	 @param arg_str_key_field the property name of the key field of the result associative array.
	 @param arg_str_value_field the property name of the value field of the result associative array.
	 @return Associative Array (one dimensional) of key value.
	 @see assoc_array_from_result_array().
	 @access Public.
	 */
	public function assoc_array_from_objects_array ($arg_arr_objects_array, $arg_str_key_field, $arg_str_value_field) {
		if(!is_array($arg_arr_objects_array)) {
			return array();
		}
		if(!$arg_str_key_field) {
			return array();
		}
		if(!$arg_str_value_field) {
			return array();
		}
		$arr = array();
		while(list(,$obj) = each($arg_arr_objects_array)) {
			if($obj->$arg_str_key_field && $obj->$arg_str_value_field) {
				$arr[$obj->$arg_str_key_field] = $obj->$arg_str_value_field;
			}
		}
		return $arr;
	}
//--------------------------------------------------------------------------------------
	/**
	 Removes private data from properties array.
	 @param arg_arr_all_properties Array of properties if not supplied it reads from $this->__properties.
	 @param arg_arr_private_keys Array of forbidden property names that should be removed from arg_arr_all_properties. If not supplied it is taken from $this->__private_keys.
	 @return Associative Array of clean propertiesto be sent to the view.
	 @access Public.
	 */
	public function secure_output ($arg_arr_all_properties=array(), $arg_arr_private_keys=array()) {
		if(!$arg_arr_all_properties) $arg_arr_all_properties = $this->__properties;
		if(!$arg_arr_all_properties) return $arg_arr_all_properties;
		if (!$arg_arr_private_keys) $arg_arr_private_keys = $this->__private_keys;
		if (!$arg_arr_private_keys) return $arg_arr_all_properties;
		while (list(,$key) = each($arg_arr_private_keys)) {
			unset($arg_arr_all_properties[$key]);
		}
		return $arg_arr_all_properties;
	}	// end function secure_output
//--------------------------------------------------------------------------------------
	/**
	 * Returns all records in the current table
	 * @param String $arg_order_by_field
	 * @param String $arg_ascendingly
	 * @return array
	 */
	public function get_all ($arg_order_by_field='', $arg_ascendingly=true) {
		return $this->get_all_by(array(), $this->__table, $arg_order_by_field, $arg_ascendingly);
	}
//--------------------------------------------------------------------------------------
	/**
	 Checks if the value of a certain field or fields is unique in the same table.
	 It needs the table name and the primary key to make sure the required fields are unique.
	 If it does not find the primary key value then this is a new entry (Like in the add method) so it won\'t check using the primary key.
	 @param arg_arr_fields The associative array of field names and field values to validate.
	 @param arg_str_table_name The table name to be checked. If it is empty, it takes its value from $this->__table.
	 @param arg_str_primary_key_field_name The field name of the primary key of the current table if empty takes its value from $this->__primary_key.
	 @return true if valid and false if not valid.
	 @see db_get_one_value().
	 @access Public.
	 */
	public function is_uniq ($arg_field_name, $arg_value, $arg_str_table_name='', $arg_str_primary_key_field_name='') {
		$arg_str_table_name = ($arg_str_table_name ? $arg_str_table_name : $this->__table);
		if (!$arg_str_table_name) throw new Exception('No table given');
		$arg_str_primary_key_field_name = ($arg_str_primary_key_field_name ? $arg_str_primary_key_field_name : $this->__primary_key);
		if (!$arg_str_primary_key_field_name) throw new Exception('No Primary Key given');
		if (!$arg_field_name) throw new Exception('No Field name to validate');

		$str_sql = 'SELECT `' . $arg_str_primary_key_field_name . '` FROM `' . $arg_str_table_name . '` WHERE `' . $arg_field_name . '`=\'' . $this->__DB->escape($arg_value) . '\'' . ($this->$arg_str_primary_key_field_name ? ' AND `' . $arg_str_primary_key_field_name . '`!=\'' . $this->$arg_str_primary_key_field_name . '\'' : '');
		return ($this->db_get_one_value($str_sql) ? false : true);
	}
//--------------------------------------------------------------------------------------
	/**
	 This method assigns a function to be the validation function of a certain field.
	 This function is created by the developer and set as a public method of the current class.
	 The validation function must have the name of the field to be validated as the first param and the field value to be validated as the second param and it must return true or false.
	 Example:

	 @param $value the value to be validated
	 function is_number_big ($value) {
	 if ($value <= 1000) {
	 return false;
	 }
	 return true;
	 }
	
	 Example calling register_validation_function:
	 $this->register_validation_function('bid', 'is_number_big');
	 */
	function register_validation_function ($arg_field_name, $arg_function_name, $arg_error_msg) {
		$arr = array();
		$arr['type'] = 'function';
		$arr['function'] = $arg_function_name;
		$arr['error_msg'] = $arg_error_msg;
		$this->__validation[$arg_field_name][] = $arr;
	}
//--------------------------------------------------------------------------------------
	public function add_validation ($arg_field_name, $arg_type, $arg_error_msg, $arg_function='', $arg_format='') {
		$arg_type = strtolower($arg_type);
		$arr_types = array('presence', 'int', 'number', 'uniq', 'email', 'username', 'function', 'format');
		if (!in_array($arg_type, $arr_types)) throw new Exception('Invalid type of validation: [' . $arg_type . '] The validation type must be one of these: ' . join(', ', $arr_types));
		if (!$arg_error_msg) throw new Exception('Please write an error messgae for the validation of: [' . $arg_field_name . ']');

		if (!$this->__validation[$arg_field_name]) $this->__validation[$arg_field_name] = array();

		$arr_new_validation = array('type' => $arg_type, 'error_msg' => $arg_error_msg);
		if ($arg_type == 'format') {
			if (!$arg_format) throw new Exception('Please specify the format regular expression param for the validation of: [' . $arg_field_name . ']');
			$arr_new_validation['format'] = $arg_format;
		} else if ($arg_type == 'function') {
			if (!$arg_function) throw new Exception('Please specify the function name param for the validation of: [' . $arg_field_name . ']');
			$arr_new_validation['function'] = $arg_function;
		}
		$this->__validation[$arg_field_name][] = $arr_new_validation;
		return true;
	}
//--------------------------------------------------------------------------------------
	/**
	 Runs the appropriate validation Function for the supplied field name.
	 @param arg_str_field_name The field name of the database field to be validated.
	 @param arg_value The value of the field to be validated.
	 @param arg_arr_validation_settings The validation settings, which is a sub array taken from $this->__validation for this field only.
	 @return true or false according to the return value of the validation function called.
	 @see is_field_valid()
	 */
	public function run_validation ($arg_field_name, $arg_value, $arg_arr_validation_settings) {
		if ($arg_arr_validation_settings['type'] == 'presence') {	// if validation type
			return ($arg_value ? true : false);
		} elseif ($arg_arr_validation_settings['type'] == 'format') {
			return preg_match('/' . $arg_arr_validation_settings['format'] . '/', $arg_value);
		} elseif ($arg_arr_validation_settings['type'] == 'int') {
			return (preg_match('/^[0-9]+$/', $arg_value) == 1);
		} elseif ($arg_arr_validation_settings['type'] == 'number') {
			return is_numeric($arg_value);
		} elseif ($arg_arr_validation_settings['type'] == 'email') {
			return $this->is_email($arg_value);
		} elseif ($arg_arr_validation_settings['type'] == 'username') {
			return $this->is_username($arg_value);
		} elseif ($arg_arr_validation_settings['type'] == 'uniq') {
			if ($this->__skip_unique_validation) {
				return true;
			} else {
				return $this->is_uniq ($arg_field_name, $arg_value);
			}
		} elseif ($arg_arr_validation_settings['type'] == 'function') {
			return call_user_func(array(&$this, $arg_arr_validation_settings['function']), $arg_value);
		}	// end if validation type
	}
//--------------------------------------------------------------------------------------
	/**
	 Loops on the array of fields and calidates them all against the array of $this->__validation
	 Then returns true or false according to the overall output of all validation methods.
	 @param $arg_arr_fields associative array of field names and values to be validated.
	 @return true or false indicating whether all fields are valid or not.
	 */
	public function are_fields_valid ($arg_arr_fields=array()) {
		if (!$arg_arr_fields || !is_array($arg_arr_fields)) return false;
		$return = true;
		while (list($field_name, $value) = each($arg_arr_fields)) {
			if (!$this->is_field_valid($field_name, $value)) $return = false;
		}
		return $return;
	}	// end function
//-------------------------------------------------------------------------------------
	/**
	 Converts an array to its string representation so that it could be saved in a php file and included later.
	 @param arg_arr_input the input array.
	 @param arg_output_array_name the name of the output array.
	 @return returns a string representation for the input array.
	 */
	public function array_to_string($arg_arr_input, $arg_output_array_name='$arr') {
		if(!is_array($arg_arr_input)) return false;
		$str_var = '';
		if($arg_output_array_name) $str_var .= $arg_output_array_name . " = array();		// Start Array\n";
		$j=0;
		while ( list($key, $value) = each($arg_arr_input) ) {
			if(is_array($value)) {
				if (is_numeric($key)) {
					$str_var .= $this->array_to_string($value, $arg_output_array_name . '[' . $key . ']');
				} else {
					$str_var .= $this->array_to_string($value, $arg_output_array_name . '[\'' . $key . '\']');
				}
			}else {
				if (is_numeric($value)) {
					$str_var .= $arg_output_array_name . '[\'' . $key . '\'] = ' . $value . ";\n";
				} else {
					$value = str_replace('\'', '\\\'', $value);
					$str_var .= $arg_output_array_name . '[\'' . $key . '\'] = \'' . $value . "';\n";
				}
			}
		}
		return $str_var;
	}
//-------------------------------------------------------------------------------------
	/**
	 Saves a string to a file
	 @param arg_string the string to be saved to the file.
	 @param arg_filename the file name of the string to be saved.
	 @param arg_bool_backup if true and there is a file with the same name it is renamed to be saved as a backup.
	 @return true if saved and false if not saved.
	 */
	public function string_to_file($arg_string, $arg_filename, $arg_bool_backup=false) {
		if(!$arg_filename) return false;
		if ($arg_bool_backup) {
			if(file_exists($arg_filename)) {	// if the file exists then take a backup of it
				rename($arg_filename, $arg_filename . uniqid('.'));
			}	// end if file exists
		}
		$fp = fopen($arg_filename, 'w');
		if (flock($fp, LOCK_EX)) { // do an exclusive lock
			fwrite($fp, $arg_string);
			flock($fp, LOCK_UN); // release the lock
		}
		fclose($fp);
		return true;
	}
//--------------------------------------------------------------------------------------
	/**
	 Adds a search critoerion to the current model class
	 @param $arg_field_name
	 */
	public function __add_search ($arg_field_name, $arg_operator='like') {
		$arr_search_operators = array('like', 'eq', 'gt', 'lt', 'ge', 'le');
		$arg_operator = strtolower(trim($arg_operator));
		if (!in_array($arg_operator, $arr_search_operators)) throw new Exception('Invalid Search Operator: [' . $arg_operator . "]. \n" . 'You can use [' . join(', ', $arr_search_operators) . '] only.');
		$this->__search_array[$arg_field_name . '_' . $arg_operator] = array($arg_field_name, $arg_operator);
		return true;
	}
//--------------------------------------------------------------------------------------
	public function __search ($arg_arr_params, $arg_order_by_field='', $arg_ascendingly=true, $arg_table="") {
		if (!$arg_table) $arg_table = $this->__table;
		if (!$arg_table) throw new Exception('No table given');
		$query = 'SELECT * FROM `' . $arg_table . '`';
		$where = ' WHERE ';
		$arr_db_params = array();
		if (is_array($arg_arr_params)) {
			while (list($k, $v) = each($arg_arr_params)) {
				if ($this->__search_array[$k] && $v != '') {	// if the field is in the search criteria and the field is not empty
					$field_name = $this->__search_array[$k][0];
					$op = $this->__search_array[$k][1];

					if ($arr_db_params) $where .= ' AND ';
					$arr_db_params[] = $v;
					if ($op == 'like') {
						$where .= '`' . $field_name . '` LIKE \'%%%s%%\'';
					} else if ($op == 'eq') {
						$where .= '`' . $field_name . '`=\'%s\'';
					} else if ($op == 'gt') {
						$where .= '`' . $field_name . '`>\'%s\'';
					} else if ($op == 'lt') {
						$where .= '`' . $field_name . '`<\'%s\'';
					} else if ($op == 'ge') {
						$where .= '`' . $field_name . '`>=\'%s\'';
					} else if ($op == 'le') {
						$where .= '`' . $field_name . '`<=\'%s\'';
					}
				}
			}
			reset($arg_arr_params);
		}
		if ($arr_db_params) $query .= $where;

		// sorting order by field
		$query .= $this->prepare_order_by($arg_order_by_field, $arg_ascendingly);
		return $this->db_select($query, $arr_db_params);
	}
//--------------------------------------------------------------------------------------
	/**
	 Adds a rollback method to be executed if is_error()
	 @param method string method name
	 @param arr_params array of method paramters
	 @return true
	 */
	public function __add_rollback_method ($arg_method_name, $arg_arr_params) {
		if (!is_array($this->__rollback_functions)) $this->__rollback_functions = array();
		array_push($this->__rollback_functions, array($arg_method_name, $arg_arr_params));
		return true;
	}
//--------------------------------------------------------------------------------------
	/**
	 Deletes a file or more
	 @param $arg_arr_files if String then it deletes one file if it is an array then all files are deleted
	 @param $arg_folder string [optional] default '' the folder in which the file(s) reside example 'uploads/page/'
	 @return true
	 */
	public function delete_files ($arg_arr_files, $arg_folder='') {
		if (is_array($arg_arr_files)) {
			while (list(, $file) = each($arg_arr_files)) {
				if (file_exists(PHP_ROOT . $arg_folder . $file)) @unlink(PHP_ROOT . $arg_folder . $file);
			}
			reset($arg_arr_files);
		} else {
			if (file_exists(PHP_ROOT . $arg_folder . (string)$arg_arr_files)) @unlink(PHP_ROOT . $arg_folder . (string)$arg_arr_files);
		}
		return true;
	}
//--------------------------------------------------------------------------------------
	/**
	 Executes the rollback methods in $this->__rollback_functions
	 Called when is_error() = true
	 */
	public function __rollback () {
		if (is_array($this->__rollback_functions) && $this->__rollback_functions) {
			while ($arr_rollback = array_shift($this->__rollback_functions)) {
				call_user_func_array(array(&$this, $arr_rollback[0]), $arr_rollback[1]);
			}
		}
	}
//--------------------------------------------------------------------------------------
	/**
	
	 */
	public function __is_file_valid ($arg_arr_file_info) {
		/*
		replace  = 0x1
		image	 = 0x2
		optional = 0x4
		multi 	 = 0x8
		uniq 	 = 0x10
		*/
		$bool_replace = (($arg_arr_file_info['flags'] & 0x1) > 0);
		$bool_image = (($arg_arr_file_info['flags'] & 0x2) > 0);
		$bool_optional = (($arg_arr_file_info['flags'] & 0x4) > 0);
		//$bool_multi = (($arg_arr_file_info['flags'] & 0x8) > 0);
		$bool_uniq = (($arg_arr_file_info['flags'] & 0x10) > 0);
		// check if flag optional=false and there is no file or old file
		if (!$bool_optional) {
			if (!$arg_arr_file_info['name'] && !$arg_arr_file_info['old_file_name']) {
				$this->add_validation_error('please_upload_a_file', array(), $arg_arr_file_info['property_name']);
				return false;
			}
		}

		// check if file is not uploaded because it is big
		if ($arg_arr_file_info['name'] && !$arg_arr_file_info['size']) {
			$this->add_validation_error('could_not_upload_your_file_may_be_it_is_too_big', array($arg_arr_file_info['name']), $arg_arr_file_info['property_name']);
			return false;
		}

		// check if file already exists and flag replace=false
		if (!$bool_uniq && !$bool_replace && $arg_arr_file_info['name'] && $arg_arr_file_info['name'] != $arg_arr_file_info['old_file_name']) {
			if (file_exists(PHP_ROOT . $this->__upload_folder . $arg_arr_file_info['name'])) {
				$this->add_validation_error('file_already_exists', array($arg_arr_file_info['name']), $arg_arr_file_info['property_name']);
				return false;
			}
		}

		// check if flag image=true and the file is not an image
		if ($arg_arr_file_info['name'] && $bool_image && strpos($arg_arr_file_info['type'], 'image/') === false) {
			$this->add_validation_error('please_upload_an_image', array(), $arg_arr_file_info['property_name']);
			return false;
		}
		return true;
	}
//--------------------------------------------------------------------------------------
	/*private function garbage_collect_temp_files_folders () {
		$arr_folders = array();
		if(!$this->__upload_folder){return false;}	// if the folder name is not valid then return false
		if (!file_exists(PHP_ROOT . $this->__upload_folder)){return false;}		// if the folder does not exists then return false
		if (!is_dir(PHP_ROOT . $this->__upload_folder)){return false;}		// if it is not a folder then return false
		$dir_folder = opendir(PHP_ROOT . $this->__upload_folder);		// open the folder 
		while ($folder = readdir($dir_folder)){		// while folder
			if (is_dir(PHP_ROOT . $this->__upload_folder . $folder)) {
				if(($folder == '.') || ($folder == '..')) continue;
				if (strpos($folder, "temp_") !== false) {
					$arr_split = split("_", $folder);
					// if temp folder is older than 1 day then delete it
					if ((time() - $arr_split[1]) > 86400) {
						$dir = opendir(PHP_ROOT . $this->__upload_folder . $folder);
						while ($file = readdir($dir)) {
							if(($file == '.') || ($file == '..')) continue;
							@unlink(PHP_ROOT . $this->__upload_folder . $folder . '/' . $file);
						}
						closedir($dir);
						@rmdir(PHP_ROOT . $this->__upload_folder . $folder);
					}
				}
			}	// end if dir
		}	// end while
		closedir($dir_folder);							// close the folder
	}*/
//--------------------------------------------------------------------------------------
	/**
	 Adds a relation to the current model
	 e.g. groups & permissions $this->__add_relation($arg_field_name="group_permissions", $arg_table="groups_permissions", $arg_remote_field"permission_id", $arg_remote_id="group_id");
	 @param $arg_field_name the hypothetical field name to be used as the field name in the model only.
	 @param String $arg_get_function_name the function that is called when we want to get the values of the relation field
	 @param String $arg_set_function_name the function that is called when we want to set the values of the relation field
	 */
	public function __add_relation ($arg_field_name, $arg_get_function_name, $arg_set_function_name="") {
		$this->__relations[$arg_field_name] = array();
		$this->__relations[$arg_field_name]['get_function'] = $arg_get_function_name;
		$this->__relations[$arg_field_name]['set_function'] = $arg_set_function_name;
	}
//--------------------------------------------------------------------------------------
	/**
	 Runs a raw query on the Database connection and returns the resultset as it is without any conversion to PHP arrays
	 @param $arg_query the query to be run
	 @return resultset resource
	 */
	public function raw_query($arg_query) {
		return $this->__DB->raw_query($arg_query);
	}
//--------------------------------------------------------------------------------------
	/**
	 Deletes a directory recursively
	 @param $path the directory path to be created
	 @return true on success and false on failure
	 */
	public function rmdir_r ($path) {
		if (!$path || !is_string($path)) return false;
		if (!is_dir($path)) return false;
		if (!file_exists($path)) return false;		// if the folder does not exists then return false
		if ($path{strlen($path)-1} != '/') $path .= '/';
		$dir_folder = opendir($path);			// open the folder
		while ($file_entry = readdir($dir_folder)) {		// while folder
			if(is_dir($path . $file_entry)) {
				if(($file_entry == '.') || ($file_entry == '..')) continue;
				$this->rmdir_r($path . ($path{strlen($path)-1} == '/' ? '' : '/') . $file_entry);
			} else {	// file
				unlink($path . ($path{strlen($path)-1} == '/' ? '' : '/') . $file_entry);
			}	// end if dir
		}	// end while
		closedir($dir_folder);
		rmdir($path);
		return true;
	}
//--------------------------------------------------------------------------------------
	/**
	 creates a directory recursively (creates its parent if it does not exist)
	 @param $path the directory path to be created
	 @return true on success and false on failure
	 */
	public function mkdir_r ($path) {
		if (!is_dir($path)) return false;
		$parent = dirname($path);
		if (!file_exists($parent)) {
			if (!$this->mkdir_r($parent)) return false;
		}
		if (!@mkdir($path)) {
			return false;
		}
		return true;
	}
//--------------------------------------------------------------------------------------
	/**
	 * Returns an array containing the database record that has this id
	 * @param long $arg_id the id of the record to get
	 * @return array the record of database table
	 * @throws Exception
	 * @throws ValidationException
	 */
	public function get_one_by_id ($arg_id) {
		return $this->get_one_row_by(array('id' => $arg_id));
	}
//--------------------------------------------------------------------------------------
	/**
	 Returns one record selected according to a field value
	 @param String $arg_field_name the field name to search with
	 @param String $arg_value the field value to search with
	 @param array $arg_arr_where [optional] default=array()
	 @return array one record from the database
	 */
	public function get_one_row_by(array $arg_arr_where=array(), $arg_table_name='') {
		if (!$arg_table_name) $arg_table_name = $this->__table;
		if (!$arg_table_name) throw new Exception("Model table name is not specified");
		return $this->db_select_one_row('SELECT * FROM ' . $arg_table_name . ($arg_arr_where ? ' WHERE ' . $this->prepare_where_statement($arg_arr_where) : ''));
	}
//--------------------------------------------------------------------------------------
	/**
	 * Gets all records from the current table that has the specified field with the specified value
	 * @param String $arg_field_name the field name to search with
	 * @param String $arg_value the field value to search with
	 * @param array $arg_arr_where [optiona] default array() any extra where conditions for the search
	 * @return array result array containing all records with the defined criteria
	 * @throws Exception
	 */
	public function get_all_by(array $arg_arr_where=array(), $arg_table_name='', $arg_order_by_field='', $arg_ascendingly=true) {
		if (!$arg_table_name) $arg_table_name = $this->__table;
		if (!$arg_table_name) throw new Exception("Model table name is not specified");
		$query = 'SELECT * FROM ' . $arg_table_name . ($arg_arr_where ? ' WHERE ' . $this->prepare_where_statement($arg_arr_where) : '') . $this->prepare_order_by($arg_order_by_field, $arg_ascendingly);
		return $this->db_select($query);
	}
//--------------------------------------------------------------------------------------
	/**
	 * Gets a certain field value by searching using a field name and value
	 * @param String $arg_selected_field_name the selected field name
	 * @param String $arg_search_field_name the search field name
	 * @param String $arg_search_value the search value
	 * @param array $arg_arr_where [optional] default array() extra where parameters
	 * @return String
	 * @throws Exception
	 */
	public function get_field_by($arg_selected_field_name, array $arg_arr_where, $arg_table_name='') {
		if (!$arg_table_name) $arg_table_name = $this->__table;
		if (!$arg_table_name) throw new Exception("Model table name is not specified");
		return $this->db_get_one_value('SELECT ' . $arg_selected_field_name . ' FROM ' . $arg_table_name . ($arg_arr_where ? ' WHERE ' . $this->prepare_where_statement($arg_arr_where) : ''));
	}
//--------------------------------------------------------------------------------------
	/**
		
	 */
	public function filter_result_array_by_field(array $arg_arr_result_array, $arg_search_field_name, $arg_search_value, $arg_operator="eq") {
		$op = strtolower($arg_operator);
		if (!$op) $op = 'eq';
		$arr_filtered_result = array();

		while (list($k, $v) = each($arg_arr_result_array)) {
			if (
			($op == 'eq' 	&& $v[$arg_search_field_name] == 	$arg_search_value) ||
				($op == 'ne' 	&& $v[$arg_search_field_name] != 	$arg_search_value) ||
				($op == 'gt' 	&& $v[$arg_search_field_name] > 	$arg_search_value) ||
				($op == 'ge' 	&& $v[$arg_search_field_name] >= 	$arg_search_value) ||
				($op == 'lt' 	&& $v[$arg_search_field_name] < 	$arg_search_value) ||
				($op == 'le' 	&& $v[$arg_search_field_name] <= 	$arg_search_value) ||
				($op == 'like' 	&& preg_match('/^' . str_replace('%', '.*', $arg_search_value) . '$/i', $v[$arg_search_field_name]))
			) {
				$arr_filtered_result[$k] = $v;
			}
		}
		reset($arg_arr_result_array);
		return $arr_filtered_result;
	}
//--------------------------------------------------------------------------------------
	/**
	 * Updates a group of fields according to a certain condition
	 * @param String $arg_field_name
	 * @param String $arg_value
	 * @param array $arg_arr_where
	 * @return boolean
	 */
	public function update_field($arg_field_name, $arg_value, array $arg_arr_where=array()) {
		return $this->auto_update(array($arg_field_name => $arg_value), $arg_arr_where);
	}
//--------------------------------------------------------------------------------------
	/**
	 * Returns an associative array from the result array
	 * @param String $arg_key_field_name the field name used to fill the keys of the array
	 * @param String $arg_value_field_name the field name used to fill the values of the array
	 * @param array $arg_arr_where extra where statements
	 * @return array
	 */
	public function get_all_assoc($arg_key_field_name, $arg_value_field_name, array $arg_arr_where=array()) {
		if (!$this->__table) throw new Exception("Model table name is not specified");
		return $this->db_get_assoc_array('SELECT * FROM ' . $this->__table . ($arg_arr_where ? ' WHERE ' . $this->prepare_where_statement($arg_arr_where) : ''), array(), $arg_key_field_name, $arg_value_field_name);
	}
//--------------------------------------------------------------------------------------
}	// end class parent_model


/*
/////////////TODO
public function __add_relation_has_many ($arg_field_name, $arg_local_relation_field, $arg_remote_table) {}
public function __add_relation_has_one ($arg_field_name, $arg_local_relation_field, $arg_remote_table) {}
public function __add_relation_belongs_to ($arg_field_name, $arg_local_relation_field, $arg_remote_table, $arg_remote_primary_key="id") {}
public function __add_relation_belongs_to_and_has_many ($arg_field_name, $arg_relation_table, $arg_local_relation_field, $arg_remote_relation_field, $arg_remote_table, $arg_remote_primary_key="id") {}
//////////TODO
*/