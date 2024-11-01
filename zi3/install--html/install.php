<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install00Html_Install extends _PW1
{
	public $importZi2;
	public $install;

	public function __construct(
		PW1_ $pw1,
		PW1_ZI3_Install00Db_ImportZi2 $importZi2,
		PW1_ZI3_Install_ $install
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Install__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		ob_start();
?>

<form method="post" action="URI:.">

<?php
$options = array();

if( $this->importZi2->has() ){
	$options['zi2'] = '__Import Database From Z Inventory Manager Ver.2__';
}

$options[] = '__Start With Blank Database__';

$keys = array_keys( $options );
$value = current( $keys );
?>

<div>
<label>
<select name="script">
<?php foreach( $options as $k => $v ) : ?>
	<option value="<?php echo $k; ?>"><?php echo $v; ?>
<?php endforeach; ?>
</select>
</label>
</div>

<p>
	<button type="submit">__Continue__</button>
</p>

</form>

<?php
		$ret = trim( ob_get_clean() );

		$response->content = $ret;
		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$conf = $this->install->conf();
		$this->install->up( $conf );

		$post = $request->data;

		$script = ( isset($post['script']) && $post['script'] ) ? $post['script'] : NULL;
		if( 'zi2' == $script ){
			$errors = $this->importZi2->run();
			foreach( $errors as $e ) $response->addError( $e->getMessage() );
		}

		$response->redirect = 'installok';
		$response->addMessage( '__Installation Complete__' );

		return $response;
	}
}