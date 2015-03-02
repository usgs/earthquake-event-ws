<?php

/**
 * JSONFeed is a JSON or JSONP feed of earthquakes.
 *
 * The JSONP feed does not accept a callback name as a paramter,
 * unlike typical JSONP requests.  This is for cacheability: everybody makes
 * the same request.
 */
class QuakemlFeed extends AbstractFeed {

  /**
   * Construct a new Quakeml Feed object.
   */
  public function __construct() {
  }

  public function getMimeType() {
    return 'application/xml';
  }

  public function getHeader ($query) {
    global $HOST_URL_PREFIX;

    $header = '<?xml version="1.0"?>' . "\n";
    $header .= '<q:quakeml' .
        ' xmlns="http://quakeml.org/xmlns/bed/1.2"' .
        ' xmlns:catalog="http://anss.org/xmlns/catalog/0.1"' .
        ' xmlns:q="http://quakeml.org/xmlns/quakeml/1.2"' .
        '>';

    $request_url = $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'];

    $request_url = str_replace('http://', '', $request_url);
    // Quakeml publicID may not contain colons
    $request_url = str_replace(':', '', $request_url);
    // Strip milliseconds off time parameters to strtotime still parses
    $request_url = preg_replace('/(time=[^\.&]+)(\.[\d]{3})?(&|$)/', '$1$3',
        $request_url);

    $header .= "\n" . '<eventParameters publicID="' .
        'quakeml:' . htmlentities($request_url) . '">';

    return $header;
  }

  public function getEntry ($event) {
    $entry = "\n";

    $publicID = self::getEventDetailFeed(
        $event['eventSource'] . $event['eventSourceCode'], 'quakeml');
    $publicID = str_replace('http://', '', $publicID);
    $publicID = str_replace(':', '', $publicID);

    $entry .= '<event' .
        ' catalog:datasource="' . $event['source'] . '"' .
        ' catalog:eventsource="' . $event['eventSource'] . '"' .
        ' catalog:eventid="' . $event['eventSourceCode'] . '"' .
        ' publicID="quakeml:' . htmlentities($publicID) . '">';

    $name = $this->getEventTitle($event);
    if ($name) {
      $entry .= '<description>';
      $entry .= '<type>earthquake name</type>';
      $entry .= '<text>' . $name . '</text>';
      $entry .= '</description>';
    }

    $preferredOriginID = null;
    $preferredMagnitudeID = null;
    $preferredFocalMechanismID = null;

    $origins = array($event);
    if (isset($event['origin'])) {
      // output all origins, first is preferred
      $origins = $event['origin'];
    }

    foreach ($origins as $origin) {
      $output = $this->getOrigin($origin);
      if ($preferredOriginID === null) {
        $preferredOriginID = $output['originPublicID'];
        $preferredMagnitudeID = $output['magnitudePublicID'];
      }
      $entry .= $output['xml'];
    }

    if (isset($event['moment-tensor'])) {
      foreach ($event['moment-tensor'] as $tensor) {
        $output = $this->getBeachball($tensor);
        if ($preferredFocalMechanismID === null) {
          $preferredFocalMechanismID = $output['beachballPublicID'];
        }
        $entry .= $output['xml'];
      }
    }

    if (isset($event['focal-mechanism'])) {
      foreach ($event['focal-mechanism'] as $tensor) {
        $output = $this->getBeachball($tensor);
        if ($preferredFocalMechanismID === null) {
          $preferredFocalMechanismID = $output['beachballPublicID'];
        }
        $entry .= $output['xml'];
      }
    }

    $entry .= $this->getElement('preferredOriginID', $preferredOriginID);
    $entry .= $this->getElement('preferredMagnitudeID', $preferredMagnitudeID);
    $entry .= $this->getElement('preferredFocalMechanismID',
        $preferredFocalMechanismID);

    // event type
    $type = $event['event_type'];
    if ($type === '') {
      $type = 'earthquake';
    }
    $entry .= $this->getElement('type', $type);

    $entry .= $this->getCreationInfo($event['source'],
        $event['eventUpdateTime'], $event['version']);
    $entry .= '</event>';


    return $entry;
  }

  public function getFooter () {
    $footer = "\n";

    $footer .= $this->getCreationInfo(null, time() . '000', null);
    // end array of entries
    $footer .= "\n" . '</eventParameters></q:quakeml>';

    return $footer;
  }

  public function getEventTitle($event) {
    if (isset($event['region'])){
      return $event['region'];
    }
    return null;
  }

