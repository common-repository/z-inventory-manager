<?php
/*
Plugin Name: PlainInventory
Plugin URI: https://www.z-inventory-manager.com/
Description: Manage your inventory and keep track of purchases and sales.
Version: 3.1.6
Author: plainware.com
Author URI: https://www.z-inventory-manager.com/
Text Domain: z-inventory-manager3
Domain Path: /languages/
*/

include_once( __DIR__ . '/zi3-base.php' );

class Z_Inventory_Manager3 extends ZI3_Base
{
	public static function modules()
	{
		return PW1_ZI3_Apps0Z0Inventory0Manager_::modules();
	}

	public function boot()
	{
		$devFile = __DIR__ . '/dev.php';
		if( file_exists($devFile) ){
			include_once( $devFile );
		}

		if( ! class_exists('PW1_Autoload') ){
			include_once( __DIR__ . '/pw1/autoload.php' );
		}
		PW1_Autoload::registerDir( 'pw1_zi3_',	__DIR__ . '/zi3' );

		parent::boot();
	}
}

$zi3 = new Z_Inventory_Manager3( __FILE__ );