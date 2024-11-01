<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions_States_Command extends _PW1
{
	public $q;
	public $query;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Transactions_States_Query $query,
		PW1_ZI3_Transactions_States_Model $model,
		PW1_ZI3_Transactions_States_Crud $crud
	)
	{}

// commands
	public function create( _PW1_ZI3_Transactions_States_Model $model )
	{
	// required
		if( ! strlen($model->title) ){
			$msg = '__Transaction State__' . ': ' . '__Title__' . ': ' . '__Required Field__';
			return new PW1_Error( $msg );
		}

	// duplicated title
		$already = $this->query->find();
		if( $already ){
			foreach( $already as $e ){
				if( $e->title == $model->title ){
					$msg = '__Transaction__' . ': ' . '__Reference__' . ': ' . '__This Value Already Exists__';
					return new PW1_Error( $msg );
				}
			}
		}

		$values = $this->model->toArray( $model );
		$res = $this->crud->create( $values );
		if( $res instanceof PW1_Error ) return $res;
		$model->id = $res;
		return $model;
	}

	public function update( _PW1_ZI3_Transactions_States_Model $old, _PW1_ZI3_Transactions_States_Model $model )
	{
		$values = $this->model->toArray( $model );
		$oldValues = $this->model->toArray( $old );

		$values = array_diff_assoc( $values, $oldValues );

		if( $values ){
			$q = $this->q->where( 'id', '=', $old->id );
			$res = $this->crud->update( $q, $values );
			if( $res instanceof PW1_Error ) return $res;
		}

		return $model;
	}

	public function delete( _PW1_ZI3_Transactions_States_Model $model )
	{
		$q = $this->q->where( 'id', '=', $model->id );
		$res = $this->crud->delete( $q );
		if( $res instanceof PW1_Error ) return $res;
		return $model;
	}
}