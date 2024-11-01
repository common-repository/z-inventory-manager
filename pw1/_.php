<?php
if( ! defined('ABSPATH') ) define( 'ABSPATH', __DIR__ );

if( ! class_exists('PW1_') ){

class _PW1
{
	public $self;
	public $pw1;
}

class PW1_
{
	public static $modules = array();

	const EXTEND_WRAP		= 'wrap';
	const EXTEND_CHAIN	= 'chain';
	const EXTEND_MERGE	= 'merge';
	const EXTEND_LISTEN	= 'listen';

	private $extenders	= array();
	private $routes		= NULL;

	private $_constructed = array();
	private $_made = array();

	private $core;
	private $handle;

	public function __construct()
	{
		$this->core = $this->make( 'PW1_Core' );
		$this->handle = $this->make( 'PW1_Handle' );
	}

	public static function versionNumFromString( $verString )
	{
		$ret = explode( '.', $verString );
		if( strlen($ret[2]) < 2 ) $ret[2] = '0' . $ret[2];
		$ret = join( '', $ret );
		$ret = (int) $ret;
		return $ret;
	}

	public static function versionStringFromFile( $fileName )
	{
		$ret = NULL;
		$fileContents = file_get_contents( $fileName );
		if( preg_match('/version:[\s\t]+?([0-9.]+)/i', $fileContents, $v) ){
			$ret = $v[1];
		}
		return $ret;
	}

	private function _extend( $src, $extender, $type, $sort = 50 )
	{
		static $sort2 = 1000;
		$sortKey = $sort . '-' . $sort2++;

		if( is_string($src) ){
			$src = strtolower( $src );
			if( FALSE === strpos($src, '@') && ('*' !== $src) ) $src .= '@';
			$key = $src;
		}
		else {
			$key = spl_object_hash( $src );
		}

		if( is_string($extender) ){
			$extender = strtolower( $extender );
			if( FALSE === strpos($extender, '@') ) $extender .= '@';
		}

		if( ! isset($this->extenders[$key]) ) $this->extenders[$key] = array();
		if( ! isset($this->extenders[$key][$type]) ) $this->extenders[$key][$type] = array();
		$this->extenders[ $key ][ $type ][ $sortKey ] = $extender;
		ksort( $this->extenders[ $key][ $type ] );

		return $this;
	}

	public function wrap( $src, $wrapper, $sort = 50 )
	{
		return $this->_extend( $src, $wrapper, static::EXTEND_WRAP, $sort );
	}

	public function merge( $src, $wrapper, $sort = 50 )
	{
		return $this->_extend( $src, $wrapper, static::EXTEND_MERGE, $sort );
	}

	public function chain( $src, $wrapper, $sort = 50 )
	{
		return $this->_extend( $src, $wrapper, static::EXTEND_CHAIN, $sort );
	}

	public function listen( $src, $wrapper, $sort = 50 )
	{
		return $this->_extend( $src, $wrapper, static::EXTEND_LISTEN, $sort );
	}

	public function routes()
	{
		return $this->call( 'PW1_Handle@routes' );
	}

	public function session()
	{
		return $this->make( 'PW1_Session' );
	}

	public function respond( PW1_Request $request )
	{
		return $this->handle->respond( $request );
	}

	public function boot( array $modules, array $bootArgs = array() )
	{
		$invoke = array();

		foreach( $modules as $moduleClassName ){
			$m = $this->make( $moduleClassName );
			if( method_exists($moduleClassName, 'boot') ){
				$invoke[ $moduleClassName ] = $m;
			}
		}

		foreach( $invoke as $moduleClassName => $m ){
			// $m->boot();
			$args = isset( $bootArgs[$moduleClassName] ) ? $bootArgs[$moduleClassName] : array();
			call_user_func_array( array($m, 'boot'), $args );
		}
	}

/* CALLS A FUNC WITH OUR EXTENSIONS */
	public function call()
	{
		$args = func_get_args();
		$funcOrString = array_shift( $args );

		$keys = array();
		$methodName = NULL;

	// construct if needed
		if( is_string($funcOrString) ){
			$func = $funcOrString;
			$func = strtolower( $func );

			$keys[] = $func;

			$pos = strpos( $func, '@' );
			if( FALSE !== $pos ){
				$className = substr( $func, 0, $pos );
				$methodName = substr( $func, $pos + 1 );
			}
			else {
				$className = $func;
			}

			$keys[] = $className . '@*';
			$keys[] = '*';

			$func = $this->construct( $className );
			if( strlen($methodName) ){
				$func = array( $func, $methodName );
			}
		}
		else {
			$func = $funcOrString;
			if( is_array($func) ){
				if( is_object($func[0]) ){
					$keys[] = spl_object_hash( $func[0] ) . '@' . $func[1];
				}
				else {
					$keys[] = $func[0] . '@' . $func[1];
				}
			}
			else {
				$keys[] = spl_object_hash( $func );
			}
		}

		$caller = array( $this, 'call' );

	// wrapped? 
		$wrappers = array();
		foreach( $keys as $key ){
			if( isset($this->extenders[$key][self::EXTEND_WRAP]) ) $wrappers = array_merge( $wrappers, $this->extenders[$key][self::EXTEND_WRAP] );
		}

		if( $wrappers ){
// _print_r( $wrappers );
			$parentFunc = $func;

		// for every entry in stack also pass in reference to parent function in stack 
			while( $wrapper = array_shift($wrappers) ){
				if( is_string($wrapper) && strlen($methodName) ){
					$wrapper = str_replace( '*', $methodName, $wrapper );
				}

				if( $wrapper === $funcOrString ) continue;

				$context = new PW1_ExtensionContext;
				$context->parentFunc = $parentFunc;
				$context->func = $funcOrString;
				$context->pw1 = $this;

				$func = function() use( $caller, $wrapper, $context ){
					$args = func_get_args();
					array_unshift( $args, $wrapper );
					array_push( $args, $context );
					$ret = call_user_func_array( $caller, $args );
					return $ret;
				};

				$parentFunc = $func;
			}
		}

	// chain? pass to chain, skip after first non null return
		$chain = array();
		foreach( $keys as $key ){
			if( isset($this->extenders[$key][self::EXTEND_CHAIN]) ) $chain = array_merge( $chain, $this->extenders[$key][self::EXTEND_CHAIN] );
		}

		$ret = NULL;

		foreach( $chain as $n ){
			// if( is_string($n) && strlen($methodName) ){
				// $n = str_replace( '*', $methodName, $n );
			// }

			$thisArgs = $args;
			array_unshift( $thisArgs, $n );
			$thisRet = call_user_func_array( $caller, $thisArgs );

			if( NULL !== $thisRet ){
				$ret = $thisRet;
				break;
			}
		}

	// this func
		if( NULL === $ret ){
			$ret = call_user_func_array( $func, $args );
		}

		if( $ret instanceof PW1_Error ){
			return $ret;
		}

	// merge?
		$merge = array();
		foreach( $keys as $key ){
			if( isset($this->extenders[$key][self::EXTEND_MERGE]) ) $merge = array_merge( $merge, $this->extenders[$key][self::EXTEND_MERGE] );
		}

		foreach( $merge as $n ){
			// if( is_string($n) && strlen($methodName) ){
				// $n = str_replace( '*', $methodName, $n );
			// }

			$thisArgs = $args;
			array_unshift( $thisArgs, $n );
			$thisRet = call_user_func_array( $caller, $thisArgs );

			if( NULL === $ret ){			$ret = $thisRet; }
			elseif( is_array($ret) ){	$ret = array_merge_recursive( $ret, $thisRet ); }
			elseif( is_string($ret) ){	$ret .= $thisRet; }
			elseif( is_object($ret) ){ $ret = $thisRet; }

			if( $ret instanceof PW1_Error ){
				return $ret;
			}
		}

	// don't listen if error
		if( $ret instanceof PW1_Error ){
			return $ret;
		}

	// listen?
		$listeners = array();
		foreach( $keys as $key ){
			if( isset($this->extenders[$key][self::EXTEND_LISTEN]) ) $listeners = array_merge( $listeners, $this->extenders[$key][self::EXTEND_LISTEN] );
		}

		if( $listeners ){
			$context = new PW1_ExtensionContext;
			$context->parentFunc = $func;
			$context->func = $funcOrString;
			$context->pw1 = $this;
			$context->ret = $ret;
		}

		foreach( $listeners as $listener ){
			if( is_string($listener) && strlen($methodName) ){
				$listener = str_replace( '*', $methodName, $listener );
			}

			$thisArgs = $args;
			array_unshift( $thisArgs, $listener );
			array_push( $thisArgs, $context );

			call_user_func_array( $caller, $thisArgs );
		}

		return $ret;
	}

/* MAKES A CLASS WRAPPED BY OUR CLASS DECORATOR */
	public function make( $className, $realOne = NULL )
	{
		if( $this instanceof $className ) return $this;

		$className = strtolower( $className );
		if( isset($this->_made[$className]) ){
			return $this->_made[$className];
		}

		if( NULL === $realOne ){
			$realOne = $this->construct( $className );
		}

		$ret = new PW1_ObjectDecorator( $realOne, $className, $this );
		$this->_made[$className] = $ret;

		return $ret;
	}

/* constructs a bare class, not wrapped */
	public function construct( $className )
	{
		if( $this instanceof $className ) return $this;

		$className = strtolower( $className );
		if( isset($this->_constructed[$className]) ){
			return $this->_constructed[$className];
		}

	// if ends with _ then it's a main class of a module
		$isModule = ( '_' === substr($className, -1) ) ? TRUE : FALSE;

	// if main module file, construct imported modules first
		if( $isModule && isset($className::$import) ){
			foreach( $className::$import as $importModuleClassName ){
				$this->construct( $importModuleClassName );
			}
		}

		$injectArgs = $this->findClassInjects( $className );

	// check injects
		foreach( $injectArgs as $argName => $argClassName ){
		// CIRCULAR REFERENCE
			if( $argClassName === $className ){ 
				exit( __METHOD__ . ': ' . __LINE__ . ': circular reference<br>' . $argClassName . ' -> ' . $className . ' -> ' . $argClassName );
			}
			$this->core->checkInject( $className, $argClassName );
		}

	// inject bare objects into constructor
		$class = $this->_getClass( $className );
		$constructorArgs = array();
		foreach( $injectArgs as $argName => $argClass ){
			$constructorArgs[ $argName ] = $this->construct( $argClass );
		}

		$ret = $class->newInstanceArgs( $constructorArgs );

	// inject wrapped versions
		foreach( $injectArgs as $argName => $argClass ){
			$ret->{$argName} = $this->make( $argClass );
		}
		$ret->self = $this->make( $className, $ret );

		$this->_constructed[$className] = $ret;
		return $ret;
	}

	public function findClassInjects( $className )
	{
		static $cache = array();

		$ret = array();

		$className = strtolower( $className );
		if( isset($cache[$className]) ){
			return $cache[$className];
		}

	// get reflection of constructor
		$classMethodName = '__construct';
		$methodReflection = $this->_getMethod( $className, $classMethodName );
		if( NULL === $methodReflection ){
			$cache[$className] = $ret;
			return $ret;
		}

		$args = $methodReflection->getParameters();
		foreach( $args as $arg ){
			$argName = $arg->getName();

			if( method_exists($arg, 'getType') ){
				$argType = $arg->getType();
				if( (! $argType) OR $argType->isBuiltin() ){
					$argClassName = NULL;
				}
				else {
					if( method_exists($argType, 'getName') ){
						$argClassName = $argType->getName();
					}
					else {
						$argClassName = '' . $argType;
					}
				}
			}
			else {
				$argClass = $arg->getClass();
				$argClassName = $argClass->getName();
			}

			if( ! $argClassName ){
				exit( __METHOD__ . ': ' . __LINE__ .  ': no class known for ' . $className . ' -> ' . $argName );
			}

			$ret[ $argName ] = $argClassName;
		}

		foreach( array_keys($ret) as $argName ){
			$ret[ $argName ] = strtolower( $ret[$argName] );
		}

		$cache[$className] = $ret;
		return $ret;
	}

/* REFLECTION HELPERS */
	private function _getMethod( $className, $methodName )
	{
		static $cache = array();

		$key = $className . '@' . $methodName;
		if( isset($cache[$key]) ){
			return $cache[$key];
		}

		$ret = NULL;

		$classReflection = $this->_getClass( $className );

		if( ! $classReflection->hasMethod($methodName) ){
			return $ret;
		}

		$ret = $classReflection->getMethod( $methodName );
		$cache[$key] = $ret;

		return $ret;
	}

	private function _getClass( $className )
	{
		static $cache = array();

		if( isset($cache[$className]) ){
			return $cache[$className];
		}

		if( ! class_exists($className) ){
			exit( __METHOD__ . ': ' . __LINE__ . ": class does not exist: '$className'" );
		}

		$ret = new ReflectionClass( $className );
		$cache[ $className ] = $ret;

		return $ret;
	}
}
}

if( ! function_exists('_print_r') ){
	function _print_r( $thing, $return = FALSE )
	{
		if( $return ){
			return '<pre>' . print_r( $thing, $return ) . '</pre>';
		}
		else {
			echo '<pre>';
			print_r( $thing, $return );
			echo '</pre>';
		}
	}
}

if( ! function_exists('esc_attr') ){
	function esc_attr( $val )
	{
		$ret = htmlspecialchars( $val );
		return $ret;
	}
}

if( ! function_exists('esc_html') ){
	function esc_html( $val )
	{
		$ret = htmlspecialchars( $val );
		return $ret;
	}
}

if( ! function_exists('str_starts_with') ){
	function str_starts_with( $haystack, $needle )
	{
		return ( substr( $haystack, 0, strlen($needle) ) === $needle );
	}
}

if( ! function_exists('str_ends_with') ){
	function str_ends_with( $haystack, $needle )
	{
		return ( substr( $haystack, -strlen($needle) ) === $needle );
	}
}