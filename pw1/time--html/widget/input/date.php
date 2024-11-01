<?php if (! defined('ABSPATH')) exit;
class PW1_Time00Html_Widget_Input_Date extends _PW1
{
	public $t;
	public $tf;

	public function __construct(
		PW1_Time_ $t,
		PW1_Time_Format $tf
	)
	{}

	public function grab( $name, array $post )
	{
		$ret = isset( $post[$name] ) ? $post[$name] : NULL;
		return $ret;
	}

	public function render( $name, $value = NULL )
	{
		if( NULL === $value ){
			$value = $this->t->setNow()->getDateDb();
		}

		$weekdays = $this->t->getWeekdays();
		$weekStartsOn = $this->t->getWeekStartsOn();
		$dateFormat = $this->tf->getDateFormat();
		$htmlId = 'pw1-system-time-input-date-' . rand( 1000, 9999 );

		ob_start();
?>

<div id="<?php echo $htmlId; ?>">

<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" data-id="value">

<button type="button" data-href="#calendar" data-id="display"><?php echo $this->tf->formatDate( $value ); ?></button>

<div data-id="calendar" style="display: none;">
<table class="pw-align-center pw-mobile">
<thead>
	<tr>
		<td class="pw-valign-middle pw-align-center">
			<button type="button" data-href="#prev" class="pw-block">&laquo;&laquo;</button>
		</td>

		<td colspan="5" class="pw-align-center pw-valign-middle" data-id="monthLabel">Month Year</td>

		<td class="pw-valign-middle pw-align-center">
			<button type="button" data-href="#next" class="pw-block">&raquo;&raquo;</button>
		</td>
	</tr>
	<tr>
		<?php foreach( $weekdays as $wkd => $label ) : ?>
			<td class="pw-col-1-7 pw-align-center"><?php echo $label; ?></td>
		<?php endforeach; ?>
	</tr>
</thead>

<tbody>
</tbody>
</table>
</div>

<script type="text/template" data-id="weekTemplate">
<tr class="pw-align-center pw-valign-middle">
<?php foreach( $weekdays as $wkd => $label ) : ?>
{<?php echo $wkd; ?>}
<?php endforeach; ?>
</tr>
</script>

<script type="text/template" data-id="dayTemplate">
<td class="pw-align-center">
<button type="button" data-href="#date-{FULLDATE}" class="pw-block">{DATE}</button>
</td>
</script>

<script type="text/template" data-id="selectedDayTemplate">
<td class="pw-align-center pw-bg2">
<button type="button" data-href="#date-{FULLDATE}" class="pw-block">{DATE}</button>
</td>
</script>

<script type="text/template" data-id="todayTemplate">
<td class="pw-align-center">
<button type="button" data-href="#date-{FULLDATE}" class="pw-block">__Today__</button>
</td>
</script>

</div>

<script language="JavaScript">
( function( $el ){
	var self = this;
	// console.log( $el.innerHTML );

	self._weekStartsOn = '<?php echo $weekStartsOn; ?>';
	self._dateFormat = '<?php echo $dateFormat; ?>';

	// var nextLink = $el.querySelector( 'a[href="#next"]' );
	var nextLink = $el.querySelector( 'button[data-href="#next"]' );
	// var prevLink = $el.querySelector( 'a[href="#prev"]' );
	var prevLink = $el.querySelector( 'button[data-href="#prev"]' );

	// var calendarLink = $el.querySelector( 'a[href="#calendar"]' );
	var calendarLink = $el.querySelector( 'button[data-href="#calendar"]' );

	self.display		= $el.querySelector( '[data-id="display"]' );
	self.calendar		= $el.querySelector( '[data-id="calendar"]' );
	self.monthLabel	= $el.querySelector( '[data-id="monthLabel"]' );
	self.weeks			= $el.querySelector( 'tbody' );
	self.valueInput	= $el.querySelector( '[data-id="value"]' );

	self.weekTemplate	= $el.querySelector( '[data-id="weekTemplate"]' );
	self.dayTemplate	= $el.querySelector( '[data-id="dayTemplate"]' );
	self.selectedDayTemplate	= $el.querySelector( '[data-id="selectedDayTemplate"]' );
	self.todayTemplate	= $el.querySelector( '[data-id="todayTemplate"]' );

	self.selectedDate = new Date();

	this.render = function( d ){
		var dd = String(d.getDate()).padStart(2, '0');
		var mm = String(d.getMonth() + 1).padStart(2, '0'); //January is 0!
		var yyyy = d.getFullYear();

		self.monthLabel.innerHTML = yyyy + '-' + mm;

		var currentMonth = d.getMonth();

	// count how many weeks
		var weekCount = 0;
		var rex = new Date( d.getTime() );
		rex.setDate( 1 );
		while( currentMonth == rex.getMonth() ){
			weekCount++;
			rex.setDate( rex.getDate() + 7 );
		}

	// move to start week
		rex.setTime( d.getTime() );
		rex.setDate( 1 );
		while( self._weekStartsOn != rex.getDay() ){
			rex.setDate( rex.getDate() - 1 );
		}

		var today = new Date();
		self.weeks.innerHTML = '';
		for( var wk = 1; wk <= weekCount; wk++ ){
			var thisWeek = self.weekTemplate.innerHTML;

			for( var ii = 0; ii <= 6; ii++ ){
				if( (self.selectedDate.getDate() == rex.getDate()) && (self.selectedDate.getMonth() == rex.getMonth()) && (self.selectedDate.getYear() == rex.getYear()) ){
					var thisDay = self.selectedDayTemplate.innerHTML;
				}
				else if( (today.getDate() == rex.getDate()) && (today.getMonth() == rex.getMonth()) && (today.getYear() == rex.getYear()) ){
					var thisDay = self.todayTemplate.innerHTML;
				}
				else {
					var thisDay = self.dayTemplate.innerHTML;
				}

				var wkd = rex.getDay();
				var dateDb = rex.getFullYear() + String(rex.getMonth() + 1).padStart(2, '0') + String(rex.getDate()).padStart(2, '0');

				thisDay = thisDay.replace( '{DATE}', rex.getDate() );
				thisDay = thisDay.replace( '{FULLDATE}', dateDb );

				thisWeek = thisWeek.replace( '{' + wkd + '}', thisDay );
				// thisWeek = thisWeek.replace( '{full' + wkd + '}', dateDb );

				rex.setDate( rex.getDate() + 1 );
			}

			self.weeks.innerHTML += thisWeek;
		}

		// var dateLinks = self.weeks.querySelectorAll( 'a[href^="#date-"]' );
		// for( var ii = 0; ii < dateLinks.length; ii++ ){
			// dateLinks[ii].addEventListener( 'click', this.clickDate );
		// }

		var dateLinks = self.weeks.querySelectorAll( 'button[data-href^="#date-"]' );
		for( var ii = 0; ii < dateLinks.length; ii++ ){
			dateLinks[ii].addEventListener( 'click', this.clickDate );
		}
	};

	this.clickDate = function( e ){
		e.preventDefault();
		// var d = e.target.getAttribute( 'href' ).substr( String('#date-').length );
		var d = e.target.getAttribute( 'data-href' ).substr( String('#date-').length );

		self.valueInput.value = d;
		self.selectedDate = new Date( parseInt(d.substr(0,4)), parseInt(d.substr(4,2)) - 1, parseInt(d.substr(6,2)) );

		self.display.style.display = "block";
		self.calendar.style.display = "none";

		var formattedDate = self.formatDate( self._dateFormat, self.selectedDate );
		self.display.innerHTML = formattedDate;
	}

	this.clickNext = function( e ){
		e.preventDefault();

		var d = self.selectedDate;
		d = ( 11 == d.getMonth() ) ? new Date( d.getFullYear() + 1, 0, 1 ) : new Date( d.getFullYear(), d.getMonth() + 1, 1 );
		self.selectedDate = d;

		self.render( self.selectedDate );
		return false;
	};

	this.clickPrev = function( e ){
		e.preventDefault();

		var d = self.selectedDate;
		d = ( 1 == d.getMonth() ) ? new Date( d.getFullYear() - 1, 11, 1 ) : new Date( d.getFullYear(), d.getMonth() - 1, 1 );
		self.selectedDate = d;

		self.render( selectedDate );
		return false;
	};

	this.clickCalendar = function( e ){
		e.preventDefault();

		self.display.style.display = "none";
		self.calendar.style.display = "block";

		self.render( self.selectedDate );
		return false;
	};

	nextLink.addEventListener( 'click', this.clickNext );
	prevLink.addEventListener( 'click', this.clickPrev );
	calendarLink.addEventListener( 'click', this.clickCalendar );

// js implementation of php's format
this.formatDate = function( format, timestamp ){
  var jsdate, f
  // Keep this here (works, but for code commented-out below for file size reasons)
  // var tal= [];
  var txtWords = [
    'Sun', 'Mon', 'Tues', 'Wednes', 'Thurs', 'Fri', 'Satur',
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December'
  ]
  // trailing backslash -> (dropped)
  // a backslash followed by any character (including backslash) -> the character
  // empty string -> empty string
  var formatChr = /\\?(.?)/gi
  var formatChrCb = function (t, s) {
    return f[t] ? f[t]() : s
  }
  var _pad = function (n, c) {
    n = String(n)
    while (n.length < c) {
      n = '0' + n
    }
    return n
  }
  f = {
    // Day
    d: function () {
      // Day of month w/leading 0; 01..31
      return _pad(f.j(), 2)
    },
    D: function () {
      // Shorthand day name; Mon...Sun
      return f.l()
        .slice(0, 3)
    },
    j: function () {
      // Day of month; 1..31
      return jsdate.getDate()
    },
    l: function () {
      // Full day name; Monday...Sunday
      return txtWords[f.w()] + 'day'
    },
    N: function () {
      // ISO-8601 day of week; 1[Mon]..7[Sun]
      return f.w() || 7
    },
    S: function () {
      // Ordinal suffix for day of month; st, nd, rd, th
      var j = f.j()
      var i = j % 10
      if (i <= 3 && parseInt((j % 100) / 10, 10) === 1) {
        i = 0
      }
      return ['st', 'nd', 'rd'][i - 1] || 'th'
    },
    w: function () {
      // Day of week; 0[Sun]..6[Sat]
      return jsdate.getDay()
    },
    z: function () {
      // Day of year; 0..365
      var a = new Date(f.Y(), f.n() - 1, f.j())
      var b = new Date(f.Y(), 0, 1)
      return Math.round((a - b) / 864e5)
    },

    // Week
    W: function () {
      // ISO-8601 week number
      var a = new Date(f.Y(), f.n() - 1, f.j() - f.N() + 3)
      var b = new Date(a.getFullYear(), 0, 4)
      return _pad(1 + Math.round((a - b) / 864e5 / 7), 2)
    },

    // Month
    F: function () {
      // Full month name; January...December
      return txtWords[6 + f.n()]
    },
    m: function () {
      // Month w/leading 0; 01...12
      return _pad(f.n(), 2)
    },
    M: function () {
      // Shorthand month name; Jan...Dec
      return f.F()
        .slice(0, 3)
    },
    n: function () {
      // Month; 1...12
      return jsdate.getMonth() + 1
    },
    t: function () {
      // Days in month; 28...31
      return (new Date(f.Y(), f.n(), 0))
        .getDate()
    },

    // Year
    L: function () {
      // Is leap year?; 0 or 1
      var j = f.Y()
      return j % 4 === 0 & j % 100 !== 0 | j % 400 === 0
    },
    o: function () {
      // ISO-8601 year
      var n = f.n()
      var W = f.W()
      var Y = f.Y()
      return Y + (n === 12 && W < 9 ? 1 : n === 1 && W > 9 ? -1 : 0)
    },
    Y: function () {
      // Full year; e.g. 1980...2010
      return jsdate.getFullYear()
    },
    y: function () {
      // Last two digits of year; 00...99
      return f.Y()
        .toString()
        .slice(-2)
    },

    // Time
    a: function () {
      // am or pm
      return jsdate.getHours() > 11 ? 'pm' : 'am'
    },
    A: function () {
      // AM or PM
      return f.a()
        .toUpperCase()
    },
    B: function () {
      // Swatch Internet time; 000..999
      var H = jsdate.getUTCHours() * 36e2
      // Hours
      var i = jsdate.getUTCMinutes() * 60
      // Minutes
      // Seconds
      var s = jsdate.getUTCSeconds()
      return _pad(Math.floor((H + i + s + 36e2) / 86.4) % 1e3, 3)
    },
    g: function () {
      // 12-Hours; 1..12
      return f.G() % 12 || 12
    },
    G: function () {
      // 24-Hours; 0..23
      return jsdate.getHours()
    },
    h: function () {
      // 12-Hours w/leading 0; 01..12
      return _pad(f.g(), 2)
    },
    H: function () {
      // 24-Hours w/leading 0; 00..23
      return _pad(f.G(), 2)
    },
    i: function () {
      // Minutes w/leading 0; 00..59
      return _pad(jsdate.getMinutes(), 2)
    },
    s: function () {
      // Seconds w/leading 0; 00..59
      return _pad(jsdate.getSeconds(), 2)
    },
    u: function () {
      // Microseconds; 000000-999000
      return _pad(jsdate.getMilliseconds() * 1000, 6)
    },

    // Timezone
    e: function () {
      var msg = 'Not supported (see source code of date() for timezone on how to add support)'
      throw new Error(msg)
    },
    I: function () {
      // DST observed?; 0 or 1
      // Compares Jan 1 minus Jan 1 UTC to Jul 1 minus Jul 1 UTC.
      // If they are not equal, then DST is observed.
      var a = new Date(f.Y(), 0)
      // Jan 1
      var c = Date.UTC(f.Y(), 0)
      // Jan 1 UTC
      var b = new Date(f.Y(), 6)
      // Jul 1
      // Jul 1 UTC
      var d = Date.UTC(f.Y(), 6)
      return ((a - c) !== (b - d)) ? 1 : 0
    },
    O: function () {
      // Difference to GMT in hour format; e.g. +0200
      var tzo = jsdate.getTimezoneOffset()
      var a = Math.abs(tzo)
      return (tzo > 0 ? '-' : '+') + _pad(Math.floor(a / 60) * 100 + a % 60, 4)
    },
    P: function () {
      // Difference to GMT w/colon; e.g. +02:00
      var O = f.O()
      return (O.substr(0, 3) + ':' + O.substr(3, 2))
    },
    T: function () {
      return 'UTC'
    },
    Z: function () {
      // Timezone offset in seconds (-43200...50400)
      return -jsdate.getTimezoneOffset() * 60
    },

    // Full Date/Time
    c: function () {
      // ISO-8601 date.
      return 'Y-m-d\\TH:i:sP'.replace(formatChr, formatChrCb)
    },
    r: function () {
      // RFC 2822
      return 'D, d M Y H:i:s O'.replace(formatChr, formatChrCb)
    },
    U: function () {
      // Seconds since UNIX epoch
      return jsdate / 1000 | 0
    }
  }

  var _date = function (format, timestamp) {
    jsdate = (timestamp === undefined ? new Date() // Not provided
      : (timestamp instanceof Date) ? new Date(timestamp) // JS Date()
      : new Date(timestamp * 1000) // UNIX timestamp (auto-convert to int)
    )
    return format.replace(formatChr, formatChrCb)
  }

  return _date(format, timestamp)
}

})( document.getElementById('<?php echo $htmlId; ?>') );

</script>

<?php 
		$ret = ob_get_clean();
		return $ret;
	}
}