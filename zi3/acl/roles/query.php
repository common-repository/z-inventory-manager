<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl_Roles_Query extends _PW1
{
	private $_cache = array();
	public $acl;
	public $crud;
	public $model;
	public $q;

	public function __construct(
		PW1_ZI3_Acl_				$acl,
		PW1_ZI3_Acl_Roles_Crud	$crud,
		PW1_ZI3_Acl_Roles_Model	$model,
		PW1_Q $q
	)
	{}

// queries
	public function findForUser( $userOrId )
	{
		$ret = NULL;
		return $ret;
	}

	public function find( _PW1_Q $q )
	{
		$ret = array();

	// in database
		$q->orderBy( 'title' );
		$res = $this->crud->read( $q );

		$bluePrintModel = $this->model->construct();
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

		foreach( $ret as $id => $model ){
			$this->_cache[$id] = $model;
		}

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

		$catalog = $this->acl->catalog();

		$e = $this->model->construct();
		$e->id = -1;
		$e->title = 'Super Administrator';
		$e->permissions = array();
		foreach( $catalog as $k ) $e->permissions[ $k ] = TRUE;
		$ret[ $e->id ] = $e;

		$e = $this->model->construct();
		$e->id = -2;
		$e->title = 'No Access';
		$e->permissions = array();
		foreach( $catalog as $k ) $e->permissions[ $k ] = FALSE;
		$ret[ $e->id ] = $e;

		return $ret;
	}

// shortcuts
	public function findById( $id )
	{
		if( ! is_array($id) ){
			if( ! $this->_cache ){
				$q2 = $this->q->construct();
				$this->self->find( $q2 );
			}

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