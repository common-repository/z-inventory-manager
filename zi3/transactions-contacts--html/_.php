<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions0Contacts00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes', __CLASS__ . '@routes' )
			;
	}

	public function routes()
	{
		$ret = array();

		$ret[] = array( 'GET',	'contacts/{id}', 	__CLASS__ . 'Contacts0Id_Index@get' );

		return $ret;
	}
}