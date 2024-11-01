<?php
class PW1_ZI3_App_Files
{
	public function getAllFiles( $dir )
	{
		$ret = $this->self->getFiles( $dir );

		$folders = $this->self->getFolders( $dir );
		reset( $folders );
		foreach( $folders as $fo ){
			$folderFiles = $this->self->getAllFiles( "$dir/$fo" );
			reset( $folderFiles );
			foreach( $folderFiles as $ff ){
				$ret[ "$fo/$ff" ] = "$fo/$ff";
			}
		}

		return $ret;
	}

	public function getFolders( $dir )
	{
		$ret = array();

		if( ! file_exists($dir) ){
			return $ret;
		}

		if( ! ($handle = opendir($dir)) ){
			return $ret;
		}

		while ( FALSE !== ($f = readdir($handle)) ){
			if( substr($f, 0, 1) == '.' )
				continue;

			if( ! is_dir( $dir . '/' . $f ) ){
				continue;
			}

			$ret[ $f ] = $f;
		}

		closedir( $handle );
		asort( $ret );

		return $ret;
	}

	public function getFiles( $dir )
	{
		$ret = array();

		if( ! file_exists($dir) ){
			return $ret;
		}

		if( ! ($handle = opendir($dir)) ){
			return $ret;
		}

		while ( FALSE !== ($f = readdir($handle)) ){
			if( substr($f, 0, 1) == '.' )
				continue;

			if( ! is_file( $dir . '/' . $f ) ){
				continue;
			}

			$ret[ $f ] = $f;
		}

		closedir( $handle );
		asort( $ret );

		return $ret;
	}

	public function humanFilesize( $bytes, $decimals = 2 )
	{
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		$ret = sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
		return $ret;
	}

	public function download( $filename, $data )
	{
	// Try to determine if the filename includes a file extension.
	// We need it in order to set the MIME type
		if (FALSE === strpos($filename, '.')){
			return FALSE;
		}

	// Grab the file extension
		$x = explode( '.', $filename );
		$extension = end( $x );

	// Load the mime types
		$mimes = array();

	// Set a default mime if we can't find it
		if ( ! isset($mimes[$extension])){
			$mime = 'application/octet-stream';
		}
		else {
			$mime = (is_array($mimes[$extension])) ? $mimes[$extension][0] : $mimes[$extension];
		}

	// Generate the server headers
		if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== FALSE){
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: ".strlen($data));
		}
		else {
			header('Content-Type: "'.$mime.'"');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header("Content-Transfer-Encoding: binary");
			header('Expires: 0');
			header('Pragma: no-cache');
			header("Content-Length: ".strlen($data));
		}

		exit($data);
	}

	public function downloadFile( $localFilename, $shortName )
	{
		if( ob_get_contents() ){
			ob_end_clean();
		}

		$fileSize = filesize( $localFilename );

		header("Type: application/force-download");
		header("Content-Type: application/force-download");
		header("Content-Length: $fileSize");

		header("Content-Transfer-Encoding: binary");
		header("Content-Disposition: attachment; filename=\"$shortName\"");

		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Connection: close");

		readfile( $localFilename );
		exit;
	}

	public function buildCsv( array $array, $separator = ',' )
	{
		$processed = array();
		reset( $array );
		foreach( $array as $a ){
			if( strpos($a, '"') !== FALSE ){
				$a = str_replace( '"', '""', $a );
			}

			$wrap = FALSE;

			if( strpos($a, $separator) !== FALSE ){
				$wrap = TRUE;
			}
			elseif( strpos($a, "\n") !== FALSE ){
				$wrap = TRUE;
			}

			if( $wrap ){
				$a = '"' . $a . '"';
			}

			$processed[] = $a;
		}

		$ret = join( $separator, $processed );
		return $ret;
	}

	public function uploadCsv( $fileName, $separator = ',' )
	{
		$ret = array();

		$handle = fopen( $fileName, 'r' );
		if( FALSE === $handle ){
			return new PW1_Error( '__Can not open the uploaded file__' );
		}

	// first line
		$line = fgetcsv( $handle, 10000, $separator );

		if( ! $line ){
			return new PW1_Error( '__The file is empty__' );
		}

	// parse titles
		$fields = array();
		for( $ii = 0; $ii < count($line); $ii++ ){
			$thisField = $line[$ii];

			if( ! $ii ){
				//check BOM for first line
				$bom = pack( "CCC", 0xef, 0xbb, 0xbf );
				if( 0 == strncmp($thisField, $bom, 3) ){
					// BOM detected
					$thisField = substr( $thisField, 3 );
				}
			}

			$thisField = preg_replace( '/[\x00-\x1F\x80-\xFF]/', '', $thisField );
			$thisField = trim( $thisField );
			$thisField = trim( $thisField, '"' );

			$thisField = strtolower( $thisField );

			$fields[$ii] = $thisField;
		}

	// now get the lines
		$data = array();
		$fieldsCount = count( $fields );

		while( ($line = fgetcsv($handle, 10000, $separator)) !== FALSE ){
			$values = array();
			for( $i = 0; $i < $fieldsCount; $i++ ){
				$fieldName = $fields[$i];
				if( isset($line[$i]) ){
					$values[ $fieldName ] = $line[$i];
				}
				else {
					$values[ $fieldName ] = '';
				}
			}

		// convert to UTF
			$keys = array_keys( $values );
			foreach( $keys as $k ){
				if( ! $this->self->seemsUtf8($values[$k]) ){
					$values[$k] = utf8_encode($values[$k]);
				}
			}

			$ret[] = $values;
		}

		return $ret;
	}

	function seemsUtf8( $str )
	{
		if( function_exists('mb_check_encoding') ){
			return mb_check_encoding( $str, 'UTF-8' );
		}

		$length = strlen( $str );
		for ($i=0; $i < $length; $i++) {
			$c = ord($str[$i]);
			if ($c < 0x80) $n = 0; # 0bbbbbbb
			elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
					return FALSE;
				}
			}
		return TRUE;
	}
}