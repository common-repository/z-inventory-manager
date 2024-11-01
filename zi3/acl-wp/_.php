<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Acl0Wp_ extends _PW1
{
	public $pw1;

	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->wrap( 'PW1_ZI3_Acl_Roles_Query@findForUser',	__CLASS__ . 'Roles_Query@wrapFindForUser' )
			->wrap( 'PW1_ZI3_Acl_Query@usersWhereCan',		__CLASS__ . 'Query@wrapUsersWhereCan' )
			;
	}
}