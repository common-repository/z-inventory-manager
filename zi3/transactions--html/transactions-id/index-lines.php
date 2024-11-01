<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions0Id_Index0Lines extends _PW1
{
	public $q;
	public $financeCalculator;
	public $financeWidget;
	public $itemsQuery;
	public $itemsWidget;
	public $transactionsQuery;
	public $transactionLinesQuery;
	public $transactionsModel;
	public $transactionsWidget;

	public function __construct(
		PW1_Q $q,
		PW1_ZI3_Finance_Lib_Calculator $financeCalculator,
		PW1_ZI3_Finance00Html_Widget $financeWidget,
		PW1_ZI3_Items_Query $itemsQuery,
		PW1_ZI3_Items00Html_Widget $itemsWidget,
		PW1_ZI3_Transactions_Query $transactionsQuery,
		PW1_ZI3_Transactions_Lines_Query $transactionLinesQuery,
		PW1_ZI3_Transactions_Model $transactionsModel,
		PW1_ZI3_Transactions00Html_Widget $transactionsWidget
	)
	{}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->transactionsQuery->findById( $id );

		$lines = $this->transactionLinesQuery->findByTransaction( $model );

		$calc = $this->financeCalculator->construct();
		foreach( $lines as $line ) $calc->add( $line->qty * $line->price );
		$grandTotal = $calc->get();

		ob_start();
?>

<section>

<h2>__Items__</h2>

<?php if( ! $lines ) : ?>

<div class="pw-box">
	__No Items__
</div>

<?php else : ?>

<table>

<thead>
<tr>
	<td>__Item__</td>
	<td class="pw-col-1 pw-lg-align-right">__Quantity__</td>
	<td class="pw-col-2 pw-lg-align-right">__Price__</td>
	<td class="pw-col-2 pw-lg-align-right">__Total__</td>
</tr>
</thead>

<tbody>
<?php foreach( $lines as $line ) : ?>
	<?php $item = $this->itemsQuery->findById( $line->item_id ); ?>
	<tr>
		<td title="__Item__"><?php echo $this->itemsWidget->presentTitle( $item, TRUE ); ?></td>
		<td title="__Quantity__" class="pw-lg-align-right"><?php echo $line->qty; ?></td>
		<td title="__Price__" class="pw-lg-align-right"><?php echo $this->financeWidget->renderPrice( $line->price ); ?></td>
		<td title="__Total__" class="pw-lg-align-right"><?php echo $this->financeWidget->renderPrice( $line->qty * $line->price ); ?></td>
	</tr>
<?php endforeach; ?>
</tbody>

<tfoot>
<tr>
	<td colspan="3">__Total__</td>
	<td class="pw-lg-align-right"><?php echo $this->financeWidget->renderPrice( $grandTotal ); ?></td>
</tr>
</tfoot>

</table>

<nav>
<ul class="pw-inline">
<?php if( $lines ) : ?>
	<li>
		<a href="URI:./lines">__Edit Items__</a>
	</li>
<?php endif; ?>

<?php
$p = array();
$p['skip'] = array();
foreach( $lines as $e ) $p['skip'][] = $e->item_id;
$p['backparam'] = 'items';
?>
<li>
	<a href="URI:./lines/new?<?php echo http_build_query($p); ?>"><span>+</span>__Add Items__</a>
</li>
</ul>
</nav>

</section>

<?php endif; ?>

<?php
		$ret = trim( ob_get_clean() );

		$response->content .= $ret;
		return $response;
	}
}