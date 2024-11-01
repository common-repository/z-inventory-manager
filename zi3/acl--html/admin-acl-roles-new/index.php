<?php
class PW1_ZI3_Acl00Html_Admin0Acl0Roles0New_Index extends _PW1
{
	public $model;
	public $command;
	public $form;

	public function __construct(
		PW1_ZI3_Acl_Roles_Model $model,
		PW1_ZI3_Acl_Roles_Command $command,
		PW1_ZI3_Acl00Html_Widget_Roles_Form $form
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__New Role__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$model = $this->model->construct();
		ob_start();
?>

<form method="post" action="URI:.">

<?php echo $this->form->render( $model ); ?>

<p>
<button type="submit">__Add New Role__</button>
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

		$model = $this->model->construct();
		$model = $this->form->grab( $post, $model );

		$res = $this->command->create( $model );
		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->redirect = '..';
		$response->addMessage( '__Permission Role Saved__' );

		return $response;
	}
}