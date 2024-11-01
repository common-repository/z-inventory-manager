<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_App00Html_Layout extends _PW1
{
	public $translate;
	public $csrf;

	public function __construct(
		PW1_ $pw1,
		PW1_ZI3_App_Translate	$translate,
		PW1_ZI3_App00Html_Csrf	$csrf
	)
	{}

	public function __invoke( PW1_Request $request, PW1_Response $response )
	{
		$isWpAdmin = ( defined('WPINC') && is_admin() ) ? TRUE : FALSE;

		$request2 = clone $request;

	// head request for title and menu
		$request2 = clone $request;
		$request2->method = 'HEAD';
		$response2 = $this->pw1->respond( $request2 );
		$menu = $response2->menu;
		$title = $response2->title;

		$response->css['core']	= 'zi3/app--html/assets/core.css';

	// top menu
		if( strlen($request->slug) ){
			$request2->slug = '';
			$response2 = $this->pw1->respond( $request2 );
			$topMenu = $response2->menu;
		}
		else {
			$topMenu = $menu;
		}

	/* prepare menu */
		ksort( $menu );

		$menuView = array();
		foreach( $menu as $menuItem ){
			$menuView[] = $this->self->renderMenuItem( $menuItem );
		}

		$contentAsMenu = array();
		if( ! $response->content ){
			if( $menuView ){
				$contentAsMenu = $menuView;
				$menuView = array();
			}
		}

	// topmenu
		ksort( $topMenu );
		$topMenuView = array();
		foreach( $topMenu as $menuItem ){
			$topMenuView[] = $this->self->renderMenuItem( $menuItem );
		}

	// breadcrumbs
		$breadcrumbs = array();
		if( strlen($request->slug) ){
			$parentSlug = $request->slug;
			do {
				$slugParts = explode( '/', $parentSlug );
				array_pop( $slugParts );
				$parentSlug = join( '/', $slugParts );

				$request2->slug = $parentSlug;
				$response2 = $this->pw1->respond( $request2 );
				$parentTitle = $response2->title;

				if( null === $parentTitle ) $parentTitle = '';
				if( strlen($parentTitle) ){
					$breadcrumbs[ $parentSlug ] = $parentTitle;
				}
			}
			while( $parentSlug );
		}

		$breadcrumbs = array_reverse( $breadcrumbs );

		$breadcrumbsView = array();
		foreach( $breadcrumbs as $bslug => $btitle ){
		// skip root
			if( ! strlen($bslug) ) continue;
			if( count($breadcrumbsView) ) $breadcrumbsView[] = '&raquo;';
			$menuItem = array( $bslug, $btitle );
			$breadcrumbsView[] = $this->self->renderMenuItem( $menuItem );
		}

		$content = $this->self->contentLayout( $request, $response );
		$topHeaderView = $this->self->topHeader( $request, $response );

		ob_start();
?>

<header>

	<?php if( $topHeaderView ) : ?>
		<?php echo $topHeaderView; ?>
	<?php endif; ?>

	<?php if( $topMenuView ) : ?>
		<div class="nav-tab-wrapper">
			<?php foreach( $topMenu as $menuItem ) : ?>
				<?php
				$current = false;
				if( $menuItem[0] == substr($request->slug, 0, strlen($menuItem[0])) ){
					$current = true;
				}
				?>
				<a class="nav-tab<?php if( $current ) : ?> nav-tab-active<?php endif; ?>" href="URI:<?php echo $menuItem[0]; ?>" title="<?php echo $menuItem[1]; ?>"><?php echo $menuItem[1]; ?></a>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>

	<?php if( $breadcrumbsView ) : ?>
		<nav class="">
			<ul class="pw-inline">
				<?php foreach( $breadcrumbsView as $e ) : ?>
				<li><?php echo $e; ?></li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>

	<?php if( $menuView  ) : ?>
		<div class="pw-hori" style="margin-top: .5em;">
			<div>
				<h1 style="padding: 0;"><?php echo $title; ?></h1>
			</div>
			<div>
				<?php echo $this->renderMenu( $menuView ); ?>
			</div>
		</div>
	<?php else : ?>
		<h1><?php echo $title; ?></h1>
	<?php endif; ?>

</header>

<div class="pw1-page-content">
	<?php if( $contentAsMenu ) : ?>
		<?php echo $this->renderMenu( $contentAsMenu ); ?>
	<?php else : ?>
		<?php echo $content; ?>
	<?php endif; ?>
</div>

<?php 
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function topHeader( PW1_Request $request, PW1_Response $response )
	{
		$ret = '';
		return $ret;
	}

	public function partialLayout( PW1_Request $request, PW1_Response $response )
	{
// echo "PARTIAL LAYOUT FOR $request->slug<br>";
// _print_r( $content );

	// head request for title and menu
		$request2 = clone $request;
		$request2->method = 'HEAD';
		$response2 = $this->pw1->respond( $request2 );
		$menu = $response2->menu;
		$title = $response2->title;

	/* prepare menu */
		ksort( $menu );
		$menuView = array();
		foreach( $menu as $menuItem ){
			$menuView[] = $this->self->renderMenuItem( $menuItem );
		}

		$content = $this->self->contentLayout( $request, $response );

		ob_start();
?>

<?php if( strlen($title) ) : ?>
<h2><?php echo $title; ?></h2>
<?php endif; ?>

<?php echo $content; ?>

<?php if( $menuView  ) : ?>
	<?php echo $this->renderMenu( $menuView ); ?>
<?php endif; ?>

<?php 
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function printLayout( PW1_Request $request, PW1_Response $response )
	{
		$request2 = clone $request;

	// head request for title and menu
		$request2 = clone $request;
		$request2->method = 'HEAD';
		$response2 = $this->pw1->respond( $request2 );
		$title = $response2->title;

		$response->css['core']	= 'zi3/app--html/assets/core.css';

		$ret = $this->self->contentLayout( $request, $response );

	// remove nav's
		$ma = array();
		preg_match_all( '/\<nav\>.+\<\/nav\>/smU', $ret, $ma );

		$count = count( $ma[0] );
		for( $ii = 0; $ii < $count; $ii++ ){
			$from = $ma[0][$ii];
			$to = '';
			$ret = str_replace( $from, $to, $ret );
		}

	// remove other a's
		$ma = array();
		preg_match_all( '/\<a[\s].+\>(.+)\<\/a\>/smU', $ret, $ma );

		$count = count( $ma[0] );
		for( $ii = 0; $ii < $count; $ii++ ){
			$from = $ma[0][$ii];
			// $to = $ma[1][$ii];
			$to = '<a>' . $ma[1][$ii] . '</a>';
			$ret = str_replace( $from, $to, $ret );
		}

		ob_start();
?>

<h1><?php echo $title; ?></h1>

<?php echo $ret; ?>

<?php 
		$ret = trim( ob_get_clean() );
		$response->content = $ret;

		return $response;
	}

	public function contentLayout( PW1_Request $request, PW1_Response $response )
	{
		$isWpAdmin = ( defined('WPINC') && is_admin() ) ? TRUE : FALSE;
		// $isWpAdmin = FALSE;

		$errors = $response->getErrors();
		$messages = $response->getMessages();
		$debugs = $response->getDebugs();

		ob_start();
?>

<?php if( $messages OR $errors OR $debugs ) : ?>
	<?php if( $errors ) : ?>

		<?php if( $isWpAdmin ) : ?>
			<div class="notice notice-error is-dismissible">
				<p>
				<?php echo join( '<br/>', $errors ); ?>
				</p>
			</div>
		<?php else : ?>
			<div class="pw-message pw-bg-lightred">
				<?php echo join( '<br/>', $errors ); ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php if( $messages ) : ?>

		<?php if( $isWpAdmin ) : ?>
			<div class="notice notice-success is-dismissible pw-auto-dismiss">
				<p>
				<?php echo join( '<br/>', $messages ); ?>
				</p>
			</div>
		<?php else : ?>
			<div class="pw-message pw-auto-dismiss pw-bg-lightgreen">
				<?php echo join( '<br/>', $messages ); ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

	<?php if( $debugs ) : ?>

		<?php if( $isWpAdmin ) : ?>
			<div class="notice notice-warning is-dismissible">
				<p>
				<?php echo join( '<br/>', $debugs ); ?>
				</p>
			</div>
		<?php else : ?>
			<div class="pw-message pw-bg-lightorange">
				<?php echo join( '<br/>', $debugs ); ?>
			</div>
		<?php endif; ?>

	<?php endif; ?>

<?php endif; ?>

<?php echo $response->content; ?>

<?php 
		$ret = trim( ob_get_clean() );
		return $ret;
	}

	public function finalize( PW1_Request $request, PW1_Response $response )
	{
		$ret = $response->content;

	// csrf
		$ret = $this->csrf->render( $ret );

	// translate
		$ret = $this->translate->translateText( $ret );

		$response->content = $ret;
		return $response;
	}

	public function renderMenuItem( $item )
	{
		if( ! is_array($item) ) $item = array( $item );

		$to = NULL;
		$label = NULL;

		if( 1 === count($item) ){
			list( $label ) = $item;
		}

		if( 2 === count($item) ){
			list( $to, $label ) = $item;
		}

		$title = strip_tags( $label );

		ob_start();
?>

<?php if( NULL !== $to ) : ?>
	<a href="URI:<?php echo $to; ?>" title="<?php echo $title; ?>"><?php echo $label; ?></a>
<?php else : ?>
	<?php echo $label; ?>
<?php endif; ?>

<?php
		$ret = trim( ob_get_clean() );
		return $ret;
	}

	public function renderMenu( array $menuView )
	{
		ob_start();
?>
	<?php if( $menuView  ) : ?>
		<?php $menuHtmlId = rand( 1000, 9999 ); ?>
		<nav>
			<div class="pw-lg-hide">
				<input type="checkbox" id="pw-submenu-<?php echo $menuHtmlId; ?>" class="pw-collapse">

				<ul class="pw-collapse-off">
					<li>
						<label role="button" for="pw-submenu-<?php echo $menuHtmlId; ?>" title="__Menu__">
							<span>&#9776;</span>__Menu__
						</label>
					</li>
				</ul>

				<ul class="pw-collapse-on">
					<?php foreach( $menuView as $e ) : ?>
						<li>
							<?php echo $e; ?>
						</li>
					<?php endforeach; ?>

					<li>
						<label role="button" for="pw-submenu-<?php echo $menuHtmlId; ?>" title="__Close Menu__">
							<span>&times;</span>__Close Menu__
						</label>
					</li>
				</ul>
			</div>

			<ul class="pw-inline pw-xs-hide">
				<?php foreach( $menuView as $e ) : ?>
					<li>
						<?php echo $e; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</nav>
	<?php endif; ?>

<?php
		$ret = trim( ob_get_clean() );
		return $ret;
	}
}