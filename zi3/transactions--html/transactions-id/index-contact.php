<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id_Index0Contact extends _PW1
{
	public $q;
	public $contactsQuery;
	public $contactsWidget;
	public $transactionsQuery;
	public $transactionsModel;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Contacts_Query $contactsQuery,
		PW1_ZI3_Contacts00Html_Widget $contactsWidget,
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions_Model $transactionsModel
	)
	{}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->transactionsQuery->findById( $id );

		$contactId = $model->contact_id;
		$contact = $contactId ? $this->contactsQuery->findById( $contactId ) : NULL;

		if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $model->type ) $title = '__Supplier__';
		if( PW1_ZI3_Transactions_Model::TYPE_SALE == $model->type ) $title = '__Customer__';

		ob_start();
?>

<section>

<h2>
<?php if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $model->type ) : ?>__Supplier__<?php endif; ?>
<?php if( PW1_ZI3_Transactions_Model::TYPE_SALE == $model->type ) : ?>__Customer__<?php endif; ?>
</h2>

<article>
	<?php if( $contact ) : ?>
		<?php echo $this->contactsWidget->presentTitleDetails( $contact, TRUE ); ?>
	<?php else : ?>
		__N/A__
	<?php endif; ?>
</article>

<nav>
<ul class="pw-inline">
<?php if( $contact ) : ?>
	<li>
		<a href="URI:./contact/change?backparam=id"><span>...</span>__Change__</a>
	</li>
	<li>
		<a href="URI:./contact?id=0"><span>&times;</span>__Remove__</a>
	</li>
<?php else : ?>
	<li>
		<a href="URI:./contact/change?backparam=id"><span>...</span>__Select__</a>
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