<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions0Contacts00Html_Contacts0Id_Index extends _PW1
{
	public $aclQuery;
	public $q;
	public $financeWidget;
	public $contactsQuery;
	public $transactionLinesQuery;
	public $transactionsQuery;
	public $transactionsWidget;

	public function __construct(
		PW1_ZI3_Acl_Query $aclQuery,
		PW1_Q $q,
		PW1_ZI3_Finance00Html_Widget $financeWidget,
		PW1_ZI3_Contacts_Query $contactsQuery,
		PW1_ZI3_Transactions_Lines_Query $transactionLinesQuery,
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions00Html_Widget $transactionsWidget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		if( ! $this->aclQuery->userCan($request->currentUserId, 'manage_transactions') ){
			return;
		}

		$contactId = $request->args[0];
		$contact = $this->contactsQuery->findById( $contactId );

		$q = $this->q->where( 'contact_id', '=', $contactId  );
		$countTransactions = $this->transactionsQuery->count( $q );

		if( $countTransactions ){
			$response->menu[ '11-view' ] = array( 'transactions?filter-contact=' . $contactId, '__View All Transactions__' );
		}

		if( $contact->is_supplier ){
			$response->menu[ '21-new-purchase' ]	= array( 'transactions/new-' . PW1_ZI3_Transactions_Model::TYPE_PURCHASE . '?contact=' . $contactId, '<span>+</span>__New Purchase__' );
		}
		if( $contact->is_customer ){
			$response->menu[ '22-new-sale' ]			= array( 'transactions/new-' . PW1_ZI3_Transactions_Model::TYPE_SALE . '?contact=' . $contactId, '<span>+</span>__New Sale__' );
		}

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		if( ! $this->aclQuery->userCan($request->currentUserId, 'manage_transactions') ){
			return;
		}

		$contactId = $request->args[0];
		$contact = $this->contactsQuery->findById( $contactId );

		$q = $this->q
			->orderBy( 'created_date', 'DESC' )
			->limit( 3 )
			;
		$q->where( 'contact_id', '=', $contactId  );

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
	<?php if( $contact->is_supplier ) : ?>
		<li>
			<a href="URI:transactions/new-<?php echo PW1_ZI3_Transactions_Model::TYPE_PURCHASE; ?>?contact=<?php echo $contact->id; ?>"><span>+</span>__New Purchase__</a>
		</li>
	<?php endif; ?>

	<?php if( $contact->is_customer ) : ?>
		<li>
			<a href="URI:transactions/new-<?php echo PW1_ZI3_Transactions_Model::TYPE_SALE; ?>?contact=<?php echo $contact->id; ?>"><span>+</span>__New Sale__</a>
		</li>
	<?php endif; ?>

</ul>
</nav>

</section>

<?php 
		$ret = trim( ob_get_clean() );

		$response->content .= $ret;
		return $response;
	}
}