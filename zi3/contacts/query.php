<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts_Query extends _PW1
{
	private $_cache = array();
	public $q;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Contacts_Model $model,
		PW1_ZI3_Contacts_Crud $crud
	)
	{}

	public function whereSearch( $s, _PW1_Q $q )
	{
		$q2 = $this->q
			->orWhere( 'title', 'LIKE', $s )
			->orWhere( 'email', 'LIKE', $s )
			->orWhere( 'phone', 'LIKE', $s )
			;
		$q->where( $q2 );

		return $q;
	}

	public function find( _PW1_Q $q )
	{
		$ret = array();

		if( ! $q->isWhereSet('state') ){
			$q->where( 'state', '=', PW1_ZI3_Items_Model::STATE_ACTIVE );
		}
		$q->orderBy( 'title' );

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

	public function count( _PW1_Q $q )
	{
		if( ! $q->isWhereSet('state') ){
			$q->where( 'state', '=', PW1_ZI3_Items_Model::STATE_ACTIVE );
		}
		$ret = $this->crud->count( $q );
		return $ret;
	}

// shortcuts
	public function findById( $id )
	{
		if( ! is_array($id) ){
			if( isset($this->_cache[$id]) ){
				return $this->_cache[$id];
			}
		}

		$id = is_array( $id ) ? array_map( 'intval', $id ) : (int) $id;

		$q = $this->q
			->where( 'id', '=', $id )
			->where( 'state', '<>', NULL )
			;

		if( ! is_array($id) ){
			$q->limit( 1 );
		}

		$ret = $this->self->find( $q );
		if( ! is_array($id) ){
			$ret = array_shift( $ret );
		}

		return $ret;
	}
}