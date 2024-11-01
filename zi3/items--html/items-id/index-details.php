<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_Items0Id_Index0Details extends _PW1
{
	public $aclQuery;
	public $query;
	public $widget;

	public function __construct(
		PW1_ZI3_Items00Html_ $_,
		PW1_ZI3_Acl_Query	$aclQuery,
		PW1_ZI3_Items_Query $query,
		PW1_ZI3_Items00Html_Widget	$widget
	)
	{}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->query->findById( $id );
		$canEdit = $this->aclQuery->userCan( $request->currentUserId, 'manage_items' );

		ob_start();
?>

<section>
<h2>__Details__</h2>

<article>
<div class="pw-grid">
	<div class="pw-col-7">
		<dl>
			<dt>
				__SKU__
			</dt>
			<dd>
				<?php echo esc_html( $model->sku ); ?>
			</dd>
		</dl>
	</div>
	<div class="pw-col-3">
		<dl>
			<dt>
				__State__
			</dt>
			<dd>
				<?php echo $this->widget->presentState( $model->state ); ?>
			</dd>
		</dl>
	</div>
	<div class="pw-col-2">
		<dl>
			<dt>
				__ID__
			</dt>
			<dd>
				<?php echo $model->id; ?>
			</dd>
		</dl>
	</div>
</div>
</article>

<nav>
<ul class="pw-inline">
	<?php if( $canEdit && ($to = $this->_->linkEdit($id)) ) :  ?>
		<li>
			<a href="<?= $to; ?>">__Edit__</a>
		</li>
	<?php endif; ?>
	<?php if( $canEdit && ($to = $this->_->linkDelete($id)) ) :  ?>
		<li>
			<a href="<?= $to; ?>"><span>&times;</span>__Delete__</a>
		</li>
	<?php endif; ?>
</ul>
</nav>
</section>

<?php
		$ret = trim( ob_get_clean() );

		$response->content .= $ret;
		return $response;
	}
}