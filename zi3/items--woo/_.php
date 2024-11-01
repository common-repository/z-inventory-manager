<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Woo_ extends _PW1
{
	public $settings;

	public function __construct(
		PW1_ $pw1,
		PW1_ZI3_Settings_ $settings
	)
	{
		if( ! function_exists('wc_get_products') ){
			return;
		}

	// persistency
		// $pw1
			// ->wrap( 'PW1_ZI3_Items_Crud@create',	__CLASS__ . 'Crud@create' )
			// ->wrap( 'PW1_ZI3_Items_Crud@read',		__CLASS__ . 'Crud@read' )
			// ->wrap( 'PW1_ZI3_Items_Crud@count',		__CLASS__ . 'Crud@count' )
			// ->wrap( 'PW1_ZI3_Items_Crud@update',	__CLASS__ . 'Crud@update' )
			// ->wrap( 'PW1_ZI3_Items_Crud@delete',	__CLASS__ . 'Crud@delete' )
			// ->wrap( 'PW1_ZI3_Items_Query@whereSearch',	__CLASS__ . 'Crud@whereSearch' )
			// ;

		// $pw1
			// ->wrap( 'PW1_ZI3_Items00Html_@linkEdit',	__CLASS__ . '@linkEdit' )
			// ->wrap( 'PW1_ZI3_Items00Html_@linkDelete',	__CLASS__ . '@linkDelete' )
			// ->wrap( 'PW1_ZI3_Items00Html_@linkNew',	__CLASS__ . '@linkNew' )
			// ;

	// settings
		$pw1
			->merge( 'PW1_Handle@routes', __CLASS__ . '@routes' )
			->merge( 'PW1_ZI3_Settings_@getDefaults',	__CLASS__ . '@settingsGetDefaults' )
			;
	}

	public function boot()
	{
		if( ! function_exists('wc_get_products') ){
			return;
		}

		$pw1 = $this->pw1;

		$integrateInventory = $this->settings->get( 'woo_integrate_inventory' );

		if( $integrateInventory ){
		// persistency
			$pw1
				->wrap( 'PW1_ZI3_Items_Crud@create',	__CLASS__ . 'Crud@create' )
				->wrap( 'PW1_ZI3_Items_Crud@read',		__CLASS__ . 'Crud@read' )
				->wrap( 'PW1_ZI3_Items_Crud@count',		__CLASS__ . 'Crud@count' )
				->wrap( 'PW1_ZI3_Items_Crud@update',	__CLASS__ . 'Crud@update' )
				->wrap( 'PW1_ZI3_Items_Crud@delete',	__CLASS__ . 'Crud@delete' )
				->wrap( 'PW1_ZI3_Items_Query@whereSearch',	__CLASS__ . 'Crud@whereSearch' )
				;

		// edit/delete inventory links
			$pw1
				->wrap( 'PW1_ZI3_Items00Html_@linkEdit',	__CLASS__ . '@linkEdit' )
				->wrap( 'PW1_ZI3_Items00Html_@linkDelete',	__CLASS__ . '@linkDelete' )
				->wrap( 'PW1_ZI3_Items00Html_@linkNew',	__CLASS__ . '@linkNew' )
				;
		}
	}

	public function linkNew()
	{
		$ret = admin_url( 'post-new.php?post_type=product' );
		return $ret;
	}

	public function linkEdit( $id )
	{
		$ret = get_edit_post_link( $id );
		return $ret;
	}

	public function linkDelete( $id )
	{
		return '';
	}

	public function routes()
	{
		$ret = array();

	// ADMIN/SETTINGS
		$ret[] = array( 'HEAD',	'admin/settings',			__CLASS__ . 'Admin0Settings_Index@head' );
		$ret[] = array( '*',		'admin/settings/woo',	__CLASS__ . 'Admin0Settings0Woo_Index@*' );

		return $ret;
	}

	public function settingsGetDefaults()
	{
		$ret = array();
		$ret['woo_integrate_inventory'] = 1;
		return $ret;
	}
}