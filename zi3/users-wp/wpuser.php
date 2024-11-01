<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Users0Wp_WpUser
{
	// public function to( PW1_Common_Users0Wp_Model $model )
	// {
		// $ret = array();

		// if( $model->id ){
			// $ret['id'] = $model->id;
		// }
		// $ret['title'] = $model->title;
		// $ret['state'] = $model->state;

		// $ret['email'] = $model->email;
		// $ret['username'] = $model->username;

		// if( strlen($model->passwordClear) ){
			// $passwordHash = $this->password->hash( $model->passwordClear );
			// $ret['password'] = $passwordHash;
		// }
		// else {
			// $ret['password'] = $model->passwordHash;
		// }

		// return $ret;
	// }

	public function from( WP_User $wpUser, PW1_ZI3_Users0Wp_Model $ret )
	{
		$ret->id = $wpUser->ID;
		$ret->title = $wpUser->display_name;
		$ret->email = $wpUser->user_email;
		$ret->username = $wpUser->user_login;
		$ret->wpUser = $wpUser;

		return $ret;
	}
}