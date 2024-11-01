<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Items00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes', __CLASS__ . '@routes' )
			;
	}

	public function linkNew()
	{
		$ret = './new';
		return $ret;
	}

	public function linkEdit( $id )
	{
		$ret = 'URI:./edit';
		return $ret;
	}

	public function linkDelete( $id )
	{
		$ret = 'URI:./delete';
		return $ret;
	}

	public function routes()
	{
		$ret = array();

	// ACL
		$ret[] = array( '*',	'items*',	__CLASS__ . 'Items_Acl@checkView', -1 );

		$ret[] = array( '*',	'items/new*',		__CLASS__ . 'Items_Acl@checkEdit', -1 );
		$ret[] = array( '*',	'items/*/delete',	__CLASS__ . 'Items_Acl@checkEdit', -1 );
		$ret[] = array( '*',	'items/*/edit',	__CLASS__ . 'Items_Acl@checkEdit', -1 );

	// /
		$ret[] = array( 'HEAD',	'',	__CLASS__ . 'Index@head' );

	// index
		$ret[] = array( 'GET',	'items',		__CLASS__ . 'Items_Index@beforeGet', -1 );
		$ret[] = array( '*',		'items',		__CLASS__ . 'Items_Index@*' );

	// index-filter
		$ret[] = array( 'GET',	'items',		__CLASS__ . 'Items_Index0Filter@beforeGet', -1 );
		$ret[] = array( 'GET',	'items',		__CLASS__ . 'Items_Index0Filter@get' );
		$ret[] = array( 'POST',	'items',		__CLASS__ . 'Items_Index0Filter@post', -1 );

	// ITEMS-SELECTOR
		$ret[] = array( '*',		'items/selector{*}',		'>items{1}?mode=select' );

	// ITEMS/NEW
		$ret[] = array( '*',	'items/new',	__CLASS__ . 'Items0New_Index@*' );

	// zoom
		$ret[] = array( '*',		'items/{id}',	__CLASS__ . 'Items0Id_Index@*' );
		$ret[] = array( 'GET',	'items/{id}',	__CLASS__ . 'Items0Id_Index0Details@get' );

	// ITEMS/{ID}/STATE
		$ret[] = array( 'POST',		'items/{id}/state',	__CLASS__ . 'Items0Id0State_Post@' );

	// ITEMS/{ID}/EDIT
		$ret[] = array( 'GET',	'items/{id}/edit',	__CLASS__ . 'Items0Id0Edit_Get@' );
		$ret[] = array( 'HEAD',	'items/{id}/edit',	__CLASS__ . 'Items0Id0Edit_Get@head' );
		$ret[] = array( 'POST',	'items/{id}/edit',	__CLASS__ . 'Items0Id0Edit_Post@' );

		return $ret;
	}
}