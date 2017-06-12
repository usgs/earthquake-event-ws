<?php

if($event == null ) {
  header('HTTP/1.0 404 Not Found');
  print '<h1>Event not found</h1>';
  return;
}

header('Content-Type: application/vnd.google-earth.kml+xml');
header('Content-Disposition: attachment; filename="' . $query->eventid . '.kml"');
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo '<kml xmlns="http://earth.google.com/kml/2.0">' .
    '<Document>' .
      '<name id="eventid">USGS Event (' . $query->eventid . ')</name>' .
      '<LookAt>' .
        '<longitude>' . $event->getLongitude() . '</longitude>' .
        '<latitude>' . $event->getLatitude() . '</latitude>' .
        '<range>500000</range>' .
      '</LookAt>' .
      '<NetworkLink>' .
        '<name>Details</name>' .
        '<refreshVisibility>0</refreshVisibility>' .
        '<Link>' .
          '<href>' .
            htmlentities(AbstractFeed::getEventDetailFeed($query->eventid, 'kmlraw')) .
          '</href>' .
          '<refreshMode>onInterval</refreshMode>' .
          '<refreshInterval>300</refreshInterval>' .
        '</Link>' .
      '</NetworkLink>' .
    '</Document>' .
  '</kml>';
