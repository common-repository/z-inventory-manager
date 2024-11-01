<?php if (! defined('ABSPATH')) exit;
interface PW1_Time_Interface
{
	public function getDayOfWeekOccurenceInMonth( $date );
	public function getDayOfWeekOccurenceInMonthFromEnd( $date );
	public function splitToWeeks( array $dates );

	public function smartModifyDown( $modify );
	public function smartModifyUp( $modify );

	public function setTz( $tz );
	public function setNow();
	public function formatToDatepicker();

	public function getSortedWeekdays();

	public function formatDateDb();
	public function setDateDb( $date );
	public function setDateTimeDb( $datetime );

	public function getDateTimeDb();
	public function getEndDateTimeDb();
	public function getDateDb( $dateTimeDb = NULL );
	public function getTimeDb( $dateTimeDb = NULL );

	public function formatDateTimeDb2();

	public function setStartDay();
	public function setEndDay();

	public function setStartWeek();
	public function setEndWeek();

	public function setStartMonth();
	public function setEndMonth();

	public function setStartYear();
	public function setEndYear();

	public function getWeekStartsOn();
	public function getYear();
	public function getDay();
	public function getWeekday( $dateDb = NULL );
	public function formatDateRange( $date1, $date2, $with_weekday = FALSE );
	public function getMonthMatrix( $skipWeekdays = array(), $overlap = FALSE );
	public function getParts();
	public function getWeekdays();
	public function sortWeekdays( $wds );
	public function getTimeInDay();

	public function getDuration( $dateTime1, $dateTime2 );
	public function getWeekNo( $date = NULL );

	public function getDifferenceInDays( $date1, $date2 );
	public function getAllDates( $startDate, $endDate, $withStartEnd = FALSE );
	public function getNextDate( $date );
	public function getPrevDate( $date );

	public function addSeconds( $dateTimeDb, $duration );
	public function dateTime( $dateDb, $timeSeconds );
}

class PW1_Time_ extends DateTime implements PW1_Time_Interface
{
	public $self;

	public $timeFormat = 'g:ia';
	public $dateFormat = 'j M Y';
	protected $weekStartsOn = 0;
	public $timezone = '';

	protected $_months = array();
	protected $_weekdays = array();

	function __construct()
	{
		date_default_timezone_set( 'UTC' );

		parent::__construct();

		$this->_months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
		$this->_weekdays = array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');

		if( defined('WPINC') ){
			$tz = get_option('timezone_string');
			if( ! strlen($tz) ){
				$offset = get_option('gmt_offset');
				if( $offset ){
					$tz = 'Etc/GMT';
					if( $offset > 0 ){
						$tz .= '+' . $offset;
					}
					else {
						$tz .= '-' . -$offset;
					}
				}
			}

			$this->setTz( $tz );
		}
	}

	public function getDayOfWeekOccurenceInMonth( $date )
	{
		$this->setDateDb( $date );
		$month = $this->getMonth();
		$rexMonth = $month;

		$ret = 0;
		while( $rexMonth == $month ){
			$ret++;
			$this->mod( '-1 week' );
			$rexMonth = $this->getMonth();
		}

		return $ret;
	}

	public function getDayOfWeekOccurenceInMonthFromEnd( $date )
	{
		$this->setDateDb( $date );
		$month = $this->getMonth();
		$rexMonth = $month;

		$ret = 0;
		while( $rexMonth == $month ){
			$ret--;
			$this->mod( '+1 week' );
			$rexMonth = $this->getMonth();
		}

		return $ret;
	}

	public function addSeconds( $dateTime, $duration )
	{
		static $cache = array();

		$key = $dateTime;
		$key .= ( $duration > 0 ) ? '+' . $duration : '-' . (-$duration);

		if( isset($cache[$key]) ){
			return $cache[$key];
		}

		$this->setDateTimeDb( $dateTime );

		if( $duration > 0 ){
			$this->mod( '+ ' . $duration . ' seconds' );
		}
		else {
			$this->mod( '- ' . (- $duration) . ' seconds' );
		}

		$thisDateTime = $this->getDateTimeDb();
		$cache[$key] = $thisDateTime;

		return $cache[$key];
	}

