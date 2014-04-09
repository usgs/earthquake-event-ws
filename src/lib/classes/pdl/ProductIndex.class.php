<?php
/**
 * This is a loose port of Product Distribution's JDBCProductIndex. Since this runs on the application
 * servers which have read-only databases, this is a read-only search. None of the methods that
 * change the database are ported.
 *
 * Typically to use this class, you need to build a ProductIndexQuery using user input from a form. Then
 * use the getEventSummaries() method to search the index using that query. This will return a list of
 * EventSummaries.
 *
 * You can get more detailed information about an event - its products, product properties, product links -
 * by using the getEvent() method. This will return an Event, which has all the detailed information
 * attached.
 *
 * @see The Java version of the ProductIndex,  gov.usgs.earthquake.indexer.JDBCProductIndex
 * @see ProductIndexQuery
 * @see EventSummary
 * @see Event
 *
 * @author sdaugherty
 */
class ProductIndex {

	public $connection = null;

	/** Variables for prepared statements */
	private $getSummaries;
	private $getSummaryStmt;
	private $getProductProperties;
	private $getProductLinks;
	private $mountPath;
	public $linkPath;

	/** Constants to store the event and product column names */
	const EVENT_TABLE = "event";
	const EVENT_INDEX_ID = "id";
	const EVENT_CREATED = "created";
	const EVENT_SOURCE = "source";
	const EVENT_SOURCE_CODE = "sourceCode";
	const EVENT_TIME = "eventTime";
	const EVENT_LATITUDE = "latitude";
	const EVENT_LONGITUDE = "longitude";
	const EVENT_DEPTH = "depth";
	const EVENT_MAGNITUDE = "magnitude";
	const EVENT_STATUS = "status";
	const EVENT_SUMMARY_LASTMODIFIED = 'lastmodified';
	const EVENT_SUMMARY_SIGNIFICANCE = 'significance';
	const EVENT_SUMMARY_TABLE = "eventSummary";
	const EVENT_SUMMARY_EVENT_ID = "eventid";
	const EVENT_SUMMARY_MAXMMI = "maxmmi";
	const EVENT_SUMMARY_ALERT_LEVEL = "alertlevel";
	const EVENT_SUMMARY_MAXCDI = "maxcdi";
	const EVENT_SUMMARY_NUM_RESPONSES = "num_responses";
	const EVENT_SUMMARY_REVIEW_STATUS = "review_status";
	const EVENT_SUMMARY_EVENT_TYPE = "event_type";
	const EVENT_SUMMARY_AZ_GAP = "azimuthal_gap";
	const EVENT_SUMMARY_MAG_TYPE = "magnitude_type";
	const EVENT_SUMMARY_REGION = "region";
	const EVENT_SUMMARY_TYPES = "types";
	const EVENT_SUMMARY_EVENTIDS = "eventids";
	const EVENT_SUMMARY_TSUNAMI = "tsunami";
	const EVENT_SUMMARY_OFFSET = "offset";
	const EVENT_SUMMARY_EVENT_SOURCES = "eventsources";
	const EVENT_SUMMARY_PRODUCT_SOURCES = "productsources";
	const EVENT_SUMMARY_NUM_STATIONS_USED = "num_stations_used";
	const EVENT_SUMMARY_MINIMUM_DISTANCE = "minimum_distance";
	const EVENT_SUMMARY_STANDARD_ERROR = "standard_error";
	const SUMMARY_TABLE = "productSummary";
	const SUMMARY_CREATED = "created";
	const SUMMARY_PRODUCT_INDEX_ID = "id";
	const SUMMARY_PRODUCT_ID = "productId";
	const SUMMARY_EVENT_ID = "eventId";
	const SUMMARY_TYPE = "type";
	const SUMMARY_SOURCE = "source";
	const SUMMARY_CODE = "code";
	const SUMMARY_UPDATE_TIME = "updateTime";
	const SUMMARY_EVENT_SOURCE = "eventSource";
	const SUMMARY_EVENT_SOURCE_CODE = "eventSourceCode";
	const SUMMARY_EVENT_TIME = "eventTime";
	const SUMMARY_EVENT_LATITUDE = "eventLatitude";
	const SUMMARY_EVENT_LONGITUDE = "eventLongitude";
	const SUMMARY_EVENT_DEPTH = "eventDepth";
	const SUMMARY_EVENT_MAGNITUDE = "eventMagnitude";
	const SUMMARY_VERSION = "version";
	const SUMMARY_STATUS = "status";
	const SUMMARY_TRACKER_URL = "trackerURL";
	const SUMMARY_PREFERRED = "preferred";
	const SUMMARY_PROPERTY_TABLE = "productSummaryProperty";
	const SUMMARY_PROPERTY_ID = "productSummaryIndexId";
	const SUMMARY_PROPERTY_NAME = "name";
	const SUMMARY_PROPERTY_VALUE = "value";
	const SUMMARY_LINK_TABLE = "productSummaryLink";
	const SUMMARY_LINK_ID = "productSummaryIndexId";
	const SUMMARY_LINK_RELATION = "relation";
	const SUMMARY_LINK_URL = "url";

	/** Properties to store the query text for prepared queries */
	/** Since php doesn't allow constants (or static properties) to be the result of an expression,
	  *  we've got to make these regular properties instead. */
	protected $GET_SUMMARIES_BY_EVENT_INDEX_ID;
	protected $GET_SUMMARY_BY_PRODUCT_INDEX_ID;
	protected $GET_lINKS_BY_PRODUCT_INDEX_ID;
	protected $GET_PROPS_BY_PRODUCT_INDEX_ID;

	public function __construct( $mountPath = '') {
		$this->mountPath = $mountPath;
		$this->setLinkPath($mountPath . "/%s"); //"/index.php?id=%s";

		$this->GET_SUMMARIES_BY_EVENT_INDEX_ID = sprintf("SELECT product.%s FROM %s product WHERE product.%s = ? ",
			self::SUMMARY_PRODUCT_INDEX_ID, self::SUMMARY_TABLE, self::SUMMARY_EVENT_ID);
		$this->GET_SUMMARY_BY_PRODUCT_INDEX_ID = sprintf("SELECT
			%s, %s, %s, %s, %s, %s, %s, %s, %s, %s,
			%s, %s, %s, %s, %s, %s FROM %s WHERE %s = ?",
			self::SUMMARY_PRODUCT_ID, self::SUMMARY_TYPE, self::SUMMARY_SOURCE,
			self::SUMMARY_CODE, self::SUMMARY_UPDATE_TIME, self::SUMMARY_EVENT_SOURCE,
			self::SUMMARY_EVENT_SOURCE_CODE, self::SUMMARY_EVENT_TIME,
			self::SUMMARY_EVENT_LATITUDE, self::SUMMARY_EVENT_LONGITUDE,
			self::SUMMARY_EVENT_DEPTH, self::SUMMARY_EVENT_MAGNITUDE,
			self::SUMMARY_VERSION, self::SUMMARY_STATUS, self::SUMMARY_TRACKER_URL,
			self::SUMMARY_PREFERRED, self::SUMMARY_TABLE, self::SUMMARY_PRODUCT_INDEX_ID);

				/** Query to get all the links for a product */
		$this->GET_LINKS_BY_PRODUCT_INDEX_ID = sprintf("SELECT
				%s, %s FROM %s WHERE %s = ?", self::SUMMARY_LINK_RELATION,
				self::SUMMARY_LINK_URL, self::SUMMARY_LINK_TABLE, self::SUMMARY_LINK_ID);

		/** Query to get all the properties for a product */
		$this->GET_PROPS_BY_PRODUCT_INDEX_ID = sprintf("SELECT
				%s, %s FROM %s WHERE %s = ?", self::SUMMARY_PROPERTY_NAME,
				self::SUMMARY_PROPERTY_VALUE, self::SUMMARY_PROPERTY_TABLE, self::SUMMARY_PROPERTY_ID);

	}

