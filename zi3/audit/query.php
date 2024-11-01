<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Audit_Query extends _PW1
{
	protected $_cache = array();

	public $q;
	public $model;
	public $crud;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Audit_Model $model,
		PW1_ZI3_Audit_Crud $crud
	)
	{}

	public function find( _PW1_Q $q )
	{
		$ret = array();

		$q
			->orderBy( 'eventDateTime', 'DESC' )
			->orderBy( 'id', 'DESC' )
			;

		$select = $q->getSelect();

		$res = $this->crud->read( $q );
		foreach( $res as $e ){
			if( $select ){
				if( count($select) > 1 ){
					$thisRet = array();
					foreach( $select as $k ) $thisRet[$k] = $e[$k];
				}
				else {
					foreach( $select as $k ) $thisRet = $e[$k];
				}
				$ret[] = $thisRet;
			}
			else {
				$model = $this->model->fromArray( $e );
				if( ! $model ) continue;
				$ret[ $model->id ] = $model;

				$this->_cache[ $model->id ] = $model;
			}
		}

		return $ret;
	}

	public function count( _PW1_Q $q )
	{
		$ret = $this->crud->count( $q );
		return $ret;
	}
}