<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id0Lines_Index extends _PW1
{
	public $q;
	public $itemsQuery;
	public $itemsWidget;
	public $transactionsModel;
	public $transactionsQuery;
	public $transactionLinesModel;
	public $transactionLinesQuery;
	public $transactionLinesCommand;
	public $transactionsItemsQuery;
	public $widget;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Items_Query $itemsQuery,
		PW1_ZI3_Items00Html_Widget $itemsWidget,
		PW1_ZI3_Transactions_Model $transactionsModel,
		PW1_ZI3_Transactions_Query $transactionsQuery,

		PW1_ZI3_Transactions_Lines_Model $transactionLinesModel,
		PW1_ZI3_Transactions_Lines_Query $transactionLinesQuery,
		PW1_ZI3_Transactions_Lines_Command $transactionLinesCommand,

		PW1_ZI3_Transactions_X_Items_Query $transactionsItemsQuery,
		PW1_ZI3_Transactions00Html_Widget $widget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Edit Items__';

	// skip those already in this transaction
		$transactionId = $request->args[0];

		$q = $this->q->construct();
		$q = $this->transactionsItemsQuery->whereNotInTransaction( $transactionId, $q );
		$count = $this->itemsQuery->count( $q );

		if( $count ){
			$model = $this->transactionsQuery->findById( $transactionId );
			$lines = $this->transactionLinesQuery->findByTransaction( $model );
			$p = array();
			$p['skip'] = array();
			foreach( $lines as $e ) $p['skip'][] = $e->item_id;
			$p['backparam'] = 'items';
			$response->menu[ '12-new'] = array( './new?' . http_build_query($p), '<span>+</span>__Add Items__' );
		}

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$transactionId = $request->args[0];
		$transaction = $this->transactionsQuery->findById( $transactionId );

		$lines = $this->transactionLinesQuery->findByTransaction( $transactionId );

	// more items
		$itemIds = isset( $request->params['items'] ) ? $request->params['items'] : array();
		$items = $this->itemsQuery->findById( $itemIds );

		foreach( $items as $item ){
			$line = $this->transactionLinesModel->construct();
			$line->transaction_id = $transactionId;
			$line->item_id = $item->id;
			$line->qty = 1;
			$line->price = $item->default_cost;
			$lines[] = $line;
		}

		ob_start();
?>

<form method="post" action="URI:.">

<table>

<thead>
<tr>
	<td>__Item__</td>
	<td class="pw-col-2 pw-lg-align-right">
		__Quantity__
	</td>

	<td class="pw-col-3 pw-lg-align-right">
		__Price__
	</td>
</tr>
</thead>

<tbody>
<?php foreach( $lines as $e ) : ?>
	<?php
	$item = $this->itemsQuery->findById( $e->item_id );
	?>
	<tr>
		<td>
			<?php echo $this->itemsWidget->presentTitle( $item, TRUE ); ?>
		</td>
		<td title="__Quantity__" class="pw-lg-align-right">
			<input type="text" name="qty[<?php echo $e->item_id; ?>]" value="<?php echo $e->qty; ?>" class="pw-lg-align-right">
		</td>

		<td title="__Price__" class="pw-lg-align-right">
			<input type="text" name="price[<?php echo $e->item_id; ?>]" value="<?php echo $e->price; ?>" class="pw-lg-align-right">
		</td>
	</tr>
<?php endforeach; ?>
</tbody>

</table>

<p>
<button type="submit">__Update Items__</button>
</p>

</form>

<?php
		$ret = trim( ob_get_clean() );
		$response->content .= $ret;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '..';

		$id = $request->args[0];

		$model = $this->transactionsQuery->findById( $id );
		$lines = $this->transactionLinesQuery->findByTransaction( $model->id );

		$linesByItem = array();
		$newLinesByItem = array();
		foreach( $lines as $e ){
			$linesByItem[ $e->item_id ] = $e;  
			$newLinesByItem[ $e->item_id ] = clone $e;  
		}

		$post = $request->data;

		foreach( $post['qty'] as $itemId => $v ){
			if( ! isset($newLinesByItem[$itemId]) ){
				$item = $this->itemsQuery->findById( $itemId );

				$line = $this->transactionLinesModel->construct();
				$line->transaction_id = $model->id;
				$line->item_id = $item->id;
				$line->qty = 1;
				$line->price = $item->default_cost;
				$newLinesByItem[$itemId] = $line;
			}

			if( $v > 0 ){
				$newLinesByItem[ $itemId ]->qty = $v;
			}
			else {
				$newLinesByItem[ $itemId ] = NULL;
			}
		}

		if( isset($post['price']) ){
			foreach( $post['price'] as $itemId => $v ){
				if( isset($newLinesByItem[$itemId]) ){
					$newLinesByItem[ $itemId ]->price = $v;
				}
			}
		}

		foreach( $newLinesByItem as $itemId => $newLine ){
			if( $newLine ){
				if( isset($linesByItem[$itemId]) ){
					$res = $this->transactionLinesCommand->update( $linesByItem[$itemId], $newLine );
				}
				else {
					$res = $this->transactionLinesCommand->create( $newLine );
				}
			}
			else {
				$res = $this->transactionLinesCommand->delete( $linesByItem[$itemId] );
			}

			if( $res instanceof PW1_Error ){
				$response->addError( $res->getMessage() );
				return $response;
			}
		}

		$response->redirect = '..';
		$response->addMessage( '__Transaction Items Updated__' );

		return $response;
	}
}