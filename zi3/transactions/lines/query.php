<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions_Lines_Query extends _PW1
{
	private $_cache = array();
	public $q;
	public $itemsQuery;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Items_Query $itemsQuery,
		PW1_ZI3_Transactions_Lines_Model $model,
		PW1_ZI3_Transactions_Lines_Crud $crud
	)
	{}

	public function find( _PW1_Q $q )
	{
		$ret = array();

		$select = $q->getSelect();

		$res = $this->crud->read( $q );
		if( $res instanceof PW1_Error ) return $res;

		foreach( $res as $e ){
			if( $select ){
				if( count($select) > 1 ){
					$thisRet = array();
					foreach( $select as $k ) $thisRet[$k] = $e[$k];
				}
				else {
					foreach( $select as $k ){
						$thisRet = $e[$k];
					}
				}
				$ret[] = $thisRet;
			}
			else {
				$model = $this->model->fromArray( $e );
				if( ! $model ) continue;
				$ret[ $model->id ] = $model;

				// $this->_cache[ $model->id ] = $model;
			}
		}

		if( ! $ret ) return $ret;

	// preload items
		if( ! $select ){
			$itemsIds = array();
			foreach( $ret as $e ) $itemsIds[ $e->item_id ] = $e->item_id;
			$q = $this->q
				->where( 'id', '=', $itemsIds )
				->where( 'state', '<>', NULL )
				;
			$items = $this->itemsQuery->find( $q );

			$ids = array_keys( $ret );
			foreach( $ids as $id ){
				$itemId = $ret[ $id ]->item_id;
				if( ! isset($items[$itemId]) ){
					unset( $ret[$id] );
				}
			}
		}

		return $ret;
	}

	public function findByTransaction( $transactionId )
	{
		static $cache = array();

		if( $transactionId instanceof _PW1_ZI3_Transactions_Model ) $transactionId = $transactionId->id;

		if( ! is_array($transactionId) ){
			if( isset($cache[$transactionId]) ) return $cache[$transactionId];
		}

		$transactionIds = is_array( $transactionId ) ? $transactionId : array( $transactionId );
		$q = $this->q->where( 'transaction_id', '=', $transactionId );
		$ret = $this->self->find( $q );

	// cache
		foreach( $transactionIds as $transactionId ) $cache[ $transactionId ] = array();
		foreach( $ret as $e ) $cache[ $e->transaction_id ][ $e->id ] = $e;

		return $ret;
	}

	public function count( _PW1_Q $q = NULL )
	{
		$ret = $this->crud->count( $q );
		return $ret;
	}

// shortcuts
	public function findById( $id )
	{
		if( isset($this->_cache[$id]) ){
			return $this->_cache[$id];
		}

		$q = $this->q->where( 'id', '=', (int) $id )->limit(1);
		$ret = $this->self->find( $q );
		if( ! is_array($id) ) $ret = array_shift( $ret );
		return $ret;
	}

// extensions
	public function listenTransactionsFind( _PW1_Q $q )
	{
		$args = func_get_args();
		$context = array_pop( $args );
		$ret = $context->ret;

	// preload lines
		$select = $q->getSelect();
		if( $ret && (! $select) ){
			$lines = $this->self->findByTransaction( array_keys($ret) );
		}
	}
}