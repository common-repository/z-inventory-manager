<?php if (! defined('ABSPATH')) exit;
interface PW1_Time_Format_Interface
{
	public function formatTime( $dateTimeDb );
	public function formatTimeInDay( $timeInDay );
	public function formatRange( $startDateTime, $endDateTime );
	public function formatTimeRange( $startDateTime, $endDateTime );
	public function formatTimeTimeRange( $startTime, $endTime );
	public function formatDate( $dateTimeDb );
	public function formatDateDate( $dateDb );
	public function formatDateWithWeekday( $dateTimeDb );
	public function formatDateRange( $date1, $date2 );
	public function formatWeekday( $weekday );
	public function formatDayOfWeek( $dateTimeDb );
	public function formatMonth( $dateTimeDb );
	public function formatMonthName( $monthNo );
	public function formatDuration( $seconds );
	public function formatDurationVerbose( $seconds, $maxMeasure = 'd' );
	public function formatDurationFromText( $text );
	public function formatFull( $dateTimeDb );
}

class PW1_Time_Format extends _PW1 implements PW1_Time_Format_Interface
{
	protected $timeFormat = 'g:ia';
	protected $dateFormat = 'j M Y';

	protected $_months = array();
	protected $_localizeMonths = array();
	protected $_weekdays = array();

	public $t;

	public function __construct(
		PW1_Time_ $t
	)
	{
		$this->_months = array( 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
		$this->_weekdays = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );

		$this->_localizeMonths = array();
		foreach( $this->_months as $m ){
			$this->_localizeMonths[ $m ] = '__' . $m . '__';
		}
	}

	public function setTimeFormat( $s )
	{
		$this->timeFormat = $s;
		return $this;
	}

	public function getTimeFormat()
	{
		return $this->timeFormat;
	}

	public function setDateFormat( $s )
	{
		$this->dateFormat = $s;
		return $this;
	}

	public function getDateFormat()
	{
		return $this->dateFormat;
	}

	public function formatTimeInDay( $timeInDay )
	{
		$dateTimeDb = $this->t->setDateDb( '20200204' )->mod( '+ ' . $timeInDay . ' seconds' )
			->getDateTimeDb()
			;
		return $this->formatTime( $dateTimeDb );
	}

	public function formatTime( $dateTimeDb )
	{
		if( NULL === $dateTimeDb ){
			return;
		}

		if( $dateTimeDb < 24*60*60 ){
			$dateTimeDb = $this->t->setNow()->setStartDay()->mod( '+ ' . $dateTimeDb . ' seconds' )
				->getDateTimeDb()
				;
		}

		$return = $this->t->setDateTimeDb( $dateTimeDb )
			->format( $this->timeFormat )
			;

		return $return;
	}

	public function formatTimeRange( $startDateTime, $endDateTime )
	{
		$start = $this->formatTime( $startDateTime );
		$end = $this->formatTime( $endDateTime );
		$return = $start . ' - ' . $end;
		return $return;
	}

	public function formatTimeTimeRange( $startTime, $endTime )
	{
		static $cache = array(); 

		$key = $startTime . '-' . $endTime;
		if( isset($cache[$key]) ){
			return $cache[$key];
		}

		$startDateTime = $this->t
			->setDateDb('20190210')
			->mod( '+' . $startTime . ' seconds' )
			->getDateTimeDb()
			;
		$endDateTime = $this->t
			->setDateDb('20190210')
			->mod( '+' . $endTime . ' seconds' )
			->getDateTimeDb()
			;

		$start = $this->formatTime( $startDateTime );
		$end = $this->formatTime( $endDateTime );

		$return = $start . ' - ' . $end;
		$cache[ $key ] = $return;

		return $return;
	}

