<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_UsersOverride00Html_Index extends _PW1
{
	public $_;
	public $usersWidget;
	public $usersQuery;
	public $aclQuery;

	public function __construct(
		PW1_ZI3_UsersOverride00Html_ $_,
		PW1_ZI3_Users00Html_Widget $usersWidget,
		PW1_ZI3_Users_Query $usersQuery,
		PW1_ZI3_Acl_Query $aclQuery
	)
	{}

	public function announce( PW1_Request $request, PW1_Response $response )
	{
		if( ! isset($request->params['*originalUserId']) ) return;

	// overriden!
		$userId = $request->currentUserId;

		// $originalUserId = $request->params['*originalUserId'];
		$user = $this->usersQuery->findById( $userId );
		$p = $this->_->myParamName();

		ob_start();
?>

<dl>
	<dt>
		__User Override In Effect__
	</dt>
	<dd>
		<ul class="pw-inline">
			<li>
				__Viewing As__: <?php echo $this->usersWidget->presentTitle( $user, 'URI:admin/users/' . $userId . '?*' . $p . '=NULL' ); ?>
			</li>
			<li>
				<nav>
				<a href="URI:.?*<?php echo $p; ?>=NULL"><span>&times;</span>__Stop User Override__</a>
				</nav>
			</li>
		</ul>
	</dd>
</dl>

<?php
		$ret = trim( ob_get_clean() );

		$response->addDebug( $ret );
		return $response;
	}

	public function currentUserId( PW1_Request $request, PW1_Response $response )
	{
		if( ! $request->currentUserId ){
			return;
		}

		$canManageUsers = $this->aclQuery->userCan( $request->currentUserId, 'manage_users' );
		if( ! $canManageUsers ){
			return;
		}

		$p = $this->_->myParamName();

		$params = PW1_HttpRequest::getGet();
		if( ! isset($params[$p]) ) return;

		$newUserId = $params[$p];
		if( ! strlen($newUserId) ) return;

		$request->params['*originalUserId'] = $request->currentUserId;
		$request->currentUserId = $newUserId;

// echo "OVERRIDE " . $request->params['*originalUserId'] . " TO " . $request->currentUserId . "<br>";

		return $request;
	}
}