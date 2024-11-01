<?php
class PW1_Uri
{
	public $src;
	public $base;
	public $slug;
	public $params = array();

	private $myParamsPrefix = '';
	private $slugParamName = 'a';

	public function __set( $name, $value )
	{
		$msg = 'Invalid property for setting: ' . get_class($this) . ': ' . $name . '<br>';
		echo $msg;
	}

	public function __get( $name )
	{
		$msg = 'Invalid property for getting: ' . get_class($this) . ': ' . $name . '<br>';
		echo $msg;
	}

	private function __construct()
	{}

	public static function construct( $myParamsPrefix = '', $slugParamName = 'a' )
	{
		$ret = new static;
		$ret->myParamsPrefix = $myParamsPrefix;
		$ret->slugParamName = $slugParamName;
		return $ret;
	}

	public static function setSlug( self $uri, $newSlug )
	{
		$ret = clone $uri;

		$oldParams = $ret->params;
		$oldSlug = $ret->slug;

		$moreParams = array();
		if( FALSE !== strpos($newSlug, '?') ){
			list( $newSlug, $paramString ) = explode( '?', $newSlug, 2 );
			parse_str( $paramString, $moreParams );
		}

		$newSlug = static::finalizeSlug( $newSlug, $oldSlug );
		$newParams = static::passParams( $oldSlug, $oldParams, $newSlug );

		$newParams = array_merge( $newParams, $moreParams );

		$ret->slug = $newSlug;
		$ret->params = $newParams;

		return $ret;
	}

	public static function fromString( self $ret, $url )
	{
		$ret = clone $ret;

		$ret->src = $url;

		$myParamsPrefix = $ret->myParamsPrefix;
		$slugParamName = $ret->slugParamName;

		$base = NULL;
		$baseParams = array();
		$myParams = array();
		$slug = NULL;

		$purl = parse_url( $url );

		// if( $this->rewrite ){
			// $path = $_SERVER['SCRIPT_NAME'];
			// if( '/' !== substr($path, -1) ){
				// $path = dirname( $path );
			// }
			// $purl['path'] = $path;
			// $purl['query'] = $_SERVER['QUERY_STRING'];
		// }

		if( isset($purl['scheme']) && isset($purl['host']) ){
			$base = $purl['scheme'] . '://'. $purl['host'] . $purl['path'];
		}
		else {
			$base = $purl['path'];
		}

		if( isset($purl['query']) && $purl['query']){
			parse_str( $purl['query'], $allParams );

			foreach( array_keys($allParams) as $k ){
				$allParams[ $k ] = PW1_HttpRequest::sanitizeTextField( $allParams[$k] );
			}

		/* get slug */
			if( isset($allParams[ $myParamsPrefix . $slugParamName]) ){
				$slug = $allParams[$myParamsPrefix . $slugParamName];
				$slug = trim( $slug, '/' );
				unset( $allParams[$myParamsPrefix . $slugParamName] );
			}

			foreach( $allParams as $k => $v ){
				if( $myParamsPrefix === substr( $k, 0, strlen($myParamsPrefix) ) ){
					$k = substr( $k, strlen($myParamsPrefix) );
					$myParams[ $k ] = $v;
				}
				else {
					$baseParams[ $k ] = $v;
				}
			}
		}

		if( $baseParams ){
			$baseHrefParams = http_build_query( $baseParams );
			$baseHrefParams = urldecode( $baseHrefParams );
			$glue = strlen( $base ) ? '?' : '';
			$base .= $glue . $baseHrefParams;
		}

		$ret->base = $base;
		$ret->params = $myParams;
		if( NULL !== $slug ){
			$ret->slug = $slug;
		}

		return $ret;
	}