  protected function getOrigin($origin) {
    $xml = '';
    $originPublicID = null;
    $magnitudePublicID = null;

    $originPublicID = $this->getPublicID(
        $origin['source'],
        $origin['type'],
        $origin['code'],
        $origin['updateTime']);

    if ($origin['eventMagnitude'] !== ''
        && $origin['eventMagnitude'] !== null) {
      $magnitudePublicID = $originPublicID . '#magnitude';
    }

    // build origin element
    $xml .= '<origin' .
        ' catalog:datasource="' . $origin['source'] . '"' .
        ' catalog:dataid="' . $origin['code'] . '"' .
        ' catalog:eventsource="' . $origin['eventSource'] . '"' .
        ' catalog:eventid="' . $origin['eventSourceCode'] . '"' .
        ' publicID="' . $originPublicID . '"' .
        '>';

    $xml .= $this->getElement('time/value',
        $this->isoFormat($origin['eventTime']));
    $xml .= $this->getElement('longitude/value', $origin['eventLongitude']);
    $xml .= $this->getElement('latitude/value', $origin['eventLatitude']);

    if ($origin['eventDepth'] !== null && $origin['eventDepth'] !== '') {
      $xml .= '<depth>';
      $xml .= '<value>' . $origin['eventDepth']*1000 . '</value>';
      if ($origin['vertical_error'] !== '') {
        $xml .= '<uncertainty>' . $origin['vertical_error']*1000 .
            '</uncertainty>';
      }
      $xml .= '</depth>';
    }

    if ($origin['horizontal_error'] !== '') {
      $xml .= '<originUncertainty>' .
        '<horizontalUncertainty>' .
          $origin['horizontal_error']*1000 .
        '</horizontalUncertainty>' .
        '<preferredDescription>' .
          'horizontal uncertainty' .
        '</preferredDescription>' .
        '</originUncertainty>';
    }

    $quality = '';
    $quality .= $this->getElement('usedPhaseCount',
        $origin['num_phases_used']);
    $quality .= $this->getElement('usedStationCount',
        $origin['num_stations_used']);
    $quality .= $this->getElement('standardError',
        $origin['standard_error']);
    $quality .= $this->getElement('azimuthalGap',
        $origin['azimuthal_gap']);
    $quality .= $this->getElement('minimumDistance',
        $origin['minimum_distance']);
    if ($quality !== '') {
      $xml .= '<quality>' . $quality . '</quality>';
    }

    $xml .= '<evaluationMode>';
    if (strtoupper($origin["review_status"]) === "REVIEWED") {
      $xml .= 'manual';
    } else {
      $xml .= 'automatic';
    }
    $xml .= '</evaluationMode>';

    $xml .= $this->getCreationInfo($origin['origin_source'],
        $origin['updateTime'], $origin['version']);
    $xml .= '</origin>';

    // output magnitude
    if ($magnitudePublicID !== null) {
      $xml .= '<magnitude' .
          ' catalog:datasource="' . $origin['source'] . '"' .
          ' catalog:dataid="' . $origin['code'] . '"' .
          ' catalog:eventsource="' . $origin['eventSource'] . '"' .
          ' catalog:eventid="' . $origin['eventSourceCode'] . '"' .
          ' publicID="' . $magnitudePublicID . '"' .
          '>';

        $xml .= '<mag>';
        // $magnitudePublicID is not null based on this value being set
        $xml .= '<value>' . $origin['eventMagnitude'] . '</value>';
        $xml .= $this->getElement('uncertainty', $origin['magnitude_error']);
        $xml .= '</mag>';
        $xml .= $this->getElement('type', $origin['magnitude_type']);
        $xml .= $this->getElement('stationCount',
            $origin['magnitude_num_stations_used']);

        $xml .= '<originID>' . $originPublicID . '</originID>';
        $xml .= '<evaluationMode>';
        if (strtoupper($origin['review_status']) === "REVIEWED") {
          $xml .= 'manual';
        } else {
          $xml .= 'automatic';
        }
        $xml .= '</evaluationMode>';

        $xml .= $this->getCreationInfo($origin['magnitude_source'],
            $origin['updateTime'], null);

      $xml .= '</magnitude>';
    }

    return array(
      'xml' => $xml,
      'originPublicID' => $originPublicID,
      'magnitudePublicID' => $magnitudePublicID
    );
  }

