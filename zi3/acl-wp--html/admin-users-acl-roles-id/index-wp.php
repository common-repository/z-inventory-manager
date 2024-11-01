<?php
class PW1_ZI3_Acl0Wp00Html_Admin0Users0Acl0Roles0Id_Index0Wp extends _PW1
{
	public $q;
	public $usersWpWidget;
	public $query;

	public function __construct(
		PW1_Q	$q,
		PW1_ZI3_Users0Wp00Html_Widget	$usersWpWidget,
		PW1_ZI3_Acl0Wp_Connections_Query	$query
	)
	{}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$roleId = $request->args[0];

		$q = $this->q->where( 'role_id', '=', $roleId );
		$connections = $this->query->find( $q );

		ob_start();
?>

<section>

<h2>__WordPress Permission Connections__</h2>

<?php if( $connections ) : ?>
	<p>
	__This role is currently connected to the following WordPress roles and users.__
	</p>

	<ul class="pw-box">
	<?php foreach( $connections as $e ) : ?>
		<?php
		$wpUser = $e->wp_user_id ? get_user_by( 'id', $e->wp_user_id ) : NULL;
		$wpRole = $e->wp_role_id ? $e->wp_role_id : NULL;
		if( ! ($wpUser OR $wpRole) ) continue;
		?>
		<li>

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

		</li>
	<?php endforeach; ?>
	</ul>

<?php else : ?>

	<p>
	__This role isn't currently connected to any WordPress role or user.__
	</p>

<?php endif; ?>

<nav>
<ul class="pw-inline">
<li>
	<a href="URI:admin/users/acl-wp">__Edit WordPress Permission Connections__</a>
</li>
</ul>

</nav>

</section>

<?php
		$ret = trim( ob_get_clean() );
		$response->content .= $ret;
		return $response;
	}
}