<?php
class PW1_ZI3_Users0Wp_Query extends _PW1
{
	public $q;
	public $model;
	public $usersWpCrud;
	public $aclWpQuery;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Users_Model $model,
		PW1_ZI3_Users0Wp_Crud $usersWpCrud,
		PW1_ZI3_Acl0Wp_Connections_Query $aclWpQuery
	)
	{}

	public function wrapFind( _PW1_Q $q )
	{
		$ret = array();

		$args = func_get_args();
		$context = array_pop( $args );

	// find acl connections
		$q2 = $this->q->construct();
		$aclConnections = $this->aclWpQuery->find( $q2 );

		if( ! $aclConnections ) return $ret;

		$includeIds = array();
		$includeRoles = array();

		foreach( $aclConnections as $e ){
			if( $e->wp_user_id ){
				$includeIds[ $e->wp_user_id ] = $e->wp_user_id;
			}
			if( $e->wp_role_id ){
				$includeRoles[ $e->wp_role_id ] = $e->wp_role_id;
			}
		}

		if( $includeRoles ){
			$args = array();
			$args[ 'role__in' ] = $includeRoles;
			$args[ 'fields' ] = 'ID';
			$res = get_users( $args );

			foreach( $res as $e ){
				$id = is_object( $e ) ? $e->ID : $e;
				$includeIds[ $id ] = $id;
			}
		}

		if( ! $includeIds ) return $ret;

		$q->where( 'id', '=', $includeIds );

		$ret = call_user_func( $context->parentFunc, $q );
		return $ret;
	}

	public function wrapFindById( $id )
	{
		$ret = NULL;

		$wpUser = get_user_by( 'id', $id );
		if( ! $wpUser ) return $ret;

		$arr = $this->usersWpCrud->wpUserToArray( $wpUser );
		$ret = $this->model->construct();
		$ret = $this->model->fromArray( $arr, $ret );

		return $ret;
	}
}