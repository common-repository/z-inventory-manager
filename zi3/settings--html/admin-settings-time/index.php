<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings00Html_Admin0Settings0Time_Index extends _PW1
{
	private $_pNames = array( 'datetime_date_format', 'datetime_time_format', 'datetime_week_starts' );
	public $settings;

	public function __construct(
		PW1_ZI3_Settings_	$settings
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Date and Time__';
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

<?php
$options = array(
	'j M Y'	=> date('j M Y'),

	'n/j/Y'	=> date('n/j/Y'),
	'm/d/Y'	=> date('m/d/Y'),
	'm-d-Y'	=> date('m-d-Y'),
	'm.d.Y'	=> date('m.d.Y'),

	'j/n/Y'	=> date('j/n/Y'),
	'd/m/Y'	=> date('d/m/Y'),
	'd-m-Y'	=> date('d-m-Y'),
	'd.m.Y'	=> date('d.m.Y'),

	'Y/m/d'	=> date('Y/m/d'),
	'Y-m-d'	=> date('Y-m-d'),
	'Y.m.d'	=> date('Y.m.d'),
	);
?>

<div>
	<label>
	<span>__Date Format__</span>
	<select name="datetime_date_format">
	<?php foreach( $options as $k => $v ) : ?>
		<option value="<?php echo $k; ?>" <?php if( $k == $values['datetime_date_format'] ) echo 'selected'; ?>><?php echo esc_html( $v ); ?>
	<?php endforeach; ?>
	</select>
	</label>
</div>

<?php
$options = array(
	'g:ia'	=> date('g:ia'),
	'g:i A'	=> date('g:i A'),
	'H:i'	=> date('H:i'),
	);
?>
<div>
	<fieldset>
		<legend style="display: block; ">
			__Time Format__
		</legend>

		<ul class="pw-inline">
		<?php foreach( $options as $k => $v ) : ?>
			<li>
				<label>
					<input type="radio" name="datetime_time_format" value="<?php echo $k; ?>" <?php if( $k == $values['datetime_time_format'] ) echo 'checked'; ?>>
					<span><?php echo esc_html( $v ); ?></span>
				</label>
			</li>
		<?php endforeach; ?>
		</ul>
	</fieldset>
</div>

<?php
$options = array(
	0	=> '__Sun__',
	1	=> '__Mon__',
	// 2	=> '__Tue__',
	// 3	=> '__Wed__',
	// 4	=> '__Thu__',
	// 5	=> '__Fri__',
	// 6	=> '__Sat__',
	);
?>
<div>
	<fieldset>
		<legend>__Week Starts On__</legend>

		<ul class="pw-inline">
		<?php foreach( $options as $k => $v ) : ?>
			<li>
				<label>
					<input type="radio" name="datetime_week_starts" value="<?php echo $k; ?>" <?php if( $k == $values['datetime_week_starts'] ) echo 'checked'; ?>>
					<span><?php echo esc_html( $v ); ?></span>
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