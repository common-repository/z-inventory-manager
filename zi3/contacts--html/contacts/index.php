<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts00Html_Contacts_Index extends _PW1
{
	const MODE_SELECT = 'select';
	const MODE_LIST = 'list';

	public $aclQuery;
	public $widget;
	public $model;
	public $query;
	public $widgetPager;
	public $q;

	public function __construct(
		PW1_ZI3_Acl_Query $aclQuery,
		PW1_ZI3_Contacts00Html_Widget $widget,
		PW1_ZI3_Contacts_Model $model,
		PW1_ZI3_Contacts_Query $query,
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

	public function post( PW1_Request $request, PW1_Response $response )
	{
		$response->redirect = '.';
		return $response;
	}

	public function whichType( PW1_Request $request )
	{
		$ret = NULL;

		if( isset($request->params['*filter-type']) ){
			$filterTypes = $request->params['*filter-type'];
			if( 1 == count($filterTypes) ) $ret = current( $filterTypes );
		}

		return $ret;
	}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$mode = $this->self->whichMode( $request );
		$filterType = $this->self->whichType( $request );

		$canEdit = $this->aclQuery->userCan( $request->currentUserId, 'manage_contacts' );

		if( static::MODE_SELECT == $mode ){
			$response->title = '__Select Contact__';
			if( PW1_ZI3_Contacts_Model::TYPE_CUSTOMER == $filterType ) $response->title = '__Select Customer__';
			if( PW1_ZI3_Contacts_Model::TYPE_SUPPLIER == $filterType ) $response->title = '__Select Supplier__';
		}
		else {
			$response->title = '__Contacts__';

			if( $canEdit ){
				$response->menu[ '11-new-1' ] = array( './new-' . PW1_ZI3_Contacts_Model::TYPE_CUSTOMER, '<span>+</span>__New Customer__' );
				$response->menu[ '11-new-2' ] = array( './new-' . PW1_ZI3_Contacts_Model::TYPE_SUPPLIER, '<span>+</span>__New Supplier__' );
			}
		}

		if( $canEdit ){
			if( $filterType ){
				if( PW1_ZI3_Contacts_Model::TYPE_CUSTOMER == $filterType ){
					$response->menu[ '11-new-1' ] = array( './new-' . PW1_ZI3_Contacts_Model::TYPE_CUSTOMER, '<span>+</span>__Add New__' );
				}
				if( PW1_ZI3_Contacts_Model::TYPE_SUPPLIER == $filterType ){
					$response->menu[ '11-new-1' ] = array( './new-' . PW1_ZI3_Contacts_Model::TYPE_SUPPLIER, '<span>+</span>__Add New__' );
				}
			}
			else {
				$response->menu[ '11-new-1' ] = array( './new-' . PW1_ZI3_Contacts_Model::TYPE_CUSTOMER, '<span>+</span>__New Customer__' );
				$response->menu[ '11-new-2' ] = array( './new-' . PW1_ZI3_Contacts_Model::TYPE_SUPPLIER, '<span>+</span>__New Supplier__' );
			}
		}

		return $response;
	}

	public function getBefore( PW1_Request $request )
	{
		if( ! isset($request->params['*q']) ) $request->params['*q'] = $this->q->construct();
		return $request;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$mode = $this->self->whichMode( $request );
		$type = $this->self->whichType( $request );

		$q = $request->params['*q'];
		$q->orderBy( 'title' );

		$totalCount = $this->query->count( $q );

		$perPage = 12;
		list( $limit, $offset ) = $this->widgetPager->getLimitOffset( $request->params, $perPage );

		$q
			->limit( $limit )
			->offset( $offset )
			;

		$models = $this->query->find( $q );

		$pagerView = $this->widgetPager->render( $totalCount, $limit, $offset );

		ob_start();
?>

<?php if( $models ) : ?>
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
								<?php
								$backParam = isset( $request->params['backparam'] ) ? $request->params['backparam'] : 'id';
								$to = 'URI:..?' . $backParam . '=' . $model->id . '&backparam=NULL&mode=NULL';
								?>
								<a href="<?php echo $to; ?>"><strong>__Select__</strong></a>
							</li>
						<?php endif; ?>
					</ul>

				</div>
			<?php endforeach; ?>
			</div>
		</section>
	<?php endforeach; ?>

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
}