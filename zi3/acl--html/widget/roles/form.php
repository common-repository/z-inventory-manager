<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl00Html_Widget_Roles_Form extends _PW1
{
	public $acl;

	public function __construct(
		PW1_ZI3_Acl_ $acl
	)
	{}

	public function render( _PW1_ZI3_Acl_Roles_Model $model )
	{
		$catalog = $this->acl->catalog();

		$permissions = $model->permissions;
		$permissions = $this->acl->finalizePermissions( $permissions );

		ob_start();
?>

<div>
	<label>
		<span>__Title__</span>
		<input type="text" name="title" value="<?php echo esc_attr( $model->title ); ?>" required>
	</label>
</div>

<div>
	<fieldset>
		<legend>__Permissions__</legend>
		<ul class="pw-inline">
		<?php foreach( $catalog as $k ) : ?>
			<?php
			$label = $this->acl->getLabel( $k );
			$on = isset( $permissions[$k] ) && $permissions[$k];
			?>
			<li>
				<label>
					<input type="checkbox" name="permissions[<?php echo $k; ?>]"<?php if( $on ) : ?> checked<?php endif; ?>><?php echo $label; ?>
				</label>
			</li>
		<?php endforeach; ?>
		</ul>
	</fieldset>
</div>

<?php 
		return ob_get_clean();
	}

	public function errors( array $post )
	{
		$ret = array();

		if( ! strlen($post['title']) ){
			$ret['title'] = '__Required Field__';
		}

		return $ret;
	}

	public function grab( array $post, _PW1_ZI3_Acl_Roles_Model $model )
	{
		$model->title = $post['title'];

		$model->permissions = array();
		if( array_key_exists('permissions', $post) ){
			foreach( $post['permissions'] as $k => $v ){
				if( $v ) $model->permissions[ $k ] = TRUE;
			}
		}

		return $model;
	}
}