	public function dateTime( $dateDb, $timeSeconds )
	{
		static $cache = array();
		// static $secToDb = NULL;
		static $secToDb = array();

		$key = $dateDb . '-' . $timeSeconds;
		if( isset($cache[$key]) ){
			return $cache[$key];
		}

		if( NULL === $secToDb ){
			$minutes = array( '00', '05', '10', '15', '20', '25', '30', '35', '40', '45', '50', '55' );
			$hours = array( '00','01','02','03','04','05','06','07','08','09','10','11','12','13','14','15','16','17','18','19','20','21','22','23' );

			$secToDb = array();
			for( $h = 0; $h < count($hours); $h++ ){
				for( $m = 0; $m < count($minutes); $m++ ){
					$sec = $h * 60 * 60 + $m * 60;
					$db = $hours[$h] . $minutes[$m];
					$secToDb[ $sec ] = $db;
				}
			}
			// $secToDb = array();
		}

		if( isset($secToDb[$timeSeconds]) ){
			$ret = $dateDb . $secToDb[$timeSeconds];
		}
		else {
			$ret = $this->setDateDb( $dateDb )
				->mod( '+ ' . $timeSeconds . ' seconds' )
				->getDateTimeDb()
				;
		}

		$cache[$key] = $ret;
		return $cache[$key];
	}

	public function splitToWeeks( array $dates )
	{
		$return = array();

		$thisWeek = array();
		foreach( $dates as $date ){
			$this->setDateDb( $date );
			$weekNo = $this->getWeekNo();
			if( ! isset($return[$weekNo]) ){
				$return[$weekNo] = array();
			}
			$return[$weekNo][] = $date;
		}

		return $return;
	}

	public function setWeekStartsOn( $s )
	{
		$this->weekStartsOn = $s;
		return $this;
	}

	public function mod( $modify )
	{
		parent::modify( $modify );
		return $this;
	}

	public function smartModifyDown( $modify )
	{
		$this->mod( $modify );

		list( $qty, $measure ) = explode( ' ', $modify );
		switch( $measure ){
			case 'days':
				$this->setStartDay();
				break;
			case 'weeks':
				$this->setStartWeek();
				break;
			case 'months':
				$this->setStartMonth();
				break;
		}

		return $this;
	}

	public function smartModifyUp( $modify )
	{
		$this->mod( $modify );

		list( $qty, $measure ) = explode( ' ', $modify );
		switch( $measure ){
			case 'days':
				$this->setEndDay();
				break;
			case 'weeks':
				$this->setEndWeek();
				break;
			case 'months':
				$this->setEndMonth();
				break;
		}

		return $this;
	}

	/* date2 - date1 */
	public function getDifferenceInDays( $date1, $date2 )
	{
		$ts1 = $this->setDateDb( $date1 )->getTimestamp();
		$ts2 = $this->setDateDb( $date2 )->getTimestamp();

		$tsDiff = $ts2 - $ts1;
		$day = 24 * 60 * 60;

		$dayDiff = floor( $tsDiff / $day );
		return $dayDiff;
	}

	public function setTz( $tz )
	{
		if( is_array($tz) )
			$tz = $tz[0];

		if( ! $tz )
			$tz = date_default_timezone_get();

		$this->timezone = $tz;

		try {
			$tzObject = new DateTimeZone( $tz );
			parent::setTimezone( $tzObject );
		}
		catch( Exception $e ){
			// echo "WRONG TIMEZONE: '$tz'<br>";
		}
	}

	public function setTs( $ts )
	{
		if( ! strlen($ts) ){
			$ts = 0;
		}

		if( function_exists('date_timestamp_set') ){
			parent::setTimestamp( $ts );
		}
		else {
			parent::__construct( '@' . $ts );
		}

		return $this;
	}

	public function setNow()
	{
		$this->setTs( time() );
		return $this;
	}

