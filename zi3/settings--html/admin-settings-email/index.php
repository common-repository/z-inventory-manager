<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings00Html_Admin0Settings0Email_Index extends _PW1
{
	private $_pNames = array( 'email_from', 'email_from_name', 'email_html' );
	public $settings;

	public function __construct(
		PW1_ZI3_Settings_	$settings
	)
	{}

	public function title( PW1_Request $request )
	{
		$ret = '__Email__';
		return $ret;
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
		<span>__Send Email From Address__</span>
		<input type="text" name="email_from" value="<?php echo esc_attr( $values['email_from'] ); ?>">
	</label>
</div>

<div>
	<label>
		<span>__Send Email From Name__</span>
		<input type="text" name="email_from_name" value="<?php echo esc_attr( $values['email_from_name'] ); ?>">
	</label>
</div>

<?php
$options = array( '1' => 'HTML', '0' => '__Plain Text__' );
?>
<div>
	<fieldset>
	<legend>__Email Format__</legend>

	<ul class="pw-inline">
	<?php foreach( $options as $k => $v ) : ?>
		<li>
			<label>
				<input type="radio" name="email_html" value="<?php echo $k; ?>" <?php if( $k == $values['email_html'] ) echo 'checked'; ?>>
				<?php echo $v; ?>
			</label>
		</li>
	<?php endforeach; ?>
	</ul>

	</fieldset>
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
		if( ! strlen($post['email_from']) ){
			$response->formErrors['email_from'] = '__Required Field__';
		}

		if( ! strlen($post['email_from_name']) ){
			$response->formErrors['email_from_name'] = '__Required Field__';
		}

		if( $response->formErrors ){
			return $response;
		}

		$values = array();
		foreach( $this->_pNames as $name ){
			$values[ $name ] = $post[ $name ];
		}

		foreach( $values as $k => $v ){
			$this->settings->set( $k, $v );
		}

		$response->redirect = '.';
		$response->addMessage( '__Settings Saved__' );

		return $response;
	}
}