<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Transactions_States_Model
{
	public $id;
	public $title;
	public $color;
	public $countFrom;
	public $countTo;

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

class PW1_ZI3_Transactions_States_Model extends _PW1
{
	public function construct()
	{
		$ret = new _PW1_ZI3_Transactions_States_Model;
		return $ret;
	}

	public function toArray( _PW1_ZI3_Transactions_States_Model $model )
	{
		$ret = array();

		if( $model->id ){
			$ret['id'] = $model->id;
		}

		$ret['title'] = $model->title;
		$ret['color'] = $model->color;
		$ret['count_from'] = $model->countFrom ? 1 : 0;
		$ret['count_to'] = $model->countTo ? 1 : 0;

		return $ret;
	}

	public function fromArray( array $array, _PW1_ZI3_Transactions_States_Model $ret = NULL )
	{
		if( NULL === $ret ){
			$ret = $this->self->construct();
		}

		$ret->id = (int) $array['id'];
		$ret->color = $array['color'];
		$ret->title = $array['title'];
		$ret->countFrom = $array['count_from'] ? TRUE : FALSE;
		$ret->countTo = $array['count_to'] ? TRUE : FALSE;

		return $ret;
	}
}