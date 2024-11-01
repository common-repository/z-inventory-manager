<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0New_Index extends _PW1
{
	public $q;
	public $itemsQuery;
	public $itemsWidget;
	public $contactsQuery;
	public $contactsWidget;
	public $model;
	public $command;
	public $transactionLinesModel;
	public $transactionLinesCommand;
	public $widget;
	public $form;

	public function __construct(
		PW1_Q $q,

		PW1_ZI3_Items_Query $itemsQuery,
		PW1_ZI3_Items00Html_Widget $itemsWidget,

		PW1_ZI3_Contacts_Query $contactsQuery,
		PW1_ZI3_Contacts00Html_Widget $contactsWidget,

		PW1_ZI3_Transactions_Model $model,
		PW1_ZI3_Transactions_Command $command,

		PW1_ZI3_Transactions_Lines_Model $transactionLinesModel,
		PW1_ZI3_Transactions_Lines_Command $transactionLinesCommand,

		PW1_ZI3_Transactions00Html_Widget $widget,
		PW1_ZI3_Transactions00Html_Widget_Form $form
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$type = $request->args[0];
		if( PW1_ZI3_Transactions_Model::TYPE_SALE == $type )	$response->title = '__New Sale__';
		if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $type )	$response->title = '__New Purchase__';

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$type = $request->args[0];

		$lines = array();

		if( isset($request->params['items']) ){
			$itemIds = $request->params['items'];
			$items = $this->itemsQuery->findById( $itemIds );
			$qtys = isset( $request->params['qty'] ) ? $request->params['qty'] : array();
			$prices = isset( $request->params['price'] ) ? $request->params['price'] : array();

			for( $ii = 0; $ii < count($itemIds); $ii++ ){
				$itemId = $itemIds[ $ii ];
				if( ! isset($items[$itemId]) ) continue;

				$item = $items[$itemId];

				$qty = isset( $qtys[$ii] ) ? $qtys[$ii] : 1;

				if( isset($prices[$ii]) ){
					$price = $prices[$ii];
				}
				else {
					if( PW1_ZI3_Transactions_Model::TYPE_SALE == $type )		$price = $item->default_price;
					if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $type )	$price = $item->default_cost;
				}

				$line = $this->transactionLinesModel->construct();
				$line->item_id = $itemId;
				$line->qty = $qty;
				$line->price = $price;

				$lines[] = $line;
			}
		}

		// if( ! $items ){
			// $to = './items?backparam=items';
			// $response->redirect = $to;
			// return $response;
		// }

		$contact = NULL;
		if( isset($request->params['contact']) ){
			$contactId = $request->params['contact'];
			$contact = $this->contactsQuery->findById( $contactId );
		}

		$model = $this->model->construct();
		$model->type = $type;

		$ret = $this->self->render( $model, $lines, $contact );

		$response->content = $ret;
		return $response;
	}

	public function render( _PW1_ZI3_Transactions_Model $model, array $lines, $contact )
	{
		if( ! strlen($model->refno) ){
			$refno = $this->command->getNewRefno( $model );
			$model->refno = $refno;
		}

		$states = $this->model->getStates( $model );
		if( NULL === $model->state ){
			$state = current( $states );
			$model->state = $state;
		}

		if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $model->type )	$contactType = PW1_ZI3_Contacts_Model::TYPE_SUPPLIER;
		if( PW1_ZI3_Transactions_Model::TYPE_SALE == $model->type )			$contactType = PW1_ZI3_Contacts_Model::TYPE_CUSTOMER;

		ob_start();
?>

<form method="post" action="URI:.">

<?php echo $this->form->render( $model ); ?>

<?php if( $contactType ) : ?>
<div>
	<fieldset>
		<legend>
			<?php if( PW1_ZI3_Contacts_Model::TYPE_CUSTOMER == $contactType ) : ?>__Customer__<?php endif; ?>
			<?php if( PW1_ZI3_Contacts_Model::TYPE_SUPPLIER == $contactType ) : ?>__Supplier__<?php endif; ?>
		</legend>
		<input type="hidden" name="contact_id" value="<?php echo $contact ? $contact->id : 0; ?>">

		<?php if( $contact ) : ?>
			<article>
				<?php echo $this->contactsWidget->presentTitle( $contact, TRUE ); ?>
			</article>
		<?php endif; ?>

		<nav>
			<ul class="pw-inline">
			<?php if( $contact ) : ?>
				<li>
					<a href="URI:./contact?backparam=contact"><span>...</span>__Change__</a>
				</li>
				<li>
					<a href="URI:.?contact=NULL"><span>&times;</span>__Remove__</a>
				</li>
			<?php else : ?>
				<li>
					<a href="URI:./contact?backparam=contact"><span>...</span>__Select__</a>
				</li>
			<?php endif; ?>
			</ul>
		</nav>
	</fieldset>
