<?php

/**
 * A callback to stream events from an FDSNIndex.
 */
class FDSNIndexCallback {

	// feed to handle output formatting
	public $feed = null;

	/**
	 * Construct a new FDSNIndexCallback.
	 */
	public function __construct() {
		// default to quakeml output
		$feed = new QuakemlFeed();
	}

	/**
	 * Called when there is a database error.
	 */
	public function onError($errorInfo) {
		// log locally
		trigger_error($errorInfo[0] . ' (' . $errorInfo[1] . '):' . $errorInfo[2]);

		// sanitize error
		throw new Exception('database error logged on server');
	}

	/**
	 * Called when a query is successful, before the first onEvent call.
	 *
	 * @param $query the query that executed and is about to generate events.
	 */
	public function onStart($query) {
		header('Content-type: ' . $this->feed->getMimeType());
		echo $this->feed->getHeader($query);
	}

	/**
	 * Called for each event found by the index.
	 *
	 * @param $event an associative array of event properties.
	 * @param $index the FDSNIndex that is executing the query.
	 */
	public function onEvent($event, $index) {
		echo $this->feed->getEntry($event);
	}

	/**
	 * Called after the last onEvent call.
	 */
	public function onEnd() {
		echo $this->feed->getFooter();
	}

}
