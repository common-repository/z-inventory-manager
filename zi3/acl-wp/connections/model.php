<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Acl0Wp_Connections_Model
{
	public $id;

	public $wp_user_id;
	public $wp_role_id;
	public $role_id;

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

class PW1_ZI3_Acl0Wp_Connections_Model extends _PW1
{
	public function construct()
	{
		$className = '_' . __CLASS__;
		$ret = new $className;
		return $ret;
	}

	public function fromArray( array $array, _PW1_ZI3_Acl0Wp_Connections_Model $ret )
	{
		if( isset($array['id']) ){
			$ret->id = (int) $array['id'];
		}

		$ret->wp_user_id = $array['wp_user_id'];
		$ret->wp_role_id = $array['wp_role_id'];
		$ret->role_id = $array['role_id'];

		return $ret;
	}

	public function toArray( _PW1_ZI3_Acl0Wp_Connections_Model $model )
	{
		$ret = array();

		if( $model->id ){
			$ret['id'] = $model->id;
		}

		$ret['wp_user_id'] = $model->wp_user_id;
		$ret['wp_role_id'] = $model->wp_role_id;
		$ret['role_id'] = $model->role_id;

		return $ret;
	}
}