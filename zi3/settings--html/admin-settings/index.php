<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings00Html_Admin0Settings_Index extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Settings__';

		$response->menu[ '11-time' ] = array( './time', '__Date and Time__' );
		// $response->menu[ '12-email' ] = array( './email', '__Email__' );

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
	// parents menu
		$request2 = clone $request;
		$request2->method = 'HEAD';
		$response2 = $this->pw1->respond( $request2 );
		$menu = $response2->menu;
		if( ! $menu ) return $response;

		ksort( $menu );
		$e = current( $menu );
		$response->redirect = $e[0];

		return $response;
	}

	public function afterGet( PW1_Request $request, PW1_Response $response )
	{
	// find parents menu
		$parentSlug = PW1_Uri::finalizeSlug( '..', $request->slug );

		$request2 = clone $request;
		$request2->slug = $parentSlug;
		$request2->method = 'HEAD';
		$response2 = $this->pw1->respond( $request2 );
		$parentMenu = $response2->menu;

		if( ! $parentMenu ) return $response;

		ksort( $parentMenu );
		$ret = $response->content;

		ob_start();
?>

<div class="pw-grid">
	<div class="pw-col-2">
		<nav>
			<ul>
			<?php foreach( $parentMenu as $e ) : ?>
				<?php
				$thisSlug = PW1_Uri::finalizeSlug( $e[0], $parentSlug );
				?>
				<li>
					<?php if( $thisSlug == $request->slug ) : ?>
						<span>&rarr; <strong><?php echo $e[1]; ?></strong></span>
					<?php else : ?>
						<a href="URI:<?php echo $thisSlug; ?>"><?php echo $e[1]; ?></a>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</nav>
	</div>

	<div class="pw-col-9">
		<?php echo $ret; ?>
	</div>
</div>

<?php 
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}
}