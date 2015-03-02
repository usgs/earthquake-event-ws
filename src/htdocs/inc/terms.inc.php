<?php

  $dateRanges = array(
    "hour" => array (
      "name" => "Past Hour",
      "help" => "Updated every 5 minutes.",
      "url" => "hour",
      'mags' => array('significant', '2.5', '1', 'all')
    ),

    "day" => array (
      "name" => "Past Day",
      "help" => "Updated every 5 minutes.",
      "url" => "day",
      'mags' => array('significant', '2.5', '1', 'all') // Hmm?
    ),

    "week" => array (
      "name" => "Past 7 Days",
      "help" => "Updated every 5 minutes.",
      "url" => "week",
      'mags' => array('significant', '4.5', '2.5', 'all') // Hmm?
    ),

    "month" => array (
      "name" => "Past 30 Days",
      "help" => "Updated every 15 minutes.",
      "url" => "month",
      'mags' => array('significant', '4.5', '2.5', 'all') // Hmm?
    )
  );

  // GeoJSON feeds are updated on a shorter interval
  $geojson = array(
    "hour" => array(
      "help" => "Updated every minute."
    ),

    "day" => array(
      "help" => "Updated every minute."
    ),

    "week" => array(
      "help" => "Updated every minute."
    ),

    "month" => array (
      "help" => "Updated every 15 minutes."
    )
  );

  $magRanges = array(
    "significant" => array (
      "name" => "Significant Earthquakes",
      "url" => "significant"
    ),

    "4.5" => array(
      "name" => "M4.5+ Earthquakes",
      "url" => "4.5"
    ),

    "2.5" => array(
      "name" => "M2.5+ Earthquakes",
      "url" => "2.5"
    ),

    "1" => array(
      "name" => "M1.0+ Earthquakes",
      "url" => "1.0"
    ),

    "all" => array(
      "name" => "All Earthquakes",
      "url" => "all"
    )
  );

?>
