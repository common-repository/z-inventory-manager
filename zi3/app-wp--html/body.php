<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_App0Wp00Html_Body extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{}

	public function __invoke( PW1_Request $request, PW1_Response $response )
	{
		$ret = $response->content;

		$css = $response->css;
		$js = $response->js;

		$request2 = clone $request;
		$request2->method = 'HEAD';
		$response2 = $this->pw1->respond( $request2 );

		$css = array_merge( $css, $response2->css );
		$js = array_merge( $js, $response2->js );

		if( is_admin() ){
			$css['core-wp-admin'] = 'zi3/app-wp--html/assets/wp-admin.css';
		}

		$cssReplace = array(
			'<button class="pw-role-primary"'	=> '<button class="button button-primary button-large"',
// '<a class="pw-role-secondary'			=> '<a class="pw-block page-title-action pw-top-auto',
			'<button class="pw-role-secondary'	=> '<button class="page-title-action pw-top-auto',
			'<button type="submit"'	=> '<button type="submit" class="button button-primary"',
			'<button type="button"'	=> '<button type="button" class="page-title-action pw-top-auto"',
			// '<button '	=> '<button class="button button-primary"',
			// '<a class="pw-menu-item'			=> '<a class="pw-block page-title-action pw-top-auto',
			// '<a class="pw-topmenu-item'			=> '<a class="pw-block page-title-action pw-top-auto',
			);

		foreach( $cssReplace as $from => $to ){
			$ret = str_replace( $from, $to, $ret );
		}

	// process links in nav
		$ma = array();
		preg_match_all( '/\<nav\>.+\<\/nav\>/smU', $ret, $ma );

		$cssReplace = array(
			'<a '	=> '<a style="margin-left: 0px;" class="pw-block page-title-action pw-top-auto" ',
		);

		$count = count( $ma[0] );
		for( $ii = 0; $ii < $count; $ii++ ){
			$from = $ma[0][$ii];
			$to = $from;

			foreach( $cssReplace as $k => $v ){
				$to = str_replace( $k, $v, $to );
			}

			$ret = str_replace( $from, $to, $ret );
		}

	// ASSETS
		$handleId = 1;

		$pluginFile = isset( $request->misc['pluginFile'] ) ? $request->misc['pluginFile'] : NULL;

	// FULL URLS FOR ASSETS
		foreach( array_keys($css) as $ii ){
			$css[$ii] = plugins_url( $css[$ii], $pluginFile );
		}

		foreach( array_keys($js) as $ii ){
			$js[$ii] = plugins_url( $js[$ii], $pluginFile );
		}

		$appVer = $pluginFile ? PW1_::versionNumFromString( PW1_::versionStringFromFile($pluginFile) ) : NULL;

		reset( $css );
		foreach( $css as $src ){
			$handle = 'pw1-' . $handleId++;
			wp_enqueue_style( $handle, $src, array(), $appVer );
		}

		reset( $js );
		foreach( $js as $src ){
			$handle = 'pw1-' . $handleId++;
			wp_enqueue_script( $handle, $src, array(), $appVer );
		}

		ob_start();
?>

<div class="wrap">
<div id="pw1">
<?php echo $ret; ?>
</div>
</div>

<?php 
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function printLayout( PW1_Request $request, PW1_Response $response )
	{
		$assetId = 1;

		$htmlTitle = $response->title;
		$htmlTitle = strip_tags( $htmlTitle );

		$pluginFile = isset( $request->misc['pluginFile'] ) ? $request->misc['pluginFile'] : NULL;

	// FULL URLS FOR ASSETS
		foreach( array_keys($response->css) as $ii ){
			$response->css[$ii] = plugins_url( $response->css[$ii], $pluginFile );
		}

		foreach( array_keys($response->js) as $ii ){
			$response->js[$ii] = plugins_url( $response->js[$ii], $pluginFile );
		}

		ob_start();
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $htmlTitle; ?></title>

<?php foreach( $response->css as $src ) : ?>
<link rel="stylesheet" type="text/css" id="pw1-<?php echo $assetId++; ?>" href="<?php echo $src; ?>">
<?php endforeach; ?>

<?php foreach( $response->js as $src ) : ?>
<script language="JavaScript" type="text/javascript" id="pw1-<?php echo $assetId++; ?>" src="<?php echo $src; ?>"></script>
<?php endforeach; ?>
</head>

<body>

<div id="pw1">
<?php echo $response->content; ?>
</div><!-- /#pw1 -->

</body>
</html>

<?php 
		$content = trim( ob_get_clean() );
		$response->content = $content;

		return $response;
	}
}