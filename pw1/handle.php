<?php
class PW1_Handle extends _PW1
{
	public $pw1;
	private $_routes = NULL;

	public function __construct(
		PW1_ $pw1
	)
	{}

	public function routes()
	{
		$ret = array();
		return $ret;
	}

	public function respond( PW1_Request $request )
	{
		$response = PW1_Response::construct();

		if( NULL === $this->_routes ){
			$this->_routes = $this->pw1->routes();
			$this->_routes = PW1_Router::prepareRoutes( $this->_routes );
			// echo 'COUNTROUTES = ' . count( $this->_routes ) . '<br>';
		}

		$routes = $this->_routes;
// _print_r( $routes );

		$forcedParams = array();
		if( FALSE !== strpos($request->slug, '?') ){
			list( $slug, $paramString ) = explode( '?', $request->slug, 2 );
			parse_str( $paramString, $forcedParams );

			$request->slug = $slug;
			foreach( $forcedParams as $k => $v ){
				// unset all
				if( ('_' === $k) && ('NULL' === $v) ){
					$request->params = array();
					continue;
				}

				if( 'NULL' === $v ){
					unset( $request->params[$k] );
				}
				else {
					$request->params[ $k ] = $v;
				}
			}
		}

	// FIND HANDLERS
		$handlers = PW1_Router::findHandlers( $routes, $request->method, $request->slug );

// echo 'HANDLE: ' . $request->method . '[' . $request->slug . ']<br>' . "<br>";
// echo "HANDLERS<br>";
// _print_r( $request );
// echo count( $handlers );
// _print_r( $handlers );
// foreach( $handlers as $h ) echo $h[0] . '<br>';
// echo '<br>';
// exit;

		foreach( $handlers as $h ){
// continue;
			list( $handler, $args, $fixedParams ) = $h;

			foreach( $fixedParams as $fpk => $fpv ) $request->params['*' . $fpk] = $fpv;
			$handlerRet = $this->self->runHandler( $handler, $args, $request, $response );
			foreach( $fixedParams as $fpk => $fpv ) unset( $request->params['*' . $fpk] );

 			if( $handlerRet instanceof PW1_Error ){
				$response->addError( $handlerRet->getMessage() );
				$response->content = $handlerRet->getMessage();
				break;
			}

			if( NULL !== $handlerRet ){
				if( $handlerRet instanceof PW1_Request ){
					$request = $handlerRet;
				}
				else {
					$response = $handlerRet;
				}
			}

			if( NULL !== $response->dispatch ){
				break;
			}


			if( in_array($request->method, array('GET')) ){
				if( NULL !== $response->redirect ){
					break;
				}
			}
		}

	// dispatch
		if( NULL !== $response->dispatch ){
		// prepare url
			$to = $response->dispatch;

			$uri = $request->uri;
			$uri = $uri::setSlug( $uri, $to );

			$uri->params = array_merge( $uri->params, $response->params );

			$request->slug = $uri->slug;
			$request->params = $uri->params;
			$request->uri = $uri;

			return $this->self->respond( $request );
		}

	// redirect
		if( NULL !== $response->redirect ){
		// prepare url
			$to = $response->redirect;

			$uri = $request->uri;
			$uri = $uri::setSlug( $uri, $to );

			$uri->params = array_merge( $uri->params, $response->params );
			$to = $uri::toString( $uri );

			$response->redirect = $to;
			$response = $this->self->redirect( $request, $response );
		}

	// process content
		if( null === $response->content ) $response->content = '';
	
		if( strlen($response->content) ){
			$content = $response->content;

		// convert slug to real urls
			$uri = $request->uri;
			preg_match_all( '/([\'"])URI\:(.*)([\'"])/U', $content, $ma );

			$replaces = array();
			$count = count( $ma[0] );
			for( $ii = 0; $ii < $count; $ii++ ){
				$what = $ma[0][$ii];
				if( isset($replaces[$what]) ) continue;

				$quote1 = $ma[1][$ii];
				$slug = $ma[2][$ii];
				$quote2 = $ma[3][$ii];

				if( is_string($slug) && ('http' == substr($slug, 0, strlen('http'))) ){
					$to = $slug;
				}
				else {
					$thisUri = $uri::setSlug( $uri, $slug );
					$to = $uri::toString( $thisUri );
				}

				$replaces[ $what ] = $quote1 . $to . $quote2;
			}

			foreach( $replaces as $what => $to ){
				$content = str_replace( $what, $to, $content );
			}

			$response->content = $content;
		}

		return $response;
	}

	public function runHandler( $handler, $args, $request, $response )
	{
		$request->args = $args;
		return $this->pw1->call( $handler, $request, $response );
	}

	public function redirect( PW1_Request $request, PW1_Response $response )
	{
	// form values and errors
		if( $response->formErrors OR $response->getErrors() ){
			$this->pw1->session()->setFlashdata( 'formErrors', $response->formErrors );
			$this->pw1->session()->setFlashdata( 'formValues', $request->data );
		}

		if( $errors = $response->getErrors() ){
			$this->pw1->session()->setFlashdata( 'errors', $errors );
		}

		if( $messages = $response->getMessages() ){
			$this->pw1->session()->setFlashdata( 'messages', $messages );
		}

		$to = $response->redirect;

		if( NULL === $to ) return;

		if( ! headers_sent() ){
			if( defined('WPINC') ){
				wp_redirect( $to );
			}
			else {
				header('Location: ' . $to);
			}
		}
		else {
			$ret = "<META http-equiv=\"refresh\" content=\"0;URL=$to\">";
			echo $ret;
		}
		exit;
	}
}