<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Acl_Roles_Model
{
	public $id;

	public $title;
	public $permissions = array();

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

class PW1_ZI3_Acl_Roles_Model extends _PW1
{
	public function construct()
	{
		$className = '_' . __CLASS__;
		$ret = new $className;
		return $ret;
	}

	public function fromArray( array $array, _PW1_ZI3_Acl_Roles_Model $ret )
	{
		if( isset($array['id']) ){
			$ret->id = (int) $array['id'];
		}

		$ret->title = $array['title'];
		$ret->permissions = $array['permissions'];

		return $ret;
	}

	public function toArray( _PW1_ZI3_Acl_Roles_Model $model )
	{
		$ret = array();

		if( $model->id ){
			$ret['id'] = $model->id;
		}
		$ret['title'] = $model->title;
		$ret['permissions'] = $model->permissions;

		return $ret;
	}
}