	public static function toString( self $uri )
	{
		$ret = $uri->base;
		$myParamsPrefix = $uri->myParamsPrefix;

		$params = array();
		$params += $uri->params;

		$keys = array_keys( $params );
		foreach( $keys as $k ){
			if( ('NULL' === $params[$k]) OR (NULL === $params[$k]) ){
			// unset all?
				if( '_' === $k ){
					$params = array();
					break;
				}
				else {
				// unset only this
					unset( $params[$k] );
				// if starts with *
					if( '*' === substr($k, 0, 1) ){
						// $k2 = substr( $k, 1 );
						// $params[$k2] = NULL;
						$params[$k] = '';
					}
				}
			}
		}

		if( NULL !== $uri->slug ){
			$params = array( $uri->slugParamName => $uri->slug ) + $params;
		}

	// prepend with our prefix if any
		if( strlen($myParamsPrefix) && $params ){
			$keys = array_keys( $params );
			foreach( $keys as $k ){
			// but skip if starts with *
				if( '*' === substr($k, 0, 1) ){
					$k2 = substr( $k, 1 );
				}
				else {
					$k2 = $myParamsPrefix . $k;
				}
				$params[ $k2 ] = $params[ $k ];
				unset( $params[$k] );
			}
		}

		if( $params ){
			$params = http_build_query( $params );
			$glue = (strpos($ret, '?') === FALSE) ? '?' : '&';
			$ret .= $glue . $params;
		}

		$ret = sanitize_url( $ret );

		return $ret;
	}

	public static function dirname( self $uri )
	{
		$ret = $uri->base;

	// with filename 
		$dotPos = strrpos( $ret, '.' );
		if( FALSE !== $dotPos ){
			$slashPos = strrpos( $ret, '/' );
			if( $dotPos > $slashPos ){
				$ret = substr( $ret, 0, $slashPos );
			}
		}

		if( '/' !== substr($ret, -1) ) $ret .= '/';
		return $ret;
	}

	public static function finalizeSlug( $ret, $currentSlug )
	{
		// $params = array();
		// if( FALSE !== strpos($ret, '?') ){
			// list( $ret, $paramString ) = explode( '?', $ret, 2 );
			// parse_str( $paramString, $params );
		// }

	// replace ../../../.. to parent/parent/parent/parent
		if( '../../../..' == substr($ret, 0, 11) ){
			$slugArray = explode( '/', $currentSlug );
			$parentParentParentSlug = implode( '/', array_slice($slugArray, 0, -4) );
			$ret = $parentParentParentSlug . substr( $ret, 11 );
		}

	// replace ../../.. to parent/parent/parent
		if( '../../..' == substr($ret, 0, 8) ){
			$slugArray = explode( '/', $currentSlug );
			$parentParentParentSlug = implode( '/', array_slice($slugArray, 0, -3) );
			$ret = $parentParentParentSlug . substr( $ret, 8 );
		}

	// replace ../.. to parent/parent
		if( '../..' == substr($ret, 0, 5) ){
			$slugArray = explode( '/', $currentSlug );
			$parentParentSlug = implode( '/', array_slice($slugArray, 0, -2) );
			$ret = $parentParentSlug . substr( $ret, 5 );
		}

	// replace .. to parent
		if( '..' == substr($ret, 0, 2) ){
			$slugArray = explode( '/', $currentSlug );
			$parentSlug = implode( '/', array_slice($slugArray, 0, -1) );
			$ret = $parentSlug . substr( $ret, 2 );
		}

	// replace . to current slug
		if( '.' === substr($ret, 0, 1) ){
			$ret = $currentSlug . substr( $ret, 1 );
		}

		// if( $params ){
			// $hrefParams = http_build_query( $params );
			// $hrefParams = urldecode( $hrefParams );
			// $glue = '?';
			// $ret .= $glue . $hrefParams;
		// }

		return $ret;
	}

