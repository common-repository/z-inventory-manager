<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl_Query extends _PW1
{
	private $_catalog = NULL;
	private $_cacheByUser = array();

	public $usersQuery;
	public $acl;
	public $queryRoles;
	public $q;

	public function __construct(
		PW1_ZI3_Users_Query		$usersQuery,
		PW1_ZI3_Acl_				$acl,
		PW1_ZI3_Acl_Roles_Query	$queryRoles,
		PW1_Q $q
	)
	{}

	private function _resetCacheByUser()
	{
		$this->_cacheByUser = array();
	}

	public function findForUser( $userOrId )
	{
		$ret = array();

		$userId = is_object( $userOrId ) ? $userOrId->id : $userOrId;
		if( isset($this->_cacheByUser[$userId]) ){
			return $this->_cacheByUser[$userId];
		}

		if( NULL === $this->_catalog ) $this->_catalog = $this->acl->catalog();
		$catalog = $this->_catalog;

		$ret = $catalog;

		$role = $this->queryRoles->findForUser( $userOrId );
		if( ! $role ){
			return $ret;
		}

		foreach( $catalog as $k ){
			$ret[ $k ] =  array_key_exists( $k, $role->permissions ) ? $role->permissions[$k] : FALSE;
		}

		$ret = $this->acl->finalizePermissions( $ret );

// _print_r( $ret );
// exit;

		$this->_cacheByUser[ $userId ] = $ret;
		return $ret;
	}

	public function anyUserCan( $permissionId )
	{
		$ret = FALSE;

		if( NULL === $this->_catalog ) $this->_catalog = $this->acl->catalog();
		$catalog = $this->_catalog;

		if( ! in_array($permissionId, $catalog) ){
			echo __METHOD__ . ": permission '$permissionId' not registered<br>";
			return $ret;
		}

		$this->_resetCacheByUser();

		$q2 = $this->q->construct();
		$allUsers = $this->usersQuery->find( $q2 );

		foreach( $allUsers as $user ){
			if( $this->self->userCan( $user, $permissionId ) ){
				$ret = TRUE;
				break;
			}
		}

		return $ret;
	}

	public function userCan( $userOrId, $permissionId )
	{
		$ret = FALSE;

		if( NULL === $this->_catalog ) $this->_catalog = $this->acl->catalog();
		$catalog = $this->_catalog;

		if( ! in_array($permissionId, $catalog) ){
			echo __METHOD__ . ": permission '$permissionId' not registered<br>";
			return $ret;
		}

		$permissionsForUser = $this->self->findForUser( $userOrId );
		$ret = ( isset( $permissionsForUser[$permissionId] ) && $permissionsForUser[$permissionId] ) ? TRUE : FALSE;
		return $ret;
	}
}