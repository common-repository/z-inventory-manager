<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Finance00Html_Admin0Settings0Finance_Index extends _PW1
{
	private $_pNames = array(
		'finance_price_format_before',
		'finance_price_format_number_decpoint',
		'finance_price_format_number_thousep',
		'finance_price_format_after'
	);

	public $settings;

	public function __construct(
		PW1_ZI3_Settings_ $settings
	)
	{}

	public function title( PW1_Request $request )
	{
		return '__Finance__';
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

<?php
$demoPrice = 54321;
$formats = array(
	array('.', ','),
	array('.', 's'),
	array('.', ''),
	array(',', 's'),
	array('.', ''),
	array(',', ''),
	array(',', '.')
);

$options = array();
foreach( $formats as $f ){
	$decPoint = $f[0];
	$thousandSep = $f[1];

	if( 's' == $decPoint ) $decPoint = ' ';
	if( 's' == $thousandSep ) $thousandSep = ' ';

	$options[ join('|', $f) ] = number_format( $demoPrice, 2, $decPoint, $thousandSep );
}

$currentFormatValue = join( '|', array($values['finance_price_format_number_decpoint'], $values['finance_price_format_number_thousep']) );
?>

<div>
	<fieldset>
	<legend>__Price Format__</legend>

	<ul class="pw-inline">
		<li>
			<input type="text" name="finance_price_format_before" size="3" value="<?php echo $values['finance_price_format_before']; ?>" class="pw-align-right">
		</li>
		<li>
			<select name="finance_price_format_number">
				<?php foreach( $options as $k => $v ) : ?>
					<option value="<?php echo esc_attr($k); ?>" <?php if( $k == $currentFormatValue ) echo 'selected'; ?>><?php echo esc_html( $v ); ?>
				<?php endforeach; ?>
			</select>
		</li>
		<li>
			<input type="text" name="finance_price_format_after" size="3" value="<?php echo $values['finance_price_format_after']; ?>">
		</li>
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
		if( $response->formErrors ){
			return $response;
		}

		$values = array();
		foreach( $this->_pNames as $name ){
			if( 'finance_price_format_number_decpoint' == $name ){
				$v = $post['finance_price_format_number'];
				$v = explode( '|', $v );
				list( $v1, $v2 ) = $v;
				$values[ $name ] = $v1;
			}
			elseif( 'finance_price_format_number_thousep' == $name ){
				$v = $post['finance_price_format_number'];
				$v = explode( '|', $v );
				list( $v1, $v2 ) = $v;
				$values[ $name ] = $v2;
			}
			else {
				$values[ $name ] = $post[ $name ];
			}
		}

		foreach( $values as $k => $v ){
			$this->settings->set( $k, $v );
		}

		$response->redirect = '.';
		$response->addMessage( '__Settings Saved__' );

		return $response;
	}
}