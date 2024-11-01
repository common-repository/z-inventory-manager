<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Transactions_Lines_Model
{
	public $id;
	public $transaction_id;
	public $item_id;
	public $qty;
	public $price;

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

class PW1_ZI3_Transactions_Lines_Model extends _PW1
{
	public function construct()
	{
		$ret = new _PW1_ZI3_Transactions_Lines_Model;
		return $ret;
	}

	public function toArray( _PW1_ZI3_Transactions_Lines_Model $model )
	{
		$ret = array();

		if( $model->id ){
			$ret['id'] = $model->id;
		}

		$ret['transaction_id'] = $model->transaction_id;
		$ret['item_id'] = $model->item_id;
		$ret['qty'] = $model->qty;
		$ret['price'] = $model->price;

		return $ret;
	}

	public function fromArray( array $array, _PW1_ZI3_Transactions_Lines_Model $ret = NULL )
	{
		if( NULL === $ret ){
			$ret = $this->self->construct();
		}

		$ret->id = (int) $array['id'];
		$ret->transaction_id = (int) $array['transaction_id'];
		$ret->item_id = (int) $array['item_id'];
		$ret->qty = $array['qty'];
		$ret->price = $array['price'];

		return $ret;
	}
}