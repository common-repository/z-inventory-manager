<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts_Command extends _PW1
{
	public $q;
	public $query;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Contacts_Query $query,
		PW1_ZI3_Contacts_Model $model,
		PW1_ZI3_Contacts_Crud $crud
	)
	{}

	public function errors( _PW1_ZI3_Contacts_Model $model )
	{
		$ret = array();

	// required
		if( ! strlen($model->title) ){
			$ret[] = '__Name__' . ': ' . '__Required Field__';
		}

	// duplicated title
		if( strlen($model->title) ){
			$q = $this->q
				->where( 'title', '=', $model->title )
				->where( 'id', '<>', $model->id )
				->where( 'state', '<>', NULL )
				->limit( 1 )
			;
			$already = $this->query->find( $q );
			if( $already ){
				$ret[] = '__Name__' . ': ' . '__This Value Already Exists__';
			}
		}

	// duplicated email
		if( strlen($model->email) ){
			$q = $this->q
				->where( 'email', '=', $model->email )
				->where( 'id', '<>', $model->id )
				->where( 'state', '<>', NULL )
				->limit( 1 )
			;
			$already = $this->query->find( $q );
			if( $already ){
				$ret[] = '__Email__' . ': ' . '__This Value Already Exists__';
			}
		}

	// duplicated phone
		if( strlen($model->phone) ){
			$q = $this->q
				->where( 'phone', '=', $model->phone )
				->where( 'id', '<>', $model->id )
				->where( 'state', '<>', NULL )
				->limit( 1 )
			;
			$already = $this->query->find( $q );
			if( $already ){
				$ret[] = '__Phone__' . ': ' . '__This Value Already Exists__';
			}
		}

		return $ret;
	}

// commands
	public function create( _PW1_ZI3_Contacts_Model $model )
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

	public function update( _PW1_ZI3_Contacts_Model $old, _PW1_ZI3_Contacts_Model $model )
	{
		$errors = $this->self->errors( $model );

		if( $errors ){
			return new PW1_Error( $errors );
		}

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

	public function delete( _PW1_ZI3_Contacts_Model $model )
	{
		$q = $this->q->where( 'id', '=', $model->id );
		$res = $this->crud->delete( $q );
		if( $res instanceof PW1_Error ) return $res;
		return $model;
	}
}