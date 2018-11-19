<?php

include_once '../conf/feeds.inc.php';


// 604800 = 1 week
$AGE_SECONDS = 604800 * 1000;
$TITLE = 'USGS ShakeAlert Events, Past Week';


// build query
$query = new FDSNQuery();
$query->contributor = 'ew';
$query->format = 'geojson';
$query->includedeleted = true;
$query->resultTitle = $TITLE;
$query->starttime = (time() - $AGE_SECONDS) . '000';

// cache for 1 minute
$CACHE_MAXAGE = 60;
include $APP_DIR . '/lib/cache.inc.php';

// serve summary feed
$service = new FDSNEventWebService($fdsnIndex);
// increase limit for summary feeds
$service->serviceLimit = 30000;
try {
  $service->handleSummaryQuery($query);
} catch (Exception $e) {
  $service->error(503, $e->getMessage());
}
