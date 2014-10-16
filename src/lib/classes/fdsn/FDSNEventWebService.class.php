<?php


class FDSNEventWebService {

	// the FDSNProductIndex to use
	public $index;

	// service version number
	public $version;
	// maximum number of earthquakes
	public $serviceLimit;

	const NO_DATA = 204;
	const BAD_REQUEST = 400;
	const NOT_FOUND = 404;
	const NOT_IMPLEMENTED = 501;
	const SERVICE_UNAVAILABLE = 503;

	// status message text
	public static $statusMessage = array(
		self::NO_DATA => 'No Data',
		self::BAD_REQUEST => 'Bad Request',
		self::NOT_FOUND => 'Not Found',
		self::NOT_IMPLEMENTED => 'Not Implemented',
		self::SERVICE_UNAVAILABLE => 'Service Unavailable'
	);


	/**
	 * @param $index {Index}
	 *      The index used to search for events.
	 * @param $redirect {Boolean} Optional. Default false.
	 *      True if NO_DATA and NOT_FOUND errors should be redirected to a
	 *      different service rather than returning the response code.
	 */
	public function __construct($index, $redirect=false) {
		$this->index = $index;

		global $CONFIG;
		$this->version = $CONFIG['FDSN_VERSION'];
		$this->serviceLimit = $CONFIG['MAX_SEARCH'];

		if ($redirect === true) {
			// May want to include some FDSN_HOST information ... ?
			$this->redirect = $CONFIG['FDSN_PATH'];
		} else {
			$this->redirect = false;
		}
	}



// WEB SERVICE METHODS


	// fdsn api methods
	public function query() {
		$query = $this->parseQuery();
		$resultType = $this->parseResultType();

		if ($query->eventid === null || $query->format === 'quakeml' ||
					$query->format === 'xml') {
			$this->handleSummaryQuery($query);
		} else {
			$this->handleDetailQuery($query, $resultType);
		}
	}

	public function handleSummaryQuery($query) {
		// check how many results would be returned
		$count = $this->index->getEventCount($query);
		if ($count === 0) {
			// adhere to specification for default format
			// allow empty feeds in other formats.
			if ($query->format === 'quakeml' || $query->format === 'xml') {
				$this->error($query->nodata, self::$statusMessage[$query->nodata],
						true);
			}
		} else if ($count > $this->serviceLimit) {
			$this->error(self::BAD_REQUEST, $count . ' matching events exceeds ' .
					'search limit of ' . $this->serviceLimit . '. Modify the search ' .
					'to match fewer events.');
		}

		// save this on query for potential output
		$query->resultCount = $count;

		// setup output format
		$callback = new FDSNIndexCallback();
		if ($query->format === 'quakeml' || $query->format === 'xml') {
			$callback->feed = new QuakemlFeed();
		} else if ($query->format === 'csv') {
			$callback->feed = new CSVFeed();
		} else if ($query->format === 'geojson') {
			$callback->feed = new GeoJSONFeed($query->callback !== null, $query->callback);
		} else if ($query->format === 'kml' || $query->format === 'kmlraw') {
			$callback->feed = new KMLFeed("depth", true);
		} else if ($query->format === 'atom') {
			$callback->feed = new AtomFeed();
		} else if ($query->format === 'text') {
			$callback->feed = new TextFeed();
		}

		// execute query and stream output
		$this->index->getEvents($query, $callback);
	}

	public function handleDetailQuery($query,
			$resultType = ProductIndexQuery::RESULT_TYPE_CURRENT) {
		global $APP_DIR;
		global $index;

		// use ProductIndex for detail
		$event = $index->getEventFromEventId($query->eventid, $resultType);

		if ($event === null) {
			if ($query->format === 'quakeml') {
				$this->error($query->nodata, self::$statusMessage[$query->nodata],
						true);
			} else {
				$this->error(self::NOT_FOUND, self::$statusMessage[self::NOT_FOUND],
						true);
			}
		}

		$eventid = $query->eventid;

		// send caching headers
		// default to 15 minutes
		$CACHE_MAXAGE = 900;
		$eventSeconds = substr($event->getTime(), 0, -3);
		if ((time() - $eventSeconds) < 7*24*60*60) {
			// if within past 7 days, only cache for 1 minute
			$CACHE_MAXAGE = 60;
		}
		include_once $APP_DIR . '/lib/cache.inc.php';

		$format = $query->format;
		if ($format == 'geojson' && $query->callback !== null) {
			$format = 'geojsonp';
			$callback = $query->callback;
		}

		// output detail format
		$detail_file = $APP_DIR . '/lib/classes/detail/' . $format . '.inc.php';
		include $detail_file;
		exit();
	}


