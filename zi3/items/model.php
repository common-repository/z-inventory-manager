<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Items_Model
{
	const _CLASS = 'item';

	public $id;
	public $title;
	public $sku;
	public $description;
	public $default_cost;
	public $default_price;
	public $state;

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

class PW1_ZI3_Items_Model extends _PW1
{
	const STATE_ACTIVE = 'active';
	const STATE_ARCHIVE = 'archive';

	public function construct()
	{
		$ret = new _PW1_ZI3_Items_Model;
		$ret->state = self::STATE_ACTIVE;
		return $ret;
	}

	public function fromArray( array $array, _PW1_ZI3_Items_Model $ret )
	{
		if( isset($array['id']) ){
			$ret->id = (int) $array['id'];
		}

		$ret->title = $array['title'];

		if( isset($array['description']) ){
			$ret->description = $array['description'];
		}

		$ret->sku = $array['sku'];

		if( isset($array['state']) && strlen($array['state']) ){
			$ret->state = $array['state'];
		}

		$ret->default_cost = $array['default_cost'];
		$ret->default_price = $array['default_price'];

		return $ret;
	}

	public function toArray( _PW1_ZI3_Items_Model $model )
	{
		$ret = array();

		if( $model->id ){
			$ret['id'] = $model->id;
		}
		$ret['title'] = $model->title;
		$ret['sku'] = $model->sku;
		$ret['description'] = $model->description;
		$ret['state'] = $model->state;
		$ret['default_cost'] = $model->default_cost;
		$ret['default_price'] = $model->default_price;

		return $ret;
	}

	public function getStates( _PW1_ZI3_Items_Model $model = NULL )
	{
		$ret = array();

		$ret[ static::STATE_ACTIVE ] = static::STATE_ACTIVE;
		$ret[ static::STATE_ARCHIVE ] = static::STATE_ARCHIVE;

		if( $model ){
			unset( $ret[$model->state] );
		}

		return $ret;
	}
}