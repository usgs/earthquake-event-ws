<?php

// entry point into the FDSN Event Webservice

if (!isset($TEMPLATE)) {
  $service = null;

  try {
    // configuration
    include_once '../conf/feeds.inc.php';

    // caching headers
    // per llastowka: cache for 60 seconds regardless of age
    $CACHE_MAXAGE = 60;
    include $APP_DIR . '/lib/cache.inc.php';

    $service = new FDSNEventWebService($fdsnIndex, $CONFIG);
    $usage = false;

    $method = param('method');
    if ($method === 'query') {
      $service->query();
    } else if ($method === 'catalogs') {
      $service->catalogs();
    } else if ($method === 'contributors') {
      $service->contributors();
    } else if ($method === 'count') {
      $service->count();
    } else if ($method === 'version') {
      $service->version();
    } else if ($method === 'application.wadl') {
      $service->wadl();
    } else if ($method === 'application.json') {
      $service->application_json();
    } else {
      $usage = true;
    }

  } catch (Exception $e) {
    if ($service !== null) {
      $service->error(503, $e->getMessage());
    } else {
      header('HTTP/1.0 503 Service Unavailable');
      print_r($e);
    }
  }

  if (!$usage) {
    return;
  }


  $TITLE = 'API Documentation - Earthquake Catalog';
  $NAVIGATION = true;

  include 'template.inc.php';
}
?>

<p>
  This is an implementation of the
  <a href="http://www.fdsn.org/webservices/FDSN-WS-Specifications-1.0.pdf">
      FDSN Event Web Service Specification</a>,
  and allows custom searches for earthquake information using a variety of
  parameters.
</p>
<p class="alert info">Please note that automated applications should use
  <a href="<?php echo $FEED_HOST . $FEED_PATH; ?>/geojson.php">
      Real-time GeoJSON Feeds</a>
  for displaying earthquake information whenever possible, as they will have
  the best performance and availability for that type of information.
</p>


<h2 id="url">URL</h2>
<p>
  <?php
    echo $HOST_URL_PREFIX . $FDSN_PATH;
  ?>/[<a href="#methods">METHOD</a>[?<a href="#parameters">PARAMETERS</a>]]
</p>


<h2 id="methods">Methods</h2>
<dl class="vertical">
  <dt>application.json</dt>
  <dd>
      request known enumerated parameter values for the interface.
  </dd>
  <dd>
    <ul class="examples">
      <li>
        <a href="<?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/application.json">
            <?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/application.json</a>
      </li>
    </ul>
  </dd>
  <dt>application.wadl</dt>
  <dd>
    request <abbr title="Web Application Description Language">WADL</abbr>
    for the interface.
  </dd>
  <dd>
    <ul class="examples">
      <li>
        <a href="<?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/application.wadl">
            <?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/application.wadl</a>
      </li>
    </ul>
  </dd>
  <dt>catalogs</dt>
  <dd>request available catalogs.</dd>
  <dd>
    <ul class="examples">
      <li>
        <a href="<?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/catalogs">
            <?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/catalogs</a>
      </li>
    </ul>
  </dd>
  <dt>contributors</dt>
  <dd>
    request available contributors
  </dd>
  <dd>
    <ul class="examples">
      <li>
        <a href="<?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/contributors">
            <?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/contributors</a>
      </li>
    </ul>
  </dd>
  <dt>count</dt>
  <dd>
    to perform a count on a data request. Count uses the same
    <a href="#parameters">parameters</a> as the query method, and is
    availablein these <a href="#format">formats</a>: plain text (default),
    geojson, and xml.
  </dd>
  <dd>
    <ul>
      <li><a href="<?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/count?format=geojson">
          <?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/count?format=geojson</a></li>
      <li><a href="<?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/count?starttime=2014-01-01&amp;endtime=2014-01-02">
          <?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/count?starttime=2014-01-01&amp;endtime=2014-01-02</a></li>
    </ul>
  </dd>
  <dt>query</dt>
  <dd>
    to submit a data request. See the <a href="#parameters">parameters</a>
    section for supported url parameters.
  </dd>
  <dd>
    <ul class="examples">
      <li>
        <a href="<?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/query?format=geojson&amp;starttime=2014-01-01&amp;endtime=2014-01-02">
            <?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/query?format=geojson&amp;starttime=2014-01-01&amp;endtime=2014-01-02</a>
      </li>
      <li>
        <a href="<?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/query?format=xml&amp;starttime=2014-01-01&amp;endtime=2014-01-02&amp;minmagnitude=5">
            <?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/query?format=xml&amp;starttime=2014-01-01&amp;endtime=2014-01-02&amp;minmagnitude=5</a>
      </li>
    </ul>
  </dd>
  <dt>version</dt>
  <dd>request full service version number</dd>
  <dd>
    <ul class="examples">
      <li>
        <a href="<?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/version">
            <?php echo $HOST_URL_PREFIX . $FDSN_PATH; ?>/version</a>
      </li>
    </ul>
  </dd>
