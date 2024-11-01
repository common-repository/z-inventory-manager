<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Db_Crud extends _PW1
{
	public $_;
	public $qb;
	public $sql;

	public function __construct(
		PW1_ZI3_Items00Db_ $_,
		PW1_Sql_QueryBuilder $qb,
		PW1_Sql_ $sql
	)
	{}

	public function create( array $values )
	{
		$qb = $this->qb->construct();

		$qb->set( $values );
		$sql = $qb->getCompiledInsert( $this->_->tableName() );
// echo $sql . '<br>';
		$id = $this->sql->query( $sql );

		return $id;
	}

	public function read( _PW1_Q $q = NULL )
	{
		$ret = array();

		$qb = $this->qb->construct();
		$qb = $this->_qWhere( $qb, $q );

	// orderby
		foreach( $q->getOrderBy() as $id => $w ){
			list( $name, $direction ) = $w;
			$qb->orderBy( $name, $direction );
		}

	// limit
		$qb->limit( $q->getLimit() );
		$qb->offset( $q->getOffset() );

		$sql = $qb->getCompiledSelect( $this->_->tableName() );
		$res = $this->sql->query( $sql );

		$ret = array();
		foreach( $res as $e ) $ret[ $e['id'] ] = $e; 

		return $ret;
	}

	public function count( _PW1_Q $q = NULL )
	{
		$ret = 0;

		$qb = $this->qb->construct();
		$qb = $this->_qWhere( $qb, $q );

		$sql = $qb->getCompiledCount( $this->_->tableName() );

		$ret = $this->sql->query( $sql );
		if( $ret instanceof PW1_Error ) return 0;

		if( $ret ){
			$ret = current( current($ret) );
			$ret = (int) $ret;
		}

	// if where's remains then load all and count

		return $ret;
	}

	public function update( _PW1_Q $q, array $values )
	{
		if( ! $values ) return;

		$where = $q->getWhere();
		if( ! $where ){
			echo __METHOD__ . ': ' . __LINE__ . ': cannot proceed without conditions!<br>';
			return;
		}

		$qb = $this->qb->construct();
		$qb = $this->_qWhere( $qb, $q );

		$qb->set( $values );
		$sql = $qb->getCompiledUpdate( $this->_->tableName() );
		$ret = $this->sql->query( $sql );

		return $ret;
	}

	public function delete( _PW1_Q $q )
	{
		$where = $q->getWhere();
		if( ! $where ){
			echo __METHOD__ . ': ' . __LINE__ . ': cannot proceed without conditions!<br>';
			return;
		}

		$qb = $this->qb->construct();
		$qb = $this->_qWhere( $qb, $q );

		$sql = $qb->getCompiledDelete( $this->_->tableName() );
		$ret = $this->sql->query( $sql );

		return $ret;
	}

	private function _qWhere( $qb, _PW1_Q $q )
	{
		$where = $q->getWhere();
		foreach( $where as $id => $w ){
			if( $w instanceof $q ){
				$qb->groupStart();
				$qb = $this->_qWhere( $qb, $w );
				$qb->groupEnd();
			}
			else {
				list( $name, $compare, $value ) = $w;
				if( 'LIKE' === $compare ){
					$qb->like( $name, $value );
				}
				else {
					$qb->where( $name . $compare, $value );
				}
			}
		}

		$orWhere = $q->getOrWhere();
		foreach( $orWhere as $id => $w ){
			if( $w instanceof $q ){
				$qb->orGroupStart();
				$qb = $this->_qWhere( $qb, $w );
				$qb->groupEnd();
			}
			else {
				list( $name, $compare, $value ) = $w;
				if( 'LIKE' === $compare ){
					$qb->orLike( $name, $value );
				}
				else {
					$qb->orWhere( $name . $compare, $value );
				}
			}
		}

		return $qb;
	}
}