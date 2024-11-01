<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_App00Html_Index extends _PW1
{
	public $pw1;

	public function __construct(
		PW1_ $pw1
	)
	{}

	public function get( PW1_Request $request )
	{
		return;
	}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Home__';
		return $response;
	}

	public function flashdata( PW1_Request $request, PW1_Response $response )
	{
		$session = $this->pw1->session();

		if( $messages = $session->getFlashdata('messages') ){
			foreach( $messages as $e ) $response->addMessage( $e );
		}

		if( $errors = $session->getFlashdata('errors') ){
			foreach( $errors as $e ) $response->addError( $e );
		}

		if( $debugs = $session->getFlashdata('debugs') ){
			foreach( $debugs as $e ) $response->addDebug( $e );
		}

		if( $formValues = $session->getFlashdata('formValues') ){
			$request->formValues = $formValues;
		}

		if( $formErrors = $session->getFlashdata('formErrors') ){
			$request->formErrors = $formErrors;
		}

		return $response;
	}
}