</dl>


<h2 id="parameters">Query method Parameters</h2>
<p>
  These parameters should be submitted as key=value pairs using the HTTP GET
  method and may not be specified more than once; if a parameter is submitted
  multiple times the result is undefined.
</p>


<h3>Formats</h3>
<p>If no format is specified <em>quakeml</em> will be returned by default.</p>
<div class="horizontal-scrolling">
  <table>
    <thead>
      <tr>
        <th>parameter</th>
        <th>type</th>
        <th>default</th>
        <th>description</th>
      </tr>
    </thead>
    <tbody>
      <tr id="format">
        <td><code>format</code></td>
        <td>String</td>
        <td>quakeml</td>
        <td>
          Specify the output format.

          <dl class="options vertical">
            <dt><code>format=csv</code></dt>
            <dd>
              Response format is
              <a href="<?php echo $FEED_HOST . $FEED_PATH; ?>/csv.php">CSV</a>.
              Mime-type is &ldquo;text/csv&rdquo;.
            </dd>
            <dt><code><a href="#format-geojson">format=geojson</a></code></dt>
            <dd>
              Response format is
              <a href="<?php echo $FEED_HOST . $FEED_PATH; ?>/geojson.php">
              GeoJSON</a>. Mime-type is &ldquo;application/json&rdquo;.
            </dd>
            <dt><code><a href="#format-kml">format=kml</a></code></dt>
            <dd>
              Response format is
              <a href="<?php echo $FEED_HOST . $FEED_PATH; ?>/kml.php">KML</a>.
              Mime-type is &ldquo;vnd.google-earth.kml+xml&rdquo;.
            </dd>
            <dt><code><a href="#format-xml">format=quakeml</a></code></dt>
            <dd>
              Alias for "xml" format.
            </dd>
            <dt><code><a href="#format-text">format=text</a></code></dt>
            <dd>
              Response format is plain text. Mime-type is
              &ldquo;text/plain&rdquo;.
            </dd>
            <dt><code><a href="#format-xml">format=xml</a></code></dt>
            <dd>
              The xml format is dependent upon the request <em>method</em> used.
            </dd>
          </dl>
        </td>
      </tr>
    </tbody>
  </table>
</div>


<h4 id="format-geojson">format=geojson</h4>
<p>
  When <code>format=geojson</code> is defined there are additional parameters
  that can be specified that control how the geojson output is generated. The
  additional web service parameters are:
</p>
<ul>
  <li><a href="#callback">callback</a></li>
  <li><a href="#jsonerror">jsonerror</a></li>
</ul>


<h4 id="format-kml">format=kml</h4>
<p>
  When <code>format=kml</code> is defined there are additional parameters
  that can be specified that control how the KML output is generated. The
  additional web service parameters are:
