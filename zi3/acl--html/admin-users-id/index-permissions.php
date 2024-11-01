<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl00Html_Admin0Users0Id_Index0Permissions extends _PW1
{
	public $acl;
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_ $acl,
		PW1_ZI3_Acl_Query $aclQuery
	)
	{}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		$userId = isset( $request->args[0] ) ? $request->args[0] : $request->currentUserId;

		$catalog = $this->acl->catalog();
		$forUser = $this->aclQuery->findForUser( $userId );

		ob_start();
?>

<section>
<h2>__Permissions__</h2>

<article>
	<ul class="pw-inline">
	<?php foreach( $catalog as $k ) : ?>
		<?php if( isset($forUser[$k]) && $forUser[$k] ) : ?>
			<li class="pw-bg1 pw-strong">
		<?php else : ?>
			<li class="pw-bg1 pw-muted2 pw-line-through">
		<?php endif; ?>
			<?php echo $this->acl->getLabel( $k ); ?>
		</li>
	<?php endforeach; ?>
	</ul>
</article>

</section>

<?php
		$ret = trim( ob_get_clean() );

		$response->content .= $ret;
		return $response;
	}
}