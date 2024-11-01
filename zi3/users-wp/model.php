<?php if (! defined('ABSPATH')) exit;
class _PW1_ZI3_Users0Wp_Model extends _PW1_ZI3_Users_Model
{
	public $wpUser;
}

class PW1_ZI3_Users0Wp_Model extends _PW1
{
	public $model;

	public function __construct(
		PW1_ZI3_Users_Model $model
	)
	{}

	public function construct()
	{
		$class = '_' . __CLASS__;
		$ret = new $class;
		return $ret;
	}

	public function fromWpUser( WP_User $wpUser, _PW1_ZI3_Users0Wp_Model $ret )
	{
		$ret->id = $wpUser->ID;
		$ret->title = $wpUser->display_name;
		$ret->email = $wpUser->user_email;
		$ret->username = $wpUser->user_login;
		$ret->wpUser = $wpUser;

		return $ret;
	}
}