</p>
<ul>
  <li><a href="#kmlanimated">kmlanimated</a></li>
  <li><a href="#kmlcolorby">kmlcolorby</a></li>
</ul>


<h4 id="format-text">format=text</h4>
<p>
  This format is only available for the <code>count</code>,
  <code>query</code>, and <code>version</code> methods.
</p>


<h4 id="format-xml">format=xml</h4>
<p>The xml format is dependent upon the request <code>method</code> used.</p>
<ul>
  <li>
    <code>method=query</code><br/>
    Response format is <a href="http://www.quakeml.org/">Quakeml 1.2</a>.
    Mime-type is "application/xml".
  </li>
  <li>
    <code>method=count</code><br/>
    Response format is xml. Mime-type is &ldquo;application/xml&rdquo;.
  </li>
</ul>


<h3>Time</h3>
<p>
  All times use ISO8601 Date/Time format. Unless a timezone is specified, UTC
  is assumed. Examples:
</p>
<ul>
  <li>
    <em><?php echo gmdate('Y-m-d'); ?></em>,
    Implicit UTC timezone, and time at start of the day (00:00:00)
  </li>
  <li>
    <em><?php echo gmdate('Y-m-d\TH:i:s'); ?></em>,
    Implicit UTC timezone.
  </li>
  <li>
    <em><?php echo gmdate('c'); ?></em>,
    Explicit timezone.
  </li>
</ul>
<div class="horizontal-scrolling">
  <table>
    <thead>
      <tr>
        <th>parameter</th>
        <th>type</th>
        <th>default</th>
        <th>description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><code>endtime</code></td>
        <td>String</td>
        <td>present time</td>
        <td>
          Limit to events on or before the specified end time.
          <small>
            NOTE: All times use ISO8601 Date/Time format. Unless a timezone is specified, UTC is assumed.
          </small>
        </td>
      </tr>
      <tr>
        <td><code>starttime</code></td>
        <td>String</td>
        <td>NOW -
        <?php echo ($DEFAULT_MAXEVENTAGE/86400); ?> days</td>
        <td>
          Limit to events on or after the specified start time.
          <small>
            NOTE: All times use ISO8601 Date/Time format. Unless a timezone is specified, UTC is assumed.
          </small>
        </td>
      </tr>
      <tr>
        <td><code>updatedafter</code></td>
        <td>String</td>
        <td>null</td>
        <td>
          Limit to events updated after the specified time.
          <small>
            NOTE: All times use ISO8601 Date/Time format. Unless a timezone is specified, UTC is assumed.
          </small>
        </td>
      </tr>
    </tbody>
  </table>
</div>


<h3>Location</h3>
<p>
  Requests that use both rectangle and circle will return the intersection,
  which may be empty, use with caution.
</p>


<h4>Rectangle</h4>
<p>
  Requests may use any combination of these parameters.
</p>
<div class="horizontal-scrolling">
  <table>
    <thead>
      <tr>
        <th>parameter</th>
        <th>type</th>
        <th>default</th>
        <th>description</th>
      </tr>
    </thead>
    <tbody>
      <tr id="minlatitude">
        <td><code>minlatitude</code></td>
        <td>Decimal <span class="range">[-90,90] degrees</span></td>
        <td>-90</td>
        <td>
          Limit to events with a latitude larger than the specified minimum.
          <small>
            NOTE: min values must be less than max values.
          </small>
        </td>
      </tr>
      <tr id="minlongitude">
        <td><code>minlongitude</code></td>
        <td>Decimal <span class="range">[-360,360] degrees</span></td>
        <td>-180</td>
        <td>
          Limit to events with a longitude larger than the specified minimum.
          <small>
            NOTE: rectangles may cross the date line by using a minlongitude &lt;
            -180 or maxlongitude &gt; 180.
          </small>
          <small>
            NOTE: min values must be less than max values.
          </small>
        </td>
      </tr>
      <tr id="maxlatitude">
        <td><code>maxlatitude</code></td>
        <td>Decimal <span class="range">[-90,90] degrees</span></td>
        <td>90</td>
        <td>
          Limit to events with a latitude smaller than the specified maximum.
          <small>
            NOTE: min values must be less than max values.
          </small>
        </td>
      </tr>
      <tr id="maxlongitude">
        <td><code>maxlongitude</code></td>
        <td>Decimal <span class="range">[-360,360] degrees</span></td>
        <td>180</td>
        <td>
          Limit to events with a longitude smaller than the specified maximum.
          <small>
            NOTE: rectangles may cross the date line by using a minlongitude &lt;
            -180 or maxlongitude &gt; 180.
          </small>
          <small>
            NOTE: min values must be less than max values.
          </small>
        </td>
      </tr>
    </tbody>
  </table>
