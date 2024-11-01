<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install00Html_InstallOk extends _PW1
{
	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Installation Complete__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$to = 'URI:../../..';
		$debug = FALSE;

		ob_start();
?>

<p>
__Thank you for installing our product.__
</p>

<p>
<a href="<?php echo $to; ?>">__Please proceed to the home page__</a>
</p>

<?php
// $appConfig = $this->pw1->getConfig();

// $appName = $appConfig['_app'];
// $appVersion = $appConfig['_version'];
// $appVersion = str_replace( '.', '', $appVersion );
// $trackPixel = 'https://www.plainware.com/tools/trackinstall/?app=' . $appName . '&ver=' . $appVersion;

// $debug = isset( $appConfig['modules']['dev'] ) ? TRUE : FALSE;
?>

<?php if( $debug ) : ?>
<a href="<?php echo $trackPixel; ?>"><?php echo $trackPixel; ?></a>
<?php else : ?>
<img src="<?php echo $trackPixel; ?>" height="0" width="0" alt=""> 
<META http-equiv="refresh" content='5;URL="<?php echo $to; ?>"'>
<?php endif; ?>

<?php
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}
}