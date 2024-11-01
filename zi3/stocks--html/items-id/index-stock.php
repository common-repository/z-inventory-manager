<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Stocks00Html_Items0Id_Index0Stock extends _PW1
{
	public $q;
	public $stocksQuery;

	public function __construct(
		PW1_Q	$q,
		PW1_ZI3_Stocks_Query	$stocksQuery
	)
	{}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$itemId = $request->args[0];

		$q = $this->q->construct();
		$stocks = $this->stocksQuery->find();
		$stocks = array_filter( $stocks, function($e) use($itemId){ return ($e->item_id == $itemId); } );

		$qty = 0;
		foreach( $stocks as $stock ) $qty += $stock->qty;

		ob_start();
?>

<section>

<h2>__Current Stock__</h2>

<article>
<?php echo $qty; ?>
</article>

</section>

<?php 
		$ret = trim( ob_get_clean() );

		$response->content .= $ret;
		return $response;
	}
}