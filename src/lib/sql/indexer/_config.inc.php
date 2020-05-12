<?php

// Ordered list of files to cat together. If the file extension is ".php", then
// the file will be run through PHP, thus providing scripted output. If the
// file extension is ".sql" then the file is just appened to the output.
$files = array(
  'event.sql',
  'productSummary.sql',
  'productSummaryLink.sql',
  'productSummaryProperty.sql',
  'extentSummary.sql'
);