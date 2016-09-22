<?php
if (!isset($TEMPLATE)) {
  $NAVIGATION = true;
  $HEAD = '
    <link rel="stylesheet" href="css/leaflet.css"/>
    <link rel="stylesheet" href="css/search.css"/>
  ';

  include '../conf/config.inc.php';

  if ($SCENARIO_MODE) {
    $TITLE = 'Search Scenario Earthquake Catalog';
  } else {
    $TITLE = 'Search Earthquake Catalog';
  }

  $FOOT = '
    <script>/*<![CDATA[*/
      var FDSN_HOST = \'' . $FDSN_HOST . '\';
      var FDSN_PATH = \'' . $FDSN_PATH . '\';
      var MAPLIST_PATH = \'' . $MAPLIST_PATH . '\';
    /*]]>*/</script>
    <script src="js/search.js"></script>
  ';

  // Use Earthquake level nav file
  include_once 'functions.inc.php';
  ob_start();
  include '_eq_navigation.inc.php';
  $NAVIGATION = ob_get_clean();

  include 'template.inc.php';
}
?>

<form method="get" action="<?php print $FDSN_HOST . $FDSN_PATH; ?>/query"
    id="fdsn-search-form" role="search">
  <header class="header" aria-label="Search form header">
    <small>
      Search results are limited to
      <?php echo number_format($MAX_SEARCH); ?> events. To get URL for a search,
        click the search button, then copy the URL from the browser address bar.
    </small>
    <?php
      if (!$SCENARIO_MODE) {
        echo '<ul>
          <li>
            <a href="' . $FDSN_HOST . $FDSN_PATH . '"
              target="_blank">Help</a>
          </li>
          <li>
            <a href="/data/comcat/">
              ANSS Comprehensive Earthquake Catalog (ComCat) Documentation
            </a>
          </li>
          <li>
            <a href="/earthquakes/browse/significant.php">
              Significant Earthquakes Archive
            </a>
          </li>
        </ul>';
      }
    ?>
  </header>

    <?php if ($SCENARIO_MODE) : ?>
      <p class="alert warning">
        You are currently searching the scenario catalog
      </p>
    <?php endif; ?>

  <h2 role="heading" id="search-basic">Basic Options</h2>
  <section class="search-basic row" aria-labelledby="search-basic">
    <?php include_once 'inc/search-basic.inc.php' ?>
  </section>

  <div class="toggle toggle-visible">
    <h2 class="label toggle-control" role="heading" id="search-advanced">
      Advanced Options
    </h2>
    <section class="search-advanced" aria-labelledby="search-advanced">
      <?php include_once 'inc/search-advanced.inc.html' ?>
    </section>
  </div>

  <div class="toggle toggle-visible">
    <h2 class="toggle-control"role="heading" id="search-output">
      Output Options
    </h2>
    <section class="search-output row" aria-labelledby="search-output">
      <?php
        /* Note this include is PHP because it needs $MAX_SEARCH info */
        include_once 'inc/search-output.inc.php'
      ?>
    </section>
  </div>

  <?php
    if ($SCENARIO_MODE) {
      echo '<p class="alert warning">You are currently searching the scenario catalog</p>';
    }
  ?>

  <footer class="footer" aria-label="Search form footer">
    <button type="submit" id="fdsn-submit">Search</button>
    <span class="output-descriptor"></span>
    <div class="search-error"></div>
  </footer>
</form>
