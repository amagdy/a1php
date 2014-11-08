<?
function is_valid_id ($arg_id)
{
	if (!is_numeric($arg_id)) return false;
	if (!is_int($arg_id)) return false;
	if ($arg_id <= 0) return false;
	return true;
}