	public function presentRange( $dateTime1, $dateTime2 )
	{
		static $cache = array(); 

		$key = $dateTime1 . '-' . $dateTime2;
		if( isset($cache[$key]) ){
			return $cache[$key];
		}

		$ret = array();

		$date1 = $this->t->getDateDb( $dateTime1 );
		$date2 = $this->t->getDateDb( $dateTime2 );

		$time1 = $this->t->setDateTimeDb( $dateTime1 )->getTimeInDay();
		$time2 = $this->t->setDateTimeDb( $dateTime2 )->getTimeInDay();

		if( 0 == $time2 ){
			$date2 = $this->t->getPrevDate( $date2 );
		}

		if( $date1 === $date2 ){
			$ret[0] = $this->formatDateWithWeekday( $dateTime1 );

		// 00:00-00:00, or 00:00-23:59 or 00:00-23:55
			if( (0 == $time1) && ( (0 == $time2) OR (86340 == $time2) OR (86100 == $time2)) ){
			}
			else {
				$ret[0] .= ' ' . $this->formatTime( $dateTime1 );
				$ret[1] = $this->formatTime( $dateTime2 );
			}
		}
		else {
		// 00:00-00:00, or 00:00-23:59 or 00:00-23:55
			if( (0 == $time1) && ( (0 == $time2) OR (86340 == $time2) OR (86100 == $time2)) ){
				$ret[0] = $this->formatDateRange( $date1, $date2 );
			}
			else {
				$ret[0] = $this->formatDateWithWeekday( $dateTime1 ) . ' ' . $this->formatTime( $dateTime1 );
				$ret[1] = $this->formatDateWithWeekday( $dateTime2 ) . ' ' . $this->formatTime( $dateTime2 );
			}
		}

		$cache[ $key ] = $ret;

		return $ret;
	}

	public function formatRange( $dateTime1, $dateTime2 )
	{
		$ret = $this->presentRange( $dateTime1, $dateTime2 );
		$ret = ( count($ret) > 1 ) ? $ret[0] . ' - ' . $ret[1] : $ret[0];
		return $ret;
	}

	public function formatFull( $dateTimeDb )
	{
		$ret = $this->formatDateWithWeekday( $dateTimeDb ) . ' ' . $this->formatTime( $dateTimeDb );
		return $ret;
	}

	public function formatDate( $dateTimeDb )
	{
		$return = $this->t->setDateTimeDb( $dateTimeDb )
			->format( $this->dateFormat )
			;

	// replace English months to localized ones
		$replaceFrom = array_keys( $this->_localizeMonths );
		$replaceTo = array_values( $this->_localizeMonths );
		$return = str_replace( $replaceFrom, $replaceTo, $return );

		return $return;
	}

	public function formatDateDate( $dateDb )
	{
		$dateTimeDb = $this->t->setDateDb( $dateDb )->getDateTimeDb();
		return $this->formatDate( $dateTimeDb );
	}

	public function formatDateWithWeekday( $dateTimeDb )
	{
		$wd = $this->t->setDateTimeDb( $dateTimeDb )->getWeekday();
		$weekdayView = $this->formatWeekday( $wd );
		$dateView = $this->formatDate( $dateTimeDb );
		$return = $weekdayView . ', ' . $dateView;

		return $return;
	}

	public function formatWeekday( $wd )
	{
		$wd = (string) $wd;
		$return = '__' . $this->_weekdays[$wd] . '__';
		return $return;
	}

	public function formatDayOfWeek( $dateTimeDb )
	{
		$wd = $this->t->setDateTimeDb( $dateTimeDb )->getWeekday();
		$ret = $this->formatWeekday( $wd );
		return $ret;
	}

	public function formatDuration( $seconds )
	{
		static $cache = array();

		$seconds = (string) $seconds;

		if( isset($cache[$seconds]) ){
			return $cache[$seconds];
		}

		$hours = floor( $seconds / (60 * 60) );
		$remain = $seconds - $hours * (60 * 60);
		$minutes = floor( $remain / 60 );

		$hoursView = $hours;
		$minutesView = sprintf( '%02d', $minutes );

		$return = $hoursView . ':' . $minutesView;

		$cache[$seconds] = $return;

		return $return;
	}

	public function formatDurationFromText( $text )
	{
		$ts1 = $this->t->setDateDb( '20190725' )->getTimestamp();
		$ts2 = $this->t->mod( '+' . $text )->getTimestamp();

		$seconds = $ts2 - $ts1;
		return $this->formatDurationVerbose( $seconds );
	}