	public function catalogs() {
		$catalogs = $this->index->getCatalogs();

		header('Content-type: application/xml');
		echo '<?xml version="1.0"?>' . "\n";
		echo '<Catalogs>';
		foreach ($catalogs as $catalog) {
			echo '<Catalog>' . $catalog . '</Catalog>';
		}
		echo '</Catalogs>';
		exit();
	}

	public function contributors() {
		$contributors = $this->index->getContributors();

		header('Content-type: application/xml');
		echo '<?xml version="1.0"?>' . "\n";
		echo '<Contributors>';
		foreach ($contributors as $contributor) {
			echo '<Contributor>' . $contributor . '</Contributor>';
		}
		echo '</Contributors>';
		exit();
	}

	public function application_json () {
		$information = array(
			'catalogs' => $this->index->getCatalogs(),
			'contributors' => $this->index->getContributors(),
			'producttypes' => $this->index->getProductTypes(),
			'eventtypes' => $this->index->getEventTypes(),
			'magnitudetypes' => $this->index->getMagnitudeTypes()
		);

		if (isset($_GET['callback'])) {
			header('Content-type: text/javascript');
			echo $_GET['callback'] . '(';
		} else {
			header('Content-type: application/json');
		}

		echo str_replace('\/', '/', json_encode($information));

		if (isset($_GET['callback'])) {
			echo ');';
		}

		exit();
	}

	// count is returned as xml [default], geojson, or geojsonp
	public function count() {

		$query = $this->parseQuery();
		$count = $this->index->getEventCount($query);
		$limit = intval($this->serviceLimit);
		$error = null;


		if ($count > $limit) {
			$error = $count . ' matching events exceeds search limit of ' .
			$limit . '. A query using these search parameters would fail. ' .
			'Modify the search to match fewer events.';
		}

		if ($query->format === 'geojson') {
			$header = '';
			$footer = '';
			$array = array(
					'count' => $count,
					'maxAllowed' => $limit
				);

			if ($error != null) {
				$array = array_merge($array, array('error' => $error));
			}

			$json = json_encode($array);

			if ($query->callback) {
				header('Content-type: text/javascript');
				$header = $query->callback . '(';
				$footer .= ');';
			} else {
				header('Content-type: application/json');
			}

			echo $header . $json . $footer;

		} else if ($query->format === 'xml') {
			// default format is xml
			header('Content-type: application/xml');
			echo '<query>';
			echo '<count>' . $count. '</count>';
			echo '<maxAllowed>' . $limit . '</maxAllowed>';
			if ($error) {
				echo '<error>' . $error . '</error>';
			}
			echo '</query>';
		} else {
			// default format is text
			header('Content-type: text/plain');
			echo $count;
		}

		exit(0);
	}

	public function version() {
		header('Content-type: text/plain');
		echo $this->version;
		exit();
	}

	public function wadl() {
		global $APP_DIR;
		// stored in external static file
		$wadl = file_get_contents($APP_DIR . '/lib/application.wadl');
		// inject base url
		$wadl = str_replace('BASEURL', htmlentities(AbstractFeed::getServiceUrl()),
				$wadl);

		header('Content-type: application/xml');
		echo $wadl;
		exit();
	}

	public function error($code, $message, $isDetail = false) {
		if ($this->redirect !== false && $isDetail &&
				($code === self::NO_DATA || $code === self::NOT_FOUND)) {

			$redirect = $this->redirect . '/query?' . $_SERVER['QUERY_STRING'];

			header('HTTP/1.0 302 Found');
			header('Location: ' . $redirect);
			exit();
		}

		if (isset($_GET['jsonerror']) && $_GET['jsonerror'] == 'true' &&
				isset($_GET['format']) && $_GET['format'] == 'geojson') {
			// For geojson requests, user wants 'jsonerror' output
			$this->jsonError($code, $message, $isDetail);
		} else {
			$this->httpError($code, $message);
		}
	}

