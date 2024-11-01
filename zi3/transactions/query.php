<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions_Query extends _PW1
{
	private $_cache = array();
	public $q;
	public $linesQuery;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,

		PW1_ZI3_Transactions_Lines_Query $linesQuery,

		PW1_ZI3_Transactions_Model $model,
		PW1_ZI3_Transactions_Crud $crud
	)
	{}

	public function whereItem( $itemId, _PW1_Q $q )
	{
		$itemId = ( $itemId instanceof _PW1_ZI3_Items_Model ) ? $itemId->id : $itemId;
		if( ! is_array($itemId) ) $itemId = array( $itemId );

	// find lines
		$q2 = $this->q->where( 'item_id', '=', $itemId );
		$es = $this->linesQuery->find( $q2 );

		$ids = array();
		foreach( $es as $e ) $ids[ $e->transaction_id ] = $e->transaction_id;

		if( ! $ids ){
			$q->where( 1, '=', 0 );
			return $q;
		}

		$ids = array_map( 'intval', $ids );
		$q->where( 'id', '=', $ids );

		return $q;
	}

	public function find( _PW1_Q $q )
	{
		$ret = array();

		$q
			->orderBy( 'created_date', 'DESC' )
			->orderBy( 'id', 'DESC' )
			;

		$select = $q->getSelect();

		$res = $this->crud->read( $q );
		foreach( $res as $e ){
			if( $select ){
				if( count($select) > 1 ){
					$thisRet = array();
					foreach( $select as $k ) $thisRet[$k] = $e[$k];
				}
				else {
					foreach( $select as $k ) $thisRet = $e[$k];
				}
				$ret[] = $thisRet;
			}
			else {
				$model = $this->model->fromArray( $e );
				if( ! $model ) continue;
				$ret[ $model->id ] = $model;

				$this->_cache[ $model->id ] = $model;
			}
		}

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
}