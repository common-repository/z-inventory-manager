<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_ extends _PW1
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

	// ACL
		$ret[] = array( '*',	'transactions*',	__CLASS__ . 'Transactions_Acl@check', -1 );

	// /
		$ret[] = array( 'HEAD',	'',		__CLASS__ . 'Index@head' );

	// index
		$ret[] = array( 'GET',	'transactions',	__CLASS__ . 'Transactions_Index@beforeGet', -1 );
		$ret[] = array( '*',		'transactions',	__CLASS__ . 'Transactions_Index@*' );

	// index-filter
		$ret[] = array( 'GET',	'transactions',	__CLASS__ . 'Transactions_Index0Filter@beforeGet', -1 );
		$ret[] = array( 'GET',	'transactions',	__CLASS__ . 'Transactions_Index0Filter@get' );
		$ret[] = array( 'POST',	'transactions',	__CLASS__ . 'Transactions_Index0Filter@post' );

	// new
		$ret[] = array( '*',		'transactions/new-{type}',	__CLASS__ . 'Transactions0New_Index@*' );

	// zoom
		$ret[] = array( '*',		'transactions/{id}',		__CLASS__ . 'Transactions0Id_Index@*' );

		$ret[] = array( 'GET',	'transactions/{id}',		__CLASS__ . 'Transactions0Id_Index0Details@get' );
		$ret[] = array( 'GET',	'transactions/{id}',		__CLASS__ . 'Transactions0Id_Index0Contact@get' );
		$ret[] = array( 'GET',	'transactions/{id}',		__CLASS__ . 'Transactions0Id_Index0Lines@get' );

		$ret[] = array( '*',	'transactions/{id}/edit',		__CLASS__ . 'Transactions0Id0Edit_Index@*' );
		$ret[] = array( '*',	'transactions/{id}/lines',		__CLASS__ . 'Transactions0Id0Lines_Index@*' );
		$ret[] = array( '*',	'transactions/{id}/delete',	__CLASS__ . 'Transactions0Id0Delete_Index@*' );

	// ITEM SELECTOR FOR NEW
		// $ret[] = array( '*',	'transactions/new-*/items*',	__CLASS__ . 'Transactions0New0Items_Prepare@', 4 );
		$ret[] = array( '*',	'transactions/new-*/items{*}',	'>items/selector{1}' );

	// ITEM SELECTOR FOR EXISTING
		$ret[] = array( '*',	'transactions/{id}/lines/new{*}',	'>items/selector{2}' );

	// CONTACT SELECTOR FOR NEW
		$ret[] = array( '*',	'transactions/new-sale/contact{*}',			'>contacts/selector{1}?filter-type[]=customer' );
		$ret[] = array( '*',	'transactions/new-purchase/contact{*}',	'>contacts/selector{1}?filter-type[]=supplier' );

	// CONTACT SELECTOR FOR EXISTING
		$ret[] = array( '*',		'transactions/{id}/contact',					__CLASS__ . 'Transactions0Id0Contact_Index@*' );
		$ret[] = array( 'GET',	'transactions/{id}/contact/change{*}',		__CLASS__ . 'Transactions0Id0Contact_Index@beforeGetSelector', -2 );
		$ret[] = array( '*',		'transactions/{id}/contact/change{*}',		'>contacts/selector{2}' );

	// ADMIN/SETTINGS
		$ret[] = array( 'HEAD',	'admin/settings',						__CLASS__ . 'Admin0Settings_Index@head' );
		$ret[] = array( '*',		'admin/settings/transactions',	__CLASS__ . 'Admin0Settings0Transactions_Index@*' );

		return $ret;
	}
}