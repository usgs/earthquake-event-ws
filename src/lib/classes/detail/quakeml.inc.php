<?php

/*
NOTE: detail.php and FDSNEventWebService::query currently do not use this file. 
However, it may be desirable to merge quakeml contributions, instead of just index properties, in the future...
*/

header('Content-type: application/json');
if( $event == null ) {
  header('HTTP/1.0 404 Not Found');
  print '{"message": "Event not found."}';
  return;
} else if ($event->isDeleted()) {
  header('HTTP/1.0 404 Not Found');
  print '{"message": "Event deleted."}';
  return;
}


if (!function_exists("quakeml_element")) {

  function quakeml_element($elementName, $propertyName, $properties) {
    $r = '';

    if (isset($properties[$propertyName])) {
      $elements = explode("/", $elementName);
      foreach ($elements as $el) {
        $r .= '<' . $el . '>';
      }

      $r .= $properties[$propertyName];

      $elements = array_reverse($elements);
      foreach ($elements as $el) {
        $r .= '</' . $el . '>';
      }
    }

    return $r;
  };

  function quakeml_creationinfo($agencyID, $creationTime, $version) {
    if ($agencyID === null && $creationTime === null && $version === null) {
      return '';
    }

    $info = '';
    $info .= '<creationInfo>';
    if ($agencyID !== null) {
      $info .= '<agencyID>' . $agencyID . '</agencyID>';
    }
    if ($creationTime !== null) {
      if (strpos("T", $creationTime) === -1) {
        // convert ms timestamp to date
      }
      $info .= '<creationTime>' . $creationTime . '</creationTime>';
    }
    if ($version !== null) {
      $info .= '<version>' . $version . '</version>';
    }
    $info .= '</creationInfo>';
    
    return $info;
  }

  function quakeml_publicid($id) {
    return 'quakeml:earthquake.usgs.gov/product/' . $id->getSource() . '/' . $id->getType() . '/' . $id->getCode() . '/' . $id->getUpdateTime();
  }
}

header('Content-type: application/xml');


$source = $event->getSource();
$sourceCode = $event->getSourceCode();
$fullid = $source . $sourceCode;
$lastModified = $event->getLastModified();
$preferredOriginID = null;
$preferredMagnitudeID = null;

$products = $event->getProducts();
$origins = isset($products['origin']) ? $products['origin'] : array();
$mechs = array_merge(
  isset($products['focal-mechanism']) ? $products['focal-mechanism'] : array(),
  isset($products['moment-tensor']) ? $products['moment-tensor'] : array()
);



echo '<?xml version="1.0"?>' . "\n";
echo '<q:quakeml xmlns="http://quakeml.org/xmlns/bed/1.2" xmlns:catalog="http://anss.org/xmlns/catalog/0.1" xmlns:q="http://quakeml.org/xmlns/quakeml/1.2">';
echo '<eventParameters publicID="quakeml:earthquake.usgs.gov/eventParameters/' . $fullid . '/' . $lastModified . '">';

