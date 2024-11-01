<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl0Wp_Connections_Query extends _PW1
{
	private $_alwaysAdmin = array(
		'administrator',
		// 'developer',
	);
	private $_cache = array();

	public $q;
	public $crud;
	public $model;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Acl0Wp_Connections_Crud	$crud,
		PW1_ZI3_Acl0Wp_Connections_Model	$model
	)
	{}

	public function find( _PW1_Q $q )
	{
		$ret = array();

	// in database
		$q->orderBy( 'id' );

		$bluePrintModel = $this->model->construct();
		$res = $this->crud->read( $q );

		foreach( $res as $e ){
			$model = clone $bluePrintModel;
			$model = $this->model->fromArray( $e, $model );
			if( ! $model ) continue;
			$ret[ $model->id ] = $model;
		}

	// builtin
		$builtin = $this->self->findBuiltin();

	// merge
		$ret = $builtin + $ret;

		foreach( $ret as $id => $model ) $this->_cache[$id] = $model;

	// check in-memory
		$ids = array_keys( $ret );
		foreach( $ids as $id ){
			if( ! $this->q->checkObject( $ret[$id], $q ) ){
				unset( $ret[$id] );
			}
		}

		return $ret;
	}

	public function findBuiltin()
	{
		$ret = array();

	// superadmins
		$id = -1;
		foreach( $this->_alwaysAdmin as $wpRoleId ){
			$e = $this->model->construct();
			$e->id = $id--;
			$e->wp_role_id = $wpRoleId;
			$e->role_id = -1;
			$ret[ $e->id ] = $e;
		}

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