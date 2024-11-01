<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts0Acl_ extends _PW1
{
	public $acl;

	public function __construct(
		PW1_ZI3_Acl_ $acl
	)
	{}

	public function boot()
	{
		$this->acl
			->register( 'view_contacts',		FALSE, '__View Contacts__' )
			->register( 'manage_contacts',	FALSE, '__Manage Contacts__' )

			->ifOn( 'manage_contacts' )->thenOn( 'view_contacts' )
			;
	}
}