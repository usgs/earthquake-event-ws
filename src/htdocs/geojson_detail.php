<?php
if (!isset($TEMPLATE)) {
  $TITLE = 'GeoJSON Detail Format';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="css/feedPages.css"/>';

  include 'template.inc.php';
}
?>

<div class="feed-format">

    <h2>Description</h2>
    <p>
      GeoJSON Detail output includes detailed information about a single
      earthquake. This matches the <a href="geojson.php">GeoJSON Summary
      output for a single feature</a>, and includes an additional property
      &ldquo;products&rdquo;, with additional information from all
      contributors to an event.
    </p>
    <p>
      Links to GeoJSON Detail feeds are included in <a href="geojson.php">
      GeoJSON Summary</a> feeds as the feature property &ldquo;detail&rdquo;.
    </p>
    <p>
      This feed adheres to the USGS Earthquakes
      <a href="/earthquakes/feed/policy.php">Feed Lifecycle Policy</a>.
    </p>

    <h2>Usage</h2>
    <p>
      GeoJSON is intended to be used as a programatic interface for
      applications.
    </p>

    <h2>Output</h2>
    <pre><code class="geojson">{
  type: "Feature",
  properties: {
    <a href='/data/comcat/data-eventterms.php#mag'>mag</a>: Decimal,
    <a href='/data/comcat/data-eventterms.php#place'>place</a>: String,
    <a href='/data/comcat/data-eventterms.php#time'>time</a>: Long Integer,
    <a href='/data/comcat/data-eventterms.php#updated'>updated</a>: Long Integer,
    <a href='/data/comcat/data-eventterms.php#tz'>tz</a>: Integer,
    <a href='/data/comcat/data-eventterms.php#url'>url</a>: String,
    <a href='/data/comcat/data-eventterms.php#felt'>felt</a>:Integer,
    <a href='/data/comcat/data-eventterms.php#cdi'>cdi</a>: Decimal,
    <a href='/data/comcat/data-eventterms.php#mmi'>mmi</a>: Decimal,
    <a href='/data/comcat/data-eventterms.php#alert'>alert</a>: String,
    <a href='/data/comcat/data-eventterms.php#status(review)'>status</a>: String,
    <a href='/data/comcat/data-eventterms.php#tsunami'>tsunami</a>: Integer,
    <a href='/data/comcat/data-eventterms.php#sig'>sig</a>:Integer,
    <a href='/data/comcat/data-eventterms.php#net'>net</a>: String,
    <a href='/data/comcat/data-eventterms.php#code'>code</a>: String,
    <a href='/data/comcat/data-eventterms.php#ids'>ids</a>: String,
    <a href='/data/comcat/data-eventterms.php#sources'>sources</a>: String,
    <a href='/data/comcat/data-eventterms.php#types'>types</a>: String,
    <a href='/data/comcat/data-eventterms.php#nst'>nst</a>: Integer,
    <a href='/data/comcat/data-eventterms.php#dmin'>dmin</a>: Decimal,
    <a href='/data/comcat/data-eventterms.php#rms'>rms</a>: Decimal,
    <a href='/data/comcat/data-eventterms.php#gap'>gap</a>: Decimal,
    <a href='/data/comcat/data-eventterms.php#magType'>magType</a>: String,
    <a href="/data/comcat/data-eventterms.php#type">type</a>: String,
    products: {
      <a href="/data/comcat/data-eventterms.php#productType">&lt;productType&gt;</a>: [
        {
          <a href="/data/comcat/data-eventterms.php#product_id">id</a>: String,
          <a href="/data/comcat/data-eventterms.php#product_id">type</a>: String,
          <a href="/data/comcat/data-eventterms.php#product_id">code</a>: String,
          <a href="/data/comcat/data-eventterms.php#product_id">source</a>: String,
          <a href="/data/comcat/data-eventterms.php#product_id">updateTime</a>: Integer,
          <a href="/data/comcat/data-eventterms.php#product_status">status</a>: String,
          properties: {
            <a href="/data/comcat/data-eventterms.php#product_propertyName">&lt;key&gt;</a>: String,
            &hellip;
          },
          <a href="/data/comcat/data-eventterms.php#preferredWeight">preferredWeight</a>: Integer,
          contents: {
            <a href="/data/comcat/data-eventterms.php#product_content">&lt;path&gt;</a>: {
              <a href="/data/comcat/data-eventterms.php#product_content">contentType</a>: String,
              <a href="/data/comcat/data-eventterms.php#product_content">lastModified</a>: Long Integer,
              <a href="/data/comcat/data-eventterms.php#product_content">length</a>: Integer,
              <a href="/data/comcat/data-eventterms.php#product_content">url</a>: String
            },
            &hellip;
          }
        },
        &hellip;
      ],
      &hellip;
    }
  },
  geometry: {
    type: "Point",
    coordinates: [
      <a href='/data/comcat/data-eventterms.php#longitude'>longitude</a>,
      <a href='/data/comcat/data-eventterms.php#latitude'>latitude</a>,
      <a href='/data/comcat/data-eventterms.php#depth'>depth</a>
    ]
  },
  <a href='/data/comcat/data-eventterms.php#id'>id</a>: String
}</code></pre>
</div>