	// maxMeasure can be d, h, m
	public function formatDurationVerbose( $seconds, $maxMeasure = 'd' )
	{
		static $cache = array();

		$seconds = (string) $seconds;

		if( isset($cache[$seconds]) ){
			return $cache[$seconds];
		}

		$measures = array( 'd' => 'd', 'h' => 'h', 'm' => 'm' );
		if( 'd' === $maxMeasure ){
		}
		if( 'h' === $maxMeasure ){
			unset( $measures['d'] );
		}
		if( 'm' === $maxMeasure ){
			unset( $measures['d'] ); unset( $measures['h'] );
		}

		$days = isset($measures['d']) ? floor( $seconds / (24 * 60 * 60) ) : 0;
		$remain = $seconds - $days * (24 * 60 * 60);
		$hours = isset($measures['h']) ? floor( $remain / (60 * 60) ) : 0;
		$remain = $remain - $hours * (60 * 60);
		$minutes = isset($measures['m']) ? floor( $remain / 60 ) : 0;

		$return = array();

		if( $days ){
			$daysView = $days;
			$daysView = $daysView . '' . '__d__';
			$return[] = $daysView;
		}

		if( $hours ){
			$hoursView = $hours;
			$hoursView = $hoursView . '' . '__h__';
			$return[] = $hoursView;
		}

		if( $minutes ){
			$minutesView = sprintf( '%02d', $minutes );
			$minutesView = $minutesView . '' . '__m__';
			$return[] = $minutesView;
		}

		$return = join( ' ', $return );

		$cache[$seconds] = $return;

		return $return;
	}

	public function formatMonth( $dateTimeDb )
	{
		$this->t->setDateTimeDb( $dateTimeDb );
		$month = $this->t->getMonth();
		$ret = $this->formatMonthName( $month );
		return $ret;
	}

	public function formatMonthYear( $dateTimeDb )
	{
		$this->t->setDateTimeDb( $dateTimeDb );

		$month = $this->t->getMonth();
		$year = $this->t->getYear();

		$ret = $this->formatMonthName( $month ) . ' ' . $year;
		return $ret;
	}

	public function formatMonthName( $monthNo )
	{
		$ret = $this->_months[ $monthNo - 1 ];
		$ret = '__' . $ret . '__';
		return $ret;
	}

