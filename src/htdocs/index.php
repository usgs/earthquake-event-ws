<?php
if (!isset($TEMPLATE)) {
  include_once '../conf/config.inc.php';
  $TITLE = 'Feeds &amp; Notifications';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="css/index.css"/>';
  include 'template.inc.php';
}
?>


<div class="row">

  <section class="one-of-two column">

    <h2>Real-time Feeds</h2>

    <ul class="no-style">
      <li>
        <h3 class="title">
          <a href="atom.php">ATOM Syndication</a>
        </h3>
        <div class="row">
          <img src="images/atom.png" class="column mobile-one-of-four"
              alt="ATOM feed icon" />
          <div class="column mobile-three-of-four">
            A basic syndication format supported by a variety of feed readers.
            This is a good option for casually subscribing to earthquake
            information.
          </div>
        </div>
      </li>

      <li>
        <h3 class="title">
          <a href="kml.php">Google Earth&trade; KML</a>
        </h3>
        <div class="row">
          <img src="images/kml.png" class="column mobile-one-of-four"
              alt="Google Earth icon" />
          <div class="column mobile-three-of-four">
            This feed format is suitable for loading into applications that
            understand Keyhole Markup Language (KML) such as Google
            Earth&trade;
          </div>
        </div>
      </li>

      <!--
        Icon Source: www.iconspedia.com
        Author: Tempest, http://tempest.deviantart.com/
        License: CC Attribution Non-Commercial No Derivatives
      -->

      <li>
        <h3 class="title">
          <a href="csv.php">Spreadsheet Format</a>
        </h3>
        <div class="row">
          <img src="images/csv.png" class="column mobile-one-of-four"
              alt="Spreadsheet icon" />
          <div class="column mobile-three-of-four">
            A simple text format suitable for loading data into spreadsheet
            applications like Microsoft Excel&trade;. This is a good option for
            manual scientific analysis.
          </div>
        </div>
      </li>

      <li>
        <h3 class="title">
          <a href="quakeml.php">QuakeML</a>
        </h3>
        <div class="row">
          <img src="images/quakeml.png" class="column mobile-one-of-four"
              alt="QuakeML icon" />
          <div class="column mobile-three-of-four">
            A flexible, extensible and modular XML representation of
            seismological data which is intended to cover a broad range of
            fields of application in modern seismology.
          </div>
        </div>
      </li>
    </ul>

  </section>


  <section class="one-of-two column">

    <h2>Real-time Notifications</h2>

    <ul class="no-style">
      <li>
        <h3 class="title">
          <a href="https://sslearthquake.usgs.gov/ens/">
            Earthquake Notification Service</a>
        </h3>
        <div class="row">
          <img src="images/ens-x2.png" class="column mobile-one-of-four"
              alt="Earthquake Notification Service icon" />
          <div class="column mobile-three-of-four">
            The Earthquake Notification Service (ENS) is a free service that
            sends you automated notifications to your email or cell phone when
            earthquakes happen.
          </div>
        </div>
      </li>

      <li>
        <h3 class="title">
          <a href="/earthquakes/ted/">Tweet Earthquake Dispatch</a>
        </h3>
        <div class="row">
          <img src="images/ted.png" class="column mobile-one-of-four"
              alt="Tweet Earthquake Dispatch icon" />
          <div class="column mobile-three-of-four">
            Tweet Earthquake Dispatch (TED) offers two Twitter accounts. On
            average, each account will produce about one tweet per day.
          </div>
        </div>
      </li>
    </ul>

  </section>

</div>


<h2>For Developers</h2>

<div class="row">

  <div class="one-of-two column">
    <ul>
      <li>
        <a href="<?php print $FDSN_URL;?>/">API Documentation - EQ Catalog</a>
      </li>
      <li>
        <a href="<?php print $FEED_URL;?>/geojson.php">GeoJSON Summary Feed</a>
      </li>
      <li>
        <a href="<?php print $FEED_URL;?>/geojson_detail.php">
            GeoJSON Detail Feed</a>
      </li>
      <li>
        <a href="<?php print $FEED_URL;?>/changelog.php">Change Log</a>
      </li>
      <li>
        <a href="<?php print $FEED_URL;?>/../policy.php">
            Feed Lifecycle Policy</a>
      </li>
    </ul>
  </div>

  <div class="one-of-two column">
    <ul>
      <li>
        <a href="https://github.com/usgs/devcorner">Developers Corner</a>
      </li>
      <li>
        <a href="<?php print $FEED_URL;?>/glossary.php">
            Glossary - Earthquake Catalog Data Terms</a>
      </li>
      <li>
        <a href="https://geohazards.usgs.gov/mailman/listinfo/realtime-feeds">
            Mailing List - Announcements</a>
      </li>
      <li>
        <a href="https://geohazards.usgs.gov/mailman/listinfo/realtime-feed-users">
            Mailing List - Forum/Questions</a>
      </li>
    </ul>
  </div>

</div>