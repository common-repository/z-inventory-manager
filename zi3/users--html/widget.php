<?php
class PW1_ZI3_Users00Html_Widget extends _PW1
{
	public function presentTitle( _PW1_ZI3_Users_Model $model, $linkTo = NULL )
	{
		$username = $model->username;
		$title = $model->title;

		if( NULL !== $linkTo ){
			if( TRUE === $linkTo ) $linkTo = 'URI:admin/users/' . $model->id;
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