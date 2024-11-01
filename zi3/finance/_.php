<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Finance_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_ZI3_Settings_@getDefaults',	__CLASS__ . '@getDefaults' )
			;
	}

	public function getDefaults()
	{
		$ret = array();

		$ret['finance_price_format_before'] = '$';
		$ret['finance_price_format_number_decpoint'] = '.';
		$ret['finance_price_format_number_thousep'] = ',';
		$ret['finance_price_format_after'] = '';

		return $ret;
	}
}