	public function jsonError ($code, $message, $isDetail = false) {
		global $HOST_URL_PREFIX;
		$callback = false;
		if (isset($_GET['callback'])) {
			$callback = $_GET['callback'];
			header('Content-type: text/javascript');
		} else {
			header('Content-type: application/json');
		}

		// Does this need to look fully like GeoJSON format?
		$response = array(
			'type' => $isDetail ? 'Feature' : 'FeatureCollection',
			'metadata' => array(
				'status' => $code,
				'generated' => time() . '000',
				'url' => $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'],
				'title' => 'Search Error',
				'api' => $this->version,
				'count' => 0,
				'error' => $message
			)
		);

		if ($isDetail) {
			$response['properties'] = null;
		} else {
			$response['features'] = array();
		}

		if ($callback) {
			echo $callback . '(';
		}
		echo preg_replace('/"(generated)":"([\d]+)"/', '"$1":$2',
				str_replace('\/', '/', json_encode($response)));

		if ($callback) {
			echo ');';
		}

		exit();
	}

	public function httpError ($code, $message) {

		if (isset(self::$statusMessage[$code])) {
			$codeMessage = ' ' . self::$statusMessage[$code];
		} else {
			$codeMessage = '';
		}

		header('HTTP/1.0 ' . $code . $codeMessage);
		if ($code < 400) {
			exit();
		}

		global $HOST_URL_PREFIX;
		global $FDSN_PATH;

		// error message for 400 or 500
		header('Content-type: text/plain');
		echo implode("\n", array(
			'Error ' . $code . ': ' . self::$statusMessage[$code],
			'',
			$message,
			'',
			'Usage details are available from ' . $HOST_URL_PREFIX . $FDSN_PATH,
			'',
			'Request:',
			$_SERVER['REQUEST_URI'],
			'',
			'Request Submitted:',
			gmdate('c'),
			'',
			'Service version:',
			$this->version
		));
		exit();
	}

// URL PARAMETER PARSING

