<?php
if (!isset($TEMPLATE)) {
  $TITLE = 'Spreadsheet Format';
  $NAVIGATION = true;
  $HEAD = '<link rel="stylesheet" href="css/feedPages.css"/>';

  include '../conf/config.inc.php';
  include 'template.inc.php';
}
?>

<div class="row feed-format">
  <div class="column three-of-five">
    <h2>Description</h2>
    <p>
      CSV is a &ldquo;comma separated values&rdquo; ASCII text file.
      See the <a
          href="http://en.wikipedia.org/wiki/Comma-separated_values"
          target="_blank">wikipedia</a> for more information on this format.
    </p>
    <p>
      This feed adheres to the USGS Earthquakes
      <a href="/earthquakes/feed/policy.php">Feed Lifecycle Policy</a>.
    </p>

    <h3>Usage</h3>
    <p>
      A simple text format suitable for loading data into spreadsheet
      applications like Microsoft Excel&trade;. This is a good option for
      manual scientific analysis.
    </p>

    <h2>Output</h2>
    <p>
      Below are the fields included in the spreadsheet output:
    </p>
    <ul>
      <li><a href="glossary.php#time">time</a></li>
      <li><a href="glossary.php#latitude">latitude</a></li>
      <li><a href="glossary.php#longitude">longitude</a></li>
      <li><a href="glossary.php#depth">depth</a></li>
      <li><a href="glossary.php#mag">mag</a></li>
      <li><a href="glossary.php#magType">magType</a></li>
      <li><a href="glossary.php#nst">nst</a></li>
      <li><a href="glossary.php#gap">gap</a></li>
      <li><a href="glossary.php#dmin">dmin</a></li>
      <li><a href="glossary.php#rms">rms</a></li>
      <li><a href="glossary.php#net">net</a></li>
      <li><a href="glossary.php#id">id</a></li>
      <li><a href="glossary.php#updated">updated</a></li>
      <li><a href="glossary.php#place">place</a></li>
      <li><a href="glossary.php#type">type</a></li>
      <li><a href="glossary.php#locationSource">locationSource</a></li>
      <li><a href="glossary.php#magSource">magSource</a></li>
      <li><a href="glossary.php#horizontalError">horizontalError</a></li>
      <li><a href="glossary.php#depthError">depthError</a></li>
      <li><a href="glossary.php#magError">magError</a></li>
      <li><a href="glossary.php#magNst">magNst</a></li>
      <li><a href="glossary.php#status">status</a></li>
    </ul>

    <p>
      Screenshot of the spreadsheet format, loaded into Microsoft Excel&trade;.
    </p>
    <img src="images/screenshot_csv.jpg" class="screenshot"
        alt="screenshot of kml feed in google earth" />
  </div>

  <div class="column two-of-five">
    <h2>Feeds</h2>
    <?php
      $format = 'csv';
      include_once 'inc/feedlinks.inc.php';
    ?>
  </div>

</div>
