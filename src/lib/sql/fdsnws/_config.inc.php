<?php

// Ordered list of files to cat together. If the file extension is ".php", then
// the file will be run through PHP, thus providing scripted output. If the
// file extension is ".sql" then the file is just appened to the output.
$files = array(
  'on_event_update_trigger.remove.sql', // Removes the trigger. Re-added later.
  'indexes.sql',
  'summarizeProductSummaryIsCurrent.sql',
  'currentProducts.sql',
  'preferredProduct.sql',
  'getEventIdBySourceAndCode.sql',
  'getEventIdByFullEventId.sql',
  'getProductProperty.sql',
  'getTsunamiLinkProduct.sql',
  'getEventLastModified.sql',
  'getEventProductSources.sql',
  'getEventProductTypes.sql',
  'getEventIds.sql',
  'getEventSummary.sql',
  'updateEventSummary.sql',
  'recreateEventSummary.sql',
  'summary_sql.php',
  'summarizeEventsUpdatedAfter.sql',
  'on_event_update_trigger.sql'
);
