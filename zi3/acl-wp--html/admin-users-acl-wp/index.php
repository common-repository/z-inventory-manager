<?php
class PW1_ZI3_Acl0Wp00Html_Admin0Users0Acl0Wp_Index extends _PW1
{
	public $aclQuery;
	public $q;
	public $query;
	public $command;
	public $rolesQuery;
	public $rolesWidget;
	public $usersWpWidget;

	public function __construct(
		PW1_ZI3_Acl_Query $aclQuery,

		PW1_Q $q,
		PW1_ZI3_Acl0Wp_Connections_Query $query,
		PW1_ZI3_Acl0Wp_Connections_Command $command,

		PW1_ZI3_Acl_Roles_Query $rolesQuery,
		PW1_ZI3_Acl00Html_Widget_Roles $rolesWidget,

		PW1_ZI3_Users0Wp00Html_Widget $usersWpWidget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__WordPress Permission Connections__';

		$response->menu[ '11-new-1' ] = array( './newrole', '<span>+</span>__Add New For WordPress Role__</a>' );
		$response->menu[ '11-new-2' ] = array( './newuser', '<span>+</span>__Add New For WordPress User__</a>' );

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$q = $this->q->construct();
		$models = $this->query->find( $q );

		$bulkForm = FALSE;
		reset( $models );
		foreach( $models as $e ){
			if( $e->id > 0 ){
				$bulkForm = TRUE;
				break;
			}
		}

		reset( $models );

		ob_start();
?>

<p>
__Here we define how WordPress users and roles can access our plugin.__
</p> 

<?php if( $bulkForm ) : ?>
<form method="post" action="URI:.">
<?php endif; ?>

<table>
<thead>
	<tr>
		<?php if( $bulkForm ) : ?>
			<td class="pw-col-1 pw-lg-align-center">__Select__</td>
		<?php endif; ?>
		<td class="pw-col-6">__WordPress__</td>
		<td class="pw-col-2">__Plugin Role__</td>
		<td class="pw-col-3">__Notes__</td>
	</tr>
</thead>

<tbody>
<?php foreach( $models as $e ) : ?>
	<?php
	$role = $this->rolesQuery->findById( $e->role_id );
	if( ! $role ) continue;

	$wpUser = $e->wp_user_id ? get_user_by( 'id', $e->wp_user_id ) : NULL;
	$wpRole = $e->wp_role_id ? $e->wp_role_id : NULL;
	if( ! ($wpUser OR $wpRole) ) continue;
	
	$editable = TRUE;
	if( $e->id < 0 ){
		$editable = FALSE;
	}
	?>

	<tr>
		<?php if( $bulkForm ) : ?>
		<td class="pw-lg-align-center">
			<?php if( $editable ) : ?>
				<label>
					<input type="checkbox" name="id[]" value="<?php echo $e->id; ?>" title="__Select__"><span class="pw-lg-hide">__Select__</span>
				</label>
			<?php endif; ?>
		</td>
		<?php endif; ?>

		<td title="__WordPress__">
			<?php if( $wpRole ) : ?>
				<ul class="pw-xs-inline">
					<li class="pw-muted2">__Role__</li>
					<li><?php echo $this->usersWpWidget->presentWpRole( $wpRole ); ?></li>
				</ul>
			<?php endif; ?>

			<?php if( $wpUser ) : ?>
				<ul class="pw-xs-inline">
					<li class="pw-muted2">__User__</li>
					<li><?php echo $this->usersWpWidget->presentWpUser( $wpUser, TRUE ); ?></li>
				</ul>
			<?php endif; ?>
		</td>

		<td title="__Plugin Role__">
			<?php echo $this->rolesWidget->presentTitle( $role, TRUE ); ?>
		</td>

		<td title="__Notes__">
			<?php if( $e->id < 1 ) : ?>
				<em>__Built-In Connection__</em>
			<?php endif; ?>
		</td>

	</tr>
<?php endforeach; ?>
</tbody>
</table>

<?php if( $bulkForm ) : ?>

<nav>
	<ul class="pw-inline">
		<li>
			__With Selected__
		</li>
		<li>
			<button type="submit"><i>&times;</i> __Remove Connection__</button>
		</li>
	</ul>
</nav>


</form>
<?php endif; ?>

<?php
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';
		$post = $request->data;

		$ids = isset( $post['id'] ) ? $post['id'] : array();
		if( ! $ids ) return $response;

	// VALIDATE POST
		if( $response->formErrors ){
			return $response;
		}

		$models = $this->query->findById( $ids );

		foreach( $models as $model ){
			$res = $this->command->delete( $model );
			if( $res instanceof PW1_Error ){
				$response->addError( $res->getMessage() );
			}
			else {
				$response->addMessage( '__Permission Connection Removed__' );
			}
		}

		$response->redirect = '.';
		return $response;
	}
}