</div>
<?php endif; ?>

<div>
<fieldset>
<legend>__Items__</legend>

<table>
	<tbody>
	<?php foreach( $lines as $e ) : ?>
		<?php
		$item = $this->itemsQuery->findById( $e->item_id );
		?>
		<tr class="pw-valign-middle">
			<td>
				<?php echo $this->itemsWidget->presentTitle( $item, TRUE ); ?>
			</td>

			<td title="__Quantity__" class="pw-col-1 pw-lg-align-right">
				<input placeholder="__Quantity__" title="__Quantity__" type="text" name="qty[<?php echo $e->item_id; ?>]" value="<?php echo $e->qty; ?>" class="pw-lg-align-right">
			</td>

			<td title="__Price__" class="pw-col-2 pw-lg-align-right">
				<input placeholder="__Price__" title="__Price__" type="text" name="price[<?php echo $e->item_id; ?>]" value="<?php echo $e->price; ?>" class="pw-lg-align-right">
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
</fieldset>

<nav>
	<ul class="pw-inline">
		<li>
			<?php
			$p = array();
			$p['skip'] = array();
			foreach( $lines as $e ) $p['skip'][] = $e->item_id;
			$p['backparam'] = 'items'
			?>
			<a href="URI:./items?<?php echo http_build_query($p); ?>"><span>+</span>__Add Items__</a>
		</li>
	</ul>
</nav>
</div>

<p>
	<?php if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $model->type ): ?>
		<button type="submit">__Create New Purchase__</button>
	<?php endif; ?>

	<?php if( PW1_ZI3_Transactions_Model::TYPE_SALE == $model->type ): ?>
		<button type="submit">__Create New Sale__</button>
	<?php endif; ?>
</p>

</form>

<?php 
		$ret = trim( ob_get_clean() );
		return $ret;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$type = $request->args[0];
		if( PW1_ZI3_Transactions_Model::TYPE_SALE == $type )		$type = PW1_ZI3_Transactions_Model::TYPE_SALE;
		elseif( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $type )	$type = PW1_ZI3_Transactions_Model::TYPE_PURCHASE;
		else $type = NULL;

		$response->redirect = '.';
		$post = $request->data;

	// VALIDATE POST
		$response->formErrors = $this->form->errors( $post );

		if( ! isset($post['state']) ){
			$response->formErrors['state'] = '__Required Field__';
		}

		if( ! isset($post['qty']) ){
			$response->formErrors['qty'] = '__Required Field__';
		}

		if( $response->formErrors ){
			return $response;
		}

		$itemIds = array();

		foreach( $post['qty'] as $itemId => $qty ){
			if( ! $qty ) continue;
			$itemIds[ $itemId ] = $itemId;
		}

		$items = $this->itemsQuery->findById( $itemIds );

		if( ! $items ){
			$response->addError( '__Items Required__' );
			return $response;
		}

		$lines = array();
		foreach( $items as $item ){
			$line = $this->transactionLinesModel->construct();

			$line->transaction_id = 0;
			$line->item_id = $item->id;
			$line->qty = $post['qty'][$item->id];
			if( isset($post['price'][$item->id]) ){
				$line->price = $post['price'][$item->id];
			}

			$line = $this->transactionLinesCommand->create( $line );

			if( $line instanceof PW1_Error ){
				$response->addError( $line->getMessage() );
				return $response;
			}

			$lines[ $line->id ] = $line;
		}

		if( ! $lines ){
			$response->addError( '__Items Required__' );
			return $response;
		}

		$model = $this->model->construct();
		$model = $this->form->grab( $post, $model );
		$model->type = $type;

		if( array_key_exists('contact_id', $post) ){
			$model->contact_id = $post['contact_id'];
		}

		$model = $this->command->create( $model );

		if( $model instanceof PW1_Error ){
			$response->addError( $model->getMessage() );

			foreach( $lines as $line ){
				$this->transactionLinesCommand->delete( $line );
			}

			return $response;
		}

		foreach( $lines as $line ){
			$newLine = clone $line;
			$newLine->transaction_id = $model->id;
			$this->transactionLinesCommand->update( $line, $newLine );
		}

		$response->redirect = '../' . $model->id;
		$response->addMessage( '__Transaction Created__' );

		return $response;
	}
}