<?php
class PW1_ZI3_Acl00Html_Admin0Acl0Roles0Id0Delete_Index extends _PW1
{
	public $query;
	public $command;

	public function __construct(
		PW1_ZI3_Acl_Roles_Query	$query,
		PW1_ZI3_Acl_Roles_Command	$command
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Delete Permission Role__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->query->findById( $id );

		ob_start();
?>

<form method="post" action="URI:.">

<p>
__Are you sure?__
</p>

<nav>
	<ul class="pw-inline">
		<li>
			<button type="submit">__Confirm Delete__</button>
		</li>
		<li>
			<a href="URI:..">__Cancel__</a>
		</li>
	</ul>
</nav>

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
		if( $response->formErrors ){
			return $response;
		}

		$id = $request->args[0];
		$model = $this->query->findById( $id );

		$res = $this->command->delete( $model );
		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->redirect = '../..';
		$response->addMessage( '__Permission Role Deleted__' );

		return $response;
	}
}