	public function formatToDatepicker()
	{
		$dateFormat = $this->dateFormat;

		$pattern = array(
			//day
			'd',	//day of the month
			'j',	//3 letter name of the day
			'l',	//full name of the day
			'z',	//day of the year

			//month
			'F',	//Month name full
			'M',	//Month name short
			'n',	//numeric month no leading zeros
			'm',	//numeric month leading zeros

			//year
			'Y', //full numeric year
			'y'	//numeric year: 2 digit
			);

		$replace = array(
			'dd','d','DD','o',
			'MM','M','m','mm',
			'yyyy','y'
		);
		foreach($pattern as &$p){
			$p = '/'.$p.'/';
		}
		return preg_replace( $pattern, $replace, $dateFormat );
	}

	public function formatDateDb()
	{
		return $this->getDateDb();
	}

	public function getDateDb( $dateTimeDb = NULL )
	{
		if( NULL === $dateTimeDb ){
			$dateFormat = 'Ymd';
			$return = $this->format( $dateFormat );
		}
		else {
			$return = substr( $dateTimeDb, 0, 8 );
		}
		return $return;
	}

	public function setDateDb( $date )
	{
		list( $year, $month, $day ) = $this->_splitDate( $date );
		$year = (int) $year;
		$month = (int) $month;
		$day = (int) $day;

		$this->setDate( $year, $month, $day );
		$this->setTime( 0, 0, 0 );
		return $this;
	}

	public function setDateTimeDb( $datetime )
	{
		$date = substr($datetime, 0, 8);
		$this->setDateDb( $date );

		$hours = substr($datetime, 8, 2);
		$minutes = substr($datetime, 10, 2);
		$this->setTime( (int) $hours, (int) $minutes, 0 );

		return $this;
	}

	protected function _splitDate( $string )
	{
		$year = substr( $string, 0, 4 );
		$month = substr( $string, 4, 2 );
		$day = substr( $string, 6, 4 );
		$return = array( $year, $month, $day );
		return $return;
	}

	public function getDateTimeDb()
	{
		$date = $this->getDateDb();
		$time = $this->getTimeDb();
		$return = $date . $time;
		return $return;
	}

	public function getEndDateTimeDb( $date = NULL )
	{
		if( NULL === $date ){
			$date = $this->getDateDb();
		}
		$time = '2400';
		$return = $date . $time;
		return $return;
	}

	public function formatDateTimeDb2()
	{
		$return = $this->format('Y-m-d H:i:s');
		return $return;
	}

	public function getTimeDb( $dateTimeDb = NULL )
	{
		if( NULL === $dateTimeDb ){
			$h = $this->format('G');
			$m = $this->format('i');

			$h = str_pad( $h, 2, 0, STR_PAD_LEFT );
			$m = str_pad( $m, 2, 0, STR_PAD_LEFT );

			$ret = $h . $m;
		}
		else {
			$ret = substr( $dateTimeDb, 8, 4 );
		}

		return $ret;
	}

	public function setStartDay()
	{
		$this->setTime( 0, 0, 0 );
		return $this;
	}

	public function setEndDay()
	{
		$this
			->setTime( 23, 59, 59 )
			;
		return $this;
	}

	public function getTimeInDay( $dateTimeDb = NULL )
	{
		$ret = NULL;

		if( NULL === $dateTimeDb ){
			$timestamp = $this->getTimestamp();

			$this->setStartDay();
			$timestamp2 = $this->getTimestamp();

			$ret = $timestamp - $timestamp2;

			$this->setTs( $timestamp );
		}
		else {
			
		}

		return $ret;
	}

	public function getWeekStartsOn()
	{
		return $this->weekStartsOn;
	}

	public function setStartWeek()
	{
		$this->setStartDay();
		$weekDay = $this->getWeekday();

		while( $weekDay != $this->weekStartsOn ){
			$this->mod( '-1 day' );
			$weekDay = $this->getWeekday();
		}

		return $this;
	}

	public function setEndWeek()
	{
		$this->setStartDay();
		$this->mod( '+1 day' );
		$weekDay = $this->getWeekday();

		while( $weekDay != $this->weekStartsOn ){
			$this->mod( '+1 day' );
			$weekDay = $this->getWeekday();
		}

		$this
			->mod( '-1 day' )
			->setEndDay()
			;
		return $this;
	}

