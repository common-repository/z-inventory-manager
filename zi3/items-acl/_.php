<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items0Acl_ extends _PW1
{
	public $acl;

	public function __construct(
		PW1_ZI3_Acl_ $acl
	)
	{}

	public function boot()
	{
		$this->acl
			->register( 'view_items',		FALSE,	'__View Inventory__' )
			->register( 'manage_items',	FALSE,	'__Manage Inventory__' )

			->ifOn( 'manage_items' )->thenOn( 'view_items' )
			;
	}
}