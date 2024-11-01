<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts00Html_Contacts0Id0Edit_Index extends _PW1
{
	public $query;
	public $command;
	public $form;
	public $widget;

	public function __construct(
		PW1_ZI3_Contacts_Query $query,
		PW1_ZI3_Contacts_Command $command,
		PW1_ZI3_Contacts00Html_Widget_Form $form,
		PW1_ZI3_Contacts00Html_Widget $widget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Edit__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->query->findById( $id );
		$model = $this->form->grab( $request->formValues, $model );

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
		$id = $request->args[0];
		$model = $this->query->findById( $id );

		$response->redirect = '.';
		$post = $request->data;

	// VALIDATE POST
		$response->formErrors = $this->form->errors( $post );
		if( $response->formErrors ){
			return $response;
		}

		$newModel = clone $model;
		$newModel = $this->form->grab( $post, $newModel );

		$res = $this->command->update( $model, $newModel );

		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->redirect = '..';
		$response->addMessage( '__Contact Updated__' );

		return $response;
	}
}