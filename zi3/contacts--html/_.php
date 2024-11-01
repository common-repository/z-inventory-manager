<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Contacts00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes', __CLASS__ . '@routes' )
			;
	}

	public function routes()
	{
		$ret = array();

	// ACL
		$ret[] = array( '*',	'contacts*',	__CLASS__ . 'Contacts_Acl@checkView', -1 );

		$ret[] = array( '*',	'contacts/new*',		__CLASS__ . 'Contacts_Acl@checkEdit', -1 );
		$ret[] = array( '*',	'contacts/*/delete',	__CLASS__ . 'Contacts_Acl@checkEdit', -1 );
		$ret[] = array( '*',	'contacts/*/edit',	__CLASS__ . 'Contacts_Acl@checkEdit', -1 );

	// /
		$ret[] = array( 'HEAD',	'',	__CLASS__ . 'Index@head' );

	// index
		$ret[] = array( 'GET',	'contacts',		__CLASS__ . 'Contacts_Index@getBefore', -1 );
		$ret[] = array( '*',		'contacts',		__CLASS__ . 'Contacts_Index@*' );

	// index-filter
		$ret[] = array( 'GET',	'contacts',		__CLASS__ . 'Contacts_Index0Filter@beforeGet', -1 );
		$ret[] = array( 'GET',	'contacts',		__CLASS__ . 'Contacts_Index0Filter@get' );
		$ret[] = array( 'POST',	'contacts',		__CLASS__ . 'Contacts_Index0Filter@post' );

	// new
		$ret[] = array( '*',	'contacts/new-{type}',			__CLASS__ . 'Contacts0New_Index@*' );

	// zoom
		$ret[] = array( '*',		'contacts/{id}',				__CLASS__ . 'Contacts0Id_Index@*' );

	// zoom - details
		$ret[] = array( 'GET',	'contacts/{id}', 				__CLASS__ . 'Contacts0Id_Index0Details@get' );

	// edit
		$ret[] = array( '*',	'contacts/{id}/edit',	__CLASS__ . 'Contacts0Id0Edit_Index@*' );

	// delete
		$ret[] = array( '*',	'contacts/{id}/delete',	__CLASS__ . 'Contacts0Id0Delete_Index@*' );

	// selector
		$ret[] = array( '*',		'contacts/selector{*}',				'>contacts{1}?mode=select' );
		$ret[] = array( 'POST',	'contacts/selector/new-{type}',	__CLASS__ . 'Contacts0New_Index@afterPostSelector', +1 );

		return $ret;
	}
}