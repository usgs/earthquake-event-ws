<?php


	if($event == null ) {
		header('HTTP/1.0 404 Not Found');
		print '<h1>Event not found</h1>';
		return;
	} else if ($event->isDeleted()) {
		header('HTTP/1.0 404 Not Found');
		print '<h1>Event deleted</h1>';
		return;
	}


	global $fdsnIndex;
	global $storage;
	global $HOST_URL_PREFIX;
	global $FEED_PATH;
	global $EVENT_PATH;


	// Used for translating MMI integers to roman numerals
	$ROMANS = array('I', 'I', 'II', 'III', 'IV', 'V', 'VI',
			'VII', 'VIII', 'IX', 'X');

	$authid = $event->getSource() . $event->getSourceCode();


	$query = new FDSNQuery();
	$query->eventid = $authid;
	$summarys = $fdsnIndex->getEvents($query);
	$summary = $summarys[0];

	$alt_ids = array();
	if ($summary['eventids'] != '') {
		$ids = explode(',', $summary['eventids']);
		for ($i=0, $len=count($ids); $i<$len; $i++) {
			$id = $ids[$i];
			if ($id !== '' && $id !== $authid) {
				$alt_ids[] = $id;
			}
		}
	}



	// Using "toArray" will fetch product contents
	$event = $event->toArray($storage);


	$smcont = null;
	$smfault = null;
	$smover = null;
	$smo = null;
	$sms = null;
	$include_sm = false;

	$lpe = null;
	$dyfizip = null;
	$dyfigeo = null;

	if (isset($event['products']['shakemap'])) {
		$sm = $event['products']['shakemap'][0];
		$smp = $sm['properties'];
		$smc = $sm['contents'];

		if (isset($smc['download/contours.kmz'])) {
			$smcont = $smc['download/contours.kmz'];
			$include_sm = true;
		}

		if (isset($smc['download/fault.kmz'])) {
			$smfault = $smc['download/fault.kmz'];
			$include_sm = true;
		}

		if (isset($smc['download/overlay.kmz'])) {
			$smover = $smc['download/overlay.kmz'];
			$include_sm = true;
		}

		// This one is legacy and only used if KMZ (above) is not available
		if (isset($smc['download/ii_overlay.png'])) {
			$smo = $smc['download/ii_overlay.png'];
			$include_sm = $include_sm || isset($smp['maximum-latitude']);
		}

		if (isset($smc['download/stations.kmz'])) {
			$sms = $smc['download/stations.kmz'];
			$include_sm = true;
		}
	}

	if (isset($event['products']['losspager'])) {
		$lp = $event['products']['losspager'][0];
		$lpp = $lp['properties'];
		$lpc = $lp['contents'];

		if (isset($lpc['pagerexpo.kmz'])) {
			$lpe = $lpc['pagerexpo.kmz'];
		}
	}

	if (isset($event['products']['dyfi'])) {
		$dyfi = $event['products']['dyfi'][0];
		$dyfip = $dyfi['properties'];
		$dyfic = $dyfi['contents'];

		if(isset($dyfic['dyfi_zip.kmz'])) {
			$dyfizip = $dyfic['dyfi_zip.kmz'];
		}

		if (isset($dyfic['dyfi_geo.kmz'])) {
			$dyfigeo = $dyfic['dyfi_geo.kmz'];
		}
	}


	$kmlfeed = new KMLFeed();
	$kmlfeed->useFolders = false;

	$latitude = $summary['eventLatitude'];
	$longitude = $summary['eventLongitude'];



	// TODO :: Should we set a max-age header or something to control caching?
	//         This could be done in the httpd configuration file alternatively.


	header('Content-Type: ' . $kmlfeed->getMimeType());


	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<kml xmlns="http://earth.google.com/kml/2.0">' .
		'<NetworkLinkControl>' .
			'<minRefreshPeriod>60</minRefreshPeriod>' .
			'<linkName>' . $kmlfeed->getEventTitle($summary) . '</linkName>' .
			'<linkSnippet maxLines="2">' .
				'Updated: ' . $kmlfeed->formatter->formatDate($summary['eventUpdateTime']);

		if ($eventid != $authid) { echo ", Auth ID: " . $authid ; }
		if (count($alt_ids) > 0) { echo ', aka: ' . implode(', ', $alt_ids); }

		echo '</linkSnippet>';

	if ($eventid != $authid) {
		echo '<message><![CDATA[' .
				'<p>The ID for the event you are currently viewing has changed.</p>' .
				'<ul>' .
					'<li>Old Event ID: ' . $eventid . '</li>' .
					'<li>New Event ID: ' . $authid . '</li>' .
				'</ul>' .
				'<p><small>' .
					'No matter what we call it you are still looking at the most' .
					' accurate and up-to-date information. Just thought you might' .
					' like to know about the change.' .
				'</small></p>' .
			']]></message>';
	}

	echo '</NetworkLinkControl>';

	echo "\n" . '<Document>' .
		$kmlfeed->getLookAt($latitude, $longitude, 500000) .
		'<open>1</open>' .
		'<Placemark id="feed">' .
			'<name>Epicenter</name>' .
			$kmlfeed->getEntryDescription($summary, array(
					'<a href="' . $HOST_URL_PREFIX . '/earthquakes/feed/">' .
						'Get Real-time Data Sent to You' .
					'</a>'
				)) .
			'<Snippet maxLines="0"></Snippet>' .
			$kmlfeed->getLookAt($latitude, $longitude, 50000) .
			'<Style>' .
				'<IconStyle>' .
					'<Icon><href>' .
						$HOST_URL_PREFIX . $FEED_PATH . '/images/kml_star.png' .
					'</href></Icon>' .
					'<scale>1</scale>' .
				'</IconStyle>' .
				'<LabelStyle><scale>1</scale></LabelStyle>' .
				$kmlfeed->getBalloonStyle() .
			'</Style>' .
			'<Point><coordinates>'.$longitude.','.$latitude.',0'.'</coordinates></Point>' .
		'</Placemark>';

	if ($include_sm || $lpe || ($dyfizip || $dyfigeo)) {
		echo "\n" . '<Folder>' .
				'<name>Impact Estimates</name>' .
				'<open>1</open>' .
				'<Style><ListStyle>' .
					'<listItemType>radioFolder</listItemType>' .
				'</ListStyle></Style>';

		// PAGER
		if ($lpe) {
			echo "\n" . '<Folder>' .
					'<name>PAGER - ' . ucwords($summary['alertlevel']) . '</name>' .
					$kmlfeed->getLookAt($latitude, $longitude, 100000, 70) .
					'<NetworkLink>' .
						'<name>Exposure</name>' .
						'<visibility>0</visibility>' .
						'<Link>' .
							'<href>' . $lpe['url'] . '</href>' .
							'<viewRefreshMode>never</viewRefreshMode>' .
						'</Link>' .
					'</NetworkLink>' .
				'</Folder>';
		} // end PAGER


		// ShakeMap
		if ($include_sm) {
			echo "\n" . '<Folder>' .
					'<name>ShakeMap - ' .
						$ROMANS[intval($summary['maxmmi'])] .
					'</name>';

			if ($smcont) {
				echo '<NetworkLink>' .
						'<name>Contours</name>' .
						'<visibility>1</visibility>' .
						'<Link>' .
							'<href>' . $smcont['url'] . '</href>' .
							'<viewRefreshMode>never</viewRefreshMode>' .
						'</Link>' .
					'</NetworkLink>';
			}

			if ($smfault) {
				echo '<NetworkLink>' .
						'<name>Faults</name>' .
						'<visibility>0</visibility>' .
						'<Link>' .
							'<href>' . $smfault['url'] . '</href>' .
							'<viewRefreshMode>never</viewRefreshMode>' .
						'</Link>' .
					'</NetworkLink>';
			}

			if ($smover) {
				echo '<NetworkLink>' .
						'<name>Intensity Overlay</name>' .
						'<visibility>0</visibility>' .
						'<Link>' .
							'<href>' . $smover['url'] . '</href>' .
							'<viewRefreshMode>never</viewRefreshMode>' .
						'</Link>' .
					'</NetworkLink>';
			} else if ($smo && isset($smp['maximum-latitude'])) {
				echo $kmlfeed->getLookAt($latitude, $longitude, 500000) .
					'<GroundOverlay>' .
						'<name>Intensity</name>' .
						'<color>9EFFFFFF</color>' .
						'<visibility>0</visibility>' .
						'<drawOrder>1</drawOrder>' .
						'<Icon>' .
							'<refreshMode>onChange</refreshMode>' .
							'<href>' . $smo['url'] . '</href>' .
						'</Icon>' .
						'<LatLonBox>' .
							'<north>' . $smp['maximum-latitude'] . '</north>' .
							'<south>' . $smp['minimum-latitude'] . '</south>' .
							'<east>' . $smp['maximum-longitude'] . '</east>' .
							'<west>' . $smp['minimum-longitude'] . '</west>' .
						'</LatLonBox>' .
					'</GroundOverlay>';
			}

			if ($sms) {
				echo '<NetworkLink>' .
						'<name>Stations</name>' .
						'<visibility>0</visibility>' .
						'<Link>' .
							'<href>' . $sms['url'] . '</href>' .
							'<refreshMode>onExpire</refreshMode>' .
						'</Link>' .
					'</NetworkLink>';
			}

			// If any of the colored layers are present, thus the legend is relevant,
			// include the legend overlay. This may be overkill logic since one of
			// these ought always exist...
			if ($smover || $smcont || ($smo && isset($smp['maximum-latitude']))) {
				echo '<ScreenOverlay>' .
						'<name>Intensity Legend</name>' .
						'<visibility>0</visibility>' .
						'<Icon><href>' .
							$HOST_URL_PREFIX . $FEED_PATH . '/images/kml_shakemap_legend.png' .
						'</href></Icon>' .
						'<overlayXY x="0" y="90" xunits="pixels" yunits="pixels"/>' .
						'<screenXY x="5" y="1" xunits="pixels" yunits="fraction"/>' .
						'<size x="0" y="0" xunits="pixels" yunits="pixels"/>' .
					'</ScreenOverlay>';
			}

			echo '</Folder>';

		} // end ShakeMap


		if ($dyfizip || $dyfigeo) {
			echo '<Folder>' .
					'<name>DYFI - ' .
						$ROMANS[intval($summary['maxcdi'])] .
					'</name>' .
					$kmlfeed->getLookAt($latitude, $longitude, 500000) .
				'<Style><ListStyle>' .
					'<listItemType>radioFolder</listItemType>' .
				'</ListStyle></Style>';

			if ($dyfizip) {
				echo '<NetworkLink>' .
						'<name>Zip Codes / Cities</name>' .
						'<visibility>0</visibility>' .
						'<Link>' .
							'<href>' . $dyfizip['url'] . '</href>' .
							'<refreshMode>onExpire</refreshMode>' .
						'</Link>' .
					'</NetworkLink>';
			}

			if ($dyfigeo) {
				echo '<NetworkLink>' .
						'<name>Geocoded locations</name>' .
						'<visibility>0</visibility>' .
						'<Link>' .
							'<href>' . $dyfigeo['url'] . '</href>' .
							'<refreshMode>onExpire</refreshMode>' .
						'</Link>' .
					'</NetworkLink>';
			};

			echo '</Folder>';

		} // end DYFI

		echo '</Folder>';

	} // end Impact


	echo $kmlfeed->getUSGSOverlay() .
		"\n" . '</Document></kml>';