	public function getLinkPath() { return $this->linkPath; }
	public function setLinkPath($linkPath) {
		$this->linkPath = $linkPath;
	}

	/**
	 * Connect to the database using a PDO object and create some prepared statements.
	 */
	public function connect( $hostname, $user, $pass, $database, $driver = "mysql" ) {
		$dsn = sprintf("%s:host=%s;dbname=%s", $driver, $hostname, $database);
		try {
			$this->connection = new PDO($dsn, $user, $pass);
		} catch (PDOException $e) {
			// Couldn't connect to database
			print "Problem connecting to the database";
		}

		try {
			$this->getSummaryStmt = $this->connection->prepare($this->GET_SUMMARY_BY_PRODUCT_INDEX_ID);
			$this->getProductProperties = $this->connection->prepare($this->GET_PROPS_BY_PRODUCT_INDEX_ID);
			$this->getProductLinks = $this->connection->prepare($this->GET_LINKS_BY_PRODUCT_INDEX_ID);
		} catch (PDOException $e) {
			print "Problem creating prepared statements";
		}
	}

	/**
	 * Gets the count of an sql query
	 * @param sql
	 */
	private function getQueryCount($sql) {
		$sql = preg_replace("/SELECT(.*?)FROM/s", "SELECT COUNT(*) FROM", $sql);
		$sql = $this->connection->prepare($sql);
		$sql->execute();
		return $sql->fetchColumn();
	}

	/**
	 * Query the database looking for all events summaries that match the parameters specified
	 * in the ProductIndexQuery. This doesn't return any products or product properties.
	 * @param productIndexQuery
	 */
	public function getEventSummaries( $productIndexQuery, $resultsCallback=null ) {

		// If the productIndexQuery wanted to search on event properties, we're going to need
		// to take a different approach, so we'll jump to another method.

		$events = array();
		$sql = sprintf("
			SELECT
				e.%s,
				e.%s,
				e.%s,
				e.%s,
				e.%s,
				e.%s,
				e.%s,
				e.%s,
				e.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s
			FROM
				%s e
			INNER JOIN
				%s es
			ON
				e.%s = es.%s
			LEFT JOIN
				%s product ON e.%s = product.%s
			",
			self::EVENT_INDEX_ID,
			self::EVENT_SOURCE,
			self::EVENT_SOURCE_CODE,
			self::EVENT_TIME,
			self::EVENT_LATITUDE,
			self::EVENT_LONGITUDE,
			self::EVENT_DEPTH,
			self::EVENT_MAGNITUDE,
			self::EVENT_STATUS,
			self::EVENT_SUMMARY_LASTMODIFIED,
			self::EVENT_SUMMARY_SIGNIFICANCE,
			self::EVENT_SUMMARY_MAXMMI,
			self::EVENT_SUMMARY_ALERT_LEVEL,
			self::EVENT_SUMMARY_MAXCDI,
			self::EVENT_SUMMARY_NUM_RESPONSES,
			self::EVENT_SUMMARY_REVIEW_STATUS,
			self::EVENT_SUMMARY_EVENT_TYPE,
			self::EVENT_SUMMARY_AZ_GAP,
			self::EVENT_SUMMARY_MAG_TYPE,
			self::EVENT_SUMMARY_REGION,
			self::EVENT_SUMMARY_TYPES,
			self::EVENT_SUMMARY_EVENTIDS,
			self::EVENT_SUMMARY_EVENT_SOURCES,
			self::EVENT_SUMMARY_TSUNAMI,
			self::EVENT_SUMMARY_OFFSET,
			self::EVENT_SUMMARY_NUM_STATIONS_USED,
			self::EVENT_SUMMARY_MINIMUM_DISTANCE,
			self::EVENT_SUMMARY_STANDARD_ERROR,
			self::EVENT_TABLE,
			self::EVENT_SUMMARY_TABLE,
			self::EVENT_INDEX_ID,
			self::EVENT_SUMMARY_EVENT_ID,
			self::SUMMARY_TABLE,
			self::EVENT_INDEX_ID,
			self::SUMMARY_EVENT_ID
			);

		$clauses = $this->buildEventClauses($productIndexQuery);
		// We'll do 2 different queries depending on if the search includes
		// product attributes/properties. By default, the event table is joined
		// with the productSummary table.
		$found = false;
		foreach( $clauses as $clause ) {
			if( strpos( $clause, "product." ) !== false ) {
				$found = true;
				break;
			}
		}

		if( !$found ) {
			// No clause references the productSummary table, so we dont need a join
			$sql = sprintf("
				SELECT
					e.%s,
					e.%s,
					e.%s,
					e.%s,
					e.%s,
					e.%s,
					e.%s,
					e.%s,
					e.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s,
					es.%s
				FROM
					%s e
				INNER JOIN
					%s es
				ON
					e.%s = es.%s
				",
				self::EVENT_INDEX_ID,
				self::EVENT_SOURCE,
				self::EVENT_SOURCE_CODE,
				self::EVENT_TIME,
				self::EVENT_LATITUDE,
				self::EVENT_LONGITUDE,
				self::EVENT_DEPTH,
				self::EVENT_MAGNITUDE,
				self::EVENT_STATUS,
				self::EVENT_SUMMARY_LASTMODIFIED,
				self::EVENT_SUMMARY_SIGNIFICANCE,
				self::EVENT_SUMMARY_MAXMMI,
				self::EVENT_SUMMARY_ALERT_LEVEL,
				self::EVENT_SUMMARY_MAXCDI,
				self::EVENT_SUMMARY_NUM_RESPONSES,
				self::EVENT_SUMMARY_REVIEW_STATUS,
				self::EVENT_SUMMARY_EVENT_TYPE,
				self::EVENT_SUMMARY_AZ_GAP,
				self::EVENT_SUMMARY_MAG_TYPE,
				self::EVENT_SUMMARY_REGION,
				self::EVENT_SUMMARY_TYPES,
				self::EVENT_SUMMARY_EVENTIDS,
				self::EVENT_SUMMARY_EVENT_SOURCES,
				self::EVENT_SUMMARY_TSUNAMI,
				self::EVENT_SUMMARY_OFFSET,
				self::EVENT_SUMMARY_NUM_STATIONS_USED,
				self::EVENT_SUMMARY_MINIMUM_DISTANCE,
				self::EVENT_SUMMARY_STANDARD_ERROR,
				self::EVENT_TABLE,
				self::EVENT_SUMMARY_TABLE,
				self::EVENT_INDEX_ID,
				self::EVENT_SUMMARY_EVENT_ID
			);
		}
		else {
			// Some clause references the productSummary table, so we need to make sure
			// the products we're searching for match the result type (CURRENT, SUPERSCEDED, etc).
			$resultTypeClause = $this->buildResultTypeClause($productIndexQuery->getResultType());

			if ($resultTypeClause !== false) {
				$clauses[] = $resultTypeClause;
			}

		}


		$first = true;
		foreach( $clauses as $clause ) {
			if ($first == true) {
				$sql .= " WHERE ";
				$first = false;
			}
			else {
				$sql .= " AND ";
			}
			$sql .= $clause;
		}

		$havings = $this->buildHavingClauses($productIndexQuery);
		$first = true;
		foreach ($havings as $having) {
			if ($first == true) {
				$sql .= ' HAVING ';
				$first = false;
			} else {
				$sql .= ' AND ';
			}
			$sql .= $having;
		}

		$max = intval($productIndexQuery->getSearchMax());
		if ($max) {
			if ($this->getQueryCount($sql) > $max) {
				throw new Exception('Search returned too many results.');
			}
		}

		// We probably don't need to make this a prepared statement - we could just execute it -
		// but meh, for the sake of consistency it'll be prepared.
		$getSummariesStmt = $this->connection->prepare($sql);
		$getSummariesStmt->execute();
		if ($resultsCallback != null) {
			while (($row = $getSummariesStmt->fetch(PDO::FETCH_ASSOC)) != null) {
				$resultsCallback->onEventSummary($this->rsToEventSummary($row));
			}
		} else {
			$event_rs = $getSummariesStmt->fetchAll(PDO::FETCH_ASSOC);

			foreach( $event_rs as $eventRow ) {
				$event = $this->rsToEventSummary($eventRow);
				$events[$event->getEventIndexId()] = $event;
			}
			return $events;
		}
	}

