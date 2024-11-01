<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
	// EXTEND - SETTINGS
		$pw1
			->merge( 'PW1_ZI3_Settings_@getDefaults',	__CLASS__ . '@settingsGetDefaults' )
			;

		$pw1
			->listen( __CLASS__ . 'Query@find', 		__CLASS__ . 'Lines_Query@listenTransactionsFind' )
			->listen( __CLASS__ . 'Command@delete',	__CLASS__ . 'Lines_Command@listenTransactionDelete' )
			;
	}

	public function settingsGetDefaults()
	{
		$ret = array();

		$ret['transactions_purchase_ref_auto'] = TRUE;
		$ret['transactions_purchase_ref_auto_prefix'] = 'PO-';
		$ret['transactions_purchase_ref_auto_method'] = 'seq';

		$ret['transactions_sale_ref_auto'] = TRUE;
		$ret['transactions_sale_ref_auto_prefix'] = 'SO-';
		$ret['transactions_sale_ref_auto_method'] = 'seq';

		return $ret;
	}
}