<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Settings_X_Acl_Catalog
{
	public function __invoke()
	{
		$ret = array();
		$ret['administration/edit-settings'] = '__Edit System Settings__';
		return $ret;
	}
}