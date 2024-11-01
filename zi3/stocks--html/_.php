<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Stocks00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes', __CLASS__ . '@routes' )
			;

	// EXTEND ITEMS
		$pw1
			->merge( 'PW1_ZI3_Items00Html_Items_Get@content',	__CLASS__ . 'Items_Get@content' )
			;
	}

	public function routes()
	{
		$ret = array();

	// ITEMS/{ID}
		$ret[] = array( 'GET',	'items/{id}',	__CLASS__ . 'Items0Id_Index0Stock@get' );

		return $ret;
	}
}