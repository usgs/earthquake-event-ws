<?php
if (!isset($TEMPLATE)) {
	$TITLE = 'Earthquake Archive Search & URL Builder';
	$NAVIGATION = true;
	$HEAD = '
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

<header class="header" role="region" aria-label="Search form header">
	<p class="help">
		Search results are limited to
		<?php echo number_format($MAX_SEARCH); ?> events.
	</p>
	<ul>
		<li>
			<a href="<?php echo $FDSN_HOST . $FDSN_PATH; ?>"
					target="_blank">Help</a>
		</li>
		<li>
			<a href="/earthquakes/map/doc_aboutdata.php"
					>About ANSS Comprehensive Catalog</a>
		</li>
	</ul>
</header>

<section class="search-basic row" role="region"
		aria-labelledby="search-basic">
	<h2 role="heading" id="search-basic">Basic Options</h2>
	<?php include_once 'inc/search-basic.inc.php' ?>
</section>

<section class="search-advanced row" role="region"
		aria-labelledby="search-advanced">
	<h2 role="heading" id="search-advanced">Advanced Options</h2>
	<?php include_once 'inc/search-advanced.inc.html' ?>
</section>

<section class="search-output row" role="region"
		aria-labelledby="search-output">
	<h2 role="heading" id="search-output">Output Options</h2>
	<?php
		/* Note this include is PHP because it needs $MAX_SEARCH info */
		include_once 'inc/search-output.inc.php'
	?>
</section>

<footer class="footer" role="region" aria-label="Search form footer">
	<button type="submit" id="fdsn-submit">Search</button>
	<span class="output-descriptor"></span>
	<div class="search-error"></div>
</footer>

</form>