	public static function passParams( $fromSlug, array $currentParams, $toSlug )
	{
		$ret = array();
		$_passPrefix = '_';
		$_flashSuffix = '_';

	// remove flash params
		$keys = array_keys( $currentParams );
		foreach( $keys as $k ){
			if( $_flashSuffix === substr($k, -strlen($_flashSuffix)) ){
				unset( $currentParams[$k] );
			}
		}

		$level = static::compareSlugs( $toSlug, $fromSlug );

		if( NULL === $level ){
			$commonParent = static::findSlugsCommonParent( $toSlug, $fromSlug );
			if( ! $commonParent ){
				return $ret;
			}

			$passParamsToParent = call_user_func( __METHOD__, $fromSlug, $currentParams, $commonParent );
			$ret = call_user_func( __METHOD__, $commonParent, $passParamsToParent, $toSlug );
			return $ret;
		}

// echo "COMPARE: '$toSlug' VS '$fromSlug': $level<br>";
// _print_r( $currentParams );

	// fixed params - those starting with *
		$keys = array_keys( $currentParams );
		$fixedKeys = array_filter( $keys, function ($e){ return ('*' === substr($e, 0, 1)); } );

		foreach( $fixedKeys as $k ){
			$ret[$k] = $currentParams[$k];
			unset( $currentParams[$k] );
		}

		if( $level >= 0 ){
			$prefix = str_repeat( $_passPrefix, $level );
			reset( $currentParams );
			foreach( $currentParams as $k => $v ){
				$k2 = $prefix . $k;
				$ret[ $k2 ] = $v;
			}
		}
		else {
			$prefix = str_repeat( $_passPrefix, -$level );
			reset( $currentParams );
			foreach( $currentParams as $k => $v ){
				if( substr($k, 0, -$level) !== $prefix ){
					continue;
				}
				$k2 = substr( $k, -$level );
				$ret[ $k2 ] = $v;
			}
		}

// _print_r( $ret );
		return $ret;
	}

	public static function compareSlugs( $toSlug, $fromSlug )
	{
		$ret = NULL;

		if( null === $toSlug ) $toSlug = '';
		if( null === $fromSlug ) $fromSlug = '';

		if( false !== strpos($toSlug, '?') ){
			list( $toSlug, $paramString ) = explode( '?', $toSlug, 2 );
		}
		if( false !== strpos($fromSlug, '?') ){
			list( $fromSlug, $paramString ) = explode( '?', $fromSlug, 2 );
		}

		if( $toSlug == $fromSlug ){
			$ret = 0;
			return $ret;
		}

	// child
		if( strlen($toSlug) > strlen($fromSlug) ){
			if( substr($toSlug, 0, strlen($fromSlug)) !== $fromSlug ){
				return $ret;
			}

			$remain = substr( $toSlug, strlen($fromSlug) );
			$ret = substr_count( $remain, '/' );
			return $ret;
		}

	// parent
		if( strlen($fromSlug) > strlen($toSlug) ){
			if( substr($fromSlug, 0, strlen($toSlug)) !== $toSlug ){
				return $ret;
			}

			$remain = substr( $fromSlug, strlen($toSlug) );
			$ret = - substr_count( $remain, '/' );
			return $ret;
		}

		return $ret;
	}

	public static function isFull( $url )
	{
		$ret = FALSE;

		$prfx = array( 'http://', 'https://', '//', 'webcal://' );
		reset( $prfx );
		foreach( $prfx as $prf ){
			if( $prf === substr($url, 0, strlen($prf)) ){
				$ret = TRUE;
				break;
			}
		}

		return $ret;
	}

	public static function getCurrent()
	{
		$ret = 'http';
		if( isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on') ){
			$ret .= 's';
		}

		$ret .= '://';

		if( isset($_SERVER['HTTP_HOST']) && $_SERVER['SERVER_PORT'] != '80'){
			if( FALSE === strpos($_SERVER['HTTP_HOST'], ':') ){
				$ret .= $_SERVER['HTTP_HOST'] . ':' . $_SERVER['SERVER_PORT'];
			}
			else {
				$ret .= $_SERVER['HTTP_HOST'];
			}
		}
		else {
			$ret .= $_SERVER['HTTP_HOST'];
		}

		if ( ! empty($_SERVER['REQUEST_URI']) ){
			$ret .= $_SERVER['REQUEST_URI'];
		}
		else {
			$ret .= $_SERVER['SCRIPT_NAME'];
		}

		// $ret = urldecode( $ret );
		return $ret;
	}

	public static function findSlugsCommonParent( $slug1, $slug2 )
	{
		$ret = array();

		$slug1 = explode( '/', $slug1 );
		$slug2 = explode( '/', $slug2 );

		$length = min( count($slug1), count($slug2) );

		for( $i = 0; $i < $length; $i++ ){
			if( $slug1[$i] !== $slug2[$i] ){
				break;
			}
			$ret[] = $slug1[$i];
		}

		$ret = join( '/', $ret );
		return $ret;
	}
}