	/**
	 * Query the database looking for all events summaries that match the parameters specified
	 * in the ProductIndexQuery. This doesn't return any products or product properties.
	 * @param productIndexQuery
	 */
	public function getEventSummariesBySource( $productIndexQuery, $resultsCallback=null ) {

		// If the productIndexQuery wanted to search on event properties, we're going to need
		// to take a different approach, so we'll jump to another method.

		$events = array();
		$sql = sprintf("
			SELECT
				product.%s as %s,
				product.%s as %s,
				product.%s as %s,
				product.%s as %s,
				product.%s as %s,
				product.%s as %s,
				product.%s as %s,
				product.%s as %s,
				product.%s as %s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s,
				es.%s
			FROM
				 %s as product
			LEFT OUTER JOIN %s as es ON (es.%s = product.%s)
			where product.%s = (
				select max(%s) as created
				from %s
				where %s= product.%s
				and %s= product.%s
				and %s= product.%s)
			and product.%s = 'origin'
			%s
			",

			self::SUMMARY_EVENT_ID, self::EVENT_INDEX_ID,
			self::SUMMARY_EVENT_SOURCE, self::EVENT_SOURCE,
			self::SUMMARY_EVENT_SOURCE_CODE, self::EVENT_SOURCE_CODE,
			self::SUMMARY_EVENT_TIME, self::EVENT_TIME,
			self::SUMMARY_EVENT_LATITUDE, self::EVENT_LATITUDE,
			self::SUMMARY_EVENT_LONGITUDE, self::EVENT_LONGITUDE,
			self::SUMMARY_EVENT_DEPTH, self::EVENT_DEPTH,
			self::SUMMARY_EVENT_MAGNITUDE, self::EVENT_MAGNITUDE,
			self::SUMMARY_STATUS, self::SUMMARY_STATUS,
			self::EVENT_SUMMARY_LASTMODIFIED,
			self::EVENT_SUMMARY_MAXMMI,
			self::EVENT_SUMMARY_ALERT_LEVEL,
			self::EVENT_SUMMARY_REVIEW_STATUS,
			self::EVENT_SUMMARY_EVENT_TYPE,
			self::EVENT_SUMMARY_AZ_GAP,
			self::EVENT_SUMMARY_MAG_TYPE,
			self::EVENT_SUMMARY_REGION,
			self::EVENT_SUMMARY_TYPES,
			self::EVENT_SUMMARY_EVENTIDS,
			self::EVENT_SUMMARY_EVENT_SOURCES,
			self::EVENT_SUMMARY_TSUNAMI,
			self::EVENT_SUMMARY_OFFSET,
			self::EVENT_SUMMARY_SIGNIFICANCE,
			self::EVENT_SUMMARY_MAXCDI,
			self::EVENT_SUMMARY_NUM_RESPONSES,
			self::EVENT_SUMMARY_NUM_STATIONS_USED,
			self::EVENT_SUMMARY_MINIMUM_DISTANCE,
			self::EVENT_SUMMARY_STANDARD_ERROR,
			self::SUMMARY_TABLE,
			self::EVENT_SUMMARY_TABLE,
			self::EVENT_SUMMARY_EVENT_ID,
			self::SUMMARY_EVENT_ID,
			self::SUMMARY_UPDATE_TIME,
			self::SUMMARY_UPDATE_TIME,
			self::SUMMARY_TABLE,
			self::SUMMARY_SOURCE,
			self::SUMMARY_SOURCE,
			self::SUMMARY_TYPE,
			self::SUMMARY_TYPE,
			self::SUMMARY_CODE,
			self::SUMMARY_CODE,
			self::SUMMARY_TYPE,
			'' //$searchMax
		);

		$clauses = $this->buildProductSummaryClauses($productIndexQuery);

		// Some clause references the productSummary table, so we need to make sure
		// the products we're searching for match the result type (CURRENT, SUPERSCEDED, etc).
		$resultTypeClause = $this->buildResultTypeClause( $productIndexQuery->getResultType() );

		if ($resultTypeClause !== false) {
			$clauses[] = $resultTypeClause;
		}


		$first = true;
		foreach( $clauses as $clause ) {
			$sql .= " AND " . $clause;
		}

		$max = intval($productIndexQuery->getSearchMax());
		if ($max) {
			if ($this->getQueryCount($sql) > $max) {
				throw new Exception('Search returned too many results.');
			}
		}

		// We probably don't need to make this a prepared statement - we could just execute it -
		// but meh, for the sake of consistency it'll be prepared.
		$getSummariesStmt = $this->connection->prepare($sql);
		$getSummariesStmt->execute();

		if ($resultsCallback != null) {
			while (($row = $getSummariesStmt->fetch(PDO::FETCH_ASSOC)) != null) {
				$resultsCallback->onEventSummary($this->rsToEventSummary($row));
			}
		} else {
			$event_rs = $getSummariesStmt->fetchAll(PDO::FETCH_ASSOC);

			foreach( $event_rs as $eventRow ) {
				$event = $this->rsToEventSummary($eventRow);
				$events[$event->getEventIndexId()] = $event;
			}

			return $events;
		}
	}

