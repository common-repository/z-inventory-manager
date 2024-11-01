<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions_Lines_Command extends _PW1
{
	public $q;
	public $query;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Transactions_Lines_Query $query,
		PW1_ZI3_Transactions_Lines_Model $model,
		PW1_ZI3_Transactions_Lines_Crud $crud
	)
	{}

// commands
	public function create( _PW1_ZI3_Transactions_Lines_Model $model )
	{
		$values = $this->model->toArray( $model );
		$res = $this->crud->create( $values );
		if( $res instanceof PW1_Error ) return $res;
		$model->id = $res;
		return $model;
	}

	public function update( _PW1_ZI3_Transactions_Lines_Model $old, _PW1_ZI3_Transactions_Lines_Model $model )
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

	public function delete( _PW1_ZI3_Transactions_Lines_Model $model )
	{
		$q = $this->q->where( 'id', '=', $model->id );
		$res = $this->crud->delete( $q );
		if( $res instanceof PW1_Error ) return $res;
		return $model;
	}

	public function listenTransactionDelete( _PW1_ZI3_Transactions_Model $transaction )
	{
		$q = $this->q->where( 'transaction_id', '=', $transaction->id );
		$lines = $this->query->find( $q );
		foreach( $lines as $e ){
			$this->self->delete( $e );
		}
	}
}