	public function formatDateRange( $date1, $date2, $withWeekday = FALSE, $withYear = TRUE )
	{
		$return = array();
		$skip = array();

		if( $date1 && (! $date2) ){
			$return = $this->formatDate( $date1 );
			if( $withWeekday ){
				$wd = $this->t->setDateDb( $date1 )->getWeekday();
				$return = $this->formatWeekday( $wd ) . ', ' . $return;
			}
			$return = $return . ' &rarr;';
			return $return;
		}

		if( (! $date1) && $date2 ){
			$return = $this->formatDate( $date2 );
			if( $withWeekday ){
				$wd = $this->t->setDateDb( $date2 )->getWeekday();
				$return = $this->formatWeekday( $wd ) . ', ' . $return;
			}
			$return = '&rarr; ' . $return;
			return $return;
		}

		if( $date1 == $date2 ){
			$viewDate1 = $this->formatDate( $date1 );
			if( $withWeekday ){
				$wd = $this->t->setDateDb( $date1 )->getWeekday();
				$viewDate1 = $this->formatWeekday( $wd ) . ', ' . $viewDate1;
			}
			$return = $viewDate1;
			return $return;
		}

	// WHOLE YEAR?
		$currentYear = $this->t->setDateDb( $date1 )->getYear();
		$year2 = $this->t->setDateDb( $date2 )->mod('+1 day')->getYear();
		if( $year2 !== $currentYear ){
			$year1 = $this->t->setDateDb( $date1 )->mod('-1 day')->getYear();
			if( $year1 !== $currentYear ){
		// BINGO!
				$return = $currentYear;
				return $return;
			}
		}

	// WHOLE MONTH?
		$day2 = $this->t->setDateDb( $date2 )->mod('+1 day')->getDay();
		if( 1 == $day2 ){
			$day1 = $this->t->setDateDb( $date1 )->getDay();

			if( 1 == $day1 ){
				$month1 = $this->t->getMonth();
				$year1 = $this->t->getYear();

				$this->t->setDateDb( $date2 );
				$month2 = $this->t->getMonth();
				$year2 = $this->t->getYear();

				if( $year1 == $year2 ){
				// BINGO!
					if( $month1 == $month2 ){
						$month = $this->t->format('n');
						$return = $this->formatMonthName( $month ) . ' ' . $year1;
						return $return;
					}
					else {
						$month2 = $this->t->format('n');
						$month1 = $this->t->setDateDb( $date1 )->format('n');
						$return = $this->formatMonthName( $month1 ) . ' - ' . $this->formatMonthName( $month2 ) . ' ' . $year1;
						return $return;
					}
				}
			}
		}

		$this->t->setDateDb( $date1 );
		$year1 = $this->t->getYear();
		$month1 = $this->t->format('n');

		$this->t->setDateDb( $date2 );
		$year2 = $this->t->getYear();
		$month2 = $this->t->format('n');

		if( $year2 == $year1 )
			$skip['year'] = TRUE;
		if( ($year2 == $year1) && ($month2 == $month1) )
			$skip['month'] = TRUE;

		if( ! $withYear ){
			$skip['year'] = TRUE;
		}

		$skip['date'] = TRUE;

		$pos_y = NULL;
		if( $skip ){
			$dateFormat = $this->dateFormat;
			$dateFormatShort = $dateFormat;

			$tags = array('m', 'n', 'M');
			foreach( $tags as $t ){
				$pos_m_original = strpos($dateFormatShort, $t);
				if( $pos_m_original !== FALSE )
					break;
			}

			if( isset($skip['year']) ){
				$pos_y = strpos($dateFormatShort, 'Y');
				if( $pos_y == 0 ){
					$dateFormatShort = substr_replace( $dateFormatShort, '', $pos_y, 2 );
				}
				else {
					$dateFormatShort = substr_replace( $dateFormatShort, '', $pos_y - 1, 2 );
				}
			}

			if( isset($skip['month']) ){
				$tags = array('m', 'n', 'M');
				foreach( $tags as $t ){
					$pos_m = strpos($dateFormatShort, $t);
					if( $pos_m !== FALSE )
						break;
				}

				// month going first, do not replace
				if( $pos_m_original == 0 ){
					// $dateFormatShort = substr_replace( $dateFormatShort, '', $pos_m, 2 );
				}
				else {
					// month going first, do not replace
					if( $pos_m == 0 ){
						$dateFormatShort = substr_replace( $dateFormatShort, '', $pos_m, 2 );
					}
					else {
						$dateFormatShort = substr_replace( $dateFormatShort, '', $pos_m - 1, 2 );
					}
				}
			}

			if( $pos_y == 0 ){ // skip year in the second part
				$dateFormat1 = $dateFormat;
				$dateFormat2 = $dateFormatShort;
			}
			else {
				$dateFormat1 = $dateFormatShort;
				$dateFormat2 = $dateFormat;
			}

			if( ! $withYear ){
				$posY = strpos($dateFormat1, 'Y');
				if( FALSE !== $posY ){
					if( $posY ){
						$dateFormat1 = substr_replace( $dateFormat1, '', $posY - 1, 2 );
					}
					else {
						$dateFormat1 = substr_replace( $dateFormat1, '', $posY, 2 );
					}
				}

				$posY = strpos($dateFormat2, 'Y');
				if( FALSE !== $posY ){
					if( $posY ){
						$dateFormat2 = substr_replace( $dateFormat2, '', $posY - 1, 2 );
					}
					else {
						$dateFormat2 = substr_replace( $dateFormat2, '', $posY, 2 );
					}
				}
			}

			$this->t->setDateDb( $date1 );

			$viewDate1 = $this->t->format( $dateFormat1 );
			if( $withWeekday ){
				$wd = $this->t->setDateDb( $date1 )->getWeekday();
				$viewDate1 = $this->formatWeekday( $wd ) . ', ' . $viewDate1;
			}
			$return[] = $viewDate1;

			$this->t->setDateDb( $date2 );
			$viewDate2 = $this->t->format( $dateFormat2 );
			if( $withWeekday ){
				$wd = $this->t->setDateDb( $date2 )->getWeekday();
				$viewDate2 = $this->formatWeekday( $wd ) . ', ' . $viewDate2;
			}
			$return[] = $viewDate2;
		}
		else {
			$viewDate1 = $this->formatDate( $date1 );
			if( $withWeekday ){
				$wd = $this->t->setDateDb( $date1 )->getWeekday();
				$viewDate1 = $this->formatWeekday( $wd ) . ', ' . $viewDate1;
			}
			$return[] = $viewDate1;

			$viewDate2 = $this->formatDate( $date2 );
			if( $withWeekday ){
				$wd = $this->t->setDateDb( $date2 )->getWeekday();
				$viewDate2 = $this->formatWeekday( $wd ) . ', ' . $viewDate2;
			}
			$return[] = $viewDate2;
		}

		if( $viewDate2 ){
			$return = $viewDate1 . ' - ' . $viewDate2;
		}
		else {
			$return = $viewDate1;
		}

		return $return;
	}
}