</div>


<h4>Circle</h4>
<p>
  Requests must include all of latitude, longitude, and maxradius to perform a
  circle search.
</p>
<div class="horizontal-scrolling">
  <table>
    <thead>
      <tr>
        <th>parameter</th>
        <th>type</th>
        <th>default</th>
        <th>description</th>
      </tr>
    </thead>
    <tbody>
      <tr id="latitude">
        <td><code>latitude</code></td>
        <td>Decimal <span class="range">[-90,90] degrees</span></td>
        <td>null</td>
        <td>
          Specify the latitude to be used for a radius search.
        </td>
      </tr>
      <tr id="longitude">
        <td><code>longitude</code></td>
        <td>Decimal <span class="range">[-180,180] degrees</span></td>
        <td>null</td>
        <td>
          Specify the longitude to be used for a radius search.
        </td>
      </tr>
      <tr id="maxradius">
        <td><code>maxradius</code></td>
        <td>Decimal <span class="range">[0, 180] degrees</span></td>
        <td>180</td>
        <td>
          Limit to events within the specified maximum number of degrees from the
          geographic point defined by the latitude and longitude parameters.

          <small>
            NOTE: This option is mutually exclusive with <a href="#maxradiuskm">
            maxradiuskm</a> and specifying both will result in an error.
          </small>
        </td>
      </tr>
      <tr id="maxradiuskm">
        <td><code>maxradiuskm</code></td>
        <td>Decimal <span class="range">[0, 20001.6] km</span></td>
        <td>20001.6</td>
        <td>
          Limit to events within the specified maximum number of kilometers from
          the geographic point defined by the latitude and longitude parameters.

          <small>
            NOTE: This option is mutually exclusive with <a href="#maxradius">
            maxradius </a> and specifying both will result in an error.
          </small>
        </td>
      </tr>
    </tbody>
  </table>
</div>


