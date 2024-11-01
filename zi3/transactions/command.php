<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions_Command extends _PW1
{
	public $q;
	public $settings;
	public $query;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Settings_ $settings,
		PW1_ZI3_Transactions_Query $query,
		PW1_ZI3_Transactions_Model $model,
		PW1_ZI3_Transactions_Crud $crud
	)
	{}

	public function getNewRefno( _PW1_ZI3_Transactions_Model $model )
	{
		$ret = '';

	// purchase
		if( $model->type > 0 ){
			if( ! $this->settings->get('transactions_purchase_ref_auto') ){
				return $ret;
			}
		}

	// sale
		if( $model->type < 0 ){
			if( ! $this->settings->get('transactions_sale_ref_auto') ){
				return $ret;
			}
		}

		if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $model->type ){
			$prefix = $this->settings->get('transactions_purchase_ref_auto_prefix');
			$method = $this->settings->get( 'transactions_purchase_ref_auto_method' );
		}

		if( PW1_ZI3_Transactions_Model::TYPE_SALE == $model->type ){
			$prefix = $this->settings->get('transactions_sale_ref_auto_prefix');
			$method = $this->settings->get( 'transactions_sale_ref_auto_method' );
		}

		$exists = TRUE;
		$biggestId = -1;

		while( $exists ){
			$exists = FALSE;

		// generate
			if( 'seq' !== $method ){
				$ret = rand( 100000, 999999 );
			}
			else {
				if( -1 === $biggestId ){
					$biggestId = 0;
					$q = $this->q
						->where( 'type', '=', $model->type )
						->orderBy( 'id', 'DESC' )
						->limit( 1 )
						;
					$current = $this->query->find( $q );
					if( $current ){
						$latest = array_shift( $current );
						$biggestId = $latest->id;
					}
				}

				$ret = ++$biggestId;
			}

			$length = 6;
			$ret = (string) $ret;
			$ret = str_pad( $ret, $length, '0', STR_PAD_LEFT );

			if( strlen($prefix) ){
				$ret = $prefix . $ret;
			}

			$q = $this->q->where( 'refno', '=', $ret )->limit( 1 );
			$exists = $this->query->find( $q );
		}

		return $ret;
	}

// commands
	public function create( _PW1_ZI3_Transactions_Model $model )
	{
	// required
		if( ! strlen($model->refno) ){
			$msg = '__Transaction__' . ': ' . '__Reference__' . ': ' . '__Required Field__';
			return new PW1_Error( $msg );
		}

	// duplicated refno
		$q = $this->q->where( 'refno', '=', $model->refno )->where( 'id', '<>', $model->id )->limit( 1 );
		$already = $this->query->find( $q );
		if( $already ){
			$msg = '__Transaction__' . ': ' . '__Reference__' . ': ' . '__This Value Already Exists__';
			return new PW1_Error( $msg );
		}

		$values = $this->model->toArray( $model );
		$res = $this->crud->create( $values );
		if( $res instanceof PW1_Error ) return $res;

		$model->id = $res;

		return $model;
	}

	public function changeState( _PW1_ZI3_Transactions_Model $model, $state )
	{
		$new = clone $model;
		$new->state = $state;
		return $this->update( $model, $new );
	}

	public function update( _PW1_ZI3_Transactions_Model $old, _PW1_ZI3_Transactions_Model $model )
	{
	// required
		if( ! strlen($model->refno) ){
			$msg = '__Transaction__' . ': ' . '__Reference__' . ': ' . '__Required Field__';
			return new PW1_Error( $msg );
		}

	// duplicated title
		if( $old->refno !== $model->refno ){
			$q = $this->q->where( 'refno', '=', $model->refno )->where( 'id', '<>', $model->id )->limit( 1 );
			$already = $this->query->find( $q );
			if( $already ){
				$msg = '__Transaction__' . ': ' . '__Reference__' . ': ' . '__This Value Already Exists__';
				return new PW1_Error( $msg );
			}
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

	public function delete( _PW1_ZI3_Transactions_Model $model )
	{
		$q = $this->q->where( 'id', '=', $model->id );
		$res = $this->crud->delete( $q );
		if( $res instanceof PW1_Error ) return $res;
		return $model;
	}
}