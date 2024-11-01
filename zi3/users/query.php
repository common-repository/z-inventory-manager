<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users_Query extends _PW1
{
	private $_cache = array();
	public $q;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q		$q,
		PW1_ZI3_Users_Model	$model,
		PW1_ZI3_Users_Crud	$crud
	)
	{}

	public function find( _PW1_Q $q )
	{
		$ret = array();

		// if( ! $q->isWhereSet('state') ){
			// $q->where( 'state', '=', PW1_ZI3_Items_Model::STATE_ACTIVE );
		// }
		$q->orderBy( 'title' );

		$bluePrintModel = $this->model->construct();
		$res = $this->crud->read( $q );

		foreach( $res as $e ){
			$model = clone $bluePrintModel;
			$model = $this->model->fromArray( $e, $model );
			if( ! $model ) continue;
			$ret[ $model->id ] = $model;

			$this->_cache[ $model->id ] = $model;
		}

		return $ret;
	}

	public function count( _PW1_Q $q )
	{
		// if( ! $q->isWhereSet('state') ){
			// $q->where( 'state', '=', PW1_ZI3_Items_Model::STATE_ACTIVE );
		// }
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