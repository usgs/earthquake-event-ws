<?php


class ProductSummary {

	public $indexId = null;
	public $id = null;
	public $status = null;
	public $trackerURL = null;
	public $properties = array();
	public $links = array();

	public $eventSource = null;
	public $eventSourceCode = null;
	public $eventTime = null;
	public $eventType = null;
	public $eventLatitude = null;
	public $eventLongitude = null;
	public $eventDepth = null;
	public $eventMagnitude = null;
	public $version = null;
	public $preferredWeight = null;

	// reference to product from storage
	public $product = null;



	public function __construct(){}


	public function getIndexId() { return $this->indexId; }
	public function setIndexId($indexId) { $this->indexId = $indexId; }

	public function getId() { return $this->id; }
	public function setId($id) { $this->id = $id; }

	public function getStatus() { return $this->status; }
	public function setStatus($status) { $this->status = $status; }

	public function getTrackerURL() { return $this->trackerURL; }
	public function setTrackerURL($trackerURL) { $this->trackerURL = $trackerURL; }

	public function getProperties() { return $this->properties; }
	public function setProperties($props) { $this->properties = $props; }

	public function getLinks() { return $this->links; }
	public function setLinks($links) { $this->links = $links; }

	public function getEventId() {
		$source = $this->getEventSource();
		$code = $this->getEventSourceCode();
		if ($source != null && $code != null) {
			return $source . $code;
		}
		return null;
	}

	public function getEventSource() { 
		if ($this->eventSource != null) {
			return strtolower($this->eventSource);
		}
		return null;
	}
	public function setEventSource($eventSource) { $this->eventSource = $eventSource; }

	public function getEventSourceCode() { 
		if ($this->eventSourceCode != null) {
			return strtolower($this->eventSourceCode);
		}
		return null;
	}
	public function setEventSourceCode($eventSourceCode) { $this->eventSourceCode = $eventSourceCode; }

	public function getEventTime() { return $this->eventTime; }
	public function setEventTime($eventTime) { $this->eventTime = $eventTime; }

	public function getEventLatitude() { return $this->eventLatitude; }
	public function setEventLatitude($eventLatitude) { $this->eventLatitude = $eventLatitude; }

	public function getEventLongitude() { return $this->eventLongitude; }
	public function setEventLongitude($eventLongitude) { $this->eventLongitude = $eventLongitude; }

	public function getEventDepth() { return $this->eventDepth; }
	public function setEventDepth($eventDepth) { $this->eventDepth = $eventDepth; }

	public function getEventMagnitude() { return $this->eventMagnitude; }
	public function setEventMagnitude($eventMagnitude) { $this->eventMagnitude = $eventMagnitude; }

	public function getVersion() { return $this->version; }
	public function setVersion($version) { $this->version = $version; }

	public function getPreferredWeight() { return $this->preferredWeight; }
	public function setPreferredWeight($preferredWeight) { $this->preferredWeight = $preferredWeight; }

	public function getUpdateTime() { return $this->id->getUpdateTime(); }
	
	public function getEventType() { return $this->eventType; }

	public function toArray() {
		$r = array(
			// should be able to look this up using "id" property, there is an index
			'indexid' => $this->getIndexId(),

			'id' => $this->getId()->toString(),
			'type' => $this->getId()->getType(),
			'code' => $this->getId()->getCode(),
			'source' => $this->getId()->getSource(),
			'updateTime' => $this->getId()->getUpdateTime(),
			'status' => $this->getStatus(),
			//'trackerURL' => $this->getTrackerURL(),
			'properties' => $this->getProperties(),
			'links' => $this->getLinks(),

			// these come from product properties, already included above
			//'eventsource' => $this->getEventSource(),
			//'eventsourcecode' => $this->getEventSourceCode(),
			//'eventtime' => $this->getEventTime(),
			//'eventlatitude' => $this->getEventLatitude(),
			//'eventlongitude' => $this->getEventLongitude(),
			//'eventdepth' => $this->getEventDepth(),
			//'eventmagnitude' => $this->getEventMagnitude(),
			//'version' => $this->getVersion(),

			'preferredWeight' => safeintval($this->getPreferredWeight())
		);

		//remove null values
		foreach ($r as $key => $value) {
			if ($value == null) {
				unset($r[$key]);
			}
		}

		// hide these values from feeds
		unset($r['properties']['eids-feeder']);
		unset($r['properties']['eids-feeder-sequence']);

		return $r;
	}

	/**
	 * Check if this product summary has the given property
	 */
	public function hasProperty( $property ) {
		return array_key_exists($property, $this->properties);
	}

	/**
	 * Comparison function to two product summaries.
	 *
	 * Uses strcmp(id, that->id)
	 */
	public function compareTo(self $that) {
		return strcmp($this->getId(), $that->getId());
	}

	public function isDeleted() {
		$status = $this->getStatus();
		if (strtoupper($status) == 'DELETE') {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Comparison function for preferredWeights in descending order.
	 */
	public function compareToPreferred(self $that) {

		// deleted products to end of list
		if (!$this->isDeleted() && $that->isDeleted()) {
			// this product isn't deleted, but the other is
			return -1;
		} else if ($this->isDeleted() && !$that->isDeleted()) {
			// this product is deleted, but the other isn't
			return 1;
		}

		// then most preferred to the front of each section
		$thisWeight = $this->getPreferredWeight();
		$thatWeight = $that->getPreferredWeight();

		if ($thisWeight > $thatWeight) {
			return -1;
		} else if ($thisWeight < $thatWeight) {
			return 1;
		} else {
			//weight's equal, compare update time
			$thisTime = $this->getUpdateTime();
			$thatTime = $that->getUpdateTime();
			
			// Check if thisTime is greater than thatTime
			if (gmp_cmp($thisTime, $thatTime) == 1) {
			//if ( $thisTime >  $thatTime) {
				return -1;
			// Check if thisTime is less than thatTime
			} else if (gmp_cmp($thisTime, $thatTime) == -1 ) {
			//} else if ( $thisTime < $thatTime ) {
				return 1;
			} else {
				return 0;
			}
		}
	}

	/**
	 * @deprecated - Use ProductSummary#getProduct($storage) instead.
	 */
	public function loadProduct($storage) {
		$this->product = $storage->getProduct($this->getId());
	}

	public function getProduct($storage = null) {
		if ($this->product == null) {
			if ($storage != null) {
				$this->product = $storage->getProduct($this->getId());
			}
		}
		return $this->product;
	}

}
