<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items0Id0Edit_Post extends _PW1
{
	public $form;
	public $query;
	public $command;

	public function __construct(
		PW1_ZI3_Items00Html_Widget_Form $form,
		PW1_ZI3_Items_Query $query,
		PW1_ZI3_Items_Command $command
	)
	{}

	public function __invoke( PW1_Request $request, PW1_Response $response )
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
		$response->addMessage( '__Item Saved__' );

		return $response;
	}
}