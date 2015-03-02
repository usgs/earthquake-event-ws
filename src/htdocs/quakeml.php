<?php
if (!isset($TEMPLATE)) {
  $TITLE = 'QuakeML Summary Format';
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
      This documentation provides information on the details of the QuakeML
      source response. For more information please see the
      <a href="https://quake.ethz.ch/quakeml/">QuakeML website</a> for a better
      understanding of the specification.
    </p>
    <p>
      This feed adheres to the USGS Earthquakes
      <a href="/earthquakes/feed/policy.php">Feed Life Cycle Policy</a>.
    </p>

    <h2>Usage</h2>
    <p>
      To request this output format, use &ldquo;format=quakeml&rdquo;.
    </p>

    <h2>Output</h2>
    <p>
      For a description of the QuakeML format, view the
      <a href="https://quake.ethz.ch/quakeml/Documents" target="_blank">
          Documents</a> section of the QuakeML website.
      <a href="https://quake.ethz.ch/quakeml/Documents">
        <img class="screenshot" src="images/screenshot_quakeml.png"
            title="QuakeML UML class diagram" alt="QuakeML UML class diagram"/>
      </a>
    </p>
  </div>

  <div class="column two-of-five">
    <h2>Feeds</h2>
    <?php
      $format = 'quakeml';
      include_once 'inc/feedlinks.inc.php';
    ?>
  </div>
</div>
