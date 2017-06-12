<?php

  include_once '../conf/config.inc.php';

  echo "<a href='/earthquakes/feed/' class='up-one-level'>" .
      "Feeds and Notifications</a>";

  print navGroup('Real-time Notifications',
    navItem('/ens/', 'Earthquake Notification Service') .
    navItem('/earthquakes/ted/', 'Tweet Earthquake Dispatch')
  );

  print navGroup('Real-time Feeds',
    navItem($FEED_URL . '/atom.php', 'ATOM') .
    navItem($FEED_URL . '/kml.php', 'KML') .
    navItem($FEED_URL . '/csv.php', 'Spreadsheet') .
    navItem($FEED_URL . '/quakeml.php', 'QuakeML') .
    navItem($FEED_URL . '/geojson.php', 'GeoJSON Summary') .
    navItem($FEED_URL . '/geojson_detail.php', 'GeoJSON Detail')

  );
  print navGroup('For Developers',
    navItem($FDSN_URL . '/', 'API Documentation - EQ Catalog') .
    navItem($FEED_URL . '/changelog.php', 'Change Log') .
    navItem('/earthquakes/feed/policy.php', 'Feed Lifecycle Policy') .
    navItem('https://github.com/usgs/devcorner', 'Developer\'s Corner') .
    navItem('/ws/', 'Web Services') .
    navItem('https://geohazards.usgs.gov/mailman/listinfo/realtime-feeds',
        'Mailing List-Announcements') .
    navItem('https://geohazards.usgs.gov/mailman/listinfo/realtime-feed-users',
        'Mailing List-Forum/Questions')
  );

?>