	/**
	 * Parse arguments to the query method.
	 *
	 * All arguments are read from $_GET and validated according to the FDSN spec.
	 *
	 * @return FDSNQuery object with parsed and validated parameters.
	 */
	public function parseQuery() {
		$query = new FDSNQuery();


		// parse and validate individual parameters
		$params = $_GET;
		foreach ($params as $name => $value) {
			if ($value === '') {
				// check for empty values in non-javascript
				continue;
			}
			if ($name === 'method') {
				// used by apache rewrites
				continue;
			} else if ($value == '') {
				// Allow empty values so non-js form works
				continue;
			} else if ($name == 'starttime' || $name == 'start') {
				$query->starttime = $this->validateTime($name, $value);
			} else if ($name ==='endtime' || $name ==='end') {
				$query->endtime = $this->validateTime($name, $value);
			} else if ($name ==='minlatitude' || $name ==='minlat') {
				$query->minlatitude = $this->validateFloat($name, $value, -90, 90);
			} else if ($name ==='maxlatitude' || $name ==='maxlat') {
				$query->maxlatitude = $this->validateFloat($name, $value, -90, 90);
			} else if ($name ==='minlongitude' || $name ==='minlon') {
				$query->minlongitude = $this->validateFloat($name, $value, -360, 360);
			} else if ($name ==='maxlongitude' || $name ==='maxlon') {
				$query->maxlongitude = $this->validateFloat($name, $value, -360, 360);
			} else if ($name ==='latitude' || $name ==='lat') {
				$query->latitude = $this->validateFloat($name, $value, -90, 90);
			} else if ($name ==='longitude' || $name ==='lon') {
				$query->longitude = $this->validateFloat($name, $value, -180, 180);
			} else if ($name ==='minradius') {
				$query->minradius = $this->validateFloat($name, $value, 0, 180);
			} else if ($name ==='maxradius') {
				$query->maxradius = $this->validateFloat($name, $value, 0, 180);
			} else if ($name==='minradiuskm') {
				$query->minradiuskm = $this->validateFloat($name, $value, 0, 20001.6);
			} else if ($name==='maxradiuskm') {
				$query->maxradiuskm = $this->validateFloat($name, $value, 0, 20001.6);
			} else if ($name ==='mindepth') {
				$query->mindepth = $this->validateFloat($name, $value, null, null);
			} else if ($name ==='maxdepth') {
				$query->maxdepth = $this->validateFloat($name, $value, null, null);
			} else if ($name ==='minmagnitude' || $name ==='minmag') {
				$query->minmagnitude = $this->validateFloat($name, $value, null, null);
			} else if ($name ==='maxmagnitude' || $name ==='maxmag') {
				$query->maxmagnitude = $this->validateFloat($name, $value, null, null);
			} else if ($name ==='magnitudetype' || $name ==='magtype') {
				$query->magnitudetype = $value;
			} else if ($name ==='includeallorigins') {
				$query->includeallorigins = $this->validateBoolean($name, $value);
			} else if ($name ==='includeallmagnitudes') {
				$query->includeallmagnitudes =  $this->validateBoolean($name, $value);
			} else if ($name ==='includearrivals') {
				$query->includearrivals =  $this->validateBoolean($name, $value);
				if ($query->includearrivals) {
					$this->error(self::NOT_IMPLEMENTED, 'includearrivals parameter is not supported');
				}
			} else if ($name ==='eventid') {
				$query->eventid = $value;
			} else if ($name ==='limit') {
				$query->limit = $this->validateInteger($name, $value, 0, $this->serviceLimit);
			} else if ($name ==='offset') {
				$query->offset = $this->validateInteger($name, $value, 1, null);
			} else if ($name ==='orderby') {
				$query->orderby = $this->validateEnumerated($name, $value, array('time', 'time-asc', 'magnitude', 'magnitude-asc'));
			} else if ($name ==='catalog') {
				$query->catalog = $this->validateEnumerated($name, $value, $this->index->getCatalogs());
			} else if ($name ==='contributor') {
				$query->contributor = $this->validateEnumerated($name, $value, $this->index->getContributors());
			} else if ($name ==='updatedafter') {
				$query->updatedafter = $this->validateTime($name, $value);
			} else if ($name ==='format') {
				$query->format = $this->validateEnumerated($name, $value, array('quakeml','geojson','csv','kml', 'kmlraw', 'xml', 'text'));
			} else if ($name ==='callback') {
				$query->callback = $value;
			} else if ($name ==='eventtype') {
				$query->eventtype = explode(",", $value); // todo: enumerate
			} else if ($name ==='reviewstatus') {
				$query->reviewstatus = $this->validateEnumerated($name, $value, array('automatic', 'reviewed'));
			} else if ($name ==='minmmi') {
				$query->minmmi = $this->validateFloat($name, $value, 0, 12);
			} else if ($name ==='maxmmi') {
				$query->maxmmi = $this->validateFloat($name, $value, 0, 12);
			} else if ($name ==='mincdi') {
				$query->mincdi = $this->validateFloat($name, $value, 0, 12);
			} else if ($name ==='maxcdi') {
				$query->maxcdi = $this->validateFloat($name, $value, 0, 12);
			} else if ($name ==='minfelt') {
				$query->minfelt = $this->validateInteger($name, $value, 0, null);
			} else if ($name ==='alertlevel') {
				$query->alertlevel = $this->validateEnumerated($name, $value, array('green', 'yellow', 'orange', 'red'));
			} else if ($name ==='mingap') {
				$query->mingap = $this->validateFloat($name, $value, 0, 360);
			} else if ($name ==='maxgap') {
				$query->maxgap = $this->validateFloat($name, $value, 0, 360);
			} else if ($name ==='minsig') {
				$query->minsig = $this->validateInteger($name, $value, 0, null);
			} else if ($name ==='maxsig') {
				$query->maxsig = $this->validateInteger($name, $value, 0, null);
			} else if ($name ==='producttype') {
				$query->producttype = $value;
			} else if ($name ==='kmlcolorby') {
				$query->kmlcolorby = $this->validateEnumerated($name, $value, array('age', 'depth'));
			} else if ($name ==='kmlanimated') {
				$query->kmlanimated = $this->validateBoolean($name, $value);
			} else if ($name ==='nodata') {
				$query->nodata = $this->validateEnumerated($name, $value, array(204, 404));
			} else if ($name ==='jsonerror') {
				// Used by this->error method. Just ignore for now
				continue;
			} else if ($name ==='includedelete') {
				// This is used by parseResultType method, don't use it here, but don't
				// throw an exception either.
				continue;
			} else {
				$this->error(self::BAD_REQUEST,
						'Unknown parameter "' . $name . '".');
			}
		}


		// validate parameter combinations

		// map {min,max}radiuskm --> {min,max}radius respectively, but only if
		// their counterpart is not explicitely set. Do this _BEFORE_ validating
		// general radial search parameters.

		if ($query->minradiuskm !== null) {
			if ($query->minradius !== null && $query->minradius !== 0) {
				// can't specify both flavors of minradius
				$this->error(self::BAD_REQUEST, 'Invalid area-circle parameter ' .
						"combination.\nminradius and minradiuskm can not both be " .
						'specified.');
			} else {
				// map minradiuskm --> minradius
				$query->minradius = $this->kmToDeg($query->minradiuskm);
			}
		}

		if ($query->maxradiuskm !== null) {
			if ($query->maxradius !== null) {
				// can't specify both flavors of maxradius
				$this->error(self::BAD_REQUEST, 'Invalid area-circle parameter ' .
						"combination.\nmaxradius and maxradiuskm can not both be " .
						'specified.');
			} else {
				// map maxradius to maxradiuskm
				$query->maxradius = $this->kmToDeg($query->maxradiuskm);
			}
		}

		// ensure radial search is complete
		if (
			// any radius parameter set
			($query->latitude !== null || $query->longitude !== null ||
					$query->maxradius !== null) &&
			// and any radius parameter left blank
			($query->latitude === null || $query->longitude === null ||
					$query->maxradius === null)
		) {
			$this->error(self::BAD_REQUEST, 'Invalid area-circle parameter ' .
					"combination.\nlatitude, longitude, and maxradius must all be " .
					'specified for area-circle.');
		}

		// rectangle search makes sense
		// min less than max latitude
		if (
			$query->minlatitude !== null && $query->maxlatitude !== null
			&& $query->minlatitude > $query->maxlatitude
		) {
			$this->error(self::BAD_REQUEST, 'minlatitude must be less than maxlatitude');
		}
		// min less than max longitude
		if (
			$query->minlongitude !== null && $query->maxlongitude !== null
			&& $query->minlongitude > $query->maxlongitude
		) {
			$this->error(self::BAD_REQUEST, 'minlongitude must be less than maxlongitude');
		}

		// searches that cross date line should have both min and max longitude
		if (
			($query->minlongitude !== null && $query->minlongitude < -180 && $query->maxlongitude === null)
			|| ($query->maxlongitude !== null && $query->maxlongitude > 180 && $query->minlongitude === null)
		) {
			$this->error(self::BAD_REQUEST, 'Searches that cross dateline require both minlongitude and maxlongitude.');
		}

		// searches should be 360 degrees of longitude or less
		if ($query->minlongitude !== null && $query->maxlongitude !== null) {
			$span = $query->maxlongitude - $query->minlongitude;
			if ($span > 360) {
				$this->error(self::BAD_REQUEST, 'Searches cannot span more than 360 degrees of longitude.');
			} else if ($span === 360) {
				// when span=360, all longitudes will match
				// clear values to simplify resulting sql
				$query->minlongitude = null;
				$query->maxlongitude = null;
			}
		}

		// only quakeml supports allorigins/magnitudes
		if (
			($query->format !== 'quakeml' && $query->format !== 'xml')
			&& ($query->includeallorigins || $query->includeallmagnitudes)
		) {
			$this->error(self::BAD_REQUEST, 'Cannot use includeallorigins or includeallmagnitudes' .
				' parameters when format is not quakeml or xml.');
		}

		// only geojson supports callback
		if (
			$query->format !== 'geojson'
			&& $query->callback !== null
		) {
			$this->error(self::BAD_REQUEST, 'Cannot use callback parameter when format is not geojson.');
		}


		// set default starttime when not specified
		if ($query->starttime === null) {
			global $DEFAULT_MAXEVENTAGE;
			if ($DEFAULT_MAXEVENTAGE !== null) {
				$query->starttime = (time() - $DEFAULT_MAXEVENTAGE) . '000';
			}
		}


		return $query;
	}

