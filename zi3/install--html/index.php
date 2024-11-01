<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install00Html_Index extends _PW1
{
	private $_isInstalled = NULL;
	public $_;
	public $aclQuery;

	public function __construct(
		PW1_ZI3_Install_ $_,
		PW1_ZI3_Acl_Query	$aclQuery
	)
	{}

	public function checkInstall( PW1_Request $request, PW1_Response $response )
	{
		if( NULL === $this->_isInstalled ){
			$this->_isInstalled = $this->_->getVersion( 'install' );
		}

		if( $this->_isInstalled ){
			$conf = $this->_->conf();
			$this->_->up( $conf );
			return $response;
		}

		if(
			str_starts_with( $request->slug, 'setupdb' ) OR
			str_starts_with( $request->slug, 'migration/install' ) OR
			str_starts_with( $request->slug, 'install' )
			){
			return $response;
		}

		$response->redirect = 'install';
		return $response;
	}

	public function can( PW1_Request $request, PW1_Response $response )
	{
		if( ! $this->aclQuery->userCan($request->currentUserId, 'manage_install') ){
			$ret = new PW1_Error( '__Not Allowed__' );
			return $ret;
		}
	}
}