	public function setStartMonth()
	{
		$year = $this->format('Y');
		$month = $this->format('m');
		$day = '01';

		$date = $year . $month . $day;
		$this
			->setDateDb( $date )
			->setTime( 0, 0, 0 )
			;

		return $this;
	}

	public function setEndMonth()
	{
		$currentMonth = $this->format('m');
		$nextMonth = $currentMonth;

		while( $currentMonth == $nextMonth ){
			$this->mod('+28 days');
			$nextMonth = $this->format('m');
		}

		$year = $this->format('Y');
		$month = $this->format('m');
		$day = '01';

		$date = $year . $month . $day;
		$this
			->setDateDb( $date )
			->mod('-1 day')
			->setEndDay()
			;

		return $this;
	}

	public function setStartYear()
	{
		$year = $this->format('Y');
		$month = '01';
		$day = '01';

		$date = $year . $month . $day;
		$this
			->setDateDb( $date )
			->setTime( 0, 0, 0 )
			;

		return $this;
	}

	public function setEndYear()
	{
		$this
			->setStartYear()
			->mod('+1 year')
			->mod('-1 day')
			;

		return $this;
	}

	public function getYear( $dateTimeDb = NULL )
	{
		if( NULL !== $dateTimeDb ){
			$this->setDateTimeDb( $dateTimeDb );
		}
		$ret = $this->format('Y');
		return $ret;
	}

	public function getDay( $dateTimeDb = NULL )
	{
		if( NULL !== $dateTimeDb ){
			$this->setDateTimeDb( $dateTimeDb );
		}
		$ret = $this->format('j');
		return $ret;
	}

	public function getWeekday( $dateDb = NULL )
	{
		static $cache = array();

		if( NULL !== $dateDb ){
			if( isset($cache[$dateDb]) ){
				return $cache[$dateDb];
			}
			$this->setDateDb( $dateDb );
		}

		$ret = $this->format('w');

		if( NULL !== $dateDb ){
			$cache[ $dateDb ] = $ret;
		}

		return $ret;
	}

	public function formatDateRange( $date1, $date2, $withWeekday = FALSE, $skipYear = FALSE )
	{
		list( $start_date_view, $end_date_view ) = $this->_formatDateRange( $date1, $date2, $withWeekday, $skipYear );

		if( $end_date_view ){
			$return = $start_date_view . ' - ' . $end_date_view;
		}
		else {
			$return = $start_date_view;
		}
		return $return;
	}

