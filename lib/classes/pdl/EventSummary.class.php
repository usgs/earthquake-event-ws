<?php


class EventSummary {

	public $eventIndexId = null;
	public $source = null;
	public $sourceCode = null;
	public $time = null;
	public $latitude = null;
	public $longitude = null;
	public $depth = null;
	public $magnitude = null;
	public $magnitudeType = null;
	public $detailLink = null;
	public $status = null;
	public $eventType = null;
	public $azimuthalGap = null;
	public $tsunami = null;
	public $offset = null;
	public $significance = null;
	public $numStationsUsed = null;
	public $minimumDistance = null;
	public $standardError = null;
	public $properties = array();
	public $eventCodes = array();
	public $lastModified = null;


	public function __construct() {}

	public function getEventIndexId() { return $this->eventIndexId; }
	public function setEventIndexId($indexId) { $this->eventIndexId = $indexId; }

	public function getSource() { return $this->source; }
	public function setSource($source) { $this->source = $source; }

	public function getSourceCode() { return $this->sourceCode; }
	public function setSourceCode($sourceCode) { $this->sourceCode = $sourceCode; }

	public function getTime() { return $this->time; }
	public function setTime($time) { $this->time = $time; }

	public function getLatitude() { return $this->latitude; }
	public function setLatitude($latitude) { $this->latitude = $latitude; }

	public function getLongitude() { return $this->longitude; }
	public function setLongitude($longitude) { $this->longitude = $longitude; }

	public function getDepth() { return $this->depth; }
	public function setDepth($depth) { $this->depth = $depth; }

	public function getMagnitude() { return $this->magnitude; }
	public function setMagnitude($magnitude) { $this->magnitude = $magnitude; }
	
	public function getMagnitudeType() { return $this->magnitudeType; }
	public function setMagnitudeType($magnitudeType) { $this->magnitudeType = $magnitudeType; }

	public function getDetailLink() { return $this->detailLink; }
	public function setDetailLink($link) { $this->detailLink = $link; }

	public function getStatus() { return $this->status; }
	public function setStatus($status) { $this->status = $status; }

	public function getEventType() { return $this->eventType; }
	public function setEventType($type) { $this->eventType = $type; }
	
	public function getAzimuthalGap() { return $this->azimuthalGap; }
	public function setAzimuthalGap($azimuthalGap) { $this->azimuthalGap = $azimuthalGap; }	

	public function getTsunami() { return $this->tsunami; }
	public function setTsunami($tsunami) { $this->tsunami = $tsunami; }

	public function getOffset () { return $this->offset; }
	public function setOffset ($offset) { $this->offset = $offset; }

	public function getSignificance () { return $this->significance; }
	public function setSignificance ($significance) { $this->significance = $significance; }

	public function getNumStationsUsed () { return $this->numStationsUsed; }
	public function setNumStationsUsed ($numStationsUsed) { $this->numStationsUsed = $numStationsUsed; }

	public function getMinimumDistance () { return $this->minimumDistance; }
	public function setMinimumDistance ($minimumDistance) { $this->minimumDistance = $minimumDistance; }
	
	public function getStandardError () { return $this->standardError; }
	public function setStandardError ($standardError) { $this->standardError = $standardError; }

	public function getProperties() { return $this->properties; }
	public function setProperties($props) { $this->properties = $props; }

	public function setProperty($name, $value) { 
		$this->properties[$name] = $value;
	}

	public function getEventCodes() { return $this->eventCodes; }
	public function setEventCodes($eventCodes) { $this->eventCodes = $eventCodes; }

	public function getLastModified() { return $this->lastModified; }
	public function setLastModified($mod) { $this->lastModified = $mod; }

	public function toArray() {
		$r = array(
			'id' => $this->getSource() . $this->getSourceCode(),
			'source' => $this->getSource(),
			'sourceCode' => $this->getSourceCode(),
			'time' => $this->getTime(),
			'latitude' => $this->getLatitude(),
			'longitude' => $this->getLongitude(),
			'depth' => $this->getDepth(),
			'magnitude' => $this->getMagnitude(),
			'magnitudeType' => $this->getMagnitudeType(),			
			'url' => $this->getDetailLink(),
			'status' => $this->getStatus(),
			'type' => $this->getEventType(),
			'azimuthalGap' => $this->getAzimuthalGap(),
			'tsunami' => $this->getTsunami(),
			'offset' => $this->getOffset(),
			'significance' => $this->getSignificance(),
			'numStationsUsed' => $this->getNumStationsUsed(),
			'minimumDistance' => $this->getMinimumDistance(),
			'standardError' => $this->getStandardError(),
			'properties' => $this->getProperties(),
			'eventCodes' => $this->getEventCodes(),
			'lastModified' => $this->getLastModified()
		);

		//remove nulls
		foreach ($r as $key => $value) {
			if ($value === null) {
				unset($r[$key]);
			}
		}

		return $r;
	}

	public function getHumanTime() {
		return gmdate('Y-m-d H:i:s', intval(substr($this->getTime(), 0, - 3))) . " UTC";
	}

	public function getHumanMagnitude() {
		// magnitude may be unknown
		$mag = $this->getMagnitude();
		if (is_numeric($mag)) {
			// format with 1 decimal place
			$mag = sprintf("%.1f", $this->getMagnitude());
		} else {
			$mag = '?';
		}
		return $mag;
	}

	public function getHumanEventType() {
		$type = $this->getEventType();
		if($type == null || strtolower($type) == 'earthquake') {
			// assume earthquake
			$type = '';
		} else {
			$temp = strtolower($type);
			if 		($temp == "quarry")		$type = "Quarry Blast";
			else if	($temp == "nuke")		$type = "Nuclear Explosion";
			else if	($temp == "rockfall")	$type = "Rockslide";
			else if	($temp == "rockburst")	$type = "Rockslide";
			else if	($temp == "sonicboom")	$type = "Sonic Boom";
			else							$type = ucwords($type);
		}
	}

	public function getRegion() {
		$properties =  $this->getProperties();
		return $properties['region'];
	}

	public function getTitle() {
		return 'M' . $this->getHumanMagnitude() . ' ' . $this->getHumanEventType() . ' - ' . $this->getRegion();
	}

}
