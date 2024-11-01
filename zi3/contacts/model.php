<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Contacts_Model
{
	const _CLASS = 'contact';

	public $id;
	public $state;

	public $title;
	public $email;
	public $phone;
	public $description;

	public $is_customer;
	public $is_supplier;

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

class PW1_ZI3_Contacts_Model extends _PW1
{
	const STATE_ACTIVE = 'active';
	const STATE_ARCHIVE = 'archive';

	const TYPE_CUSTOMER = 'customer';
	const TYPE_SUPPLIER = 'supplier';

	public function construct()
	{
		$ret = new _PW1_ZI3_Contacts_Model;

		$states = $this->self->getStates();
		$ret->state = current( $states );

		return $ret;
	}

	public function fromArray( array $array, _PW1_ZI3_Contacts_Model $ret = NULL )
	{
		if( NULL === $ret ){
			$ret = $this->self->construct();
		}

		if( isset($array['id']) ){
			$ret->id = (int) $array['id'];
		}

		$ret->title = $array['title'];
		$ret->email = $array['email'];
		$ret->phone = $array['phone'];
		$ret->description = $array['description'];

		if( isset($array['state']) && strlen($array['state']) ){
			$ret->state = $array['state'];
		}

		$ret->is_customer = isset( $array['is_customer'] ) && $array['is_customer'] ? TRUE : FALSE;
		$ret->is_supplier = isset( $array['is_supplier'] ) && $array['is_supplier'] ? TRUE : FALSE;

		return $ret;
	}

	public function toArray( _PW1_ZI3_Contacts_Model $model )
	{
		$ret = array();

		if( $model->id ){
			$ret['id'] = $model->id;
		}
		$ret['title'] = $model->title;
		$ret['email'] = $model->email;
		$ret['phone'] = $model->phone;
		$ret['state'] = $model->state;
		$ret['description'] = $model->description;

		$ret['is_customer'] = $model->is_customer ? 1 : 0;
		$ret['is_supplier'] = $model->is_supplier ? 1 : 0;

		return $ret;
	}

	public function getStates( _PW1_ZI3_Contacts_Model $model = NULL )
	{
		$ret = array();

		$ret[ static::STATE_ACTIVE ] = static::STATE_ACTIVE;
		$ret[ static::STATE_ARCHIVE ] = static::STATE_ARCHIVE;

		if( $model ){
			unset( $ret[$model->state] );
		}

		return $ret;
	}

	public function getTypes()
	{
		$ret = array();

		$ret[ static::TYPE_CUSTOMER ] = static::TYPE_CUSTOMER;
		$ret[ static::TYPE_SUPPLIER ] = static::TYPE_SUPPLIER;

		return $ret;
	}
}