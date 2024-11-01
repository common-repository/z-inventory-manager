<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl0Wp_Roles_Query extends _PW1
{
	public $q;
	public $rolesQuery;
	public $aclWpConnectionsQuery;

	public function __construct(
		PW1_Q	$q,
		PW1_ZI3_Acl_Roles_Query $rolesQuery,
		PW1_ZI3_Acl0Wp_Connections_Query $aclWpConnectionsQuery
	)
	{}

	public function wrapFindForUser( $userOrId )
	{
		$args = func_get_args();
		$context = array_pop( $args );

		$ret = call_user_func_array( $context->parentFunc, $args );

	// find connections
		$userId = ( $userOrId instanceof _PW1_ZI3_Users_Model ) ? $userOrId->id : $userOrId;

		$roleId = NULL;

	// group connections
		$q2 = $this->q->construct();
		$wpUser = get_userdata( $userId );
		if( isset($wpUser->roles) && ($wpUser->roles) ){
			$q2->where( 'wp_role_id', '=', $wpUser->roles );
			$groupConnections = $this->aclWpConnectionsQuery->find( $q2 );
			foreach( $groupConnections as $e ){
				$roleId = $e->role_id;
			}
		}

	// user connections
		$q2 = $this->q->construct();
		$q2->where( 'wp_user_id', '=', $userId );
		$userConnections = $this->aclWpConnectionsQuery->find( $q2 );
		foreach( $userConnections as $e ){
			$roleId = $e->role_id;
		}

		if( NULL!== $roleId ){
			$ret = $this->rolesQuery->findById( $roleId );
		}

		return $ret;
	}
}