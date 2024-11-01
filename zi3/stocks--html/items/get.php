<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Stocks00Html_Items_Get extends _PW1
{
	public $stocksQuery;

	public function __construct(
		PW1_ZI3_Stocks_Query $stocksQuery
	)
	{}

	public function content( _PW1_ZI3_Items_Model $model )
	{
		$ret = array();

		static $stocks = NULL;
		if( NULL === $stocks ){
			$stocks = $this->stocksQuery->find();
		}

		$qty = 0;
		$thisStocks = array_filter( $stocks, function($e) use($model){ return ($model->id == $e->item_id); } );
		foreach( $thisStocks as $e ) $qty += $e->qty;

		$ret['stock']['content']	= $qty;
		$ret['stock']['header']		= '__In Stock__';
		$ret['stock']['class']		= 'pw-col-1 pw-lg-align-right';

		return $ret;
	}
}