<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items0Selector_Index extends _PW1
{
	public $index;
	public $indexPost;
	public $widgetPager;
	public $q;
	public $query;
	public $widget;

	public function __construct(
		PW1_ZI3_Items00Html_Items_Get $index,
		PW1_ZI3_Items00Html_Items_Post $indexPost,
		PW1_Html_Widget_Pager $widgetPager,
		PW1_Q $q,
		PW1_ZI3_Items_Query $query,
		PW1_ZI3_Items00Html_Widget $widget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Item Selector__';

		$response->menu[ '101-new' ] = array( './new', '<span>+</span>__New Item__' );

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$q = $this->q->construct();

	// forced to skip
		$skipIds = array();
		if( isset($request->params['*skip']) ) $skipIds = array_merge( $skipIds, $request->params['*skip'] );
		if( isset($request->params['skip']) ) $skipIds = array_merge( $skipIds, $request->params['skip'] );

		if( $skipIds ){
			$skipIds = array_map( 'intval', $skipIds );
			$q->where( 'id', '<>', $skipIds );
		}

	// currently in cart
		$inCartIds = isset( $request->params['cart'] ) ? $request->params['cart'] : array();
		$inCart = $inCartIds ? $this->query->findById( $inCartIds ) : array();
		if( $inCartIds ){
			$inCartIds = array_map( 'intval', $inCartIds );
			$q->where( 'id', '<>', $inCartIds );
		}

	// general
		$q = $this->index->filterQuery( $q, $request );

		$totalCount = $this->query->count( $q );

		$perPage = 20;
		list( $limit, $offset ) = $this->widgetPager->getLimitOffset( $request->params, $perPage );

		$q
			->limit( $limit )
			->offset( $offset )
			;

		$models = $this->query->find( $q );

		$d = array();
		foreach( $models as $model ){
			$thisRet = $this->index->content( $model );

			$prependRet = array();

			$prependRet['select']['header'] = '__Select__';
			$prependRet['select']['class'] = 'pw-col-1 pw-lg-align-center';
			$prependRet['select']['content'] = $this->self->contentSelector( $model );

			$thisRet = $prependRet + $thisRet;
			$d[ $model->id ] = $thisRet;
		}

		$pagerView = $this->widgetPager->render( $totalCount, $limit, $offset );
		$filterView = $this->index->renderFilter( $request );

		ob_start();
?>

<div class="pw-grid">
	<div class="pw-col-9">

		<?php if( $models ) : ?>

			<form method="post" action="URI:.">

				<?php echo $this->index->renderContent( $d, $pagerView ); ?>

				<p>
				<?php if( 0 ) : ?>
					<button type="submit">__Add To Cart__</button>
				<?php endif; ?>
					<button type="submit">__Add Selected Items__</button>
				</p>

			</form>

		<?php else : ?>

			<div class="pw-box">
				__No Results__
			</div>

		<?php endif; ?>

	</div>

	<div class="pw-col-3">
		<ul>

		<?php if( $inCart ) : ?>
			<li>
				<div class="pw-box">
					<form method="post" action="URI:.?checkout=1">

					<?php $htmlId = 'pw1_collapse_' . rand( 1000, 9999 ); ?>
					<input type="checkbox" class="pw-collapse" name="<?php echo $htmlId; ?>" id="<?php echo $htmlId; ?>">
					<ul class="pw-collapse-off">
						<li>
							<label role="button" for="<?php echo $htmlId; ?>"><span>&darr;</span>__Cart__ [<?php echo count( $inCart ); ?>]</label>
						</li>
						<li>
							<button type="submit">__Proceed__<span>&rarr;</span></button>
						</li>
					</ul>

					<ul class="pw-collapse-on">
						<li>
							<label role="button" for="<?php echo $htmlId; ?>"><span>&uarr;</span>__Cart__ [<?php echo count( $inCart ); ?>]</label>
						</li>
						<li>
							<ul>
								<?php foreach( $inCart as $e ) : ?>
								<li>
									<a href="" class="pw-float-right pw-block" style="display: block2; "><span>&times;</span>__Remove__</a>
									<?php echo $this->widget->presentTitle( $e, TRUE ); ?>
								</li>
								<?php endforeach; ?>
							</ul>
						</li>
						<li>
							<button type="submit">__Proceed__<span>&rarr;</span></button>
						</li>
					</ul>
					</form>
				</div>
			</li>
		<?php endif; ?>

		<li>
			<div class="pw-box">
				<?php echo $filterView; ?>
			</div>
		</li>
	</div>
</div>

<?php 
		$ret = trim( ob_get_clean() );

		$response->content = $ret;
		return $response;
	}

	public function contentSelector( _PW1_ZI3_Items_Model $model )
	{
		ob_start();
?>

<label>
	<input type="checkbox" name="id[]" value="<?php echo $model->id; ?>" title="__Select__"><span class="pw-lg-hide">__Select__</span>
</label>

<?php 
		return trim( ob_get_clean() );
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		if( isset($request->params['checkout']) && $request->params['checkout'] ){
			return $this->self->postCheckout( $request, $response );
		}

		if( isset($request->params['filter']) && $request->params['filter'] ){
			return $this->self->postFilter( $request, $response );
		}

		$backParam = isset( $request->params['backparam'] ) ? $request->params['backparam'] : 'id';

		$response->redirect = '.';

		$post = $request->data;

		$ids = isset( $post['id'] ) ? $post['id'] : array();
		if( ! is_array($ids) ) $ids = array( $ids );

		if( ! $ids ){
			return $response;
		}

		// $cart = isset( $request->params['cart'] ) ? $request->params['cart'] : array();
		// foreach( $ids as $id ) $cart[] = $id;
		// $response->params['cart'] = $cart;
		// $response->params['offset'] = NULL;
		// $response->redirect = '.';

		$back = isset( $request->params['_' . $backParam] ) ? $request->params['_' . $backParam] : array();
		$back = array_merge( $back, $ids );

		$response->params[$backParam] = $back;
		$response->params['offset'] = NULL;

		$response->redirect = '..';

		return $response;
	}

	public function postCheckout( PW1_Request $request, PW1_Response $response )
	{
		$backParam = isset( $request->params['backparam'] ) ? $request->params['backparam'] : 'id';

		$back = isset( $request->params['_' . $backParam] ) ? $request->params['_' . $backParam] : array();
		$cart = isset( $request->params['cart'] ) ? $request->params['cart'] : array();

		$back = array_merge( $back, $cart );
		$back = array_unique( $back );

		$response->params[$backParam] = $back;
		$response->params['offset'] = NULL;

		$response->redirect = '..';
		return $response;
	}

	public function postFilter( PW1_Request $request, PW1_Response $response )
	{
		return $this->indexPost->filter( $request, $response );
	}
}