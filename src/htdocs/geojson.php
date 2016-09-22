<?php
if (!isset($TEMPLATE)) {
  $TITLE = 'GeoJSON Summary Format';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="css/feedPages.css"/>';

  include '../conf/config.inc.php';
  include 'template.inc.php';
}
?>

<div class="row feed-format">
  <div class="column three-of-five">
    <h3>Description</h3>
    <p>
      GeoJSON is a format for encoding a variety of geographic data structures.
      A GeoJSON object may represent a geometry, a feature, or a collection of
      features. GeoJSON uses the
      <a href="http://www.json.org/" target="_blank">JSON standard</a>.
      The GeoJSONP feed uses the same JSON response, but the GeoJSONP response
      is wrapped inside the function call, eqfeed_callback. See the
      <a href="http://www.geojson.org/" target="_blank">GeoJSON site</a>
      for more information.
    </p>
    <p>
      This feed adheres to the USGS Earthquakes
      <a href="/earthquakes/feed/policy.php">Feed Life Cycle Policy</a>.
    </p>

    <h3>Usage</h3>
    <p>
      GeoJSON is intended to be used as a programatic interface for
      applications.
    </p>

    <h3>Output</h3>
    <pre><code class="geojson">{
  type: "FeatureCollection",
  metadata: {
    <a href="/data/comcat/data-eventterms.php#metadata_generated">generated</a>: Long Integer,
    <a href="/data/comcat/data-eventterms.php#metadata_url">url</a>: String,
    <a href="/data/comcat/data-eventterms.php#metadata_title">title</a>: String,
    <a href="/data/comcat/data-eventterms.php#metadata_api">api</a>: String,
    <a href="/data/comcat/data-eventterms.php#metadata_count">count</a>: Integer,
    <a href="/data/comcat/data-eventterms.php#metadata_status">status</a>: Integer
  },
  bbox: [
    <a href="/data/comcat/data-eventterms.php#longitude">minimum longitude</a>,
    <a href="/data/comcat/data-eventterms.php#latitude">minimum latitude</a>,
    <a href="/data/comcat/data-eventterms.php#depth">minimum depth</a>,
    <a href="/data/comcat/data-eventterms.php#longitude">maximum longitude</a>,
    <a href="/data/comcat/data-eventterms.php#latitude">maximum latitude</a>,
    <a href="/data/comcat/data-eventterms.php#depth">maximum depth</a>
  ],
  features: [
    {
      type: "Feature",
      properties: {
        <a href="/data/comcat/data-eventterms.php#mag">mag</a>: Decimal,
        <a href="/data/comcat/data-eventterms.php#place">place</a>: String,
        <a href="/data/comcat/data-eventterms.php#time">time</a>: Long Integer,
        <a href="/data/comcat/data-eventterms.php#updated">updated</a>: Long Integer,
        <a href="/data/comcat/data-eventterms.php#tz">tz</a>: Integer,
        <a href="/data/comcat/data-eventterms.php#url">url</a>: String,
        <a href="/data/comcat/data-eventterms.php#detail">detail</a>: String,
        <a href="/data/comcat/data-eventterms.php#felt">felt</a>:Integer,
        <a href="/data/comcat/data-eventterms.php#cdi">cdi</a>: Decimal,
        <a href="/data/comcat/data-eventterms.php#mmi">mmi</a>: Decimal,
        <a href="/data/comcat/data-eventterms.php#alert">alert</a>: String,
        <a href="/data/comcat/data-eventterms.php#status">status</a>: String,
        <a href="/data/comcat/data-eventterms.php#tsunami">tsunami</a>: Integer,
        <a href="/data/comcat/data-eventterms.php#sig">sig</a>:Integer,
        <a href="/data/comcat/data-eventterms.php#net">net</a>: String,
        <a href="/data/comcat/data-eventterms.php#code">code</a>: String,
        <a href="/data/comcat/data-eventterms.php#ids">ids</a>: String,
        <a href="/data/comcat/data-eventterms.php#sources">sources</a>: String,
        <a href="/data/comcat/data-eventterms.php#types">types</a>: String,
        <a href="/data/comcat/data-eventterms.php#nst">nst</a>: Integer,
        <a href="/data/comcat/data-eventterms.php#dmin">dmin</a>: Decimal,
        <a href="/data/comcat/data-eventterms.php#rms">rms</a>: Decimal,
        <a href="/data/comcat/data-eventterms.php#gap">gap</a>: Decimal,
        <a href="/data/comcat/data-eventterms.php#magType">magType</a>: String,
        <a href="/data/comcat/data-eventterms.php#type">type</a>: String
      },
      geometry: {
        type: "Point",
        coordinates: [
          <a href="/data/comcat/data-eventterms.php#longitude">longitude</a>,
          <a href="/data/comcat/data-eventterms.php#latitude">latitude</a>,
          <a href="/data/comcat/data-eventterms.php#depth">depth</a>
        ]
      },
      <a href="/data/comcat/data-eventterms.php#id">id</a>: String
    },
    &hellip;
  ]
}</code></pre>
  </div>

  <div class="column two-of-five">
    <h3>Feeds</h3>
    <?php
      $format = 'geojson';
      include_once 'inc/feedlinks.inc.php';
    ?>
  </div>
</div>