<h3>Other</h3>
<div class="horizontal-scrolling">
  <table>
    <thead>
      <tr>
        <th>parameter</th>
        <th>type</th>
        <th>default</th>
        <th>description</th>
      </tr>
    </thead>
    <tbody>
      <tr id="catalog">
        <td><code>catalog</code></td>
        <td>String</td>
        <td>null</td>
        <td>
          Limit to events from a specified catalog. Use the <a href="./catalogs">Catalogs Method</a> to find available catalogs.
          <small>
            NOTE: when catalog and contributor are omitted, the most preferred
            information from any catalog or contributor for the event is returned.
          </small>
        </td>
      </tr>
      <tr id="contributor">
        <td><code>contributor</code></td>
        <td>String</td>
        <td>null</td>
        <td>
          Limit to events contributed by a specified contributor. Use the
          <a href="./contributors">Contributors Method</a> to find available
          contributors.
          <small>
            NOTE: when catalog and contributor are omitted, the most preferred
            information from any catalog or contributor for the event is returned.
          </small>
        </td>
      </tr>
      <tr id="eventid">
        <td><code>eventid</code></td>
        <td>String</td>
        <td>null</td>
        <td>
          Select a specific event by ID; event identifiers are data center
          specific.
          <small>
            NOTE: Selecting a specific event implies includeallorigins,
            includeallmagnitudes, and, additionally, associated moment tensor and
            focal-mechanisms are included.
          </small>
        </td>
      </tr>
      <tr id="includeallmagnitudes">
        <td><code>includeallmagnitudes</code></td>
        <td>Boolean</td>
        <td>false</td>
        <td>
          Specify if all magnitudes for the event should be included, default is data
          center dependent but is suggested to be the preferred magnitude only.
          <small>
            NOTE: because magnitudes and origins are strongly associated, this
            parameter is interchangeable with includeallmagnitudes
          </small>
        </td>
      </tr>
      <tr id="includeallorigins">
        <td><code>includeallorigins</code></td>
        <td>Boolean</td>
        <td>false</td>
        <td>
          Specify if all origins for the event should be included, default is data
          center dependent but is suggested to be the preferred origin only.
          <small>
            NOTE: because magnitudes and origins are strongly associated,
            this parameter is interchangable with includeallmagnitudes
          </small>
        </td>
      </tr>
      <tr id="includearrivals">
        <td><code>includearrivals</code></td>
        <td>Boolean</td>
        <td>false</td>
        <td>
          Specify if phase arrivals should be included.
          <small>NOTE: NOT CURRENTLY IMPLEMENTED</small>
        </td>
      </tr>
      <tr id="includedeleted">
        <td><code>includedeleted</code></td>
        <td>Boolean</td>
        <td>false</td>
        <td>
          Specify if deleted products and events should be included.

          <p>
          <small>
            Deleted events otherwise return the HTTP status
            <code>409 Conflict</code>.
          </small>
          </p>

          <p>
          <small>
            NOTE: Only supported by the <code>csv</code> and <code>geojson</code> formats,
            which include <code>status</code>.
          </small>
          </p>
        </td>
      </tr>
      <tr id="includesuperseded">
        <td><code>includesuperseded</code></td>
        <td>Boolean</td>
        <td>false</td>
        <td>
          Specify if superseded products should be included.
          This also includes all deleted products, and is mutually exclusive to
          the <a href="#includedeleted">includedeleted</a> parameter.
          <small>
            NOTE: Only works when specifying <a href="#eventid">eventid</a>
            parameter.
          </small>
        </td>
      </tr>
      <tr id="limit">
        <td><code>limit</code></td>
        <td>Integer
          <span class="range">[1,<?php echo $service->serviceLimit; ?>]</span>
        </td>
        <td>null</td>
        <td>
          Limit the results to the specified number of events.
          <small>
            NOTE: The service limits queries to <?php echo $service->serviceLimit; ?>,
            and any that exceed this limit will generate a HTTP response code
            &ldquo;400 Bad Request&rdquo;.
          </small>
        </td>
      </tr>
  <!--     <tr>
        <td>magnitudetype</td>
        <td>Decimal <span class="range">[-100, 1000] km</span></td>
        <td>null</td>
        <td>
          Specify a magnitude type to use for testing the minimum and maximum limits.
          <small>
            NOTE: this will only return events reported with this magnitude
            type.
          </small>
        </td>
      </tr> -->
      <tr id="maxdepth">
        <td><code>maxdepth</code></td>
        <td>Decimal <span class="range">[-100, 1000] km</span></td>
        <td>1000</td>
        <td>Limit to events with depth less than the specified maximum.</td>
      </tr>
      <tr id="maxmagnitude">
        <td><code>maxmagnitude</code></td>
        <td>Decimal</td>
        <td>null</td>
        <td>Limit to events with a magnitude smaller than the specified maximum.</td>
      </tr>
      <tr id="mindepth">
        <td><code>mindepth</code></td>
        <td>Decimal <span class="range">[-100, 1000] km</span></td>
        <td>-100</td>
        <td>Limit to events with depth more than the specified minimum.</td>
      </tr>
      <tr id="minmagnitude">
        <td><code>minmagnitude</code></td>
        <td>Decimal</td>
        <td>null</td>
        <td>Limit to events with a magnitude larger than the specified minimum.</td>
      </tr>
      <tr id="offset">
        <td><code>offset</code></td>
        <td>Integer<span class="range">[1,&infin;]</span></td>
        <td>1</td>
        <td>Return results starting at the event count specified, starting at 1.</td>
      </tr>
      <tr id="orderby">
        <td><code>orderby</code></td>
        <td>String</td>
        <td>time</td>
        <td>Order the results. The allowed values are:
            <dl class="vertical options">
              <dt><code>orderby=time</code></dt>
              <dd>order by origin descending time</dd>

              <dt><code>orderby=time-asc</code></dt>
              <dd>order by origin ascending time</dd>

              <dt><code>orderby=magnitude</code></dt>
              <dd>order by descending magnitude</dd>

              <dt><code>orderby=magnitude-asc</code></dt>
              <dd>order by ascending magnitude</dd>
            </dl>
        </td>
      </tr>
    </tbody>
  </table>
