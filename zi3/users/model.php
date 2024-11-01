<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Users_Model
{
	const _CLASS = 'user';

	public $id;

	public $title;
	public $username;
	public $email;

	public function __set( $name, $value )
	{
		$msg = 'Invalid property for setting: ' . get_class($this) . ': ' . $name . '<br>';
		// echo $msg;
	}

	public function __get( $name )
	{
		$msg = 'Invalid property for getting: ' . get_class($this) . ': ' . $name . '<br>';
		// echo $msg;
	}
}

class PW1_ZI3_Users_Model extends _PW1
{
	public function construct()
	{
		$ret = new _PW1_ZI3_Users_Model;
		return $ret;
	}

	public function fromArray( array $array, _PW1_ZI3_Users_Model $ret )
	{
		if( isset($array['id']) ){
			$ret->id = (int) $array['id'];
		}

		$ret->title = $array['title'];
		$ret->email = $array['email'];
		$ret->username = $array['username'];

		return $ret;
	}

	public function toArray( _PW1_ZI3_Users_Model $model )
	{
		$ret = array();

		if( $model->id ){
			$ret['id'] = $model->id;
		}
		$ret['title'] = $model->title;
		$ret['email'] = $model->email;
		$ret['username'] = $model->username;

		return $ret;
	}
}