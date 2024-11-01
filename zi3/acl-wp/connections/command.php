<?php if (! defined('ABSPATH')) exit;
interface PW1_ZI3_Acl0Wp_Connections_Command_Interface
{
	public function errors( _PW1_ZI3_Acl0Wp_Connections_Model $model );
	public function create( _PW1_ZI3_Acl0Wp_Connections_Model $model );
	public function delete( _PW1_ZI3_Acl0Wp_Connections_Model $model );
}

class PW1_ZI3_Acl0Wp_Connections_Command extends _PW1 implements PW1_ZI3_Acl0Wp_Connections_Command_Interface
{
	public $aclQuery;
	public $query;
	public $model;
	public $crud;
	public $q;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery,
		PW1_ZI3_Acl0Wp_Connections_Query $query,
		PW1_ZI3_Acl0Wp_Connections_Model $model,
		PW1_ZI3_Acl0Wp_Connections_Crud $crud,
		PW1_Q	$q
	)
	{}

	public function errors( _PW1_ZI3_Acl0Wp_Connections_Model $model )
	{
		$ret = array();

	// required
		if( ! strlen($model->role_id) ){
			$ret[] = '__Permission Role__' . ': ' . '__Required Field__';
		}

	// duplicated
		if( strlen($model->role_id) && strlen($model->wp_role_id) ){
			$q = $this->q
				->where( 'role_id', '=', $model->role_id )
				->where( 'wp_role_id', '=', $model->wp_role_id )
				->where( 'id', '<>', $model->id )
				->limit( 1 )
				;
			$already = $this->query->find( $q );
			if( $already ){
				$ret[] = '__Already Exists__';
			}
		}

	// duplicated
		if( strlen($model->role_id) && strlen($model->wp_user_id) ){
			$q = $this->q
				->where( 'role_id', '=', $model->role_id )
				->where( 'wp_user_id', '=', $model->wp_user_id )
				->where( 'id', '<>', $model->id )
				->limit( 1 )
				;
			$already = $this->query->find( $q );
			if( $already ){
				$ret[] = '__Already Exists__';
			}
		}

		return $ret;
	}

// commands
	public function create( _PW1_ZI3_Acl0Wp_Connections_Model $model )
	{
		$errors = $this->self->errors( $model );

		if( $errors ){
			return new PW1_Error( $errors );
		}

		$values = $this->model->toArray( $model );
		$res = $this->crud->create( $values );
		if( $res instanceof PW1_Error ) return $res;

		$model->id = $res;

	// now check if it creates a situation that nobody will be able to manage users
		$anyUserCan = $this->aclQuery->anyUserCan( 'manage_users' );
		if( ! $anyUserCan ){
			// roll back
			$q = $this->q->where( 'id', '=', $model->id );
			$this->crud->delete( $q );

			$errors = array( '__Error Updating Permission Connections__' . ': ' . '__Nobody Will Be Able To Manage Users__' );
			$res = new PW1_Error( $errors );
			return $res;
		}

		return $model;
	}

	public function delete( _PW1_ZI3_Acl0Wp_Connections_Model $model )
	{
		$q = $this->q->where( 'id', '=', $model->id );
		$res = $this->crud->delete( $q );
		if( $res instanceof PW1_Error ) return $res;

	// now check if it creates a situation that nobody will be able to manage users
		$anyUserCan = $this->aclQuery->anyUserCan( 'manage_users' );
		if( ! $anyUserCan ){
			// roll back
			$values = $this->model->toArray( $model );
			$res = $this->crud->create( $values );

			$errors = array( '__Error Updating Permission Connections__' . ': ' . '__Nobody Will Be Able To Manage Users__' );
			$res = new PW1_Error( $errors );
			return $res;
		}

		return $model;
	}
}