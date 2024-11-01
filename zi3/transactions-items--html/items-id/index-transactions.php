<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions0Items00Html_Items0Id_Index0Transactions extends _PW1
{
	public $aclQuery;
	public $q;
	public $financeWidget;
	public $itemsQuery;
	public $transactionLinesQuery;
	public $transactionsQuery;
	public $transactionsWidget;

	public function __construct(
		PW1_ZI3_Acl_Query $aclQuery,
		PW1_Q $q,
		PW1_ZI3_Finance00Html_Widget $financeWidget,
		PW1_ZI3_Items_Query $itemsQuery,
		PW1_ZI3_Transactions_Lines_Query $transactionLinesQuery,
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions00Html_Widget $transactionsWidget
	)
	{}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		if( ! $this->aclQuery->userCan($request->currentUserId, 'manage_transactions') ){
			return;
		}

		$id = $request->args[0];
		$item = $this->itemsQuery->findById( $id );

		$q = $this->q
			->orderBy( 'created_date', 'DESC' )
			->limit( 3 )
			;
		$q = $this->transactionsQuery->whereItem( $item, $q );
		$transactions = $this->transactionsQuery->find( $q );

		ob_start();
?>

<section>

<h2>__Recent Transactions__</h2>

<ul class="pw-box">
<?php if( $transactions ) : ?>
	<?php foreach( $transactions as $e ) : ?>
		<li>
			<?php echo $this->transactionsWidget->presentTitle( $e, TRUE ); ?>
		</li>
	<?php endforeach; ?>
<?php else : ?>
	<li>
		__No Results__
	</li>
<?php endif; ?>
</ul>

<nav>
<ul class="pw-inline">
	<li>
		<a href="URI:transactions?filter-item=<?php echo $item->id; ?>"><span>&#9776;</span>__View All Transactions__</a>
	</li>
	<li>
		<a href="URI:transactions/new-<?php echo PW1_ZI3_Transactions_Model::TYPE_PURCHASE; ?>?items[]=<?php echo $item->id; ?>"><span>+</span>__New Purchase__</a>
	</li>
	<li>
		<a href="URI:transactions/new-<?php echo PW1_ZI3_Transactions_Model::TYPE_SALE; ?>?items[]=<?php echo $item->id; ?>"><span>+</span>__New Sale__</a>
	</li>
</ul>
</nav>

</section>

<?php 
		$ret = trim( ob_get_clean() );

		$response->content .= $ret;
		return $response;
	}
}