	protected function rsToEventSummary($eventRow) {

		$eventIndexId = $eventRow[self::EVENT_INDEX_ID];
		$eventId = $eventRow[self::EVENT_SOURCE] . $eventRow[self::EVENT_SOURCE_CODE];
		// Make a new event summary and add the attributes
		$event = new EventSummary();
		$event->setEventIndexId($eventIndexId);
		$event->setSource($eventRow[self::EVENT_SOURCE]);
		$event->setSourceCode($eventRow[self::EVENT_SOURCE_CODE]);
		$event->setTime($eventRow[self::EVENT_TIME]);
		$event->setLatitude($eventRow[self::EVENT_LATITUDE]);
		$event->setLongitude($eventRow[self::EVENT_LONGITUDE]);
		$event->setDepth($eventRow[self::EVENT_DEPTH]);
		$event->setMagnitude($eventRow[self::EVENT_MAGNITUDE]);
		$event->setMagnitudeType($eventRow[self::EVENT_SUMMARY_MAG_TYPE]);
		$event->setDetailLink( sprintf($this->linkPath, $eventId) );
		$event->setTsunami($eventRow[self::EVENT_SUMMARY_TSUNAMI]);
		$event->setOffset($eventRow[self::EVENT_SUMMARY_OFFSET]);
		$event->setSignificance($eventRow[self::EVENT_SUMMARY_SIGNIFICANCE]);
		$event->setStatus($eventRow[self::EVENT_STATUS]);
		$event->setEventType($eventRow[self::EVENT_SUMMARY_EVENT_TYPE]);
		$event->setAzimuthalGap($eventRow[self::EVENT_SUMMARY_AZ_GAP]);
		$event->setNumStationsUsed($eventRow[self::EVENT_SUMMARY_NUM_STATIONS_USED]);
		$event->setMinimumDistance($eventRow[self::EVENT_SUMMARY_MINIMUM_DISTANCE]);
		$event->setStandardError($eventRow[self::EVENT_SUMMARY_STANDARD_ERROR]);
		$event->setProperty( self::EVENT_SUMMARY_MAXMMI,
				$eventRow[self::EVENT_SUMMARY_MAXMMI]);
		$event->setProperty( self::EVENT_SUMMARY_ALERT_LEVEL,
				$eventRow[self::EVENT_SUMMARY_ALERT_LEVEL]);
		$event->setProperty( self::EVENT_SUMMARY_MAXCDI,
				$eventRow[self::EVENT_SUMMARY_MAXCDI]);
		$event->setProperty( self::EVENT_SUMMARY_NUM_RESPONSES,
				$eventRow[self::EVENT_SUMMARY_NUM_RESPONSES]);
		$event->setProperty( self::EVENT_SUMMARY_REVIEW_STATUS,
				$eventRow[self::EVENT_SUMMARY_REVIEW_STATUS]);
		$event->setProperty( self::EVENT_SUMMARY_AZ_GAP,
				$eventRow[self::EVENT_SUMMARY_AZ_GAP]);
		$event->setProperty( self::EVENT_SUMMARY_MAG_TYPE,
				$eventRow[self::EVENT_SUMMARY_MAG_TYPE]);
		$event->setProperty( self::EVENT_SUMMARY_REGION,
				$eventRow[self::EVENT_SUMMARY_REGION]);
		$event->setProperty( self::EVENT_SUMMARY_TYPES,
				$eventRow[self::EVENT_SUMMARY_TYPES]);
		$event->setProperty( self::EVENT_SUMMARY_EVENTIDS,
				$eventRow[self::EVENT_SUMMARY_EVENTIDS]);
		$event->setProperty( self::EVENT_SUMMARY_EVENT_SOURCES,
				$eventRow[self::EVENT_SUMMARY_EVENT_SOURCES]);
		$event->setLastModified($eventRow[self::EVENT_SUMMARY_LASTMODIFIED]);
		return $event;


	}
	/**
	 * Wrapper function for getEvent($indexId) that allows you to use $eventId.
	 * If this function detects that the request came for one of the non-preferred
	 * eventIds, it will issue a redirect to the details page for the preferred
	 * eventId.
	 */
	public function getEventFromEventId($eventId,
			$resultType = ProductIndexQuery::RESULT_TYPE_CURRENT ) {

		try {
			$sql = '
				SELECT
					getEventIdByFullEventId(' .
						$this->connection->quote($eventId) .  '
					) AS ' . self::EVENT_INDEX_ID . '
				;
			';

			return $this->_getEventFromSQL($sql, $eventId, $resultType);
		} catch (Exception $e_function) {
			trigger_error($e_function->getMessage());
		}

		// If we got here, we failed to find an event. Just return null.
		return null;
	}


	protected function _getEventFromSQL($sql, $eventId,
			$resultType = ProductIndexQuery::RESULT_TYPE_CURRENT) {

		$getIndexIdStmt = $this->connection->prepare($sql);
		$getIndexIdStmt->execute();
		$row = $getIndexIdStmt->fetch(PDO::FETCH_ASSOC);
		$getIndexIdStmt->closeCursor();

		if ($row != null) {
			$indexId = intval($row[self::EVENT_INDEX_ID]);
			return $this->getEvent($indexId, $resultType);
		} else {
			throw new Exception('Failed to get event for ' . $eventId .
					' using SQL: ' . $sql);
		}
	}

	/**
	 * Query the database to get the event with the given event index id
	 *
	 * @param eventIndexId
	 * @param resultType
	 * @return Event object
	 */
	public function getEvent( $indexId, $resultType = ProductIndexQuery::RESULT_TYPE_CURRENT ) {
		$event = new Event($indexId);
		// It should be fine to query each of the products individually
		// because we only have 1 event.

		$summaryIndexIds = $this->getSummaryIndexIds($indexId, $resultType);
		if (count($summaryIndexIds) == 0) {
			// event has no products, assume it does not exist
			return null;
		}

		foreach( $summaryIndexIds as $summaryIndexId ) {
			$event->addProduct( $this->getSummary($summaryIndexId) );
		}

		return $event;

	}

	protected function buildHavingClauses ($query) {
		$clauseList = array();

		if ($query == null) {
			return $clauseList;
		}

		$min_significance = $query->getMinSignificance();
		if ($min_significance !== null) {
			$clauseList[] = sprintf('%s >= %s', self::EVENT_SUMMARY_SIGNIFICANCE,
					$min_significance);
		}

		$max_significance = $query->getMaxSignificance();
		if ($max_significance !== null) {
			$clauseList[] = sprintf('%s >= %s', self::EVENT_SUMMARY_SIGNIFICANCE,
					$max_significance);
		}

		return $clauseList;
	}

