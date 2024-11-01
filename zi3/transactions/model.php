<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Transactions_Model
{
	const _CLASS = 'transaction';

	public $id;
	public $refno = '';
	public $created_date;
	public $description;

	public $contact_id;

	public $type;
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

class _PW1_ZI3_Transactions_ModelLine
{
	public $id;
	public $transactionId;
	public $itemId;
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

class PW1_ZI3_Transactions_Model extends _PW1
{
	const TYPE_PURCHASE = 'purchase';
	const TYPE_SALE = 'sale';

	const STATE_DRAFT = 'draft';
	const STATE_ISSUED = 'issued';

	public $settings;

	public function __construct(
		PW1_ZI3_Settings_	$settings
	)
	{}

	public function construct()
	{
		$ret = new _PW1_ZI3_Transactions_Model;
		return $ret;
	}
 
	public function toArray( _PW1_ZI3_Transactions_Model $model )
	{
		$ret = array();

		if( $model->id ){
			$ret['id'] = $model->id;
		}

		$ret['refno'] = $model->refno;
		$ret['description'] = $model->description;
		$ret['type'] = $model->type;
		$ret['state'] = $model->state;
		$ret['created_date'] = $model->created_date;
		$ret['contact_id'] = $model->contact_id;

		return $ret;
	}

	public function fromArray( array $array, _PW1_ZI3_Transactions_Model $ret = NULL )
	{
		if( NULL === $ret ){
			$ret = $this->self->construct();
		}

		$ret->id = (int) $array['id'];
		$ret->refno = $array['refno'];
		$ret->description = $array['description'];
		$ret->type = $array['type'];
		$ret->state = $array['state'];
		$ret->created_date = $array['created_date'];
		$ret->contact_id = $array['contact_id'];

		return $ret;
	}

	public function getStates( _PW1_ZI3_Transactions_Model $model = NULL )
	{
		$ret = array();

		$ret[ static::STATE_DRAFT ] = static::STATE_DRAFT;
		$ret[ static::STATE_ISSUED ] = static::STATE_ISSUED;

		if( $model ){
			unset( $ret[$model->state] );
		}

		return $ret;
	}

	public function getTypes()
	{
		$ret = array();

		$ret[ static::TYPE_PURCHASE ] = static::TYPE_PURCHASE;
		$ret[ static::TYPE_SALE ] = static::TYPE_SALE;

		return $ret;
	}

	protected function _generateSeqNo()
	{
		$methodOfGeneration = $this->settings->get( 'transactions_purchase_ref_auto_method' );
		$seq = ( 'seq' == $methodOfGeneration ) ? TRUE : FALSE; 

		if( $seq ){
			static $biggestId = NULL;

			if( NULL === $biggestId ){
				$biggestId = 0;

				$q = $this->q->orderBy( 'id' )->limit( 1 );
				$exists = $this->query->find( $q );
				if( $exists ){
					$latest = array_shift( $exists );
					$biggestId = $latest->id;
				}
			}
			$biggestId++;
			$ret = $biggestId;
		}
		else {
			$ret = rand( 100000, 999999 );
		}

		$length = 6;
		$ret = (string) $ret;
		$ret = str_pad( $ret, $length, '0', STR_PAD_LEFT);

		return $ret;
	}
}