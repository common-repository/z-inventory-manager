<?php
class PW1_Router
{
	public static function patternToRe( $pattern )
	{
		$ret = $pattern;

	// match all
		if( '*' === $ret ){
			return $ret;
		}

	// already re
		if( ('/' == substr($ret, 0, 1)) && ('/' == substr($ret, -1)) ) return $ret;

	// no wildcards
		$starPos = strpos( $pattern, '*' );
		$figurePos = strpos( $pattern, '{' );

		if( ( FALSE === $figurePos ) && ( FALSE === $starPos ) ) return $ret;

	// okay, convert to re
		$ret = str_replace( '/', '\/', $ret );

		if( FALSE !== $starPos ){
			$ret = str_replace( '*', '.*', $ret );
		}

		if( FALSE !== $figurePos ){
			$ret = str_replace( '{.*}', '(.*)', $ret );
		}

	// find {param} like things
		if( FALSE !== $figurePos ){
			$ret = str_replace( '{id}', '([\d\_\-]+)', $ret );
			$ret = preg_replace( '/\{(\w*)\}/', '([^\/\:]+)', $ret );
		}

		$ret = '/^' . $ret . '$/';
		return $ret;
	}

	public static function patternMatchesSlug( $pattern, $slug )
	{
		$ok = FALSE;
		$params = array();

	// re?
		if( '/' === substr($pattern, 0, 1) ){
			if( preg_match($pattern, $slug, $params) ){
				$ok = TRUE;
				array_shift( $params );
			}
		}
		else {
			if( ('*' === $pattern) OR ($slug === $pattern) ){
				$ok = TRUE;
				$params = array();
			}
		}

		$ret = array( $ok, $params );
		return $ret;
	}

	public static function prepareRoutes( array $routes )
	{
		$ret = array();

		$defaultPriority = 5;

		foreach( $routes as $r ){
			$method = $r[0];
			$pattern = $r[1];
			$handler = $r[2];
			$priority = isset( $r[3] ) ? $r[3] : 0;
			$fixedKey = isset( $r[4] ) ? $r[4] : '';

			$priority = $defaultPriority + $priority;

			$key = $priority * 1000 + count($ret) + 1;

			if( FALSE !== strpos( $method, ',' ) ){
				$methods = explode( ',', $method );
			}
			else {
				$methods = array( $method );
			}

		// convert to regular expression
			$pattern = static::patternToRe( $pattern );

			foreach( $methods as $method ){
				$ret[ $key++ ] = array( $method, $pattern, $handler, $fixedKey );
			}
		}

		ksort( $ret );
		return $ret;
	}

	public static function findHandlers( array $routes, $method, $slug )
	{
		$ret = array();

		$resort = FALSE;
		$fixedKeys = array();

// _print_r( $routes );
// exit;

		foreach( $routes as $k => $r ){
			$thisMethod = $r[0];
			$thisPattern = $r[1];
			$thisHandler = $r[2];
			$fixedKey = isset( $r[3] ) ? $r[3] : '';

		// method?
			if( ( '*' !== $thisMethod ) && ( $thisMethod !== $method ) ){
			// if( ( '*' !== $thisMethod ) && ( ! isset($methods[$thisMethod]) ) ){
				continue;
			}

			list( $ok, $args ) = static::patternMatchesSlug( $thisPattern, $slug );

			if( ! $ok ){
				continue;
			}

			$fixedParams = array();

// if( $ok ){
	// echo "'$thisPattern' MATCHES '$slug' => $thisHandler<br>";
// }

		// if handler contains '*' replace it with methodname
			if( is_string($thisHandler) && (FALSE !== strpos($thisHandler, '*')) ){
				$thisHandler = str_replace( '*', strtolower($method), $thisHandler );
			}
			elseif( is_array($thisHandler) && (FALSE !== strpos($thisHandler[1], '*')) ){
				$thisHandler[1] = str_replace( '*', strtolower($method), $thisHandler[1] );
			}

		// dispatch to another?
			if( is_string($thisHandler) && ( '>' === substr($thisHandler, 0, 1) ) ){
				$dispatchTo = substr( $thisHandler, 1 );

			// replace with args if needed
				if( $args && (FALSE !== strpos($dispatchTo, '{')) ){
					$argKeys = array_keys( $args );
					foreach( $argKeys as $ak ){
						 $dispatchTo = str_replace( '{' . ($ak + 1) . '}', $args[$ak], $dispatchTo );
					}
				}

				if( FALSE !== strpos($dispatchTo, '?') ){
					list( $dispatchTo, $dispatchParamString ) = explode( '?', $dispatchTo, 2 );
					parse_str( $dispatchParamString, $fixedParams );
				}

// _print_r( $fixedParams );

// echo "DISPATCH TO '$dispatchTo'<br>";

				$subHandlers = static::findHandlers( $routes, $method, $dispatchTo );
				foreach( $subHandlers as $subK => $subHandler ){
					$subHandler[2] = array_merge( $subHandler[2], $fixedParams );
					$ret[ $subK ] = $subHandler;
				}
				// _print_r( $ret );
				// exit;

				$resort = TRUE;
				continue;
			}

			if( strlen($fixedKey) ){
			// replace if this one has bigger priority number i.e. goes later
				if( (! isset($fixedKeys[$fixedKey]) ) OR ( $fixedKeys[$fixedKey] <= $k ) ){
					if( isset($fixedKeys[$fixedKey]) ){
						unset( $ret[ $fixedKeys[$fixedKey] ] );
					}
					$fixedKeys[ $fixedKey ] = $k;
				}
				else {
					continue;
				}
			}

			if( NULL === $thisHandler ){
				unset( $ret[$k] );
				continue;
			}

			$ret[ $k ] = array( $thisHandler, $args, $fixedParams );
		}

		if( $resort ) ksort( $ret );
		return $ret;
	}
}
