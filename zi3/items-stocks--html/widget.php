<?php
class PW1_ZI3_Items0Stocks00Html_Widget extends _PW1
{
	public $q;
	public $stocksQuery;

	public function __construct(
		PW1_Q	$q,
		PW1_ZI3_Stocks_Query	$stocksQuery
	)
	{}

	public function content( _PW1_ZI3_Items_Model $model )
	{
		$ret = array();
		$ret[ '31-stock' ] = $this->self->presentStock( $model );
		return $ret;
	}

	public function presentStock( _PW1_ZI3_Items_Model $model )
	{
		$itemId = $model->id;

		$q = $this->q->construct();
		$stocks = $this->stocksQuery->find();
		$stocks = array_filter( $stocks, function($e) use($itemId){ return ($e->item_id == $itemId); } );

		$qty = 0;
		foreach( $stocks as $stock ) $qty += $stock->qty;

		ob_start();
?>

<ul class="pw-xs-inline">
<li>
	__In Stock__
</li>
<?php if( $qty > 0 ) : ?>
	<li class="pw-color-olive">
		<?php echo $qty; ?>
	</li>
<?php else : ?>
	<li class="pw-color-red">
		<?php echo $qty; ?>
	</li>
<?php endif; ?>
</ul>

<?php
		return trim( ob_get_clean() );
	}
}
