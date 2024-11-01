<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id0Edit_Index extends _PW1
{
	public $transactionsQuery;
	public $transactionsCommand;
	public $widget;
	public $form;

	public function __construct(
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions_Command $transactionsCommand,
		PW1_ZI3_Transactions00Html_Widget $widget,
		PW1_ZI3_Transactions00Html_Widget_Form $form
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
		$model = $this->transactionsQuery->findById( $id );

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
		$response->redirect = '..';

		$post = $request->data;

	// VALIDATE POST
		// $sure = isset( $post['sure'] ) ? $post['sure'] : FALSE;
		// if( ! $sure ){
			// $response->formErrors[ 'sure' ] = '__Required Field__';
		// }

		// $response->formErrors = $this->form->errors( $post );
		if( $response->formErrors ){
			return $response;
		}

		$id = $request->args[0];
		$model = $this->transactionsQuery->findById( $id );

		$newModel = clone $model;
		$newModel = $this->form->grab( $post, $newModel );

		$res = $this->transactionsCommand->update( $model, $newModel );

		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->redirect = '..';
		$response->addMessage( '__Transaction Updated__' );

		return $response;
	}
}