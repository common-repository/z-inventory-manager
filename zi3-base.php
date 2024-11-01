<?php
if (! defined('ABSPATH')) exit; // Exit if accessed directly

if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
	add_action( 'admin_notices',
		create_function( '',
			"echo '<div class=\"error\"><p>" .
			__('PlainInventory requires PHP 5.3 to function properly. Please upgrade PHP or deactivate PlainInventory.', 'z-inventory-manager') ."</p></div>';"
			)
	);
	return;
}

if( ! class_exists('ZI3_Base') ){
class ZI3_Base
{
	protected $pw1Param = 'pw1';
	protected $myAdminLabel = 'PlainInventory';
	protected $myAdminPage = 'z-inventory-manager3';
	protected $myShortPage = 'zi3';
	protected $pw1SlugParam = 'zi3a';

	protected $pw1;
	protected $handleResult;
	protected $pluginFile;

	public function __construct( $pluginFile )
	{
		$this->pluginFile = $pluginFile;

		add_action( 'admin_init', array($this, 'adminInit') );
		add_action( 'admin_menu', array($this, 'adminMenu') );
		add_action( 'admin_menu', array($this, 'adminSubmenu') );

		add_action( 'init', array($this, 'intercept') );
		add_filter('plugin_row_meta', array($this, 'pluginLinks'), 10, 2);
	}

	public function pluginLinks( $pluginMeta, $pluginFile )
	{
// echo basename($pluginFile) . ' VS ' . basename($this->pluginFile) . "<br>";
// return $pluginMeta;

		$isRelevant = ( basename($pluginFile) == basename($this->pluginFile) );
		if( ! $isRelevant ){
			return $pluginMeta;
		}

		if( ! current_user_can('update_plugins') ){
			return $pluginMeta;
		}

		$adminUrl = get_admin_url() . 'admin.php?page=' . $this->myAdminPage;

		$this->pw1();
		$uri = PW1_Uri::construct( $this->myShortPage );
		$uri = $uri::fromString( $uri, $adminUrl );

		$slug = 'acl-wp-reset';
		$newUri = $uri::setSlug( $uri, $slug );
		$to = $uri::toString( $newUri );

		$linkText = 'Reset Permissions';
		$pluginMeta[] = sprintf('<a href="%s">%s</a>', esc_attr($to), $linkText);

		return $pluginMeta;
	}

	public function pw1()
	{
		if( NULL === $this->pw1 ) $this->boot();
		return $this->pw1;
	}

	public function boot()
	{
		$this->pw1 = new PW1_;

		// $devFile = __DIR__ . '/dev.php';
		// if( file_exists($devFile) ){
			// include_once( $devFile );
			// $this->pw1->make( 'PW1_ZI3_Dev' );
		// }

	// init
		global $wpdb;

		if( is_multisite() ){
			// $shareDatabase = get_site_option( 'locatoraid_share_database', 0 );
			$shareDatabase = FALSE;
			$dbPrefix = $shareDatabase ? $wpdb->base_prefix : $wpdb->prefix;
		}
		else {
			$dbPrefix = $wpdb->prefix;
		}

	// prefix database tables
		$this->pw1->wrap( 'PW1_Sql_@tableName', function( $tableName ) use( $dbPrefix ){
			return $dbPrefix . $tableName;
		});

		$pluginFile = $this->pluginFile;

	// plugin file to layout for assets
		$this->pw1->wrap( 'PW1_ZI3_App0Wp_@pluginFile', function() use( $pluginFile ){
			return $pluginFile;
		});

	// translate
		$pluginDir = dirname( $pluginFile );
		$this->pw1->wrap( 'PW1_ZI3_App0Wp_Translate@pluginDir', function() use( $pluginDir ){
			return $pluginDir;
		});

		$translateDomain = 'z-inventory-manager3';
		$this->pw1->wrap( 'PW1_ZI3_App0Wp_Translate@domain', function() use( $translateDomain ){
			return $translateDomain;
		});

		$modules = static::modules();
		$this->pw1->boot( $modules );
	}

	public function adminMenu()
	{
		// $mainLabel = get_site_option( $this->myPage . '_menu_title' );
		// if( ! strlen($mainLabel) ){
			// $mainLabel = $this->adminMenuLabel;
		// }

		$mainLabel = $this->myAdminLabel;

		$menuIcon = isset($this->menuIcon) ? $this->menuIcon : NULL;
		$menuIcon = 'dashicons-clipboard';
		$menuSlug = $this->myAdminPage;

		$requireCap = 'read';
		$page = add_menu_page(
			$mainLabel,
			$mainLabel,
			$requireCap,
			$menuSlug,
			array( $this, 'render' ),
			$menuIcon,
			31
			);
	}

	public function adminInit()
	{
		if( ! $this->isMeAdmin() ){
			return;
		}

		$uri = PW1_Uri::construct( $this->myShortPage );
		$uri = $uri::fromString( $uri, $uri::getCurrent() );

		$request = PW1_Request::init( $uri );
		$request->misc[ 'pluginFile' ] = $this->pluginFile;
		$response = $this->pw1()->respond( $request );
		$this->handleResult = $response->content;
	}

	public function adminSubmenu()
	{
		global $submenu;

		$this->pw1();

		$menuRequest = PW1_Request::construct();
		$menuRequest->method = 'HEAD';
		$menuRequest->slug = '';

		$menuSlug = $this->myAdminPage;

		$response = $this->pw1()->respond( $menuRequest );

		$menuItems = $response->menu;
		if( ! $menuItems ){
			remove_menu_page( $menuSlug );
			return;
		}

		ksort( $menuItems );

		$mySubmenuCount = 0;

		$translate = $this->pw1()->make( 'PW1_ZI3_App_Translate' );

		$adminUrl = get_admin_url() . 'admin.php?page=' . $menuSlug;

		$uri = PW1_Uri::construct( $this->myShortPage );
		$uri = $uri::fromString( $uri, $adminUrl );

		foreach( $menuItems as $item ){
			if( ! is_array($item) ){
				continue;
			}

			list( $slug, $label ) = $item;
			$label = $translate->translateText( $label );

			if( is_string($slug) && ('http' == substr($slug, 0, strlen('http'))) ){
				$to = $slug;
			}
			else {
				$newUri = $uri::setSlug( $uri, $slug );
				$to = $uri::toString( $newUri );
			}

			remove_submenu_page( $menuSlug, $to );

			$ret = add_submenu_page(
				$menuSlug,					// parent
				$label,						// page_title
				$label,						// menu_title
				'read',						// capability
				$menuSlug . '-' . $slug,	// menu_slug
				'__return_null'
				);

			if( ! array_key_exists($menuSlug, $submenu) ){
				continue;
			}

			$mySubmenu = $submenu[$menuSlug];
			$mySubmenuIds = array_keys( $mySubmenu );
			$mySubmenuId = array_pop( $mySubmenuIds );

			$submenu[$menuSlug][$mySubmenuId][2] = $to;
			$mySubmenuCount++;
		}

		if( isset($submenu[$menuSlug][0]) && ($submenu[$menuSlug][0][2] == $menuSlug) ){
			unset($submenu[$menuSlug][0]);
		}

		if( ! $mySubmenuCount ){
			remove_menu_page( $menuSlug );
		}
	}

	public function render()
	{
		echo $this->handleResult;
	}

	public function isMeAdmin()
	{
		$ret = FALSE;
		if( ! isset($_REQUEST['page']) ){
			return $ret;
		}

		$page = sanitize_text_field( $_REQUEST['page'] );
		if( $page == $this->myAdminPage ){
			$ret = TRUE;
		}

		return $ret;
	}

// intercepts if in the front page our slug is given then it's ours
	public function intercept()
	{
		if( ! $this->isIntercepted() ){
			return;
		}

		$pw1 = $this->pw1();

		$uri = PW1_Uri::construct( $this->myShortPage );
		$uri = $uri::fromString( $uri, $uri::getCurrent() );

		$request = PW1_Request::init( $uri );
		$request->misc[ 'pluginFile' ] = $this->pluginFile;

		$response = $this->pw1()->respond( $request );
		$this->handleResult = $response->content;

		echo $this->render();
		exit;
	}

	public function isIntercepted()
	{
		$ret = FALSE;

	// in wp-admin
		if( is_admin() ){
			if( ! $this->isMeAdmin() ){
				return $ret;
			}

			if( ! isset($_REQUEST[$this->pw1SlugParam]) ){
				return $ret;
			}

			$p = sanitize_text_field( $_REQUEST[$this->pw1SlugParam] );
			if( FALSE !== strpos($p, ':') ){
				$ret = TRUE;
			}
		}
	// in front end
		else {
			if( ! array_key_exists($this->pw1Param, $_GET) ){
				return $ret;
			}

			$p = sanitize_text_field( $_GET[$this->pw1Param] );
			if( $p == $this->myShortPage ){
				$ret = TRUE;
			}
		}

		return $ret;
	}
}
}