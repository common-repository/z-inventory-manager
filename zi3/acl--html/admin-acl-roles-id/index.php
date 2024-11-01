<?php
class PW1_ZI3_Acl00Html_Admin0Acl0Roles0Id_Index extends _PW1
{
	public $query;
	public $command;
	public $form;

	public function __construct(
		PW1_ZI3_Acl_Roles_Query	$query,
		PW1_ZI3_Acl_Roles_Command	$command,
		PW1_ZI3_Acl00Html_Widget_Roles_Form	$form
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->query->findById( $id );
		$response->title = esc_html( $model->title );

		$response->menu[ '99-delete' ] = array( './delete', '<span>&times;</span>__Delete__' );

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->query->findById( $id );

		ob_start();
?>

<form method="post" action="URI:.">

<?php echo $this->form->render( $model ); ?>

<p>
<button type="submit">__Save__</button>
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
		$response->formErrors = $this->form->errors( $post );
		if( $response->formErrors ){
			return $response;
		}

		$id = $request->args[0];
		$model = $this->query->findById( $id );

		$newModel = clone $model;
		$newModel = $this->form->grab( $post, $newModel );

		$res = $this->command->update( $model, $newModel );
		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->redirect = '..';
		$response->addMessage( '__Permission Role Saved__' );

		return $response;
	}
}