	protected function _formatDateRange( $date1, $date2, $with_weekday = FALSE, $skipYear = FALSE )
	{
		$return = array();
		$skip = array();

		if( $date1 == $date2 ){
			$this->setDateDb( $date1 );
			$view_date1 = $this->formatDate();
			if( $with_weekday ){
				$view_date1 = $this->getWeekdayName() . ', ' . $view_date1;
			}
			$return[] = $view_date1;
			$return[] = NULL;
			return $return;
		}

		$this->setDateDb( $date1 );
		$year1 = $this->getYear();
		$month1 = $this->format('n');

		$this->setDateDb( $date2 );
		$year2 = $this->getYear();
		$month2 = $this->format('n');

		if( $skipYear ){
			$skip['year'] = TRUE;
		}

		if( $year2 == $year1 )
			$skip['year'] = TRUE;
		if( $month2 == $month1 )
			$skip['month'] = TRUE;

		if( $skip ){
			$date_format = $this->dateFormat;
			$date_format_short = $date_format;

			$tags = array('m', 'n', 'M');
			foreach( $tags as $t ){
				$pos_m_original = strpos($date_format_short, $t);
				if( $pos_m_original !== FALSE )
					break;
			}

			if( isset($skip['year']) ){
				$pos_y = strpos($date_format_short, 'Y');
				if( $pos_y == 0 ){
					$date_format_short = substr_replace( $date_format_short, '', $pos_y, 2 );
				}
				else {
					$date_format_short = substr_replace( $date_format_short, '', $pos_y - 1, 2 );
				}

				$date_format_wo_year = $date_format_short;
			}

			if( isset($skip['month']) ){
				$tags = array('m', 'n', 'M');
				foreach( $tags as $t ){
					$pos_m = strpos($date_format_short, $t);
					if( $pos_m !== FALSE )
						break;
				}

				// month going first, do not replace
				if( $pos_m_original == 0 ){
					// $date_format_short = substr_replace( $date_format_short, '', $pos_m, 2 );
				}
				else {
					// month going first, do not replace
					if( $pos_m == 0 ){
						$date_format_short = substr_replace( $date_format_short, '', $pos_m, 2 );
					}
					else {
						$date_format_short = substr_replace( $date_format_short, '', $pos_m - 1, 2 );
					}
				}
			}

			if( $pos_y == 0 ){ // skip year in the second part
				$date_format1 = $date_format;
				$date_format2 = $date_format_short;
			}
			else {
				$date_format1 = $date_format_short;
				$date_format2 = $date_format;
				if( $skipYear ){
					$date_format2 = $date_format_wo_year;
				}
			}

			$this->setDateDb( $date1 );

			$view_date1 = $this->formatDate( $date_format1 );
			if( $with_weekday ){
				$view_date1 = $this->getWeekdayName() . ', ' . $view_date1;
			}
			$return[] = $view_date1;

			$this->setDateDb( $date2 );
			$view_date2 = $this->formatDate( $date_format2 );

			if( $with_weekday ){
				$view_date2 = $this->getWeekdayName() . ', ' . $view_date2;
			}
			$return[] = $view_date2;
		}
		else {
			$this->setDateDb( $date1 );
			$view_date1 = $this->formatDate();
			if( $with_weekday ){
				$view_date1 = $this->getWeekdayName() . ', ' . $view_date1;
			}
			$return[] = $view_date1;

			$this->setDateDb( $date2 );
			$view_date2 = $this->formatDate();
			if( $with_weekday ){
				$view_date2 = $this->getWeekdayName() . ', ' . $view_date2;
			}
			$return[] = $view_date2;
		}

		return $return;
	}

	public function getMonthMatrix( $skipWeekdays = array(), $overlap = FALSE )
	{
		// $overlap = TRUE; // if to show dates of prev/next month
		// $overlap = FALSE; // if to show dates of prev/next month

		$matrix = array();
		$currentMonthDay = 0;

		$currentDate = $this->formatDateDb();
		$thisMonth = substr( $currentDate, 4, 2 );

		$this->setStartMonth();
		if( $overlap ){
			$this->setStartWeek();
		}
		$startDate = $this->formatDateDb();

// echo "END DATE = $endDate<br>";

		$this
			->setDateDb( $currentDate )
			->setEndMonth()
			;
		if( $overlap ){
			$this->setEndWeek();
		}
		$this->mod('-1 second');

		$endDate = $this->formatDateDb();
// echo "START/END DATE = $startDate/$endDate<br>";

		$rexDate = $startDate;
		if( $overlap ){
			$this->setDateDb( $startDate );
			$this->setStartWeek();
			$rexDate = $this->formatDateDb();
		}

		$this->setDateDb( $startDate );
		$this->setStartWeek();
		$rexDate = $this->formatDateDb();

// echo "START DATE = $startDate, END DATE = $endDate, REX DATE = $rexDate<br>";

		$this->setDateDb( $rexDate );
		while( $rexDate <= $endDate ){
			$week = array();
			$weekSet = FALSE;
			$thisWeekStart = $rexDate;

			for( $weekDay = 0; $weekDay <= 6; $weekDay++ ){
				$thisWeekday = $this->getWeekday();
				$setDate = $rexDate;

				if( ! $overlap ){
					if( 
						( $rexDate > $endDate ) OR
						( $rexDate < $startDate )
						){
						$setDate = NULL;
						}
				}

				// $week[ $thisWeekday ] = $setDate;

				if( (! $skipWeekdays) OR (! in_array($thisWeekday, $skipWeekdays)) ){
					if( NULL !== $setDate ){
						$rexMonth = substr( $setDate, 4, 2 );
// echo "$rexMonth VS $thisMonth<br>";

						if( ! $overlap ){
							if( $rexMonth != $thisMonth ){
								$setDate = NULL;
							}
						}
					}

					$wki = $this->getWeekday();
					$week[ $wki ] = $setDate;
					if( NULL !== $setDate ){
						$weekSet = TRUE;
					}
				}

				$this->mod('+1 day');
				$rexDate = $this->formatDateDb();

// echo "R = $rexDate<br>";
				// if( $exact && ($rexDate >= $endDate) ){
					// break;
				// }
			}

			if( $weekSet )
				$matrix[$thisWeekStart] = $week;
		}

		return $matrix;
	}

