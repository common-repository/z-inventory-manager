<?php if (! defined('ABSPATH')) exit;
class PW1_ZI3_Install_ extends _PW1
{
	public function __construct(
		PW1_ $pw1
	)
	{}

	public function getVersion( $migrationName )
	{
		$ret = NULL;
		return $ret;
	}

	public function setVersion( $migrationName, $version )
	{
		$ret = NULL;
		return $ret;
	}

	public function conf()
	{
		$ret = array();
		return $ret;
	}

	public function up( array $migrations )
	{
		foreach( $migrations as $migrationName => $migrationVersions ){
			$installedVersion = $this->self->getVersion( $migrationName );

			ksort( $migrationVersions );
			foreach( $migrationVersions as $needVersion => $handlers ){
				if( $needVersion <= $installedVersion ){
					continue;
				}

				list( $handlerUp, $handlerDown ) = $handlers;

				$ret = $this->pw1->call( $handlerUp );
				if( ! ($ret instanceof PW1_Error) ){
					$this->self->setVersion( $migrationName, $needVersion );
				}
			} 
		}
	}

	public function down( array $migrations )
	{
		$migrations = array_reverse( $migrations );

		foreach( $migrations as $migrationName => $migrationVersions ){
			$installedVersion = $this->self->getVersion( $migrationName );

			if( $installedVersion <= 0 ){
				continue;
			}

			krsort( $migrationVersions );

			foreach( $migrationVersions as $needVersion => $handlers ){
				if( $needVersion > $installedVersion ){
					continue;
				}

				list( $handlerUp, $handlerDown ) = $handlers;
				if( ! $handlerDown ) continue;

				$this->pw1->call( $handlerDown );
				$this->self->setVersion( $migrationName, $needVersion - 1 );
			} 
		}
	}
}