	public function parseResultType () {
		// By default, assume user wants only current products
		$resultType = ProductIndexQuery::RESULT_TYPE_CURRENT;

		if (isset($_GET['includedelete']) && $_GET['includedelete'] === 'true') {
			$resultType = ProductIndexQuery::RESULT_TYPE_CURRENT_WITH_DELETE;
		}

		return $resultType;
	}

	/**
	 * Validate a time parameter.
	 *
	 * @param $param parameter name, for error message.
	 * @param $value parameter value
	 * @return value as epoch millisecond timestamp, exit with error if invalid.
	 */
	protected function validateTime($param, $value) {
		$parsed = strtotime($value);
		if ($parsed === false) {
			$this->error(self::BAD_REQUEST,
				'Bad ' . $param . ' value "' . $value . '".' .
				' Valid values are ISO-8601 timestamps.');
		}
		return $parsed . '000';
	}

	/**
	 * Validate a boolean parameter.
	 *
	 * @param $param parameter name, for error message
	 * @param $value parameter value
	 * @return value as boolean if valid ("true" or "false", case insensitively), exit with error if invalid.
	 */
	protected function validateBoolean($param, $value) {
		$val = strtolower($value);
		if ($val !== 'true' && $val !== 'false') {
			$this->error(self::BAD_REQUEST,
					'Bad ' . $param . ' value "' . $value . '".' .
					' Valid values are (case insensitive): "TRUE", "FALSE".');
		}
		return ($val === 'true');
	}

