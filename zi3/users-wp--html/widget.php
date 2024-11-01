<?php
class PW1_ZI3_Users0Wp00Html_Widget extends _PW1
{
	public $model;
	public $modelWp;

	public function __construct(
		PW1_ZI3_Users_Model $model,
		PW1_ZI3_Users0Wp_Model $modelWp
	)
	{}

	public function presentWpRole( $wpRoleId )
	{
		global $wp_roles;
		$ret = isset( $wp_roles->roles[$wpRoleId]['name'] ) ? $wp_roles->roles[$wpRoleId]['name'] : $wpRoleId;
		return $ret;
	}

	public function presentWpUser( WP_User $wpUser, $linkTo = NULL )
	{
		$model = $this->modelWp->construct();
		$model = $this->modelWp->fromWpUser( $wpUser, $model );
		return $this->self->presentTitle( $model, $linkTo );
	}

	public function presentTitle( _PW1_ZI3_Users0Wp_Model $model, $linkTo = NULL )
	{
		$username = $model->username;
		$title = $model->title;

		if( NULL !== $linkTo ){
			if( TRUE === $linkTo ){
				$linkTo = get_edit_user_link( $model->id );
			} 
		}

		ob_start();
?>

<?php if( NULL === $linkTo ) : ?>
	<span title="<?php echo esc_attr($title); ?>"><?php echo esc_html($title); ?></span>
<?php else : ?>
	<a href="<?php echo $linkTo; ?>" title="<?php echo esc_attr($title); ?>"><?php echo esc_html($username); ?><span><?php echo esc_html($title); ?></span></a>
<?php endif; ?>

<?php
		return trim( ob_get_clean() );
	}
}