	/**
	 * Build a list of all the pieces of the WHERE clause relevant to the
	 * event table. If the query doesn't set any properties, this
	 * method will return an empty list. It is up to the calling methods to
	 * check if the clause list is empty when they build their WHERE clause.
	 *
	 *
	 * @param query
	 * @return Array containing clauses in the form: column="value"
	 */
	protected function buildEventClauses( $query ) {
	 	/**
		* All of the values from $query are sanitized using PDO::quote. This is necessary
	 	* to prevent SQL injection attacks because we aren't using the PDO::bind functions.
		*/
		$clauseList = array();

		if ($query == null) {
			return $clauseList; /* No query = No clauses */
		}

		/**
		 * 08/16/12 -- EMM: It seems like product ids are never set and so we
		 * should not need this logic. If something breaks we can re-add this
		 * code.

		   Verification:
		      find EventApp -name '*.php' -exec grep -iH 'setProductIds' {} \;

		 *//*

		// If there are one or more productIds we should build this clause
		$productIds = $query->getProductIds();
		if (count($productIds) > 0 ) {
			// Begin an "IN" clause
			$clause = sprintf("%s IN ('%s", self::SUMMARY_PRODUCT_ID,
					$productIds[0]);

			// Loop over any remaining productIds and add them to clause
			array_shift($productIds);
			foreach($productIds as $pid ) {
				$clause += "', '";
				$clause += $pid;
			}

			// Finish off our clause and add it to our clauseList
			$clause += "')";
			$clauseList[] = $clause;
		}

		/* END: productIds logic **/

		// --------------------------------------------
		// Build clauses for columns in the event table
		// --------------------------------------------

		// Do magnitudes first (on event table) to optimize index performance.
		$minMag = $query->getMinEventMagnitude();
		if ($minMag !== null) {
			$clauseList[] = sprintf("e.%s>=%s", self::EVENT_MAGNITUDE,
					$this->connection->quote($minMag));
		}
		$maxMag = $query->getMaxEventMagnitude();
		if ($maxMag !== null) {
			$clauseList[] = sprintf("e.%s<=%s", self::EVENT_MAGNITUDE,
					$this->connection->quote($maxMag));
		}

		$eventSourceCode = $query->getEventSourceCode();
		if ($eventSourceCode !== null) {
			$clauseList[] = sprintf("e.%s=%s",
					self::EVENT_SOURCE_CODE, $this->connection->quote($eventSourceCode));

			$eventSource = $query->getEventSource();

			if($eventSource !== null) {
				$clauseList[] = sprintf("e.%s=%s",
						self::EVENT_SOURCE, $this->connection->quote($eventSource));
			}
		}

		$minTime = $query->getMinEventTime();
		if ($minTime !== null) {
			$clauseList[] = sprintf("e.%s>=%s", self::EVENT_TIME,
					$this->connection->quote($minTime));
		}
		$maxTime = $query->getMaxEventTime();
		if ($maxTime !== null) {
			$clauseList[] = sprintf("e.%s<=%s", self::EVENT_TIME,
					$this->connection->quote($maxTime));
		}

		$minLat = $query->getMinEventLatitude();
		if ($minLat !== null) {
			$clauseList[] = sprintf("e.%s>=%s", self::EVENT_LATITUDE,
					$this->connection->quote($minLat));
		}
		$maxLat = $query->getMaxEventLatitude();
		if ($maxLat !== null) {
			$clauseList[] = sprintf("e.%s<=%s", self::EVENT_LATITUDE,
					$this->connection->quote($maxLat));
		}

		$minDepth = $query->getMinEventDepth();
		if ($minDepth !== null) {
			$clauseList[] = sprintf("e.%s>=%s", self::EVENT_DEPTH,
					$this->connection->quote($minDepth));
		}
		$maxDepth = $query->getMaxEventDepth();
		if ($maxDepth !== null) {
			$clauseList[] = sprintf("e.%s<=%s", self::EVENT_DEPTH,
					$this->connection->quote($maxDepth));
		}

		$minLon = $query->getMinEventLongitude();
		$maxLon = $query->getMaxEventLongitude();
		// Normalize the longitudes between -180 and 180
		$minLon = $this->normalizeLongitude($minLon);
		$maxLon = $this->normalizeLongitude($maxLon);

		if ($minLon !== null && $maxLon !== null && ($maxLon < $minLon)) {
			// If the normalized maxLon is less than the normalized minLon, the
			// span crosses
			// the date line
			// If the range crosses the date line, split it into 2 clauses
			$lonClause = sprintf(
					"((e.%s >= %s AND e.%s <= 180) OR (e.%s <= %s AND e.%s > -180))",
					self::EVENT_LONGITUDE, $this->connection->quote($minLon),
					self::EVENT_LONGITUDE, self::EVENT_LONGITUDE,
					$this->connection->quote($maxLon), self::EVENT_LONGITUDE);
			$clauseList[] = $lonClause;
		} else {
			if ($minLon !== null) {
				$clauseList[] = sprintf("e.%s>=%s", self::EVENT_LONGITUDE,
					$this->connection->quote($minLon));
			}
			if ($maxLon !== null) {
				$clauseList[] = sprintf("e.%s<=%s", self::EVENT_LONGITUDE,
					$this->connection->quote($maxLon));
			}
		}

		//returns all events where status is not 'DELETE'
		$showDeletedEvent = $query->getShowDeletedEvent();
		if ($showDeletedEvent !== TRUE) {
			$clauseList[] = sprintf("UPPER(e.%s)!=UPPER('%s')", self::EVENT_STATUS,
					'DELETE');
		}

		// --------------------------------------------
		// Build clauses for columns in the productSummary table
		// --------------------------------------------
		$minUpdateTime = $query->getMinProductUpdateTime();
		if ($minUpdateTime !== null) {
			$clauseList[] = sprintf("product.%s>=%s", self::SUMMARY_UPDATE_TIME,
					$this->connection->quote($minUpdateTime));
		}
		$maxUpdateTime = $query->getMaxProductUpdateTime();
		if ($maxUpdateTime !== null) {
			$clauseList[] = sprintf("product.%s<=%s", self::SUMMARY_UPDATE_TIME,
					$this->connection->quote($maxUpdateTime));
		}
/*
		$source = $query->getProductSource();
		if ($source !== null) {
			$clauseList[] = sprintf("product.%s=%s", self::SUMMARY_SOURCE,
					$this->connection->quote($source));
		}
*/

		$sources = $query->getProductSources();
		if ($sources !== null && !empty($sources)) {
			$sourceClause = '(';
			$firstSource = true;
			foreach( $sources as $source ) {
				if( $firstSource === true ) {
					$firstSource = false;
				}
				else {
					$sourceClause .= ' OR ';
				}
				$sourceClause .= sprintf(" es.%s LIKE %s", self::EVENT_SUMMARY_PRODUCT_SOURCES,
					$this->connection->quote('%'.$source.'%') );
			}
			$sourceClause .= ')';

			$clauseList[] = $sourceClause;
		}

		$code = $query->getProductCode();
		if ($code !== null) {
			$clauseList[] = sprintf("product.%s=%s", self::SUMMARY_CODE,
					$this->connection->quote($code));
		}

		$version = $query->getProductVersion();
		if ($version !== null) {
			$clauseList[] = sprintf("product.%s=%s", self::SUMMARY_VERSION,
					$this->connection->quote($version));
		}

		$status = $query->getProductStatus();
		if ($status !== null) {
			$clauseList[] = sprintf("product.%s=%s", self::SUMMARY_STATUS,
					$this->connection->quote($status));
		}

		// --------------------------------------------
		// Build clauses for columns in the eventSummary table
		// --------------------------------------------
		$eventId = $query->getEventId();
		if( $eventId !== null ) {
			$clauseList[] = sprintf("es.%s LIKE %s", self::EVENT_SUMMARY_EVENTIDS,
					$this->connection->quote('%,'.$eventId.',%'));
		}

		$eventSource = $query->getEventSource();
		if ($eventSource !== null) {
			$clauseList[] = sprintf("es.%s LIKE %s", self::EVENT_SUMMARY_EVENT_SOURCES,
					$this->connection->quote('%,'.$eventSource.',%'));
		}

		// TEMPORARY CHANGE -- EJH 6/26/2012

		/* Eventually the catalog search will be handled by the eventSource search.
		 * In order to search based on a catalog source I needed to modify the eventSource
		 * search to remove the trailing comma ( '%cnss%' ).  This is necessary because
		 * I included the network name in the eventSource while loading historic data.
		 * This means the eventSource for the ANSS comoposite catalog is 'cnss_ak' not 'cnss'
		 */

		$catalogSource = $query->getCatalogSource();
		if ($catalogSource !== null) {
			$clauseList[] = sprintf("es.%s LIKE %s", self::EVENT_SUMMARY_EVENT_SOURCES,
					$this->connection->quote('%,'.$catalogSource.'%'));
		}

		$maxMaxmmi = $query->getMaxMaxMMI();
		if ($maxMaxmmi !== null ) {
			$clauseList[] = sprintf("es.%s<=%s", self::EVENT_SUMMARY_MAXMMI,
					$this->connection->quote($maxMaxmmi));
		}

		$minMaxmmi = $query->getMinMaxMMI();
		if ($minMaxmmi !== null ) {
			$clauseList[] = sprintf("es.%s>=%s", self::EVENT_SUMMARY_MAXMMI,
					$this->connection->quote($minMaxmmi));
		}

		$alertlevel = $query->getAlertLevel();
		if ($alertlevel !== null ) {
			$clauseList[] = sprintf("es.%s=%s", self::EVENT_SUMMARY_ALERT_LEVEL,
					$this->connection->quote($alertlevel));
		}

		$review_status = $query->getReviewStatus();
		if ($review_status !== null ) {
			$clauseList[] = sprintf("es.%s=%s", self::EVENT_SUMMARY_REVIEW_STATUS,
					$this->connection->quote($review_status));
		}

		$minAz_gap = $query->getMinAzimuthalGap();
		if ($minAz_gap !== null ) {
			$clauseList[] = sprintf("es.%s>=%s", self::EVENT_SUMMARY_AZ_GAP,
					$this->connection->quote($minAz_gap));
		}

		$maxAz_gap = $query->getMaxAzimuthalGap();
		if ($maxAz_gap !== null ) {
			$clauseList[] = sprintf("es.%s<=%s", self::EVENT_SUMMARY_AZ_GAP,
					$this->connection->quote($maxAz_gap));
		}

		$mag_type = $query->getMagnitudeType();
		if ($mag_type !== null ) {
			$clauseList[] = sprintf("es.%s=%s", self::EVENT_SUMMARY_MAG_TYPE,
					$this->connection->quote($mag_type));
		}

		$event_type = $query->getEventType();
		if ($event_type !== null ) {
			if ($event_type == 'earthquake') {
				$clauseList[] = sprintf(
					"es.%s=%s or es.%s is Null",
					self::EVENT_SUMMARY_EVENT_TYPE,
					$this->connection->quote($event_type),
					self::EVENT_SUMMARY_EVENT_TYPE
				);
			} else {
				$clauseList[] = sprintf(
					"es.%s=%s",
					self::EVENT_SUMMARY_EVENT_TYPE,
					$this->connection->quote($event_type)
				);
			}
		}

		$types = $query->getProductTypes();
		if ($types !== null && !empty($types)) {
			$typeClause = '(';
			$firstType = true;
			foreach( $types as $type ) {
				if( $firstType === true ) {
					$firstType = false;
				}
				else {
					$typeClause .= ' AND ';
				}
				$typeClause .= sprintf(" es.%s LIKE %s", self::EVENT_SUMMARY_TYPES,
					$this->connection->quote('%'.$type.'%') );
			}
			$typeClause .= ')';

			$clauseList[] = $typeClause;
		}

		return $clauseList;
	}

