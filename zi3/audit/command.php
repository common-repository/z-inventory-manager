<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Audit_Command extends _PW1
{
	public $q;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Audit_Model $model,
		PW1_ZI3_Audit_Crud $crud
	)
	{}

// commands
	public function create( _PW1_ZI3_Audit_Model $model )
	{
		$values = $this->model->toArray( $model );
		$res = $this->crud->create( $values );
		if( $res instanceof PW1_Error ) return $res;

		$model->id = $res;

		return $model;
	}

	public function delete( _PW1_Q $q )
	{
		$res = $this->crud->delete( $q );
		return $res;
	}
}