	/**
	 * Validate an integer parameter.
	 *
	 * @param $param parameter name, for error message
	 * @param $value parameter value
	 * @param $min minimum valid value for parameter, or null if no minimum.
	 * @param $max maximum valid value for parameter, or null if no maximum.
	 * @return value as integer if valid (integer and in range), exit with error if invalid.
	 */
	protected function validateInteger($param, $value, $min, $max) {
		if (
				!ctype_digit($value)
				|| ($min !== null && intval($value) < $min)
				|| ($max !== null && intval($value) > $max)
		) {
			$message = '';
			if ($min === null && $max === null) {
				$message = 'integers';
			} else {
				$message = '';
				if ($min !== null) {
					$message .= $min . ' <= ';
				}
				$message .= $param;
				if ($max !== null) {
					$message .= ' <= ' . $max;
				}
			}
			$this->error(self::BAD_REQUEST, 'Bad ' . $param . ' value "' . $value . '".' .
					' Valid values are ' . $message);
		}
		return intval($value);
	}

	/**
	 * Validate a float parameter.
	 *
	 * @param $param parameter name, for error message
	 * @param $value parameter value
	 * @param $min minimum valid value for parameter, or null if no minimum.
	 * @param $max maximum valid value for parameter, or null if no maximum.
	 * @return value as float if valid (float and in range), exit with error if invalid.
	 */
	protected function validateFloat($param, $value, $min, $max) {
		if (
				!is_numeric($value)
				|| ($min !== null && floatval($value) < $min)
				|| ($max !== null && floatval($value) > $max)
		) {
			if ($min === null && $max === null) {
				$message = 'numeric';
			} else {
				$message = '';
				if ($min !== null) {
					$message .= $min . ' <= ';
				}
				$message .= $param;
				if ($max !== null) {
					$message .= ' <= ' . $max;
				}
			}

			$this->error(self::BAD_REQUEST, 'Bad ' . $param . ' value "' . $value . '".' .
					' Valid values are ' . $mesasge);
		}
		return floatval($value);
	}

	/**
	 * Validate a parameter that has an enumerated list of valid values.
	 *
	 * @param $param parameter name, for error message
	 * @param $value parameter value
	 * @param $enum array of valid parameter values.
	 * @return value if valid (in array), exit with error if invalid.
	 */
	protected function validateEnumerated($param, $value, $enum) {
		if (!in_array($value, $enum)) {
			$this->error(self::BAD_REQUEST, 'Bad ' . $param . ' value "' . $value . '".' .
				' Valid values are: "' . implode('", "', $enum) . '".');
		}
		return $value;
	}

	/**
	 * Converts an input kilometer distance to a degrees of arc distance.
	 *
	 */
	protected function kmToDeg ($km) {
		return $km / 111.12;   // What Paul thinks
		//return $km / 111.2;  // What NASA/math thinks 111.19492664455873...
	}


}
