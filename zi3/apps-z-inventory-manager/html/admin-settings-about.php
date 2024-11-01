<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Apps0Z0Inventory0Manager_Html_Admin0Settings0About extends _PW1
{
	public function get( PW1_Request $request, PW1_Response $response )
	{
		$pluginFile = $request->misc['pluginFile'];
		$verString = PW1_::versionStringFromFile( $pluginFile );

		ob_start();
?>

<h2>PlainInventory</h2>

<dl>
	<dt>
		__Installed Version__
	</dt>
	<dd>
		<?php echo $verString; ?>
	</dd>
</dl>

<?php
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}
}