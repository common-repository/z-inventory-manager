<?php
if( ! defined('ABSPATH') ) define( 'ABSPATH', __DIR__ );

if( ! class_exists('PW1_Autoload') ){

class PW1_Autoload
{
	protected static $dirs = array(
		array( 'pw1_', __DIR__ )
	);

	public static function autoload( $inclass )
	{
		$class = $inclass;
		$class = strtolower( $class );

	// find dir by prefix
		foreach( static::$dirs as $autoloadDir ){
			list( $prefix, $dir ) = $autoloadDir;

			if( $prefix !== substr($class, 0, strlen($prefix)) ){
				continue;
			}

			$shortClass = $class;
			$shortClass = substr( $shortClass, strlen($prefix) );
			if( ! strlen($shortClass) ){
				$shortClass = '_';
			}

			$thisFile = static::classToFile( $shortClass );
			$thisFile = $dir . DIRECTORY_SEPARATOR . $thisFile . '.php';

	// echo __CLASS__ . ": '$prefix': FOR '$inclass' TRY $thisFile<br/><br/>\n";
			$fileExists = file_exists( $thisFile );
			if( $fileExists ){
				require( $thisFile );
				break;
			}
			else {
				echo __CLASS__ . ": FOR '$inclass' TRIED $thisFile<br/><br/>\n";
				// echo "SHORTC CLASS = '$shortClass'<br>";
			}
		}
	}

	public static function registerDir( $prefix, $dir )
	{
		array_unshift( static::$dirs, array($prefix, $dir) );
	}

	public static function classToFile( $className )
	{
		$ret = $className;

	// cut starting 'pw1_'
		// $ret = substr( $ret, 4 );

		$ret = strtolower( $ret );
		$ret = str_replace( '_', DIRECTORY_SEPARATOR, $ret );
		// $ret = str_replace( '_', '/', $ret );
		$ret = str_replace( '0', '-', $ret );

		if( DIRECTORY_SEPARATOR === substr($ret, -1) ){
			$ret = substr( $ret, 0, -1 ) . DIRECTORY_SEPARATOR . '_';
			// $ret = substr( $ret, 0, -1 ) . '/_';
		}

		return $ret;
	}
}

spl_autoload_register( 'PW1_Autoload::autoload' );
}