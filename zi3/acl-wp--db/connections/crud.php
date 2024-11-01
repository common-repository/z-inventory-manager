<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl0Wp00Db_Connections_Crud extends _PW1
{
	public $_;
	public $qb;
	public $sql;

	public function __construct(
		PW1_ZI3_Acl0Wp00Db_ $_,
		PW1_Sql_QueryBuilder $qb,
		PW1_Sql_ $sql
	)
	{}

	public function create( array $values )
	{
		$qb = $this->qb->construct();

		$qb->set( $values );
		$sql = $qb->getCompiledInsert( $this->_->tableNameConnections() );
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

		$sql = $qb->getCompiledSelect( $this->_->tableNameConnections() );
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

		$sql = $qb->getCompiledDelete( $this->_->tableNameConnections() );
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