<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Promo00Html_Promo_Index extends _PW1
{
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Acl_Query	$aclQuery
	)
	{}

	public function head( PW1_Request $request, PW1_Response $response )
	{
		$response->title = '__Add-Ons__';
		return $response;
	}

	public function get( PW1_Request $request, PW1_Response $response )
	{
		ob_start();
?>

<div class="pw-grid">
	<div class="pw-col-4">
		<article style="padding: 1em;">
			<header style="margin-bottom: .5em;">
				<h3 style="text-align: start;">
					<a href="https://www.z-inventory-manager.com/help-copy-transactions/" target="_blank">Copy Sales and Purchases</a>
				</h3>
			</header>
			<p>
			Quickly create new sales or purchases by duplicating existing ones.
			</p>
		</article>
	</div>

	<div class="pw-col-4">
		<article style="padding: 1em;">
			<header style="margin-bottom: .5em;">
				<h3 style="text-align: start;">
					<a href="https://www.z-inventory-manager.com/help-inventory-stats/" target="_blank">Inventory Stats</a>
				</h3>
			</header>
			<p>
			Keep track of total purchases and sales, average cost and price.
			</p>
		</article>
	</div>

	<div class="pw-col-4">
		<article style="padding: 1em;">
			<header style="margin-bottom: .5em;">
				<h3 style="text-align: start;">
					<a href="https://www.z-inventory-manager.com/help-transaction-history/" target="_blank">Transaction History</a>
				</h3>
			</header>
			<p>
			Keep track of all the changes ever made to a transaction.
			</p>
		</article>
	</div>
</div>

<?php if( 0 ) : ?>
<br>

<div class="pw-grid">
	<div class="pw-col-6">
		<article>
			<h3>PlainInventory Pro</h3>

			<ul>
				<li>All Pro Features</li>
				<li>Support for <b>1 Site</b></li>
				<li>One-Time Payment - Lifetime Updates</li>
				<li>Our Copyright Labels Removal <b>Not Allowed</b></li>
			</ul>

		</article>
	</div>

	<div class="pw-col-6">
		<article>
			<h3>PlainInventory Developer</h3>

			<ul>
				<li>All Pro Features</li>
				<li>Support for <b>Unlimited Sites</b></li>
				<li>One-Time Payment - Lifetime Updates</li>
				<li>Our Copyright Labels Removal <b>Allowed</b></li>
			</ul>

		</article>
	</div>
</div>
<?php endif; ?>

<div style="text-align: center; margin: 1em 0;">
Get the Pro version for all of these nice features!
</div>

<div style="text-align: center; margin: 0.5em 0 2em 0;">
<a class="button-primary" style="display: block; text-align: center; font-size: 1.5em;" target="_blank" href="https://www.z-inventory-manager.com/order/">Order Now</a>
</div>

<?php 
		$ret = trim( ob_get_clean() );
		$response->content = $ret;
		return $response;
	}
}