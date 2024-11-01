<?php if (! defined('ABSPATH')) exit;
interface PW1_ZI3_Acl_Roles_Command_Interface
{
	public function errors( _PW1_ZI3_Acl_Roles_Model $model );
	public function create( _PW1_ZI3_Acl_Roles_Model $model );
	public function update( _PW1_ZI3_Acl_Roles_Model $old, _PW1_ZI3_Acl_Roles_Model $model );
	public function delete( _PW1_ZI3_Acl_Roles_Model $model );
}

class PW1_ZI3_Acl_Roles_Command extends _PW1 implements PW1_ZI3_Acl_Roles_Command_Interface
{
	public $q;
	public $query;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q	$q,
		PW1_ZI3_Acl_Roles_Query	$query,
		PW1_ZI3_Acl_Roles_Model	$model,
		PW1_ZI3_Acl_Roles_Crud	$crud
	)
	{}

	public function errors( _PW1_ZI3_Acl_Roles_Model $model )
	{
		$ret = array();

	// required
		if( ! strlen($model->title) ){
			$ret[] = '__Title__' . ': ' . '__Required Field__';
		}

	// duplicated title
		if( strlen($model->title) ){
			$q = $this->q
				->where( 'title', '=', $model->title )
				->where( 'id', '<>', $model->id )
				->limit( 1 )
				;
			$already = $this->query->find( $q );

			if( $already ){
				$ret[] = '__Title__' . ': ' . '__This Value Already Exists__';
			}
		}

		return $ret;
	}

// commands
	public function create( _PW1_ZI3_Acl_Roles_Model $model )
	{
		$errors = $this->self->errors( $model );

		if( $errors ){
			return new PW1_Error( $errors );
		}

		$values = $this->model->toArray( $model );
		$res = $this->crud->create( $values );
		if( $res instanceof PW1_Error ) return $res;

		$model->id = $res;

		return $model;
	}

	public function update( _PW1_ZI3_Acl_Roles_Model $old, _PW1_ZI3_Acl_Roles_Model $model )
	{
		$errors = $this->self->errors( $model );

		if( $errors ){
			return new PW1_Error( $errors );
		}

		$values = $this->model->toArray( $model );
		$oldValues = $this->model->toArray( $old );

		$updateValues = array();
		foreach( $oldValues as $k => $v ){
			if( is_array($v) ){
				if( array_diff_key($v, $values[$k]) OR array_diff_key($values[$k], $v) ){
					$updateValues[ $k ] = $values[$k];
				}
			}
			else {
				if( $v != $values[$k] ){
					$updateValues[ $k ] = $values[$k];
				}
			}
		}

		if( ! $updateValues ) return $model;

		$q = $this->q->where( 'id', '=', $old->id );
		$res = $this->crud->update( $q, $updateValues );
		if( $res instanceof PW1_Error ) return $res;

		return $model;
	}

	public function delete( _PW1_ZI3_Acl_Roles_Model $model )
	{
		$q = $this->q->where( 'id', '=', $model->id );
		$res = $this->crud->delete( $q );
		if( $res instanceof PW1_Error ) return $res;
		return $model;
	}
}