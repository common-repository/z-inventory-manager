<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items_Index extends _PW1
{
	const MODE_SELECT = 'select';
	const MODE_LIST = 'list';
	public $aclQuery;
	public $widget;
	public $financeWidget;
	public $model;
	public $query;
	public $widgetPager;
	public $q;

	public function __construct(
		PW1_ZI3_Items00Html_ $_,
		PW1_ZI3_Acl_Query $aclQuery,
		PW1_ZI3_Items00Html_Widget $widget,
		PW1_ZI3_Finance00Html_Widget $financeWidget,
		PW1_ZI3_Items_Model $model,
		PW1_ZI3_Items_Query $query,
		PW1_Html_Widget_Pager $widgetPager,
		PW1_Q $q
	)
	{}

	public function whichMode( PW1_Request $request )
	{
		$ret = static::MODE_LIST;
		if( isset($request->params['*mode']) ) $ret = $request->params['*mode'];
		if( isset($request->params['mode']) ) $ret = $request->params['mode'];

		return $ret;
	}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$mode = $this->self->whichMode( $request );
		$canEdit = $this->aclQuery->userCan( $request->currentUserId, 'manage_items' );

		if( static::MODE_SELECT == $mode ){
			$response->title = '__Select Items__';
		}
		else {
			$response->title = '__Inventory__';
		}

		if( $canEdit && ($to = $this->_->linkNew()) ){
			$response->menu[ '101-new' ] = array( $to, '<span>+</span>__New Item__' );
		}

		return $response;
	}

	public function beforeGet( PW1_Request $request )
	{
		if( ! isset($request->params['*q']) ) $request->params['*q'] = $this->q->construct();
		return $request;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$mode = $this->self->whichMode( $request );

		$q = $request->params['*q'];
		$q->orderBy( 'title' );

		if( isset($request->params['skip']) ){
			$skipIds = $request->params['skip'];
			if( ! is_array($skipIds) ) $skipIds = array( $skipIds );
			$q->where( 'id', '<>', $skipIds );
		}

		$totalCount = $this->query->count( $q );

		$perPage = 10;
		list( $limit, $offset ) = $this->widgetPager->getLimitOffset( $request->params, $perPage );

		$q
			->limit( $limit )
			->offset( $offset )
			;

		$models = $this->query->find( $q );
		$pagerView = $this->widgetPager->render( $totalCount, $limit, $offset );

		$backParam = isset( $request->params['backparam'] ) ? $request->params['backparam'] : 'id';
		// $to = 'URI:..?' . $backParam . '=' . $model->id . '&backparam=NULL&mode=NULL';

		ob_start();
?>

<?php if( $models ) : ?>
	<?php if( static::MODE_SELECT == $mode ) : ?>
		<form method="post" action="URI:.">
	<?php endif; ?>

	<?php
	$rows = array_chunk( $models, 4 );
	?>
	<?php foreach( $rows as $thisModels ) : ?>
		<section>
			<div class="pw-grid">
			<?php foreach( $thisModels as $model ) : ?>
				<div class="pw-col-3">

					<ul class="pw-box">
						<li>
							<?php echo $this->widget->render( $model ); ?>
						</li>
						<?php if( static::MODE_SELECT == $mode ) : ?>
							<li>
								<label>
									<input type="checkbox" name="<?php echo $backParam; ?>[]" value="<?php echo $model->id; ?>" title="__Select__"><span>__Select__</span>
								</label>
							</li>
						<?php endif; ?>
					</ul>

				</div>
			<?php endforeach; ?>
			</div>
		</section>
	<?php endforeach; ?>

	<?php if( static::MODE_SELECT == $mode ) : ?>
		<p>
			<button type="submit">__Use Selected Items__</button>
		</p>
		</form>
	<?php endif; ?>

<?php else : ?>

	<section>
		<article>
			__No Results__
		</article>
	</section>

<?php endif; ?>

<?php if( strlen($pagerView) ) : ?>
<section>
	<?php echo $pagerView; ?>
</section>
<?php endif; ?>

<?php 
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$mode = $this->self->whichMode( $request );
		if( static::MODE_SELECT == $mode ){
			return $this->self->postSelector( $request, $response );
		}
	}

	public function postSelector( PW1_Request $request, PW1_Response $response )
	{
		$post = $request->data;

		$backParam = isset( $request->params['backparam'] ) ? $request->params['backparam'] : 'id';
		$ids = isset( $post[$backParam] ) ? $post[$backParam] : array();

		$alreadyIds = isset( $request->params['_' . $backParam] ) ? $request->params['_' . $backParam] : array();

		$ids = array_merge( $alreadyIds, $ids );
		$ids = array_unique( $ids );

		$response->params[ $backParam ] = $ids;
		$response->redirect = '..';

		return $response;
	}
}