  protected function getBeachBall($beachball) {
    $xml = '';
    $beachballPublicID = null;
    // derived information
    $originPublicID = null;
    $magnitudePublicID = null;

    $beachballPublicID = $this->getPublicID(
        $beachball['source'],
        $beachball['type'],
        $beachball['code'],
        $beachball['updateTime']);

    // build beachball element
    $xml .= '<focalMechanism' .
        ' catalog:datasource="' . $beachball['source'] . '"' .
        ' catalog:dataid="' . $beachball['code'] . '"' .
        ' catalog:eventsource="' . $beachball['eventSource'] . '"' .
        ' catalog:eventid="' . $beachball['eventSourceCode'] . '"' .
        ' publicID="' . $beachballPublicID . '"' .
        '>';

    if ($beachball['nodal_plane_1_strike'] !== '') {
      $xml .= '<nodalPlanes>' .
          '<nodalPlane1>' .
            $this->getElement('strike/value',
                $beachball['nodal_plane_1_strike']) .
            $this->getElement('dip/value', $beachball['nodal_plane_1_dip']) .
            $this->getElement('rake/value', $beachball['nodal_plane_1_rake']) .
          '</nodalPlane1>' .
          '<nodalPlane2>' .
            $this->getElement('strike/value',
                $beachball['nodal_plane_2_strike']) .
            $this->getElement('dip/value', $beachball['nodal_plane_2_dip']) .
            $this->getElement('rake/value', $beachball['nodal_plane_2_rake']) .
          '</nodalPlane2>' .
        '</nodalPlanes>';
    }

    if (isset($beachball['scalar_moment'])) {
      $xml .= '<momentTensor>' .
          '<scalarMoment>' . $beachball['scalar_moment'] . '</scalarMoment>' .
          '<tensor>' .
            $this->getElement('Mrr/value', $beachball['tensor_mrr']) .
            $this->getElement('Mtt/value', $beachball['tensor_mtt']) .
            $this->getElement('Mpp/value', $beachball['tensor_mpp']) .
            $this->getElement('Mrt/value', $beachball['tensor_mrt']) .
            $this->getElement('Mrp/value', $beachball['tensor_mrp']) .
            $this->getElement('Mtp/value', $beachball['tensor_mtp']) .
          '</tensor>';

      $xml .= $this->getElement('doubleCouple',
          $beachball['percent_double_couple']);
      $xml .= $this->getElement('methodID', $beachball['beachball_type']);

      if ($beachball['derived_depth'] !== '' ||
          $beachball['derived_latitude'] !== '') {
        $originPublicID = $beachballPublicID . '#origin';
        $xml .= $this->getElement('derivedOriginID', $originPublicID);
      }
      if ($beachball['derived_magnitude'] !== '') {
        $magnitudePublicID = $beachballPublicID . '#magnitude';
        $xml .= $this->getElement('momentMagnitudeID', $magnitudePublicID);
      }

      $xml .= '</momentTensor>';
    }

    $xml .= '<evaluationMode>';
    if (strtoupper($beachball["review_status"]) === "REVIEWED") {
      $xml .= 'manual';
    } else {
      $xml .= 'automatic';
    }
    $xml .= '</evaluationMode>';

    $xml .= $this->getCreationInfo(
        $beachball['beachball_source'],
        $beachball['updateTime'],
        $beachball['version']);

    $xml .= '</focalMechanism>';


    // add derived origin
    if ($originPublicID !== null) {
      $xml .= '<origin publicID="' . $originPublicID . '">' .
          $this->getElement('time/value', $beachball['derived_eventtime']) .
          $this->getElement('latitude/value', $beachball['derived_latitude']) .
          $this->getElement('longitude/value', $beachball['derived_longitude']);

      if ($beachball['derived_depth'] !== '') {
        $xml .= $this->getElement('depth/value',
            $beachball['derived_depth']*1000) .
            '<depthType>from moment tensor inversion</depthType>';
      }

      $xml .= '</origin>';
    }

    // add derived magnitude
    if ($magnitudePublicID !== null) {
      $xml .= '<magnitude publicID="' . $magnitudePublicID . '">' .
          $this->getElement('mag/value', $beachball['derived_magnitude']) .
          $this->getElement('type', $beachball['derived_magnitude_type']) .
          $this->getElement('originID', $originPublicID) .
        '</magnitude>';
    }


    return array(
      'xml' => $xml,
      'beachballPublicID' => $beachballPublicID,
      'originPublicID' => $originPublicID,
      'magnitudePublicID' => $magnitudePublicID
    );
  }

  protected function getElement($elementName, $value=null) {
    $r = '';

    if ($value !== null && $value !== '') {
      $elements = explode("/", $elementName);
      foreach ($elements as $el) {
        $r .= '<' . $el . '>';
      }

      $r .= $value;

      $elements = array_reverse($elements);
      foreach ($elements as $el) {
        $r .= '</' . $el . '>';
      }
    }

    return $r;
  }

  protected function isoFormat($time) {
    $seconds = substr($time, 0, -3);
    $millis = substr($time, -3);
    return gmdate('Y-m-d\TH:i:s', $seconds) . '.' . $millis . 'Z';
  }

  protected function getCreationInfo($agencyID, $creationTime, $version) {
    if ($agencyID === null && $creationTime === null && $version === null) {
      return '';
    }

    $info = '';
    $info .= '<creationInfo>';
    if ($agencyID !== null) {
      $info .= '<agencyID>' . $agencyID . '</agencyID>';
    }
    if ($creationTime !== null) {
      if (!strpos("T", $creationTime)) {
        // convert ms timestamp to date
        $creationTime = $this->isoFormat($creationTime);
      }
      $info .= '<creationTime>' . $creationTime . '</creationTime>';
    }
    if ($version !== null) {
      $info .= '<version>' . $version . '</version>';
    }
    $info .= '</creationInfo>';

    return $info;
  }

  protected function getPublicID($source, $type, $code, $updateTime) {
    global $HOST_URL_PREFIX;
    global $storage_url;

    $prefix = str_replace('http://', '', $HOST_URL_PREFIX);
    $prefix = str_replace(':', '', $prefix);

    return 'quakeml:' . $prefix . $storage_url . '/' . $type . '/' . $code .
        '/' . $source . '/' . $updateTime . '/product.xml';
  }

}

?>
