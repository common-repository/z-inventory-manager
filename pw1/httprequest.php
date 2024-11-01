<?php
class PW1_HttpRequest
{
	public function __construct()
	{
		if( ! defined('WPINC') ){
			static::_sanitizeGet();
			static::_sanitizeCookie();
			static::_sanitizePost();
		}
	}

	public static function getPost()
	{
		$ret = NULL;

		if( empty($_POST) ){
			$ret = file_get_contents( 'php://input' );
		}
		else {
			$ret = array();
			foreach( array_keys($_POST) as $key ){
				$ret[$key] = static::_fetch_from_array($_POST, $key);
			}
		}

		if( $_FILES ){
			foreach( array_keys($_FILES) as $k ){
				if( isset($_FILES[$k]) && is_uploaded_file($_FILES[$k]['tmp_name']) ){
					$ret[$k] = $_FILES[$k];
				}
			}
		}

		if( ! $ret ) $ret = array();

		return $ret;
	}

	public static function getGet()
	{
		$ret = array();
		foreach( array_keys($_GET) as $key ){
			$ret[ $key ] = static::_fetch_from_array( $_GET, $key );
		}
		return $ret;
	}

	public static function getReferrer()
	{
		$ret = NULL;
		if( isset($_SERVER['HTTP_REFERER']) && strlen($_SERVER['HTTP_REFERER']) ){
			$ret = $_SERVER['HTTP_REFERER'];
		}
		return $ret;
	}

	public static function isAjax()
	{
		$ret = FALSE;
		if( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') ){
			$ret = TRUE;
		}
		return $ret;
	}

	public static function getMethod()
	{
		$ret = isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'get';
		$ret = strtoupper( $ret );
		return $ret;
	}

	public static function getIpAddress()
	{
		$ret = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
		return $ret;
	}

	public static function getUserAgent()
	{
		$ret = ( ! isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];
		return $ret;
	}

	public static function getCookie( $index )
	{
		return static::_fetch_from_array( $_COOKIE, $index );
	}

	protected static function _fetch_from_array($array, $index = '')
	{
		if ( ! isset($array[$index])){
			return FALSE;
		}
		$return = $array[$index];
		if( ! is_array($return) ){
			$return = trim( $return );
		}
		return $return;
	}

	protected static function _sanitizeGet()
	{
		if (is_array($_GET) AND count($_GET) > 0){
			foreach ($_GET as $key => $val){
				$_GET[ static::_cleanInputKeys($key) ] = static::_cleanInputData($val);
			}
		}
	}

	protected static function _sanitizePost()
	{
		if (is_array($_POST) AND count($_POST) > 0){
			foreach ($_POST as $key => $val){
				$_POST[ static::_cleanInputKeys($key) ] = static::_cleanInputData($val);
			}
		}
	}

	protected static function _sanitizeCookie()
	{
		if (is_array($_COOKIE) AND count($_COOKIE) > 0){
			unset($_COOKIE['$Version']);
			unset($_COOKIE['$Path']);
			unset($_COOKIE['$Domain']);

			foreach ($_COOKIE as $key => $val){
				$_COOKIE[ static::_cleanInputKeys($key) ] = static::_cleanInputData($val);
			}
		}
	}

	protected static function _cleanInputKeys( $str )
	{
		if ( ! preg_match("/^[a-z0-9:_\/\-\~]+$/i", $str)){
			exit('Disallowed Key Characters on: ' . '"' . esc_html($str) . '"' . '<br>');
		}
		return $str;
	}

	protected static function _cleanInputData( $str )
	{
		if( is_array($str) ){
			$new_array = array();
			foreach ($str as $key => $val){
				$new_array[ static::_cleanInputKeys($key) ] = static::_cleanInputData($val);
			}
			return $new_array;
		}

		/* We strip slashes if magic quotes is on to keep things consistent
		   NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
			it will probably not exist in future versions at all.
		*/
		$need_strip = FALSE;
		if( version_compare(PHP_VERSION, '5.4', '<') && get_magic_quotes_gpc() ){
			$need_strip = TRUE;
		}
		elseif( defined('WPINC') ){
			$need_strip = TRUE;
		}

		if( $need_strip ){
			$str = stripslashes($str);
		}

		// Remove control characters
		$str = static::removeInvisibleCharacters( $str );

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE){
			$str = str_replace(array("\r\n", "\r", "\r\n\n"), PHP_EOL, $str);
		}

		return $str;
	}

	public static function removeInvisibleCharacters( $str, $url_encoded = TRUE )
	{
		$non_displayables = array();

		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)
		if ($url_encoded){
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do {
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}

	public static function sanitizeTextField( $str )
	{
		if( is_array($str) ){
			$ret = array();
			foreach( array_keys($str) as $k ){
				$ret[$k] = static::sanitizeTextField( $str[$k] );
			}
			return $ret;
		}

		if( function_exists('sanitize_text_field') ){
			return sanitize_text_field( $str );
		}

		$ret = (string) $str;

		// $ret = wp_check_invalid_utf8( $str );

		if ( strpos( $ret, '<' ) !== false ) {
			$wp_pre_kses_less_than_callback = function( $matches ){
				$ret = $matches[0];
				if ( FALSE === strpos( $ret, '>' ) ) {
					$ret = htmlspecialchars( $ret, ENT_QUOTES );
				}
				return $ret;
			};
			$ret = preg_replace_callback( '%<[^>]*?((?=<)|>|$)%', $wp_pre_kses_less_than_callback, $ret );

		// This will strip extra whitespace for us.
			$ret = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $ret );
			$ret = strip_tags( $ret );
			$ret = preg_replace( '/[\r\n\t ]+/', ' ', $ret );

			// Use html entities in a special case to make sure no later
			// newline stripping stage could lead to a functional tag
			$ret = str_replace( "<\n", "&lt;\n", $ret );
		}

		$ret = preg_replace( '/[\r\n\t ]+/', ' ', $ret );

		$ret = trim( $ret );

		$found = FALSE;
		while ( preg_match( '/%[a-f0-9]{2}/i', $ret, $match ) ) {
			$ret = str_replace( $match[0], '', $ret );
			$found = TRUE;
		}

		if( $found ){
			// Strip out the whitespace that may now exist after removing the octets.
			$ret = trim( preg_replace( '/ +/', ' ', $ret ) );
		}

		return $ret;
	}
}
