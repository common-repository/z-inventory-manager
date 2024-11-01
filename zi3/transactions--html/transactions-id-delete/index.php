<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id0Delete_Index extends _PW1
{
	public $transactionsQuery;
	public $transactionsCommand;
	public $transactionLinesQuery;
	public $transactionsModel;
	public $transactionsWidget;

	public function __construct(
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions_Command $transactionsCommand,
		PW1_ZI3_Transactions_Lines_Query $transactionLinesQuery,
		PW1_ZI3_Transactions_Model $transactionsModel,
		PW1_ZI3_Transactions00Html_Widget $transactionsWidget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->transactionsQuery->findById( $id );

		if( PW1_ZI3_Transactions_Model::TYPE_SALE == $model->type ) $title = '__Delete Sale__';
		if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $model->type ) $title = '__Delete Purchase__';

		$response->title = $title;

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->transactionsQuery->findById( $id );

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

		$res = $this->transactionsCommand->delete( $model );
		if( $res instanceof PW1_Error ){
			$response->addError( $res->getMessage() );
			return $response;
		}

		$response->redirect = '../..';
		$response->addMessage( '__Transaction Deleted__' );

		return $response;
	}
}