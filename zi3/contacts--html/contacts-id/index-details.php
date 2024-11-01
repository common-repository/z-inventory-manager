<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts00Html_Contacts0Id_Index0Details extends _PW1
{
	public $aclQuery;
	public $query;
	public $widget;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery,
		PW1_ZI3_Contacts_Query $query,
		PW1_ZI3_Contacts00Html_Widget	$widget
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$canEdit = $this->aclQuery->userCan( $request->currentUserId, 'manage_contacts' );

		if( $canEdit ){
			$response->menu[ '11-edit' ]		= array( './edit', '__Edit__' );
		}

		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$id = $request->args[0];
		$model = $this->query->findById( $id );

		$canEdit = $this->aclQuery->userCan( $request->currentUserId, 'manage_contacts' );

		ob_start();
?>

<section>
<h2>__Details__</h2>

<article>
<div class="pw-grid">
	<div class="pw-col-5">
		<dl>
			<dt>
				__Phone Number__
			</dt>
			<dd>
				<?php echo esc_html( $model->phone ); ?>
			</dd>
		</dl>
	</div>
	<div class="pw-col-5">
		<dl>
			<dt>
				__Email__
			</dt>
			<dd>
				<?php echo esc_html( $model->email ); ?>
			</dd>
		</dl>
	</div>
	<div class="pw-col-1">
		<dl>
			<dt>
				__State__
			</dt>
			<dd>
				<?php echo $this->widget->presentState( $model->state ); ?>
			</dd>
		</dl>
	</div>
	<div class="pw-col-1">
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
		<?php if( $canEdit ) : ?>
		<li>
			<a href="URI:./edit">__Edit__</a>
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