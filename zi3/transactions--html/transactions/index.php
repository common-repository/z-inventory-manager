<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Transactions_Index extends _PW1
{
	public $widgetPager;
	public $q;
	public $widget;
	public $tf;
	public $financeCalculator;
	public $financeWidget;
	public $model;
	public $command;
	public $transactionLinesQuery;
	public $query;

	public function __construct(
		PW1_Html_Widget_Pager $widgetPager,
		PW1_Q $q,
		PW1_ZI3_Transactions00Html_Widget $widget,
		PW1_Time_Format $tf,
		PW1_ZI3_Finance_Lib_Calculator $financeCalculator,
		PW1_ZI3_Finance00Html_Widget $financeWidget,

		PW1_ZI3_Transactions_Model $model,
		PW1_ZI3_Transactions_Command $command,

		PW1_ZI3_Transactions_Lines_Query $transactionLinesQuery,
		PW1_ZI3_Transactions_Query $query
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Transactions__';

		$response->menu[ '11-new-1' ] = array( './new-' . PW1_ZI3_Transactions_Model::TYPE_PURCHASE, '<span>+</span>__New Purchase__' );
		$response->menu[ '11-new-2' ] = array( './new-' . PW1_ZI3_Transactions_Model::TYPE_SALE, '<span>+</span>__New Sale__' );

		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';
		return $response;
	}

	public function beforeGet( PW1_Request $request, PW1_Response $response )
	{
		$request->params['*q'] = $this->q->construct();
		return $request;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$q = $request->params['*q'];

		// $type = $request->args[0];
		// if( PW1_ZI3_Transactions_Model::TYPE_SALE == $type )		$q->where( 'type', '=', PW1_ZI3_Transactions_Model::TYPE_SALE );
		// if( PW1_ZI3_Transactions_Model::TYPE_PURCHASE == $type )	$q->where( 'type', '=', PW1_ZI3_Transactions_Model::TYPE_PURCHASE );

		// $q = $this->self->filterQuery( $q, $request );

		$totalCount = $this->query->count( $q );

		$perPage = 10;
		list( $limit, $offset ) = $this->widgetPager->getLimitOffset( $request->params, $perPage );

		$q
			->limit( $limit )
			->offset( $offset )
			;

		$models = $this->query->find( $q );

		$pagerView = $this->widgetPager->render( $totalCount, $limit, $offset );

		ob_start();
?>

<section>

<?php if( ! $models ) : ?>

	<article>
		__No Results__
	</article>

<?php else : ?>

<table>
<thead>
	<tr>
		<td>
			__Reference__
		</td>
		<td class="pw-col-2">
			__Date__
		</td>
		<td class="pw-col-1">
			__Items__
		</td>
		<td class="pw-col-2">
			__Total__
		</td>
	</tr>
</thead>

<tbody>
<?php foreach( $models as $model ) : ?>
	<tr>
		<td>
			<?php echo $this->widget->presentTitle( $model, TRUE ); ?>
		</td>
		<td>
			<?php echo $this->tf->formatDateWithWeekday( $model->created_date ); ?>
		</td>
		<td>
			<?php
			$lines = $this->transactionLinesQuery->findByTransaction( $model );
			$itemsQty = 0;
			foreach( $lines as $line ) $itemsQty += $line->qty;
			?>
			<?php echo $itemsQty; ?>
		</td>
		<td>
			<?php
			$calc = $this->financeCalculator->construct();
			foreach( $lines as $line ) $calc->add( $line->qty * $line->price );
			$grandTotal = $calc->get();
			?>
			<?php echo $this->financeWidget->renderPrice( $grandTotal ); ?>
		</td>
	</tr>
<?php endforeach; ?>
</tbody>

<tfoot>
</tfoot>

</table>
<?php endif; ?>

</section>

<?php if( strlen($pagerView) ) : ?>
<section>
	<?php echo $pagerView; ?>
</section>
<?php endif; ?>

<?php 
		$ret = trim( ob_get_clean() );

		$response->content = $ret;
		return $response;
	}
}