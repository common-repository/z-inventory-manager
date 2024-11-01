<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id0Contact_Index extends _PW1
{
	public $transactionsQuery;
	public $transactionsCommand;
	public $contactsQuery;
	public $contactsWidget;

	public function __construct(
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions_Command $transactionsCommand,
		PW1_ZI3_Contacts_Query $contactsQuery,
		PW1_ZI3_Contacts00Html_Widget $contactsWidget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$transactionId = $request->args[0];
		$model = $this->transactionsQuery->findById( $transactionId );

		if( isset($request->params['id']) ){
			$newContactId = $request->params['id'];

			if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $model->type ) $response->title = '__Change Supplier__';
			if( PW1_ZI3_Transactions_Model::TYPE_SALE == $model->type ) $response->title = '__Change Customer__';
		}

		return $response;
	}

	public function beforeGetSelector( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->transactionsQuery->findById( $id );

		if( PW1_ZI3_Transactions_Model::TYPE_SALE == $model->type ) $request->params['*filter-type'] = array( PW1_ZI3_Contacts_Model::TYPE_CUSTOMER );
		if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $model->type ) $request->params['*filter-type'] = array( PW1_ZI3_Contacts_Model::TYPE_SUPPLIER );

		return $request;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->transactionsQuery->findById( $id );

		$contactId = $model->contact_id;
		$contact = $contactId ? $this->contactsQuery->findById( $contactId ) : NULL;

		$changeContact = FALSE;
		$newContact = NULL;

		if( isset($request->params['id']) ){
			$newContactId = $request->params['id'];
			if( $newContactId != $contactId ){
				$changeContact = TRUE;
			}
			if( $newContactId ){
				$newContact = $this->contactsQuery->findById( $newContactId );
			}
		}

		ob_start();
?>

<?php if( $changeContact ) : ?>

<form method="post">
<div class="pw-box">
	<ul class="pw-inline">
		<?php if( $contact ) : ?>
			<li class="pw-line-through"><?php echo $this->contactsWidget->presentTitle( $contact, TRUE ); ?></li>
		<?php endif; ?>

		<?php if( $newContact ) : ?>
			<li class="pw-xs-hide">&rarr;</li><li class="pw-lg-hide">&darr;</li>
			<li><?php echo $this->contactsWidget->presentTitle( $newContact, TRUE ); ?></li>
		<?php endif; ?>
	</ul>
</div>

<div>
	<nav>
		<ul class="pw-inline">
			<li>
				<button type="submit">__Confirm Change__</button>
			</li>
			<li>
				<a href="URI:..">__Cancel__</a>
			</li>
		</ul>
	</nav>
</div>
</form>

<?php endif; ?>


<?php
		$ret = trim( ob_get_clean() );

		$response->content .= $ret;
		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';

		if( ! isset($request->params['id']) ){
			return $response;
		}

		$id = $request->args[0];
		$model = $this->transactionsQuery->findById( $id );

		$newModel = clone $model;

		$contactId = $request->params['id'];
		if( $contactId ){
			$contact = $this->contactsQuery->findById( $contactId );
			$newModel->contact_id = $contactId;
		}
		else {
			$newModel->contact_id = NULL;
		}

		$model = $this->transactionsCommand->update( $model, $newModel );

		if( $model instanceof PW1_Error ){
			$response->addError( $model->getMessage() );
			return $response;
		}

		$response->redirect = '..';
		$response->addMessage( '__Contact Changed__' );

		return $response;
	}
}