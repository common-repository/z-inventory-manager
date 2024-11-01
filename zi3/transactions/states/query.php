<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions_States_Query extends _PW1
{
	private $_cache = NULL;
	public $q;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Transactions_States_Model $model,
		PW1_ZI3_Transactions_States_Crud $crud
	)
	{}

	public function find()
	{
		if( NULL !== $this->_cache ) return $this->_cache;

		$ret = array();

		$q = $this->q->construct();
		$q->orderBy( 'title', 'ASC' );

		$res = $this->crud->read( $q );
		if( $res instanceof PW1_Error ) return $res;

		foreach( $res as $e ){
			$model = $this->model->fromArray( $e );
			if( ! $model ) continue;
			$ret[ $model->id ] = $model;
		}

		$this->_cache = $ret;
		return $ret;
	}

// shortcuts
	public function findById( $id )
	{
		if( NULL === $this->_cache ) $this->self->find();

		$ret = isset( $this->_cache[$id] ) ? $this->_cache[$id] : NULL;
		return $ret;
	}
}