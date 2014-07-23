<?php

class TextFeed extends AbstractFeed {

	public function getMimeType() {
		return 'text/plain';
	}

	public function getHeader ($query=null) {
		return "#EventID|Time|Latitude|Longitude|Depth/km|Author|Catalog|Contributor|ContributorID|MagType|Magnitude|MagAuthor|EventLocationName\n";
	}

	public function getEntry ($event) {
		if ($event == null) { return ''; }

		return implode('|', array(
			$event['eventSource'] . $event['eventSourceCode'],
			$this->formatter->formatDateIso($event['eventTime'], null, true),
			$event['eventLatitude'],
			$event['eventLongitude'],
			$event['eventDepth'],
			$event['origin_source'],
			$event['eventSource'],
			$event['source'],
			$event['eventSource'] . $event['eventSourceCode'],
			$event['magnitude_type'],
			$event['eventMagnitude'],
			$event['magnitude_source'],
			$event['region']
		)) . "\n";
	}

	public function getFooter () {
		return '';
	}

}

?>
