<?php


abstract class AbstractFeed {

	public $formatter;

	public function __construct() {
		$this->formatter = new Formatter();
	}


	// functions to be overridden by subclasses
	public function getMimeType() { return 'text/plain'; }
	public abstract function getHeader ($query);
	public abstract function getEntry ($event);
	public abstract function getFooter ();


	public static function getFeedUrlPrefix() {
		global $HOST_URL_PREFIX;
		global $FEED_PATH;
		return $HOST_URL_PREFIX . $FEED_PATH;
	}

	public static function getServiceUrl() {
		global $HOST_URL_PREFIX;
		global $FDSN_PATH;
		return $HOST_URL_PREFIX . $FDSN_PATH . '/';
	}

	public static function getEventDetailLink($eventid) {
		global $CONFIG;
		global $HOST_URL_PREFIX;
		return $HOST_URL_PREFIX . $CONFIG['EVENT_PATH'] . '/' . $eventid;
	}

	public static function getEventDetailFeed($eventid, $format) {
		global $HOST_URL_PREFIX;
		global $FDSN_PATH;
		global $FEED_PATH;

		if (strpos($_SERVER['REQUEST_URI'], $FDSN_PATH) !== FALSE) {
			// if on fdsn, stay on fdsn
			if ($format === 'geojsonp') {
				// fdsn doesn't support geojsonp format, users can append callback manually
				$format = 'geojson';
			}
			return $HOST_URL_PREFIX . $FDSN_PATH . '/query?eventid=' . $eventid . '&format=' . $format;
		} else {
			// on feeds
			return $HOST_URL_PREFIX . $FEED_PATH . '/detail/' . $eventid . '.' . $format;
		}
	}

	public function getEventTitle($event) {
		return
				$this->formatter->formatMagnitude($event['eventMagnitude']) .
				' - ' . $event['region'];
	}


	protected function getQuickSummaryCSS() {
		$css = '
h2 {
	margin-bottom:0;
	font-size:16px;
	font-weight:bold;
}

.quicksummary {
	float:left;
}
.quicksummary > a {
	float:left;
	display:block;
	padding:4px;
	margin-right:8px;
	text-decoration:none;
	border:1px solid #333;
}
.quicksummary > a.tsunamilogo {
	padding:0;
}

.roman { font-family:Georgia, Times, serif; }
.pager-pending { background-color: #FFF; color: #000; }
.pager-green { background-color:#00b04f; color: #fff; }
.pager-yellow { background-color: #ff0; color: #000; }
.pager-orange { background-color: #f90; color: #000; }
.pager-red { background-color: #f00; color: #fff; }
.mmi-I{color:#000; background-color:#FFFFFF;}
.mmi-II{color:#000; background-color:#ACD8E9;}
.mmi-III{color:#000; background-color:#ACD8E9;}
.mmi-IV{color:#000; background-color:#83D0DA;}
.mmi-V{color:#000; background-color:#7BC87F;}
.mmi-VI{color:#000; background-color:#F9F518;}
.mmi-VII{color:#000; background-color:#FAC611;}
.mmi-VIII{color:#000; background-color:#FA8A11;}
.mmi-IX{color:#FFF; background-color:#F7100C;}
.mmi-X{color:#FFF; background-color:#C80F0A;}
.mmi-XI{color:#FFF; background-color:#C80F0A;}
.mmi-XII{color:#FFF; background-color:#C80F0A;}

dl {
	clear:left;
	font-family: Helvetica;
	line-height: 1.3;
}
dt {
	color: #999;
	margin-top: .5em;
}
dd {
	margin: 0;
}

.links{clear:both;line-height:1.3;}
';

		$css = str_replace("\n", "", $css);
		$css = str_replace("\t", "", $css);
		$css = str_replace(" ", "", $css);

		return $css;
	}


	protected function getQuickSummary($event, $includeLocation=true) {
		static $romans = array('I', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');

		$content = '';
		$eventpage = self::getEventDetailLink($event['eventSource'] . $event['eventSourceCode']);


		// QUICK SUMMARY

		$alertlevel = null;
		$maxmmi = null;
		$feltreports = null;
		$feltmmi = null;
		$tsunamiflag = (intval($event['tsunami']) == 1);

		if ($event['alertlevel'] != '') {
			$alertlevel = strtolower($event['alertlevel']);
		}
		if ($event['maxmmi'] != '') {
			$maxmmi = round($event['maxmmi']);
		}
		if ($event['maxcdi'] != '') {
			$feltmmi = round($event['maxcdi']);
		}
		if ($event['num_responses'] != '') {
			$feltreports = $event['num_responses'];
		}

		$summaries = array();

		if ($alertlevel !== null) {
			$summaries[] = '<a'
				. ' href="' . $eventpage . '#pager"'
				. ' title="PAGER estimated impact alert level"'
				. ' class="pager-' . $alertlevel . '"'
				. '>PAGER - <strong class="roman">' . strtoupper($alertlevel) . '</strong></a>';
		}

		if ($maxmmi !== null) {
			$romanmmi = $romans[$maxmmi];
			$summaries[] .= '<a'
				. ' href="' . $eventpage . '#shakemap"'
				. ' title="ShakeMap maximum estimated intensity"'
				. ' class="mmi-' . $romanmmi . '"'
				. '>ShakeMap - <strong class="roman">' . $romanmmi . '</strong></a>';
		}

		if ($feltmmi !== null) {
			$romanmmi = $romans[$feltmmi];
			$summaries[] .= '<a'
				. ' href="' . $eventpage . '#dyfi"'
				. ' class="mmi-' . $romanmmi . '"'
				. ' title="Did You Feel It? maximum reported intensity ('
				. $feltreports . ' reports)"'
				. '>DYFI? - <strong class="roman">' . $romanmmi . '</strong></a>';
		}

		if ($tsunamiflag) {
			$summaries[] .= '<a class="tsunamilogo"' .
					' href="http://www.tsunami.gov/"' .
					' title="Tsunami Warning Center">' .
					' <img src="' .
						self::getFeedUrlPrefix() .
						'/images/tsunami-wave-warning.jpg" alt="Tsunami Warning Center"/>' .
					'</a>';
		}

		if (count($summaries) > 0) {
			$content .= '<p class="quicksummary">' .
					implode(' ', $summaries) .
					'</p>';
		}

		$content .= '<dl>' .
				'<dt>Time</dt>' .
				'<dd>' . $this->formatter->formatDate($event['eventTime']) . '</dd>' .
				'<dd>' . $this->formatter->formatDate($event['eventTime'], $event['offset']) . ' at epicenter</dd>' .
				($includeLocation ?
					'<dt>Location</dt>' .
					'<dd>' . $this->formatter->formatLatitude($event['eventLatitude']) .
							' ' . $this->formatter->formatLongitude($event['eventLongitude']) . '</dd>'
					: ''
				) .
				'<dt>Depth</dt>' .
				'<dd>' . $this->formatter->formatDepth($event['eventDepth']) . '</dd>' .
				'</dl>';

		return $content;
	}

}

