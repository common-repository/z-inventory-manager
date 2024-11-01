<?php
class PW1_ZI3_Acl00Html_Widget_Roles extends _PW1
{
	public $acl;

	public function __construct(
		PW1_ZI3_Acl_ $acl
	)
	{}

	public function presentTitle( _PW1_ZI3_Acl_Roles_Model $model, $linkTo = NULL )
	{
		$ret = $model->title;
		$attrTitle = $ret;

		if( NULL !== $linkTo ){
			if( TRUE === $linkTo ) $linkTo = 'URI:admin/users/acl-roles/' . $model->id;
		}

	// builtin?
		if( $model->id < 0 ) $linkTo = NULL;

		ob_start();
?>

<?php if( NULL === $linkTo ) : ?>
	<span title="<?php echo esc_attr($attrTitle); ?>"><?php echo esc_html($ret); ?></span>
<?php else : ?>
	<a href="<?php echo $linkTo; ?>" title="<?php echo esc_attr($attrTitle); ?>"><?php echo esc_html($ret); ?></a>
<?php endif; ?>

<?php
		return trim( ob_get_clean() );
	}

	public function presentPermissions( _PW1_ZI3_Acl_Roles_Model $model )
	{
		$catalog = $this->acl->catalog();

		$permissions = $model->permissions;
		$permissions = $this->acl->finalizePermissions( $permissions );

		$all = TRUE;
		reset( $catalog );
		foreach( $catalog as $k ){
			if( (!isset($permissions[$k])) OR (! $permissions[$k]) ){
				$all = FALSE;
				break;
			}
		}

		$none = TRUE;
		reset( $catalog );
		foreach( $catalog as $k ){
			if( isset($permissions[$k]) ){
				if( $permissions[$k] ){
					$none = FALSE;
					break;
				}
			}
			else {
				// if( $default ){
					// $none = FALSE;
					// break;
				// }
			}
		}


		ob_start();
?>

<?php if( $all ) : ?>

	- __All Permissions__ - 

<?php elseif( $none ) : ?>

	- __No Permissions__ - 

<?php else : ?>

	<ul class="pw-inline">
	<?php foreach( $permissions as $k => $v ) : ?>
	<?php		if( ! $v ) continue; ?>
		<li class="pw-bg1 pw-rounded">
			<?php echo $this->acl->getLabel( $k ); ?>
		</li>
	<?php endforeach; ?>
	</ul>

<?php endif; ?>

<?php
		return trim( ob_get_clean() );
	}
}