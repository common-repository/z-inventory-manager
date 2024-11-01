<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Stocks_Model extends _PW1
{
	public $item_id;
	public $qty;

	public function __set( $name, $value )
	{
		$msg = 'Invalid property for setting: ' . get_class($this) . ': ' . $name . '<br>';
		echo $msg;
	}

	public function __get( $name )
	{
		$msg = 'Invalid property for getting: ' . get_class($this) . ': ' . $name . '<br>';
		echo $msg;
	}
}

class PW1_ZI3_Stocks_Model extends _PW1
{
	public function construct()
	{
		$ret = new _PW1_ZI3_Stocks_Model;
		return $ret;
	}
}