	public function getParts()
	{
		$full = $this->formatDateTimeDb();

		$year = substr( $full, 0, 4 );
		$month = substr( $full, 4, 2 );
		$day = substr( $full, 6, 2 );
		$hour = substr( $full, 8, 2 );
		$min = substr( $full, 10, 2 );

		$return = array( $year, $month, $day, $hour, $min );
		return $return;
	}

	public function getSortedWeekdays()
	{
		$return = array( 0, 1, 2, 3, 4, 5, 6 );
		$return = $this->sortWeekdays( $return );
		return $return;
	}

	public function getWeekdays()
	{
		$return = array();

		$wkds = array( 0, 1, 2, 3, 4, 5, 6 );
		$wkds = $this->sortWeekdays( $wkds );

		reset( $wkds );
		foreach( $wkds as $wkd ){
			$return[ $wkd ] = $this->_weekdays[$wkd];
		}
		return $return;
	}

	public function sortWeekdays( $wds )
	{
		$return = array();
		$later = array();

		sort( $wds );
		reset( $wds );
		foreach( $wds as $wd ){
			if( $wd < $this->weekStartsOn )
				$later[] = $wd;
			else
				$return[] = $wd;
		}
		$return = array_merge( $return, $later );
		return $return;
	}

	public function getDuration( $dateTime1, $dateTime2 )
	{
		if( $dateTime1 == $dateTime2 ){
			return 0;
		}

		// $timestamp1 = $this->getTimestamp();
		$timestamp1 = $this->setDateTimeDb( $dateTime1 )->getTimestamp();
		$timestamp2 = $this->setDateTimeDb( $dateTime2 )->getTimestamp();

		$ret = abs( $timestamp2 - $timestamp1 );
		return $ret;
	}

	public function getMonth()
	{
		$month = $this->format('n');
		return $month;
	}

	public function getWeekNo( $date = NULL )
	{
		if( NULL !== $date ){
			$this->setDateDb( $date );
		}

		$ret = $this->format('W'); // but it works out of the box for week starts on monday
		$weekday = $this->getWeekday();
		if( ! $weekday ){ // sunday
			if( ! $this->weekStartsOn ){
				$ret = $ret + 1;
			}
		}

		return $ret;
	}

	public function getAllDates( $startDate, $endDate, $withStartEnd = FALSE )
	{
		static $cache = array();
		$key = $startDate . '-' . $endDate;
		$key .= $withStartEnd ? '-1' : '-0';
		if( isset($cache[$key]) ){
			return $cache[$key];
		}

		$return = array();

		$rexDate = $startDate;
		$this->setDateDb( $rexDate );
		while( $rexDate <= $endDate ){
			if( $withStartEnd ){
				$startDateTime = $this->dateTime( $rexDate, 0 );
				$endDateTime = $this->getEndDateTimeDb( $rexDate );
				$return[ $rexDate ] = array( $startDateTime, $endDateTime );
			}
			else {
				$return[] = $rexDate;
			}
			$rexDate = $this->getNextDate( $rexDate );
		}

		$cache[$key] = $return;
		return $cache[$key];
	}

	public function getNextDate( $date )
	{
		static $cache = array();
		$key = $date;
		if( isset($cache[$key]) ){
			return $cache[$key];
		}

		$ret = $this->setDateDb( $date )->mod( '+1 day' )->getDateDb();
		$cache[$key] = $ret;
		return $cache[$key];
	}

	public function getPrevDate( $date )
	{
		static $cache = array();
		$key = $date;
		if( isset($cache[$key]) ){
			return $cache[$key];
		}

		$ret = $this->setDateDb( $date )->mod( '-1 day' )->getDateDb();
		$cache[$key] = $ret;
		return $cache[$key];
	}
}