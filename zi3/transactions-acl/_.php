<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions0Acl_ extends _PW1
{
	public $acl;

	public function __construct(
		PW1_ZI3_Acl_	$acl
	)
	{}

	public function boot()
	{
		$this->acl
			->register( 'manage_transactions', FALSE, '__Manage Transactions__' )
			;

		$this->acl
			->ifOn( 'manage_transactions' )->thenOn( 'view_contacts' )
			->ifOn( 'manage_transactions' )->thenOn( 'view_items' )
			;
	}
}