echo '<event catalog:datasource="anss" catalog:eventsource="' . $source . '" catalog:eventid="' . $sourceCode . '" publicID="quakeml:earthquake.usgs.gov/event/' . $fullid . '">';


  // output each origin product
  foreach ($origins as $origin) {
  
    $id = $origin->getId();
    $props = $origin->getProperties();
  
    $publicID = quakeml_publicid($id);
  
    if ($preferredOriginID === null) {
      $preferredOriginID = $publicID;
      if (isset($props['magnitude'])) {
        $preferredMagnitudeID = $publicID . "/magnitude";
      }
    }

    // output origin
    echo '<origin catalog:datasource="' . $id->getSource() . '" catalog:eventsource="' . $props["eventsource"] . '" catalog:eventid="' . $props["eventsourcecode"] . '" catalog:dataid="' . $id->getCode() . '" publicID="' . $publicID . '">';
  
      echo quakeml_element('time/value', 'eventtime', $props);
      echo quakeml_element('longitude/value', 'longitude', $props);
      echo quakeml_element('latitude/value', 'latitude', $props);

      if (isset($props["depth"])) {
        echo '<depth>';
        echo '<value>' . ($props["depth"]*1000) . '</value>';
        
        if (isset($props["vertical-error"])) {
          echo '<uncertainty>' . ($props["vertical-error"]*1000) . '</uncertainty>';
        }
        echo '</depth>';
      }

      $quality = '';
      $quality .= quakeml_element('usedPhaseCount', 'num-phases-used', $props);
      $quality .= quakeml_element('usedStationCount', 'num-stations-used', $props);
      $quality .= quakeml_element('standardError', 'standard-error', $props);
      $quality .= quakeml_element('azimuthalGap', 'azimuthal-gap', $props);
      $quality .= quakeml_element('minimumDistance', 'minimum-distance', $props);
      if ($quality != '') {
        echo '<quality>' . $quality . '</quality>';
      }
    
      echo quakeml_creationinfo($id->getSource(), $origin->getUpdateTime(), $origin->getVersion());
    
      echo '<evaluationMode>';
      if (isset($props["review-status"]) && strtoupper($props["review-status"]) == "REVIEWED") {
        echo 'manual';
      } else {
        echo 'automatic';
      }
      echo '</evaluationMode>';
  
    echo '</origin>';


    // output magnitude
    if (isset($props['magnitude'])) {
      echo '<magnitude catalog:datasource="' . $id->getSource() . '" catalog:eventsource="' . $props["eventsource"] . '" catalog:eventid="' . $props["eventsourcecode"] . '" catalog:dataid="' . $id->getCode() . '" publicID="' . $publicID . '/magnitude">';
  
        echo '<mag>';
          echo '<value>' . $props['magnitude'] . '</value>';
          echo quakeml_element('uncertainty', 'magnitude-error', $props);
        echo '</mag>';
    
        echo quakeml_element('type', 'magnitude-type', $props);
        echo quakeml_element('stationCount', 'magnitude-num-stations-used', $props);
        echo quakeml_element('azimuthalGap', 'magnitude-azimuthal-gap', $props);
    
        echo '<originID>' . $publicID . '</originID>';
        echo '<evaluationMode>';
        if (isset($props["review-status"]) && strtoupper($props["review-status"]) == "REVIEWED") {
          echo 'manual';
        } else {
          echo 'automatic';
        }
        echo '</evaluationMode>';
    
        echo quakeml_creationinfo(
            isset($props['magnitude-source']) ? $props['magnitude-source'] : null, 
            null,
            null
          );
  
      echo '</magnitude>';
    }
  }


  // output focal mechanism and moment tensor products
  foreach ($mechs as $mech) {
    $id = $mech->getId();
    $props = $mech->getProperties();
    $publicID = quakeml_publicid($id);

    $derivedOriginID = null;
    $derivedMagnitudeID = null;

    echo '<focalMechanism catalog:datasource="' . $id->getSource() . '" catalog:eventsource="' . $props["eventsource"] . '" catalog:eventid="' . $props["eventsourcecode"] . '" catalog:dataid="' . $id->getCode() . '" publicID="' . $publicID . '">';

      // fm and mt may have nodal planes
      if (isset($props['nodal-plane-1-strike'])) {
        
        echo '<nodalPlanes>';
        echo '<nodalPlane1>';
          echo quakeml_element('strike/value', 'nodal-plane-1-strike', $props);
          echo quakeml_element('dip/value', 'nodal-plane-1-dip', $props);
          echo quakeml_element('rake/value', 'nodal-plane-1-rake', $props);
        echo '</nodalPlane1>';
        echo '<nodalPlane2>';
          echo quakeml_element('strike/value', 'nodal-plane-2-strike', $props);
          echo quakeml_element('dip/value', 'nodal-plane-2-dip', $props);
          echo quakeml_element('rake/value', 'nodal-plane-2-rake', $props);
        echo '</nodalPlane2>';
        echo '</nodalPlanes>';
      }

      // moment tensor specific properties
      if ($id->getType() == 'moment-tensor') {

        echo '<momentTensor>';
          echo quakeml_element('doubleCouple', 'percent-double-couple', $props);
          echo quakeml_element('scalarMoment/value', 'scalar-moment', $props);
          echo quakeml_element('methodID', 'beachball-type', $props);

          if (isset($props['derived-latitude'])) {
            $derivedOriginID = $publicID . '/origin';
            echo '<derivedOriginID>' . $derivedOriginID . '</derivedOriginID>';
          }
          if (isset($props['derived-magnitude'])) {
            $derivedMagnitudeID = $publicID . '/magnitude';
            echo '<derivedMagnitudeID>' . $derivedMagnitudeID . '</derivedMagnitudeID>';
          }

          echo '<tensor>';
          echo quakeml_element('mpp/value', 'tensor-mpp', $props);
          echo quakeml_element('mrp/value', 'tensor-mrp', $props);
          echo quakeml_element('mrr/value', 'tensor-mrr', $props);
          echo quakeml_element('mrt/value', 'tensor-mrt', $props);
          echo quakeml_element('mtp/value', 'tensor-mtp', $props);
          echo quakeml_element('mtt/value', 'tensor-mtt', $props);
          echo '</tensor>';

          echo quakeml_element('methodID', 'beachball-type', $props);
        echo '</momentTensor>';
      }

      echo '<evaluationMode>';
      if (isset($props["review-status"]) && strtoupper($props["review-status"]) == "REVIEWED") {
        echo 'manual';
      } else {
        echo 'automatic';
      }
      echo '</evaluationMode>';

      echo quakeml_creationinfo(
          isset($props['beachball-source']) ? $props['beachball-source'] : null, 
          $id->getUpdateTime(),
          isset($props['version']) ? $props['version'] : null
        );

    echo '</focalMechanism>';


    // mt derived origin 
    if ($derivedOriginID !== null) {
      echo '<origin publicID="' . $derivedOriginID . '">';
        echo quakeml_element('time/value', 'derived-eventtime', $props);
        echo quakeml_element('longitude/value', 'derived-longitude', $props);
        echo quakeml_element('latitude/value', 'derived-latitude', $props);
        if (isset($props["derived-depth"])) {
          echo '<depth>';
          echo '<value>' . ($props["derived-depth"]*1000) . '</value>';
          echo '</depth>';
        }
      echo '</origin>';
    }

    // mt derived magnitude
    if ($derivedMagnitudeID !== null) {
      echo '<magnitude publicID="' . $derivedMagnitudeID . '">';
        echo quakeml_element('mag/value', 'derived-magnitude', $props);
        echo quakeml_element('type', 'derived-magnitude-type', $props);
      echo '</magnitude>';
    }
  }



  if ($preferredOriginID !== null) {
    echo '<preferredOriginID>' . $preferredOriginID . '</preferredOriginID>';
  }

  if ($preferredMagnitudeID !== null) {
    echo '<preferredMagnitudeID>' . $preferredMagnitudeID . '</preferredMagnitudeID>';
  }

echo '</event>';

echo '</eventParameters>';
echo '</q:quakeml>';
