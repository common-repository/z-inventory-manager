<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_App0Wp_Translate extends _PW1
{
	private $domain;
	private $pluginDir;
	private $locale;

	private $_init = FALSE;

	public function init()
	{
		if( $this->_init ) return;
		$this->_init = TRUE;

		$this->domain = $this->self->domain();
		$this->pluginDir = $this->self->pluginDir();
		$this->locale = $this->self->locale();

		$pluginDir = $this->pluginDir;
		$domain = $this->domain;

		// $this->domain = $domain;
		// $this->locale = $config->getConfigLocale();

		$langDir = plugin_basename( $pluginDir ) . '/languages';
		$langFullDir = $pluginDir . '/languages';

		add_filter( 'locale', array($this, 'setWpLocale') );

		// load_plugin_textdomain( $this->domain, '', $langDir );
		$locale = get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, $domain );

	// Polylang plugin
		if( function_exists('pll_current_language') ){
			$locale = pll_current_language( 'locale' );
		}

		$mofile = $domain . '-' . $locale . '.mo';
		$fullMofile = $langFullDir . '/' . $mofile;

// echo "FULL MOFILE = '$fullMofile'<br>";

		$load_result = load_textdomain( $domain, $fullMofile );
		if( ! $load_result ){
			$load_result = load_plugin_textdomain( $domain, '', $langDir );
		}

		remove_filter( 'locale', array($this, 'setWpLocale') );
	}

	public function locale()
	{
		return '';
	}

	public function pluginDir()
	{
		return '';
	}

	public function domain()
	{
		return '';
	}

	public function __constructOld( $domain, $pluginDir, $locale = NULL )
	{
		$this->domain = $domain;
		// $this->locale = $config->getConfigLocale();

		$langDir = plugin_basename( $pluginDir ) . '/languages';
		$langFullDir = $pluginDir . '/languages';

		add_filter( 'locale', array($this, 'setWpLocale') );

		// load_plugin_textdomain( $this->domain, '', $langDir );
		$locale = get_locale();
		$locale = apply_filters( 'plugin_locale', $locale, $domain );

	// Polylang plugin
		if( function_exists('pll_current_language') ){
			$locale = pll_current_language( 'locale' );
		}

		$mofile = $domain . '-' . $locale . '.mo';
		$fullMofile = $langFullDir . '/' . $mofile;
		$load_result = load_textdomain( $domain, $fullMofile );
		if( ! $load_result ){
			$load_result = load_plugin_textdomain( $domain, '', $langDir );
		}

		remove_filter( 'locale', array($this, 'setWpLocale') );
	}

	public function setWpLocale( $locale )
	{
		if( $this->locale ){
			$locale = $this->locale;
		}
		return $locale;
	}

	public function translateString( $string )
	{
		$this->init();

		$ret = __( $string, $this->domain );
		if( $ret == $string ){
			$ret = __( $string );
		}
		return $ret;
	}
}