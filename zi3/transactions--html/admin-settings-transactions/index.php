<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Transactions00Html_Admin0Settings0Transactions_Index extends _PW1
{
	private $_pNames = array(
		'transactions_purchase_ref_auto', 'transactions_purchase_ref_auto_prefix', 'transactions_purchase_ref_auto_method',
		'transactions_sale_ref_auto', 'transactions_sale_ref_auto_prefix', 'transactions_sale_ref_auto_method',
	);
	public $settings;

	public function __construct(
		PW1_ZI3_Settings_	$settings
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Transactions__';
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
<fieldset>
	<legend>__Purchases__</legend>

	<?php $htmlId = 'pw1_collapse_' . rand( 1000, 9999 ); ?>
	<input type="checkbox" name="transactions_purchase_ref_auto" id="<?php echo $htmlId; ?>" class="pw-collapse" style="display: inline-block"<?php if( $values['transactions_purchase_ref_auto'] ) : ?> checked<?php endif; ?>>
	<div style="display: inline-block;">
		<label for="<?php echo $htmlId; ?>">
			__Auto-Generate Reference Numbers__
		</label>
	</div>

	<div class="pw-grid pw-collapse-on">
		<div class="pw-col-6">
			<label>
				<span>__Reference Number Prefix__</span>
				<input type="text" name="transactions_purchase_ref_auto_prefix" value="<?php echo $values['transactions_purchase_ref_auto_prefix']; ?>">
			</label>
		</div>

		<div class="pw-col-6">
			<fieldset>
				<legend>__Auto-Generated Numbers__</legend>
				<?php
				$options = array();
				$options['seq'] = '__Sequential__';
				$options['random'] = '__Random__';
				?>
				<ul class="pw-inline">
					<?php foreach( $options as $k => $label ) : ?>
						<li>
							<label>
								<input type="radio" name="transactions_purchase_ref_auto_method" value="<?php echo $k; ?>"<?php if( $k == $values['transactions_sale_ref_auto_method'] ): ?> checked<?php endif; ?>>
								<span><?php echo $label; ?></span>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
		</div>
	</div>
</fieldset>
</div>

<div>
<fieldset>
	<legend>__Sales__</legend>

	<?php $htmlId = 'pw1_collapse_' . rand( 1000, 9999 ); ?>
	<input type="checkbox" name="transactions_sale_ref_auto" id="<?php echo $htmlId; ?>" class="pw-collapse" style="display: inline-block"<?php if( $values['transactions_sale_ref_auto'] ) : ?> checked<?php endif; ?>>
	<div style="display: inline-block;">
		<label for="<?php echo $htmlId; ?>">
			__Auto-Generate Reference Numbers__
		</label>
	</div>

	<div class="pw-grid pw-collapse-on">
		<div class="pw-col-6">
			<label>
				<span>__Reference Number Prefix__</span>
				<input type="text" name="transactions_sale_ref_auto_prefix" value="<?php echo $values['transactions_sale_ref_auto_prefix']; ?>">
			</label>
		</div>

		<div class="pw-col-6">
			<fieldset>
				<legend>__Auto-Generated Numbers__</legend>
				<?php
				$options = array();
				$options['seq'] = '__Sequential__';
				$options['random'] = '__Random__';
				?>
				<ul class="pw-inline">
				<?php foreach( $options as $k => $label ) : ?>
					<li>
						<label>
							<input type="radio" name="transactions_sale_ref_auto_method" value="<?php echo $k; ?>"<?php if( $k == $values['transactions_sale_ref_auto_method'] ): ?> checked<?php endif; ?>>
							<span><?php echo $label; ?></span>
						</label>
					</li>
				<?php endforeach; ?>
				</ul>
			</fieldset>
		</div>
	</div>
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