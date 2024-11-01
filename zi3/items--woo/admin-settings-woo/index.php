<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Woo_Admin0Settings0Woo_Index extends _PW1
{
	private $_pNames = array(
		'woo_integrate_inventory',
	);
	public $settings;

	public function __construct(
		PW1_ZI3_Settings_	$settings
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = 'WooCommerce';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$values = array();
		foreach( $this->_pNames as $name ){
			$values[ $name ] = $this->settings->get( $name );
		}

		ob_start();
?>

<form method="post" action="URI:.">

<div>
	<label>
		<input type="checkbox" name="woo_integrate_inventory" value="1"<?php if( $values['woo_integrate_inventory'] ) : ?> checked<?php endif; ?>>
		__Integrate Inventory With WooCommerce__
	</label>
</div>

<p>
<button type="submit">__Save__</button>
</p>

</form>

<?php
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';

		$post = $request->data;

	// VALIDATE POST
		if( $response->formErrors ){
			return $response;
		}

		$values = array();
		foreach( $this->_pNames as $name ){
			$values[ $name ] = isset( $post[$name] ) ? $post[ $name ] : 0;
		}

		foreach( $values as $k => $v ){
			$this->settings->set( $k, $v );
		}

		$response->redirect = '.';
		$response->addMessage( '__Settings Saved__' );

		return $response;
	}
}