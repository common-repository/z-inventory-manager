<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install00Db_Install extends _PW1
{
	public $_;
	public $dbForge;

	public function __construct(
		PW1_ZI3_Install00Db_ $_,
		PW1_Sql_Forge $dbForge
	)
	{}

	public function conf()
	{
		$ret = array();
		$ret['install'][1] = array( __CLASS__ . '@up1', __CLASS__ . '@down1' );
		return  $ret;
	}

	public function up1()
	{
		$this->dbForge->addField(
			array(
				'name' => array(
					'type' => 'VARCHAR(64)',
					'null' => FALSE,
					),
				'version' => array(
					'type'	=> 'INTEGER',
					'null'	=> FALSE,
					),
				)
			);
		$this->dbForge->addKey( 'name', TRUE );

		$ret = $this->dbForge->createTable( $this->_->tableName() );
		return $ret;
	}   

	public function down1()
	{
		$ret = $this->dbForge->dropTable( $this->_->tableName() );
		return $ret;
	}
}