	/**
	 * Look in the database for all the attributes, links, and properties associated with the given
	 * product summary.
	 *
	 * @param summaryIndexId
	 * @return Map of property name to property value
	 */
	public function getSummary( $summaryIndexId ) {
		$summary = new ProductSummary();
		$summary->setIndexId($summaryIndexId);

		// -------------------------------------------------------------------
		// -- Add basic summary information
		// -------------------------------------------------------------------

		// Query the index for raw information
		$this->getSummaryStmt->bindValue(1, $summaryIndexId, PDO::PARAM_INT);
		$this->getSummaryStmt->execute();
		$results = $this->getSummaryStmt->fetch(PDO::FETCH_ASSOC);

		// Parse the raw information and set the summary parameters
		if ( count($results) > 0 ) {
			try {
				$summary->setId(
					ProductId::parse($results[self::SUMMARY_PRODUCT_ID]));
			} catch (Exception $e) {
				// Product ID not allowed to be null
				print "Product ID is null <br />";

				$this->getSummaryStmt->closeCursor();
				return null;
			}

			if ($summary->getId() == null) {
				$this->getSummaryStmt->closeCursor();
				return null;
			}

			// Set some simple types. Null values are fine.
			$summary->setEventSource($results[self::SUMMARY_EVENT_SOURCE]);
			$summary->setEventSourceCode($results[self::SUMMARY_EVENT_SOURCE_CODE]);

			// The caught exceptions here are fine. Just use null as their value

			$summary->setEventTime($results[self::SUMMARY_EVENT_TIME]);
			$summary->setEventLatitude(floatval($results[self::SUMMARY_EVENT_LATITUDE]));
			$summary->setEventLongitude(floatval($results[self::SUMMARY_EVENT_LONGITUDE]));
			$summary->setEventDepth(floatval($results[self::SUMMARY_EVENT_DEPTH]));
			$summary->setEventMagnitude(floatval($results[self::SUMMARY_EVENT_MAGNITUDE]));

			// Set some more simple values
			$summary->setVersion( $results[self::SUMMARY_VERSION] );
			$summary->setStatus( $results[self::SUMMARY_STATUS] );
			$summary->setTrackerURL( $results[self::SUMMARY_TRACKER_URL] );

			// This will default to 0 if not set in index db
			$summary->setPreferredWeight($results[self::SUMMARY_PREFERRED]);
		}

		// must close result set to keep from blocking transaction
		$this->getSummaryStmt->closeCursor();

		// Add summary link information
		$summary->setLinks($this->getSummaryLinks($summaryIndexId));

		// Add summary property information
		$summary->setProperties($this->getSummaryProperties($summaryIndexId));

		// Return our generated result. Note this is never null.
		return $summary;
	}

	/**
	 * Use the event index id to get a list of all of the product summary ids
	 * associated with that event
	 *
	 * @param eventIndexId
	 * @return List of product index ids
	 */
	protected function getSummaryIndexIds( $eventIndexId, $resultType ) {
		$summaryIndexIds = array();

		$query = $this->GET_SUMMARIES_BY_EVENT_INDEX_ID;
		$resultTypeClause = $this->buildResultTypeClause($resultType, $query);
		if( $resultTypeClause !== false ) {
			$query .= ' AND ' . $resultTypeClause;
		}

		$getSummariesStmt = $this->connection->prepare($query);

		$getSummariesStmt->bindValue(1, $eventIndexId, PDO::PARAM_INT);
		$getSummariesStmt->execute();
		$results = $getSummariesStmt->fetchAll(PDO::FETCH_ASSOC);

		foreach( $results as $row ) {
			$summaryIndexIds[] = $row[self::SUMMARY_PRODUCT_INDEX_ID];
		}

		$getSummariesStmt->closeCursor();
		return $summaryIndexIds;
	}

	/**
	 * Look in the database for all the properties associated with the given
	 * product summary.
	 *
	 * @param summaryIndexId
	 * @return Assoc array mapping property name to property value
	 */
	protected function getSummaryProperties( $summaryIndexId ) {
		// Create our object to populate and return
		$properties = array();

		$this->getProductProperties->bindValue(1, $summaryIndexId, PDO::PARAM_INT);
		$this->getProductProperties->execute();
		$results = $this->getProductProperties->fetchAll(PDO::FETCH_ASSOC);
		foreach( $results as $row ) {
			$name = html_entity_decode($row[self::SUMMARY_PROPERTY_NAME]);
			$value = html_entity_decode($row[self::SUMMARY_PROPERTY_VALUE]);

			// Only include if this was a valid property
			if ($name != null && $value != null) {
				// Add this link back to the map of links
				$properties[$name] = $value;
			}
		}

		// must close result set to keep from blocking transaction
		$this->getProductProperties->closeCursor();

		// Return our mapping of generated properties. Note this is never null
		// but may be empty.
		return $properties;
	}

	/**
	 * Look in the database for all the links associated with the given
	 * product summary.
	 *
	 * @param summaryIndexId
	 * @return Assoc array mapping link relation (link type) to URL
	 */
	protected function getSummaryLinks( $summaryIndexId ) {
		// Create our object to populate and return
		$links = array();

		$this->getProductLinks->bindValue(1, $summaryIndexId, PDO::PARAM_INT);
		$this->getProductLinks->execute();
		$results = $this->getProductLinks->fetchAll(PDO::FETCH_ASSOC);
		foreach( $results as $row ) {
			$relation = $row[self::SUMMARY_LINK_RELATION];
			$uriStr = $row[self::SUMMARY_LINK_URL];

			$l = null;

			// Only include valid properties
			if ($relation != null && $uriStr != null) {
				// Case when no links for this relation yet
				if ($l == null) {
					$l = array();
				}

				$l[] = $uriStr;

				// Add this link back to the map of links
				$links[$relation] = $l;
			}
		}

		// must close result set to keep from blocking transaction
		$this->getProductLinks->closeCursor();

		// Return our mapping of generated links. Note this is never null but
		// may be empty.
		return $links;
	}


