<?php
class PW1_ZI3_App0Wp00Html_Csrf
{
	private $actionName = 'post';
	private $tokenName = 'pw1-csrf';
	private $disabled = FALSE;

	public function render( $content )
	{
		$hidden = wp_nonce_field( $this->actionName, $this->tokenName, TRUE, FALSE );
		$ret = str_replace( '</form>', $hidden . '</form>', $content );
		return $ret;
	}

	public function check()
	{
		if( $this->disabled ){
			return;
		}

		if( ! isset($_POST[$this->tokenName])){
			// echo "want token name " . $this->tokenName . '<br>';
// _print_r( $_POST );
			echo 'csrf: no token';
			return $ret;
			// exit;
		}

		$nonce = $_POST[$this->tokenName];
		if( ! wp_verify_nonce( $nonce, $this->actionName ) ){
			echo 'csrf: token mismatch';
			exit;
		}

		// We kill this since we're done and we don't want to polute the _POST array
		unset( $_POST[$this->tokenName] );
		return $this;
	}
}