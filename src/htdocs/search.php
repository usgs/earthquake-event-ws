<?php
if (!isset($TEMPLATE)) {
  $TITLE = 'Search Earthquake Archives';
  $NAVIGATION = true;
  $HEAD = '
    <link rel="stylesheet" href="css/leaflet.css"/>
    <link rel="stylesheet" href="css/search.css"/>
  ';

  include '../conf/config.inc.php';
  $FOOT = '
    <script>/*<![CDATA[*/
      var FDSN_HOST = \'' . $FDSN_HOST . '\';
      var FDSN_PATH = \'' . $FDSN_PATH . '\';
    /*]]>*/</script>
    <script src="js/search.js"></script>
  ';

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
    <ul>
      <li>
        <a href="<?php echo $FDSN_HOST . $FDSN_PATH; ?>"
          target="_blank">Help</a>
      </li>
      <li>
        <a href="/earthquakes/map/doc_aboutdata.php">
            About the ANSS Comprehensive Catalog and Important Caveats
        </a>
      </li>
    </ul>
  </header>

  <h2 role="heading" id="search-basic">Basic Options</h2>
  <section class="search-basic row" aria-labelledby="search-basic">
    <?php include_once 'inc/search-basic.inc.php' ?>
  </section>

  <div class="toggle toggle-visible row">
    <h2 class="label toggle-control" role="heading" id="search-advanced">Advanced Options</h2>
    <section class="search-advanced" aria-labelledby="search-advanced">
      <?php include_once 'inc/search-advanced.inc.html' ?>
    </section>
  </div>
  <div class="toggle toggle-visible">
    <h2 class="toggle-control"role="heading" id="search-output">Output Options</h2>
    <section class="search-output row" aria-labelledby="search-output">
      <?php
        /* Note this include is PHP because it needs $MAX_SEARCH info */
        include_once 'inc/search-output.inc.php'
      ?>
    </section>
  </div>
  <footer class="footer" aria-label="Search form footer">
    <button type="submit" id="fdsn-submit">Search</button>
    <span class="output-descriptor"></span>
    <div class="search-error"></div>
  </footer>
</form>