	/**
	 * For queries that search the product table, we have to make sure we're only
	 * getting products that match the query's result type
	 *
	 * @param resultType One of the constants ProductIndexQuery::RESULT_TYPE_*
	 * @return Either a NOT EXISTS query for current results, an EXISTS query
	 *		for superseded results, or false for all other result types.
	 */
	protected function buildResultTypeClause( $resultType ) {
		$clause = false;
		// They're trying to search on product attributes, so we have to make sure we're
		// only searching for products that match the query's result type.

		// 2013-08-01 -- EMM (EQH-2259) ::
		// Do not include status=DELETE products in results unless using
		// resultType = ProductIndexQuery::*_WITH_DELETE variation.

		if ($resultType == ProductIndexQuery::RESULT_TYPE_CURRENT) {
			$clause = sprintf("product.%s != 'DELETE' AND NOT EXISTS (
					SELECT
						ps.%s
					FROM
						%s ps
					WHERE
						ps.%s = product.%s AND
						ps.%s = product.%s AND
						ps.%s = product.%s AND
						ps.%s > product.%s
					)",
					self::SUMMARY_STATUS,
					self::SUMMARY_PRODUCT_INDEX_ID, self::SUMMARY_TABLE,
					self::SUMMARY_TYPE, self::SUMMARY_TYPE, self::SUMMARY_SOURCE,
					self::SUMMARY_SOURCE, self::SUMMARY_CODE, self::SUMMARY_CODE,
					self::SUMMARY_UPDATE_TIME, self::SUMMARY_UPDATE_TIME
				);
		} else if ( // Same as above, but now include deleted products
				$resultType == ProductIndexQuery::RESULT_TYPE_CURRENT_WITH_DELETE) {
			$clause = sprintf("NOT EXISTS (
					SELECT
						ps.%s
					FROM
						%s ps
					WHERE
						ps.%s = product.%s AND
						ps.%s = product.%s AND
						ps.%s = product.%s AND
						ps.%s > product.%s
					)",
					self::SUMMARY_PRODUCT_INDEX_ID, self::SUMMARY_TABLE,
					self::SUMMARY_TYPE, self::SUMMARY_TYPE, self::SUMMARY_SOURCE,
					self::SUMMARY_SOURCE, self::SUMMARY_CODE, self::SUMMARY_CODE,
					self::SUMMARY_UPDATE_TIME, self::SUMMARY_UPDATE_TIME
				);
		} else if ($resultType == ProductIndexQuery::RESULT_TYPE_SUPERSEDED) {
			// If they only want superseded products, make a slightly different
			// clause that has a subquery
			$clause = sprintf("product.%s != 'DELETE' AND EXISTS (
					SELECT
						%s
					FROM
						%s ps
					WHERE
						ps.%s = product.%s AND
						ps.%s = product.%s AND
						ps.%s = product.%s AND
						ps.%s > product.%s
					)",
					self::SUMMARY_STATUS,
					self::SUMMARY_PRODUCT_INDEX_ID, self::SUMMARY_TABLE,
					self::SUMMARY_TYPE, self::SUMMARY_TYPE, self::SUMMARY_SOURCE,
					self::SUMMARY_SOURCE, self::SUMMARY_CODE, self::SUMMARY_CODE,
					self::SUMMARY_UPDATE_TIME, self::SUMMARY_UPDATE_TIME
				);
		} else if ( // Same as above, but now include deleted products
				$resultType == ProductIndexQuery::RESULT_TYPE_SUPERSEDED_WITH_DELETE) {
			$clause = sprintf("EXISTS (
					SELECT
						%s
					FROM
						%s ps
					WHERE
						ps.%s = product.%s AND
						ps.%s = product.%s AND
						ps.%s = product.%s AND
						ps.%s > product.%s
					)",
					self::SUMMARY_STATUS,
					self::SUMMARY_PRODUCT_INDEX_ID, self::SUMMARY_TABLE,
					self::SUMMARY_TYPE, self::SUMMARY_TYPE, self::SUMMARY_SOURCE,
					self::SUMMARY_SOURCE, self::SUMMARY_CODE, self::SUMMARY_CODE,
					self::SUMMARY_UPDATE_TIME, self::SUMMARY_UPDATE_TIME
				);
		}

		return $clause;
	}

	/**
	 * Convert the given longitude to be between -180 and 180. If the given
	 * value is already in the range, this method just returns the value.
	 *
	 * @param lon
	 * @return double normalized between -180 and 180
	 */
	protected function normalizeLongitude( $lon ) {
		if( $lon === null ) {
			return null;
		}
		while ($lon < -180.0) { $lon += 360.0; }
		while ($lon >  180.0) { $lon -= 360.0; }

		if ($lon <= 180 && $lon > -180) {
			return $lon;
		}

		return $lon;
	}


