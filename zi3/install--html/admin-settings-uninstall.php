<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install00Html_Admin0Settings0Uninstall extends _PW1
{
	public $_;

	public function __construct(
		PW1_ZI3_Install_ $_
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Uninstall__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		ob_start();
?>

<form method="post" action="URI:.">

<p>
__All current data will be deleted.__
</p>

<div>
<label>
	<input type="checkbox" name="sure">__Are you sure?__
</label>
</div>

<p>
	<button type="submit">__Confirm Uninstall__</button>
</p>

</form>

<?php
		$ret = trim( ob_get_clean() );

		$response->content = $ret;
		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$post = $request->data;

		$sure = isset( $post['sure'] ) ? $post['sure'] : FALSE;
		if( ! $sure ){
			$response->redirect = '.';
			$response->formErrors[ 'sure' ] = '__Required Field__';
			return $response;
		}

		$response->redirect = '';

		$conf = $this->_->conf();
		$this->_->down( $conf );
		$response->addMessage( '__Uninstall Complete__' );

		return $response;
	}
}