</div>


<h3 id="extensions">Extensions</h3>
<div class="horizontal-scrolling">
  <table>
    <thead>
      <tr>
        <th>parameter</th>
        <th>type</th>
        <th>default</th>
        <th>description</th>
      </tr>
    </thead>
    <tbody>
      <tr id="alertlevel">
        <td><code>alertlevel</code></td>
        <td>String</td>
        <td>null</td>
        <td>
          Limit to events with a specific PAGER alert level. The allowed values
          are:

          <dl class="vertical options">
            <dt><code>alertlevel=green</code></dt>
            <dd>Limit to events with PAGER alert level "green".</dd>

            <dt><code>alertlevel=yellow</code></dt>
            <dd>Limit to events with PAGER alert level "yellow".</dd>

            <dt><code>alertlevel=orange</code></dt>
            <dd>Limit to events with PAGER alert level "orange".</dd>

            <dt><code>alertlevel=red</code></dt>
            <dd>Limit to events with PAGER alert level "red".</dd>
          </dl>
        </td>
      </tr>
      <tr id="callback">
        <td><code>callback</code></td>
        <td>String</td>
        <td>null</td>
        <td>
          Convert GeoJSON output to a JSONP response using this callback.
          Mime-type is &ldquo;text/javascript&rdquo;.

          <p>
            Callback values are restricted to the characters
            <code>[A-Za-z0-9\._]+</code>
          </p>

          <small>
            NOTE: Must be used with format=geojson
          </small>
        </td>
      </tr>
      <tr id="eventtype">
        <td><code>eventtype</code></td>
        <td>String</td>
        <td>null</td>
        <td>
          Limit to events of a specific type.

          <small>
            NOTE: &ldquo;earthquake&rdquo; will filter non-earthquake events.
          </small>
        </td>
      </tr>
      <tr id="jsonerror">
        <td><code>jsonerror</code></td>
        <td>Boolean</td>
        <td>false</td>
        <td>
          Request JSON(P) formatted output even on API error results.

          <small>
            NOTE: Must be used with format=geojson
          </small>
        </td>
      </tr>
      <tr id="kmlanimated">
        <td><code>kmlanimated</code></td>
        <td>Boolean</td>
        <td>false</td>
        <td>
          Whether to include timestamp in generated kml, for google earth
          animation support.

          <small>
            NOTE: Must be used with format=kml
          </small>
        </td>
      </tr>
      <tr id="kmlcolorby">
        <td><code>kmlcolorby</code></td>
        <td>String</td>
        <td>age</td>
        <td>
          How earthquakes are colored. Accepted values are:

          <dl class="vertical options">
            <dt><code>kmlcolorby=age</code></dt>
            <dd>Color events in KML by age.</dd>
            <dt><code>kmlcolorby=depth</code></dt>
            <dd>Color events in KML by depth.</dd>
            <dd></dd>
          </dl>

          <small>
            NOTE: Must be used with format=kml
          </small>
        </td>
      </tr>
      <tr id="maxcdi">
        <td><code>maxcdi</code></td>
        <td>Decimal <span class="range">[0,12]</span></td>
        <td>null</td>
        <td>
          Maximum value for Maximum Community Determined Intensity reported by
          DYFI.
        </td>
      </tr>
      <tr id="maxgap">
        <td><code>maxgap</code></td>
        <td>Decimal <span class="range">[0,360] degrees</span></td>
        <td>null</td>
        <td>
          Limit to events with no more than this azimuthal gap.
        </td>
      </tr>
      <tr id="maxmmi">
        <td><code>maxmmi</code></td>
        <td>Decimal <span class="range">[0,12]</span></td>
        <td>null</td>
        <td>
          Maximum value for Maximum Modified Mercalli Intensity reported by ShakeMap.
        </td>
      </tr>
      <tr id="maxsig">
        <td><code>maxsig</code></td>
        <td>Integer</td>
        <td>null</td>
        <td>
          Limit to events with no more than this significance.
        </td>
      </tr>
      <tr id="mincdi">
        <td><code>mincdi</code></td>
        <td>Decimal</td>
        <td>null</td>
        <td>
          Minimum value for Maximum Community Determined Intensity reported by DYFI.
        </td>
      </tr>
      <tr id="minfelt">
        <td><code>minfelt</code></td>
        <td>Integer<span class="range">[1,&infin;]</span></td>
        <td>null</td>
        <td>
          Limit to events with this many DYFI responses.
        </td>
      </tr>
      <tr id="mingap">
        <td><code>mingap</code></td>
        <td>Decimal<span class="range">[0,360] degrees</span></td>
        <td>null</td>
        <td>
          Limit to events with no less than this azimuthal gap.
        </td>
      </tr>
      <tr id="minsig">
        <td><code>minsig</code></td>
        <td>Integer</td>
        <td>null</td>
        <td>
          Limit to events with no less than this significance.
        </td>
      </tr>
      <tr id="nodata">
        <td><code>nodata</code></td>
        <td>Integer <span class="range">(204|404)</span></td>
        <td>204</td>
        <td>
          Define the error code that will be returned when no data is found.
        </td>
      </tr>
      <tr id="producttype">
        <td><code>producttype</code></td>
        <td>String</td>
        <td>null</td>
        <td>
          Limit to events that have this type of product associated.
          Example producttypes:

          <!-- TODO? generate a feed that returns a complete list -->
          <ul class="options">
            <li>moment-tensor</li>
            <li>focal-mechanism</li>
            <li>shakemap</li>
            <li>losspager</li>
            <li>dyfi</li>
          </ul>
        </td>
      </tr>
      <tr id="productcode">
        <td><code>productcode</code></td>
        <td>String</td>
        <td>null</td>
        <td>
          Return the event that is associated with the productcode. The event will
          be returned even if the productcode is not the preferred code for the
          event. Example productcodes:
          <ul class="options">
            <li>nn00458749</li>
            <li>at00ndf1fr</li>
          </ul>
        </td>
      </tr>
      <tr id="reviewstatus">
        <td><code>reviewstatus</code></td>
        <td>String</td>
        <td>all</td>
        <td>
          Limit to events with a specific review status. The different review
          statuses are:

          <dl class="vertical options">
            <dt><code>reviewstatus=automatic</code></dt>
            <dd>Limit to events with review status "automatic".</dd>
            <dt><code>reviewstatus=reviewed</code></dt>
            <dd>Limit to events with review status "reviewed".</dd>
          </dl>
        </td>
      </tr>
    </tbody>
  </table>
</div>
