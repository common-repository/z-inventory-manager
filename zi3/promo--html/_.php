<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Promo00Html_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{
		$pw1
			->merge( 'PW1_Handle@routes', __CLASS__ . '@routes' )
			->merge( 'PW1_ZI3_App00Html_Layout@topHeader', __CLASS__ . '@topHeader' )
			;
	}

	public function routes()
	{
		$ret = array();

	// ACL
		// $ret[] = array( '*',	'contacts*',	__CLASS__ . 'Contacts_Acl@check', -1 );

	// /
		$ret[] = array( 'HEAD',	'',	__CLASS__ . 'Index@head' );
		$ret[] = array( '*',	'promo',	__CLASS__ . 'Promo_Index@*' );

		return $ret;
	}

	public function topHeader( PW1_Request $request, PW1_Response $response )
	{
		ob_start();
?>

<?php if( 1 ) : ?>
<div class="notice notice-success inline" style="padding: .5em 1em;">
	Interested in more features? Upgrade to <a target="_blank" href="https://www.z-inventory-manager.com/order/"><strong>PlainInventory Pro</strong></a> or check out the <a href="<?= admin_url('admin.php?page=z-inventory-manager3&zi3a=promo'); ?>">Add-Ons</a> page!
</div>
<?php endif; ?>

<?php if( 0 ) : ?>
<article style="margin: 1em 0 0 0; padding: .5em 1em;">
	In the Pro version: <a href="https://www.z-inventory-manager.com/help-copy-transactions/" target="_blank">Copy Sales and Purchases</a>,
	<a href="https://www.z-inventory-manager.com/help-transaction-history/" target="_blank">Transaction History</a>, 
	<a href="https://www.z-inventory-manager.com/help-inventory-stats/" target="_blank">Inventory Stats</a>.
<?php if( 0 ) : ?>
	<br/>
	Skyrocket your productivity with just <b>$39</b> <u>one time payment</u>, no recurring fees or renewals.
<?php endif; ?>
	<br/>
	<a href="https://www.z-inventory-manager.com/order/" target="_blank" style="display: block;">Order Now!</a>
</article>
<?php endif; ?>

<?php
		$ret = trim( ob_get_clean() );
		return $ret;
	}
}