/**
	 * This is only called when the product source is defined in the searchView.
	 * This method will return a list of where clauses that apply search parameters
	 * to the productSummary table, instead of the event table.
	 *
	 *
	 * @param query
	 * @return Array containing clauses in the form: column="value"
	 */
	protected function buildProductSummaryClauses( $query ) {
	 	/**
		* All of the values from $query are sanitized using PDO::quote. This is necessary
	 	* to prevent SQL injection attacks because we aren't using the PDO::bind functions.
		*/
		$clauseList = array();

		if ($query == null) {
			return $clauseList; /* No query = No clauses */
		}

		// If there are one or more productIds we should build this clause
/*		$productIds = $query->getProductIds();
		if (count($productIds) > 0 ) {
			// Begin an "IN" clause
			$clause = sprintf("%s IN ('%s", self::SUMMARY_PRODUCT_ID,
					$productIds[0]);

			// Loop over any remaining productIds and add them to clause
			array_shift($productIds);
			foreach($productIds as $pid ) {
				$clause += "', '";
				$clause += $pid;
			}

			// Finish off our clause and add it to our clauseList
			$clause += "')";
			$clauseList[] = $clause;
		}*/

		// --------------------------------------------
		// Build clauses for columns in the event table
		// --------------------------------------------

		$eventSourceCode = $query->getEventSourceCode();
		if ($eventSourceCode !== null) {
			$clauseList[] = sprintf("product.%s=%s",
					self::EVENT_SOURCE_CODE, $this->connection->quote($eventSourceCode));
		}

		//returns all products where status is not 'DELETE'
		$showDeletedProduct = $query->getShowDeletedProduct();
		if ($showDeletedProduct !== TRUE) {
			$clauseList[] = sprintf("UPPER(product.%s)!=UPPER('%s')", self::SUMMARY_STATUS,
					'DELETE');
		}

		$minTime = $query->getMinEventTime();
		if ($minTime !== null) {
			$clauseList[] = sprintf("product.%s>=%s", self::SUMMARY_EVENT_TIME,
					$this->connection->quote($minTime));
		}
		$maxTime = $query->getMaxEventTime();
		if ($maxTime !== null) {
			$clauseList[] = sprintf("product.%s<=%s", self::SUMMARY_EVENT_TIME,
					$this->connection->quote($maxTime));
		}

		$minLat = $query->getMinEventLatitude();
		if ($minLat !== null) {
			$clauseList[] = sprintf("product.%s>=%s", self::SUMMARY_EVENT_LATITUDE,
					$this->connection->quote($minLat));
		}
		$maxLat = $query->getMaxEventLatitude();
		if ($maxLat !== null) {
			$clauseList[] = sprintf("product.%s<=%s", self::SUMMARY_EVENT_LATITUDE,
					$this->connection->quote($maxLat));
		}

		$minDepth = $query->getMinEventDepth();
		if ($minDepth !== null) {
			$clauseList[] = sprintf("product.%s>=%s", self::SUMMARY_EVENT_DEPTH,
					$this->connection->quote($minDepth));
		}
		$maxDepth = $query->getMaxEventDepth();
		if ($maxDepth !== null) {
			$clauseList[] = sprintf("product.%s<=%s", self::SUMMARY_EVENT_DEPTH,
					$this->connection->quote($maxDepth));
		}

		$minMag = $query->getMinEventMagnitude();
		if ($minMag !== null) {
			$clauseList[] = sprintf("product.%s>=%s", self::SUMMARY_EVENT_MAGNITUDE,
					$this->connection->quote($minMag));
		}
		$maxMag = $query->getMaxEventMagnitude();
		if ($maxMag !== null) {
			$clauseList[] = sprintf("product.%s<=%s", self::SUMMARY_EVENT_MAGNITUDE,
					$this->connection->quote($maxMag));
		}

		$minLon = $query->getMinEventLongitude();
		$maxLon = $query->getMaxEventLongitude();
		// Normalize the longitudes between -180 and 180
		$minLon = $this->normalizeLongitude($minLon);
		$maxLon = $this->normalizeLongitude($maxLon);

		if ($minLon !== null && $maxLon !== null && ($maxLon < $minLon)) {
			// If the normalized maxLon is less than the normalized minLon, the
			// span crosses
			// the date line
			// If the range crosses the date line, split it into 2 clauses
			$lonClause = sprintf(
					"((product.%s >= %s AND product.%s <= 180) OR (product.%s <= %s AND product.%s > -180))",
					self::SUMMARY_EVENT_LONGITUDE, $this->connection->quote($minLon),
					self::SUMMARY_EVENT_LONGITUDE, self::SUMMARY_EVENT_LONGITUDE,
					$this->connection->quote($maxLon), self::SUMMARY_EVENT_LONGITUDE);
			$clauseList[] = $lonClause;
		} else {
			if ($minLon !== null) {
				$clauseList[] = sprintf("product.%s>=%s", self::SUMMARY_EVENT_LONGITUDE,
					$this->connection->quote($minLon));
			}
			if ($maxLon !== null) {
				$clauseList[] = sprintf("product.%s<=%s", self::SUMMARY_EVENT_LONGITUDE,
					$this->connection->quote($maxLon));
			}
		}

		// --------------------------------------------
		// Build clauses for columns in the productSummary table
		// --------------------------------------------
		$minUpdateTime = $query->getMinProductUpdateTime();
		if ($minUpdateTime !== null) {
			$clauseList[] = sprintf("product.%s>=%s", self::SUMMARY_UPDATE_TIME,
					$this->connection->quote($minUpdateTime));
		}
		$maxUpdateTime = $query->getMaxProductUpdateTime();
		if ($maxUpdateTime !== null) {
			$clauseList[] = sprintf("product.%s<=%s", self::SUMMARY_UPDATE_TIME,
					$this->connection->quote($maxUpdateTime));
		}
/*
		$source = $query->getProductSource();
		if ($source !== null) {
			$clauseList[] = sprintf("product.%s=%s", self::SUMMARY_SOURCE,
					$this->connection->quote($source));
		}
*/

		$sources = $query->getProductSources();
		if ($sources !== null && !empty($sources)) {
			$sourceClause = '(';
			$firstSource = true;
			foreach( $sources as $source ) {
				if( $firstSource === true ) {
					$firstSource = false;
				}
				else {
					$sourceClause .= ' OR ';
				}
				$sourceClause .= sprintf(" product.%s LIKE %s", self::SUMMARY_SOURCE,
					$this->connection->quote($source));
			}
			$sourceClause .= ')';

			$clauseList[] = $sourceClause;
		}

		$code = $query->getProductCode();
		if ($code !== null) {
			$clauseList[] = sprintf("product.%s=%s", self::SUMMARY_CODE,
					$this->connection->quote($code));
		}

		$version = $query->getProductVersion();
		if ($version !== null) {
			$clauseList[] = sprintf("product.%s=%s", self::SUMMARY_VERSION,
					$this->connection->quote($version));
		}

		$status = $query->getProductStatus();
		if ($status !== null) {
			$clauseList[] = sprintf("product.%s=%s", self::SUMMARY_STATUS,
					$this->connection->quote($status));
		}

		// --------------------------------------------
		// Build clauses for columns in the eventSummary table
		// --------------------------------------------
		$eventId = $query->getEventId();
		if( $eventId !== null ) {
			$clauseList[] = sprintf("es.%s LIKE %s", self::EVENT_SUMMARY_EVENTIDS,
					$this->connection->quote('%,'.$eventId.',%'));
		}

		$eventSource = $query->getEventSource();
		if ($eventSource !== null) {
			$clauseList[] = sprintf("es.%s LIKE %s", self::EVENT_SUMMARY_EVENT_SOURCES,
					$this->connection->quote('%,'.$eventSource.',%'));
		}

		// TEMPORARY CHANGE -- EJH 6/26/2012

		/* Eventually the catalog search will be handled by the eventSource search.
		 * In order to search based on a catalog source I needed to modify the eventSource
		 * search to remove the trailing comma ( '%cnss%' ).  This is necessary because
		 * I included the network name in the eventSource while loading historic data.
		 * This means the eventSource for the ANSS comoposite catalog is 'cnss_ak' not 'cnss'
		 */

		$catalogSource = $query->getCatalogSource();
		if ($catalogSource !== null) {
			$clauseList[] = sprintf("es.%s LIKE %s", self::EVENT_SUMMARY_EVENT_SOURCES,
					$this->connection->quote('%,'.$catalogSource.'%'));
		}

		$maxMaxmmi = $query->getMaxMaxMMI();
		if ($maxMaxmmi !== null ) {
			$clauseList[] = sprintf("es.%s<=%s", self::EVENT_SUMMARY_MAXMMI,
					$this->connection->quote($maxMaxmmi));
		}

		$minMaxmmi = $query->getMinMaxMMI();
		if ($minMaxmmi !== null ) {
			$clauseList[] = sprintf("es.%s>=%s", self::EVENT_SUMMARY_MAXMMI,
					$this->connection->quote($minMaxmmi));
		}

		$alertlevel = $query->getAlertLevel();
		if ($alertlevel !== null ) {
			$clauseList[] = sprintf("es.%s=%s", self::EVENT_SUMMARY_ALERT_LEVEL,
					$this->connection->quote($alertlevel));
		}

		$review_status = $query->getReviewStatus();
		if ($review_status !== null ) {
			$clauseList[] = sprintf("es.%s=%s", self::EVENT_SUMMARY_REVIEW_STATUS,
					$this->connection->quote($review_status));
		}

		$minAz_gap = $query->getMinAzimuthalGap();
		if ($minAz_gap !== null ) {
			$clauseList[] = sprintf("es.%s>=%s", self::EVENT_SUMMARY_AZ_GAP,
					$this->connection->quote($minAz_gap));
		}

		$maxAz_gap = $query->getMaxAzimuthalGap();
		if ($maxAz_gap !== null ) {
			$clauseList[] = sprintf("es.%s<=%s", self::EVENT_SUMMARY_AZ_GAP,
					$this->connection->quote($maxAz_gap));
		}

		$mag_type = $query->getMagnitudeType();
		if ($mag_type !== null ) {
			$clauseList[] = sprintf("es.%s=%s", self::EVENT_SUMMARY_MAG_TYPE,
					$this->connection->quote($mag_type));
		}

		$types = $query->getProductTypes();
		if ($types !== null && !empty($types)) {
			$typeClause = '(';
			$firstType = true;
			foreach( $types as $type ) {
				if( $firstType === true ) {
					$firstType = false;
				} else {
					$typeClause .= ' AND ';
				}
				$typeClause .= sprintf(" es.%s LIKE %s", self::EVENT_SUMMARY_TYPES,
					$this->connection->quote('%'.$type.'%') );
			}
			$typeClause .= ')';

			$clauseList[] = $typeClause;
		}

		return $clauseList;
	}


	public function getConnection() {
		return $this->connection;
	}

}
?>
