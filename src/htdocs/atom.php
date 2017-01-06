<?php
if (!isset($TEMPLATE)) {
  $TITLE = 'ATOM Syndication';
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
      This documentation goes over the details of the ATOM source response.
      Many browsers (or other feed readers) will render this format in a
      reader-specific manner. See the
      <a target="_blank" href="https://www.w3.org/2005/Atom">
        ATOM specification
      </a>
      or
      <a target="_blank" href="https://www.atomenabled.org/developers/">
        Atom Enabled
      </a>
      for general information.
    </p>
    <p>
      This feed adheres to the USGS Earthquakes
      <a href="/earthquakes/feed/policy.php">Feed Lifecycle Policy</a>.
    </p>

    <h2>Usage</h2>
    <p>
      To request this output format, use &ldquo;format=atom&rdquo;.
    </p>

    <h2>Output</h2>
    <p>Screenshot of the Magnitude Atom feed.</p>
    <img src="images/screenshot_atom.jpg" class="screenshot"
        alt="screenshot of the earthqauke Atom feed"/>
  </div>

  <div class="column two-of-five">
    <h2>Feeds</h2>
    <?php
      $format = 'atom';
      include_once 'inc/feedlinks.inc.php';
    ?>
  </div>
</div>
