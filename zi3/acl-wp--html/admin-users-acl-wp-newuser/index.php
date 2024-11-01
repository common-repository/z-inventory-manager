<?php
class PW1_ZI3_Acl0Wp00Html_Admin0Users0Acl0Wp0NewUser_Index extends _PW1
{
	public $q;
	public $rolesWidget;
	public $usersWpWidget;
	public $model;
	public $command;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Acl_Roles_Query $rolesQuery,
		PW1_ZI3_Acl00Html_Widget_Roles $rolesWidget,
		PW1_ZI3_Users0Wp00Html_Widget $usersWpWidget,
		PW1_ZI3_Acl0Wp_Connections_Model $model,
		PW1_ZI3_Acl0Wp_Connections_Command $command
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$wpUser = NULL;
		if( isset($request->params['user']) ){
			$wpUserId = $request->params['user'];
			$wpUser = get_user_by( 'id', $wpUserId );
		}

		if( $wpUser ){
			$response->title = '__New Permission Connection For WordPress User__';
		}
		else {
			$response->title = '__Select WordPress User__';
		}

		return $response;
	}

	public function getSelectUser( PW1_Request $request, PW1_Response $response )
	{
		$wpUsers = get_users();

		ob_start();
?>
<table>
<thead>
	<tr>
		<td>
			__WordPress User__
		</td>
	</tr>
</thead>

<tbody>
<?php foreach( $wpUsers as $wpUser ) : ?>
	<tr>
		<td>
			<?php echo $this->usersWpWidget->presentWpUser( $wpUser, 'URI:.?user=' . $wpUser->ID ); ?>
		</td>
 	</tr>
<?php endforeach; ?>
</tbody>

</table>

<?php
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$wpUser = NULL;
		if( isset($request->params['user']) ){
			$wpUserId = $request->params['user'];
			$wpUser = get_user_by( 'id', $wpUserId );
		}

		if( ! $wpUser ) return $this->self->getSelectUser( $request, $response );

		$q = $this->q->construct();
		$roles = $this->rolesQuery->find( $q );

		global $wp_roles;
		$wpRoles = array_keys( $wp_roles->roles );

		$model = $this->model->construct();
		ob_start();
?>

<form method="post" action="URI:.">

<div>
	<dl>
		<dt>
			__WordPress User__
		</dt>
		<dd>
			<input type="hidden" name="wp_user_id" value="<?php echo $wpUser->ID; ?>">
			<?php echo $this->usersWpWidget->presentWpUser( $wpUser, TRUE ); ?>
		</dd>
	</dl>

	<nav>
		<ul class="pw-inline">
			<li>
				<a href="URI:.?user=NULL"><span>...</span>__Change__</a>
			</li>
		</ul>
	</nav>
</div>

<div>
	<fieldset>
		<legend>__Plugin Role__</legend>
		<ul class="pw-inline">
		<?php foreach( $roles as $e ) : ?>
			<li>
				<label>
					<input type="radio" name="role_id" value="<?php echo $e->id; ?>"><?php echo $this->rolesWidget->presentTitle( $e ); ?>
				</label>
			</li>
		<?php endforeach; ?>
		</ul>
	</fieldset>
</div>

<p>
<button type="submit">__Create Permission Connection__</button>
</p>

</form>

<?php
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';
		$post = $request->data;

	// VALIDATE POST
		if( ! (isset($post['role_id']) && strlen($post['role_id'])) ){
			$response->formErrors['role_id'] = '__Required Field__';
		}
		if( ! (isset($post['wp_user_id']) && strlen($post['wp_user_id'])) ){
			$response->formErrors['wp_user_id'] = '__Required Field__';
		}

		if( $response->formErrors ){
			return $response;
		}

		$roleId = $post['role_id'];
		$wpUserId = $post['wp_user_id'];

		$model = $this->model->construct();
		$model->role_id = $roleId;
		$model->wp_user_id = $wpUserId;

		$res = $this->command->create( $model );
		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->redirect = '..';
		$response->addMessage( '__Permission Connection Saved__' );

		return $response;
	}
}