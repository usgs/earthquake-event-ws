<?php

// Ordered list of files to cat together. If the file extension is ".php", then
// the file will be run through PHP, thus providing scripted output. If the
// file extension is ".sql" then the file is just appened to the output.
$files = array(
  'get_point.sql',
  'point_in_one_polygon.sql',
  'point_in_polygon.sql',
  'feplus.sql',
  'get_feregion.sql',
  'get_region_name.sql',
  'data.sql'
);