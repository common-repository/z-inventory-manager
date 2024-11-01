<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users0Wp00Html_Admin0Users_Index extends _PW1
{
	public $widgetPager;
	public $widget;
	public $model;
	public $query;
	public $aclQuery;
	public $aclQueryRoles;
	public $aclWidgetRoles;
	public $q;

	public function __construct(
		PW1_Html_Widget_Pager $widgetPager,
		PW1_ZI3_Users00Html_Widget $widget,
		PW1_ZI3_Users_Model $model,
		PW1_ZI3_Users_Query $query,
		PW1_ZI3_Acl_Query $aclQuery,
		PW1_ZI3_Acl_Roles_Query $aclQueryRoles,
		PW1_ZI3_Acl00Html_Widget_Roles $aclWidgetRoles,
		PW1_Q $q
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Users__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$q = $this->q->construct();
		$q->orderBy( 'title' );

		$totalCount = $this->query->count( $q );

		$perPage = 10;
		list( $limit, $offset ) = $this->widgetPager->getLimitOffset( $request->params, $perPage );

		$q
			->limit( $limit )
			->offset( $offset )
			;

		$models = $this->query->find( $q );

		$d = array();
		foreach( $models as $model ){
			$d[ $model->id ] = $this->self->content( $model );
		}

		$pagerView = $this->widgetPager->render( $totalCount, $limit, $offset );

		ob_start();
?>

<p>
__The list of WordPress users who can access our plugin.__
</p>

<?php if( $models ) : ?>
	<?php echo $this->self->renderContent( $d, $pagerView ); ?>
<?php else : ?>
	<div class="pw-box">
		__No Results__
	</div>
<?php endif; ?>

<?php 
		$ret = trim( ob_get_clean() );

		$response->content = $ret;
		return $response;
	}

	public function renderContent( array $d, $pagerView )
	{
		$header = current( $d );

		ob_start();
?>

<table>
<thead>
<tr>
<?php foreach( $header as $k => $v ) : ?>
	<td<?php if( isset($v['class']) ) : ?> class="<?php echo $v['class']; ?>"<?php endif; ?>>
		<?php echo $v['header']; ?>
	</td>
<?php endforeach; ?>
</tr>
</thead>

<tbody>
<?php foreach( $d as $id => $d2 ) : ?>
<tr>
<?php foreach( $d2 as $k => $v ) : ?>
	<td title="<?php echo esc_attr($v['header']); ?>"<?php if( isset($v['class']) ) : ?> class="<?php echo $v['class']; ?>"<?php endif; ?>>
		<?php echo $v['content']; ?>
	</td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>

</tbody>

<?php if( strlen($pagerView) ) : ?>
<tfoot>
<tr>
	<td colspan="<?php echo count($header); ?>">
		<?php echo $pagerView; ?>
	</td>
</tr>
</tfoot>
<?php endif; ?>


</table>

<?php 
		return trim( ob_get_clean() );
	}

	public function content( _PW1_ZI3_Users0Wp_Model $model )
	{
		$ret = array();

		$ret['title']['content']	= $this->widget->presentTitle( $model, TRUE );
		$ret['title']['header']		= '__WordPress User__';
		$ret['title']['class']		= 'pw-col-5';

		$ret['role']['content']		= $this->self->contentPluginRole( $model );
		$ret['role']['header']		= '__Plugin Role__';
		$ret['role']['class']		= 'pw-col-7';

		return $ret;
	}

	public function contentPluginRole( _PW1_ZI3_Users0Wp_Model $model )
	{
		$role = $this->aclQueryRoles->findForUser( $model );

		ob_start();
?>

<dl>
<dt>
	<?php echo $this->aclWidgetRoles->presentTitle( $role, TRUE ); ?>
</dt>
<dd>
	<?php echo $this->aclWidgetRoles->presentPermissions( $role ); ?>
</dd>
</dl>

<?php 
		return ob_get_clean();
	}
}