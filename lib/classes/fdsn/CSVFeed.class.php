<?php

class CSVFeed extends AbstractFeed {

	public function getMimeType() {
		return 'text/csv';
	}

	public function getHeader ($query=null) {
		return "time,latitude,longitude,depth,mag,magType,nst,gap,dmin,rms,net,id,updated\n";
	}

	public function getEntry ($event) {
		if ($event == null) { return ''; }

		return implode(",", array(
			$this->formatter->formatDateIso($event['eventTime']),
			$event['eventLatitude'],
			$event['eventLongitude'],
			$event['eventDepth'],
			$event['eventMagnitude'],
			$event['magnitude_type'],
			$event['num_stations_used'],
			$event['azimuthal_gap'],
			$event['minimum_distance'],
			$event['standard_error'],
			$event['eventSource'],
			$event['eventSource'] . $event['eventSourceCode'],
			$this->formatter->formatDateIso($event['eventUpdateTime'])
		)) . "\n";
	}

	public function getFooter () {
		return '';
	}

}

?>
