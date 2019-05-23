<?php

//Entry point into product webservice

if (!isset($TEMPLATE)){
  //Include config

  try {
    include $APP_DIR . "../conf/feeds.inc.php";

    //Cache headers
    $CACHE_MAXAGE=60;
    include $APP_DIR . "/lib/cache.inc.php";

    $usingSite = false;

    //Create service and query
    $service = new ProductWebService($index);

    //Query service if paramaters are provided, else load page
    if (ProductWebService::existRequiredParams()){
      $service->query();
    } else {
      $usingSite = true;
    }

  } catch (Exception $e) { //Wrap up exceptions as HTTP errors
    //Handle errors exactly as in fdsn entrypoint
    if ($service !== null) {
      $service->error(503, $e->getMessage());
    } else {
      header('HTTP/1.0 503 Service Unavailable');
      print_r($e);
    }
  }

  if (!$usingSite) return;

  $TITLE = "API Documentation - Product Catalog";
  $NAVIGATION = true;

  $PRODUCT_URL = "/product.php";

  include "template.inc.php";

}

?>

<p>This web service allows for querying of the ComCat product database using key paramaters.</p>

<h2 id="url">URL</h2>
<p>
  <?php echo $HOST_URL_PREFIX . $PRODUCT_URL //Placeholder for eventual Product URL?> 
</p>

<h2 id="method">Methods</h2>
<dl class = "vertical">
  <dt>query</dt>
  <dd>
    to submit a data request. See the <a href="#parameters">parameters</a>
    section for supported url parameters.
  </dd>
  <dd>
    <ul class = "examples">
      <li>
        <a href="<?php echo $HOST_URL_PREFIX . $PRODUCT_URL; ?>?source=us&amp;type=geoserve&amp;code=us70003l6p&amp;updateTime=1557943644040">
              <?php echo $HOST_URL_PREFIX . $PRODUCT_URL; ?>?source=us&amp;type=geoserve&amp;code=us70003l6p&amp;updateTime=1557943644040</a>
      </li>
      <li>
        <a href="<?php echo $HOST_URL_PREFIX . $PRODUCT_URL; ?>?source=us&amp;type=geoserve&amp;code=us70003l6p">
              <?php echo $HOST_URL_PREFIX . $PRODUCT_URL; ?>?source=us&amp;type=geoserve&amp;code=us70003l6p</a>
      </li>
    </ul>
  </dd>
</dl>

<h2 id="parameters">Parameters</h2>
<p>
These parameters should be submitted as key=value pairs using the HTTP GET method. Source, type, and code are required parameters - any others are optional to refine the search.
</p>
<dl class = "vertical">
  <dt>source</dt>
  <dd>
    the organization that submitted the product.
  </dd>

  <dt>type</dt>
  <dd>
    the associated product type.
  </dd>

  <dt>code</dt>
  <dd>
    the product code assigned by the source.
  </dd>
  
  <dt>updatetime</dt>
  <dd>
    the updateTime for a desired product. If unspecified, the latest product is returned.
  </dd>
</dl>