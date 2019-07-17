<?php

//Entry point into product webservice

if (!isset($TEMPLATE)) {
  //Include config

  try {
    include $APP_DIR . "../conf/feeds.inc.php";

    //Cache headers
    $CACHE_MAXAGE=60;
    include $APP_DIR . "/lib/cache.inc.php";

    //Create service and query
    $service = new ProductWebService($index, $HOST_URL_PREFIX . $PRODUCT_URL);

    //Query service if paramaters are provided, else load page
    if (!empty($_GET)){
      $service->query($_GET);
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

  $TITLE = "API Documentation - Product Catalog";
  $NAVIGATION = true;

  include "template.inc.php";

}

?>

<p>This web service allows for querying of the ComCat product database using key paramaters.</p>

<h2 id="url">URL</h2>
<p>
  <?php echo $HOST_URL_PREFIX . $PRODUCT_URL;?> 
</p>

<h2 id="examples">Examples</h2>
<ul>
    <li>
      <a href="<?php echo $HOST_URL_PREFIX . $PRODUCT_URL; ?>?source=us&amp;type=geoserve&amp;code=us70003l6p&amp;updateTime=1557943644040">
            <?php echo $HOST_URL_PREFIX . $PRODUCT_URL; ?>?source=us&amp;type=geoserve&amp;code=us70003l6p&amp;updateTime=1557943644040</a>
    </li>
    <li>
      <a href="<?php echo $HOST_URL_PREFIX . $PRODUCT_URL; ?>?minupdatetime=07-01-2019&amp;maxupdatetime=07-02-2019&amp;minlongitude=-109&amp;maxlongitude=-102&amp;minlatitude=37&amp;maxlatitude=41">
            <?php echo $HOST_URL_PREFIX . $PRODUCT_URL; ?>?minupdatetime=07-01-2019&amp;maxupdatetime=07-02-2019&amp;minlongitude=-109&amp;maxlongitude=-102&amp;minlatitude=37&amp;maxlatitude=41</a>
    </li>
</ul>

<h2 id="parameters">Parameters</h2>
<p>
These parameters should be submitted as key=value pairs using the HTTP GET method.
</p>

  <h3>Detail Service</h3>
  <p>The detail service can return a detail on a product given the following parameters:</p>
  <table>
    <thead>
      <tr>
        <th>Parameter</th>
        <th>Type</th>
        <th>Required?</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>source</code></td>
        <td>String</td>
        <td>YES</td>
        <td>The product-submitting entity.</td>
      </tr>
      <tr>
        <td><code>type</code></td>
        <td>String</td>
        <td>YES</td>
        <td>The associated product type.</td>
      </tr>
      <tr>
        <td><code>code</code></td>
        <td>String</td>
        <td>YES</td>
        <td>Product code, assigned by the source.</td>
      </tr>
      <tr>
        <td><code>updatetime</code></td>
        <td>String</td>
        <td>NO</td>
        <td>The update time of the desired product. If unspecified, latest is provided.</td>
      </tr>
    <tbody>
  </table>

  <h3>Time</h3>
  <p>All times are included as ISO-8601 Date/Time format. Examples follow:</p>
  <ul>
    <li><em><?php echo gmdate('Y-m-d'); ?></em> : Today's date, expressed at 00:00:00 UTC.</li>
    <li><em><?php echo gmdate('Y-m-d\TH:i:s'); ?></em> : Today's date, expressed at the current time with the implicit UTC timezone.</li>
    <li><em><?php echo gmdate('c'); ?></em> : Today's date, expressed at the current time with an explicit timezone attached.</li>
  </ul>
  <table>
    <thead>
      <tr>
        <th>Parameter</th>
        <th>Type</th>
        <th>Default</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>minupdatetime</code></td>
        <td>String</td>
        <td>0 BCE</td>
        <td>Lower bound on product updateTime during search.</td>
      </tr>
      <tr>
        <td><code>maxupdatetime</code></td>
        <td>String</td>
        <td>NOW</td>
        <td>Upper bound on product updateTime during search.</td>
      </tr>
      <tr>
        <td><code>time</code></td>
        <td>String</td>
        <td>NONE</td>
        <td>Searches for products with a time range containing this time.</td>
      </tr>
      <tr>
        <td><code>starttime</code></td>
        <td>String</td>
        <td>0 BCE</td>
        <td>Lower bound on associated eventTime during search.</td>
      </tr>
      <tr>
        <td><code>endtime</code></td>
        <td>String</td>
        <td>NOW</td>
        <td>Upper bound on associated eventTime during search.</td>
      </tr>
    </tbody>
  </table>

  <h3>Location</h3>
  <p>Provided as either a point or bounds to search.</p>
  <table>
    <thead>
      <tr>
        <th>Parameter</th>
        <th>Type</th>
        <th>Default</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>latitude</code></td>
        <td>Decimal, [-90,90]</td>
        <td>NONE</td>
        <td>Searches for products with a latitude range containing this latitude</td>
      </tr>
      <tr>
        <td><code>longitude</code></td>
        <td>Decimal, [-180,180]</td>
        <td>NONE</td>
        <td>Searches for products with a longitude range containing this longitude</td>
      </tr>
      <tr>
        <td><code>minlatitude</code></td>
        <td>Decimal, [-90,90]</td>
        <td>-90</td>
        <td>Lower bound on associated eventLatitude during search.</td>
      </tr>
      <tr>
        <td><code>maxlatitude</code></td>
        <td>Decimal, [-90,90]</td>
        <td>90</td>
        <td>Upper bound on associated eventLatitude during search.</td>
      </tr>
      <tr>
        <td><code>minlongitude</code></td>
        <td>Decimal, [-360,360]</td>
        <td>-180</td>
        <td>Lower bound on associated eventLongitude during search. May be less than -180 when specifying searches crossing the dateline.</td>
      </tr>
      <tr>
        <td><code>maxlongitude</code></td>
        <td>Decimal, [-360,360]</td>
        <td>180</td>
        <td>Upper bound on associated eventLongitude during search. May be greater than 180 when specifying searches crossing the dateline.</td>
      </tr>
    </tbody>
  </table>

  <h3>Properties</h3>
  <p>Some product properties are searchable.</p>
  <table>
    <thead>
      <tr>
        <th>Parameter</th>
        <th>Type</th>
        <th>Default</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>minmagnitude</code></td>
        <td>Decimal, [0,10]</td>
        <td>0</td>
        <td>Lower bound on associated magnitude during search.</td>
      </tr>
      <tr>
        <td><code>maxmagnitude</code></td>
        <td>Decimal, [0,10]</td>
        <td>10</td>
        <td>Upper bound on associated magnitude during search.</td>
      </tr>
    </tbody>
  </table>

  <h3>Other</h3>
  <table>
    <thead>
      <tr>
        <th>Parameter</th>
        <th>Type</th>
        <th>Default</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>includedeleted</code></td>
        <td>Boolean</td>
        <td>FALSE</td>
        <td>Includes previously deleted products in the query.</td>
      </tr>
      <tr>
        <td><code>includesuperseded</code></td>
        <td>Boolean</td>
        <td>FALSE</td>
        <td>Includes previous versions of products in the query.</td>
      </tr>
      <tr>
        <td><code>orderby</code></td>
        <td>String</td>
        <td>time, descending</td>
        <td>Changes the output order. Options are <code>time</code>,<code>time-asc</code>,<code>id</code>,<code>id-asc</code>,
          <code>magnitude</code>,<code>magnitude-asc</code>. Each defaults to descending order, unless the <code>-asc</code> option is provided.
        </td>
      </tr>
      <tr>
        <td><code>limit</code></td>
        <td>Integer</td>
        <td>None</td>
        <td>Limits the output to a fixed number of products.</td>
      </tr>
      <tr>
        <td><code>offset</code></td>
        <td>Integer</td>
        <td>None</td>
        <td>Offsets the returned results by a fixed number of products. Often used in conjunction with <code>limit</code>.</td>
      </tr>
    </tbody>
  </table>