<?
/**
@file xml.php
@class xml.
XML Parser class.
This class changes php arrays into xml text and vice versa.
*/
require_once(dirname(__FILE__) . "/object.php");
class xml extends object
{
	function xml () {
		$this->object();
	}
//-------------------------------------------------------------------
	/**
	XML to Array.
	Converts the XML text that was generated by this class to an array.
	It can work with unidimensional and multidimensional associative arrays.
	@param arg_str_xml The xml text to be changed into an array.
	@return Array.
	@see prv_xml_to_array().
	@access Public.
	*/
	function xml_to_array($arg_str_xml)
	{	$arr_raw_xml = array();
        $parser = xml_parser_create();
		xml_parse_into_struct($parser, $arg_str_xml, $arr_raw_xml);
		$arr_out = array();
		$this->prv_xml_to_array($arr_raw_xml, $arr_out);
		return $arr_out;
	}	// end function xml_to_array
//-----------------------------------------------------------------------------	
	/**
	Private XML to Array.
	Converts xml to array recursively.
	@param arg_tags the raw array of tags got from xml_parse_into_struct is passed by reference to keep the position of the pointer in the array through function calls.
	@param arg_current_tag the current array to be filled is passed by reference because it is changed within the function.
	@return Array.
	@access Private.
	*/
	function prv_xml_to_array(&$arg_tags, &$arg_current_tag)
	{
		while(list(, $arr_tag) = each($arg_tags))
		{
			if($arr_tag['level'] > 1)
			{
				if ($arr_tag['type']=="complete") 		// if type = complete
				{		
					$arg_current_tag[$arr_tag['attributes']['KEY']] = $arr_tag['value'];
				} 
				elseif ($arr_tag['type']=="open")		// if type = open
				{
					$this->prv_xml_to_array($arg_tags, $arg_current_tag[$arr_tag['attributes']['KEY']]);
				} 
				elseif ($arr_tag['type']=="close")		// if type = close
				{
					return;
				}	// end if type
			}	// end if level > 1
		}	// end while arg_tags		
	}	// end function prv_xml_to_array
//-------------------------------------------------------------------
	/**
	Sets padding in xml text.
	Helps to make the xmlcode readable but can be disabled by emptying arg_str_pad when we do not need to read the xml code.
	@param arg_int_pad_number the number of indentation pads in this tag.
	@param arg_str_pad the single pad size.
	@return String pad.
	@access Private.
	*/
	function pad($arg_int_pad_number=0, $arg_str_pad="")
	{
		if(($arg_int_pad_number===0) || ($arg_str_pad===""))return "";
		$i = 0;
		$str_pad = "";
		while($i < $arg_int_pad_number){
			$str_pad .= $arg_str_pad;
			$i++;
		}
		return ($str_pad ? "\n" . $str_pad : "");
	}	// end function pad
//-------------------------------------------------------------------
	/**
	Private Array to XML.
	changes php arrays into xml text recursively.
	@param arg_arr_array the array to be changed into XML.
	@param arg_int_pad_number the number of pads of the current tag.
	@param arg_str_pad the indentation pad text.
	@return String xml text.
	@see array_to_xml().
	@access Private.
	*/
	function prv_array_to_xml($arg_arr_array, $arg_int_pad_number=0, $arg_str_pad="")
	{
		$str_xml = "";
		while(list($k, $v) = each($arg_arr_array)){
			$str_xml .= $this->pad($arg_int_pad_number, $arg_str_pad) . "<a key=\"" . htmlspecialchars($k) . "\">";
			if(is_array($v)){
				$str_xml .= $this->prv_array_to_xml($v, $arg_int_pad_number+1, $arg_str_pad);
			}else{
				$str_xml .= $this->pad($arg_int_pad_number+1, $arg_str_pad) . htmlspecialchars($v);
			}
			$str_xml .= $this->pad($arg_int_pad_number, $arg_str_pad) . "</a>";
		}
		return $str_xml;
	}	// end function prv_array_to_xml
//-------------------------------------------------------------------
	/** 
	Array to XML.
	changes php arrays into xml text recursively.
	@param arg_arr_array the array to be changed into XML.
	@param arg_str_operation_name the name of the main xml tag.
	@param arg_str_pad the indentation pad text.
	@return String xml text.
	@see prv_array_to_xml().
	@access Public.
	*/
	function array_to_xml($arg_arr_array, $arg_str_operation_name="response", $arg_str_pad="")
	{
		if(!is_array($arg_arr_array))return false;
		$str_xml = "<$arg_str_operation_name>";
		$str_xml .= $this->prv_array_to_xml($arg_arr_array, 1, $arg_str_pad);
		$str_xml .= ($arg_str_pad==="" ? "" : "\n") . "</$arg_str_operation_name>";
		return $str_xml;
	}	// end function array_to_xml
//-------------------------------------------------------------------
} // end of class xml

