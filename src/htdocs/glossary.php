<?php
if (!isset($TEMPLATE)) {
  $TITLE = 'Glossary - Earthquake Catalog Data Terms';
  $NAVIGATION = true;
  $HEAD = '
    <link rel="stylesheet" href="css/glossary.css"/>
  ';

  include 'template.inc.php';
}
?>

<div class="row">
  <section id="contents" class="column one-of-two">
    <h2>Event Terms</h2>
    <ul>
      <li><a href="#alert">alert</a></li>
      <li><a href="#cdi">cdi</a></li>
      <li><a href="#code">code</a></li>
      <li><a href="#depth">depth</a></li>
      <li><a href="#depthError">depthError</a></li>
      <li><a href="#detail">detail</a></li>
      <li><a href="#dmin">dmin</a></li>
      <li><a href="#felt">felt</a></li>
      <li><a href="#gap">gap</a></li>
      <li><a href="#horizontalError">horizontalError</a></li>
      <li><a href="#id">id</a></li>
      <li><a href="#ids">ids</a></li>
      <li><a href="#latitude">latitude</a></li>
      <li><a href="#locationSource">locationSource</a></li>
      <li><a href="#longitude">longitude</a></li>
      <li><a href="#mag">mag</a></li>
      <li><a href="#magError">magError</a></li>
      <li><a href="#magNst">magNst</a></li>
      <li><a href="#magSource">magSource</a></li>
      <li><a href="#magType">magType</a></li>
      <li><a href="#mmi">mmi</a></li>
      <li><a href="#net">net</a></li>
      <li><a href="#nst">nst</a></li>
      <li><a href="#place">place</a></li>
      <li><a href="#rms">rms</a></li>
      <li><a href="#sig">sig</a></li>
      <li><a href="#sources">sources</a></li>
      <li><a href="#status">status</a></li>
      <li><a href="#time">time</a></li>
      <li><a href="#tsunami">tsunami</a></li>
      <li><a href="#type">type</a></li>
      <li><a href="#types">types</a></li>
      <li><a href="#tz">tz</a></li>
      <li><a href="#updated">updated</a></li>
      <li><a href="#url">url</a></li>
    </ul>
</section>
<section class="column one-of-two">
    <h2>Metadata Terms</h2>
    <ul>
      <li><a href="#metadata_api">api</a></li>
      <li><a href="#metadata_count">count</a></li>
      <li><a href="#metadata_generated">generated</a></li>
      <li><a href="#metadata_title">title</a></li>
      <li><a href="#metadata_url">url</a></li>
      <li><a href="#metadata_status">status</a></li>
    </ul>

    <h2>Product Terms</h2>
    <ul>
      <li><a href="#product_content">content</a></li>
      <li><a href="#product_id">id</a></li>
      <li><a href="#product_property">link</a></li>
      <li><a href="#product_property">property</a></li>
      <li><a href="#product_preferredWeight">preferredWeight</a></li>
      <li><a href="#product_status">status</a></li>
    </ul>
  </section>
</div>

<div class="row">
  <section class="column one-of-one">
    <h2>Event Terms</h2>
    <dl class="typelist vertical">
      <dt id="alert">alert</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>
            &ldquo;green&rdquo;, &ldquo;yellow&rdquo;, &ldquo;orange&rdquo;,
            &ldquo;red&rdquo;.
          </dd>
          <dt>Description</dt>
          <dd>
            The alert level from the <a
            href="http://earthquake.usgs.gov/research/pager/"
            title="Prompt Assessment of Global Earthquakes for Response"
            >PAGER earthquake impact scale</a>.
          </dd>
        </dl>
      </dd>

      <dt id="cdi">cdi</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[0.0, 10.0]</dd>
          <dt>Description</dt>
          <dd>
            The maximum reported <a
            href="http://earthquake.usgs.gov/learn/glossary/?term=intensity"
            >intensity</a> for the event. Computed by <a
            href="http://earthquake.usgs.gov/research/dyfi/"
            title="Did You Feel It?">DYFI</a>. While typically reported as a
            roman numeral, for the purposes of this API, intensity is expected
            as the <strong>decimal</strong> equivalent of the roman numeral.
            Learn more about <a
            href="http://earthquake.usgs.gov/learn/topics/mag_vs_int.php"
            >magnitude vs. intensity</a>.
          </dd>
        </dl>
      </dd>

      <dt id="code">code</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt><dd>"2013lgaz", "c000f1jy", "71935551"</dd>
          <dt>Description</dt>
          <dd>
            An identifying code assigned by - and <strong>unique</strong> from -
            the <a href="#net">corresponding source</a> for the event.
          </dd>
        </dl>
      </dd>

      <dt id="depth">depth</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[0, 1000]</dd>
          <dt>Description</dt>
          <dd>Depth of the event in kilometers.</dd>
        </dl>
      </dd>

      <dt id="depthError">depthError</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[0, 100]</dd>
          <dt>Description</dt>
          <dd>Uncertainty of reported depth of the event in kilometers.</dd>
        </dl>
      </dd>

      <dt id="detail">detail</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Description</dt>
          <dd>
            Link to <a href="geojson_detail.php">GeoJSON detail</a> feed from a
              <a href="geojson.php">GeoJSON summary</a> feed.
            <p>
              NOTE: When searching and using geojson with callback, no callback
              is included in the <code>detail</code> url.
            </p>
          </dd>
        </dl>
      </dd>

      <dt id="dmin">dmin</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[0.4, 7.1]</dd>
          <dt>Description</dt>
          <dd>
            Horizontal distance from the epicenter to the nearest station
            (in degrees). 1 degree is approximately 111.2 kilometers.
            In general, the smaller this number, the more reliable is the
            calculated depth of the earthquake.
          </dd>
        </dl>
      </dd>

      <dt id="felt">felt</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Integer</dd>
          <dt>Typical Values</dt><dd>[44, 843]</dd>
          <dt>Description</dt>
          <dd>
            The total number of felt reports submitted to the <a
            href="http://earthquake.usgs.gov/research/dyfi/"
            title="Did You Feel It?">DYFI?</a> system.
          </dd>
        </dl>
      </dd>

      <dt id="gap">gap</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[0.0, 180.0]</dd>
          <dt>Description</dt>
          <dd>
            The largest azimuthal gap between azimuthally adjacent stations (in
            degrees). In general, the smaller this number, the more reliable is
            the calculated horizontal position of the earthquake.
          </dd>
        </dl>
      </dd>

      <dt id="horizontalError">horizontalError</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[0, 100]</dd>
          <dt>Description</dt>
          <dd>Uncertainty of reported location of the event in kilometers.</dd>
        </dl>
      </dd>

      <dt id="id">id</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>
            A (generally) <a href="#net">two-character network identifier</a>
            with a (generally) <a href="#code">eight-character network-assigned
            code</a>.
          </dd>
          <dt>Description</dt>
          <dd>
            A unique identifier for the event.
            This is the current preferred id for the event, and may change over
            time. <a href="#ids">See the "ids" GeoJSON format property</a>.
          </dd>
        </dl>
      </dd>

      <dt id="ids">ids</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>
            ",ci15296281,us2013mqbd,at00mji9pf,"
          </dd>
          <dt>Description</dt>
          <dd>
            A comma-separated list of <a href="#id">event ids</a> that are
            associated to an event.
          </dd>
        </dl>
      </dd>

      <dt id="latitude">latitude</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[-90.0, 90.0]</dd>
          <dt>Description</dt>
          <dd>
            Decimal degrees latitude. Negative values for southern latitudes.
          </dd>
        </dl>
      </dd>

      <dt id="locationSource">locationSource</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt><dd>
            ak, at, ci, hv, ld, mb, nc, nm, nn, pr, pt, se, us, uu, uw
          </dd>
          <dt>Description</dt>
          <dd>
            The network that originally authored the reported location of this
            event.
          </dd>
        </dl>
      </dd>

      <dt id="longitude">longitude</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[-180.0, 180.0]</dd>
          <dt>Description</dt>
          <dd>
            Decimal degrees longitude. Negative values for western longitudes.
          </dd>
        </dl>
      </dd>

      <dt id="mag">mag</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[-1.0, 10.0]</dd>
          <dt>Description</dt>
          <dd>
            The magnitude for the event. <a
            href="http://earthquake.usgs.gov/learn/glossary/?term=magnitude"
            >Learn more about magnitudes</a>.
          </dd>
        </dl>
      </dd>

      <dt id="magError">magError</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[0, 100]</dd>
          <dt>Description</dt>
          <dd>Uncertainty of reported magnitude of the event.</dd>
        </dl>
      </dd>

      <dt id="magNst">magNst</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Integer</dd>
          <dt>Description</dt>
          <dd>
            The total number of seismic stations used to calculate
            the magnitude for this earthquake.
          </dd>
        </dl>
      </dd>

      <dt id="magSource">magSource</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt><dd>
            ak, at, ci, hv, ld, mb, nc, nm, nn, pr, pt, se, us, uu, uw
          </dd>
          <dt>Description</dt>
          <dd>
            Network that originally authored the reported magnitude for this
            event.
          </dd>
        </dl>
      </dd>

      <dt id="magType">magType</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>
            &ldquo;Md&rdquo;, &ldquo;Ml&rdquo;, &ldquo;Ms&rdquo;,
            &ldquo;Mw&rdquo;, &ldquo;Me&rdquo;, &ldquo;Mi&rdquo;,
            &ldquo;Mb&rdquo;, &ldquo;MLg&rdquo;
          </dd>
          <dt>Description</dt>
          <dd>
            The method or algorithm used to calculate the preferred magnitude
            for the event. <a
            href="http://earthquake.usgs.gov/earthquakes/map/doc_aboutdata.php#magnitudes"
            >Learn more about magnitude types.</a>
          </dd>
        </dl>
      </dd>

      <dt id="mmi"><abbr title="Modified Mercalli Intensity">mmi</abbr></dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[0.0, 10.0]</dd>
          <dt>Description</dt>
          <dd>
            The maximum estimated instrumental <a
            href="http://earthquake.usgs.gov/learn/glossary/?term=intensity"
            >intensity</a> for the event.  Computed by <a
            href="http://earthquake.usgs.gov/research/shakemap/">ShakeMap</a>.
            While typically reported as a roman numeral, for the purposes of
            this API, intensity is expected as the <strong>decimal</strong>
            equivalent of the roman numeral. Learn more about <a
            href="http://earthquake.usgs.gov/learn/topics/mag_vs_int.php"
            >magnitude vs. intensity</a>.
          </dd>
        </dl>
      </dd>

      <dt id="net">net</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>
            ak, at, ci, hv, ld, mb, nc, nm, nn, pr, pt, se, us, uu, uw
          </dd>
          <dt>Description</dt>
          <dd>
            The ID of a data contributor.
            Identifies the network considered to be the preferred source of
            information for this event.
          </dd>
        </dl>
      </dd>

      <dt id="nst">nst</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Integer</dd>
          <dt>Description</dt>
          <dd>
            The total number of seismic stations which reported
            P- and S-arrival times for this earthquake.
          </dd>
        </dl>
      </dd>

      <dt id="place">place</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Description</dt>
          <dd>
            Textual description of named geographic region near to the event.
            This may be a city name, or a <a
            href="http://earthquake.usgs.gov/learn/topics/flinn_engdahl.php"
            >Flinn-Engdahl Region</a> name.
          </dd>
        </dl>
      </dd>

      <dt id="rms">rms</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Decimal</dd>
          <dt>Typical Values</dt><dd>[0.13,1.39]</dd>
          <dt>Description</dt>
          <dd>
            The root-mean-square (RMS) travel time residual, in sec, using all
            weights. This parameter provides a measure of the fit of the
            observed arrival times to the predicted arrival times for this
            location. Smaller numbers reflect a better fit of the data. The
            value is dependent on the accuracy of the velocity model used to
            compute the earthquake location, the quality weights assigned to
            the arrival time data, and the procedure used to locate the
            earthquake.
          </dd>
        </dl>
      </dd>

      <dt id="sig">sig</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Integer</dd>
          <dt>Typical Values</dt><dd>[0, 1000]</dd>
          <dt>Description</dt>
          <dd>
            A number describing how significant the event is. Larger numbers
            indicate a more significant event. This value is determined on a
            number of factors, including: magnitude, maximum MMI, felt reports,
            and estimated impact.
          </dd>
        </dl>
      </dd>

      <dt id="sources">sources</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>
            ",us,nc,ci,"
          </dd>
          <dt>Description</dt>
          <dd>
            A comma-separated list of <a href="#net">network contributors</a>.
          </dd>
        </dl>
      </dd>

      <dt id="status">status</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>
            &ldquo;automatic&rdquo;,
            &ldquo;reviewed&rdquo;,
            &ldquo;deleted&rdquo;
          </dd>
          <dt>Description</dt>
          <dd>Indicates whether the event has been reviewed by a human.</dd>
        </dl>
      </dd>

      <dt id="time">time</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Long Integer</dd>
          <dt>Description</dt>
          <dd>
            Time when the event occurred. Times are reported in <em>
            milliseconds</em> since the epoch (
            <code>1970-01-01T00:00:00.000Z</code>), and do not include
            <a href="http://www.nist.gov/pml/div688/leapseconds.cfm">leap
                seconds</a>.
            In certain output formats, the date is formatted for readability.
          </dd>
        </dl>
      </dd>

      <dt id="tsunami">tsunami</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Integer</dd>
          <dt>Description</dt>
          <dd>
            This flag is set to "1" for large events in oceanic regions and "0"
            otherwise. <strong>The existence or value of this flag does not
            indicate if a tsunami actually did or will exist</strong>. If the
            flag value is "1", the event will include a link to the NOAA Tsunami
            website for tsunami information. The USGS is not responsible for
            Tsunami warning; we are simply providing a link to the authoritative
            NOAA source.

            <p>See <a target="_blank"
            href="http://www.tsunami.gov/">http://www.tsunami.gov/</a> for all
            current tsunami alert statuses.</p>
          </dd>
        </dl>
      </dd>

      <dt id="type">type</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>&ldquo;earthquake&rdquo;, &ldquo;quarry&rdquo;</dd>
          <dt>Description</dt>
          <dd>
            Type of seismic event.
          </dd>
        </dl>
      </dd>

      <dt id="types">types</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>&ldquo;,cap,dyfi,general-link,origin,p-wave-travel-times,phase-data,&rdquo;</dd>
          <dt>Description</dt>
          <dd>
            A comma-separated list of product types associated to this event.
          </dd>
        </dl>
      </dd>

      <dt id="tz">tz</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Integer</dd>
          <dt>Typical Values</dt><dd>[-1200, +1200]</dd>
          <dt>Description</dt>
          <dd>Timezone offset from UTC in minutes at the event epicenter.</dd>
        </dl>
      </dd>

      <dt id="updated">updated</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Long Integer</dd>
          <dt>Description</dt>
          <dd>
            Time when the event was most recently updated.
            Times are reported in <em>milliseconds</em> since the
            <abbr title="January 1, 1970">epoch</abbr>. In certain output
            formats, the date is formatted for readability.
          </dd>
        </dl>
      </dd>

      <dt id="url">url</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Description</dt>
          <dd>
            Link to USGS Event Page for event.
          </dd>
        </dl>
      </dd>
    </dl>

    <h2>Metadata Terms</h2>
    <dl class="typelist vertical">
      <dt id="metadata_api">api</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Description</dt>
          <dd>
            Version of API that generated feed.
          </dd>
        </dl>
      </dd>

      <dt id="metadata_count">count</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Integer</dd>
          <dt>Description</dt>
          <dd>
            Number of earthquakes in feed.
          </dd>
        </dl>
      </dd>

      <dt id="metadata_generated">generated</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Long Integer</dd>
          <dt>Description</dt>
          <dd>
            Time when the feed was most recently updated.
            Times are reported in <em>milliseconds</em> since the
            <abbr title="January 1, 1970">epoch</abbr>.
          </dd>
        </dl>
      </dd>

      <dt id="metadata_title">title</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Typical Values</dt>
          <dd>
            &ldquo;USGS Magnitude 1+ Earthquakes, Past Day&rdquo;,
            &ldquo;USGS Magnitude 4.5+ Earthquakes, Past Month&rdquo;
          </dd>
          <dt>Description</dt>
          <dd>
            The title of the feed.
          </dd>
        </dl>
      </dd>

      <dt id="metadata_url">url</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Description</dt>
          <dd>
            Url of the feed.
          </dd>
        </dl>
      </dd>

      <dt id="metadata_status">status</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Integer</dd>
          <dt>Description</dt>
          <dd>
            HTTP status code of response.
          </dd>
        </dl>
      </dd>
    </dl>

    <h2>Product Terms</h2>
    <dl class="typelist vertical">
      <dt id="product_content">content</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Object</dd>
          <dt>Description</dt>
          <dd>
            A file or group of bytes associated with a product.

            <ul class="attribute-list">
              <li>
                <em>contentType</em>
                String mime type of this content.
              </li>
              <li>
                <em>lastModified</em>
                Millisecond timestamp when this content was modified.
              </li>
              <li>
                <em>length</em>
                Integer number of bytes in this content.
              </li>
              <li>
                <em>&lt;path&gt;</em>
                String relative path within this product, frequently a filename.
              </li>
              <li>
                <em>url</em>
                Link to download this content.
                When <code>&lt;path&gt;</code> is empty (""),
                there will be <code>bytes</code> property with content inline.
              </li>
            </ul>
          </dd>
        </dl>
      </dd>

      <dt id="product_id">id</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Description</dt>
          <dd>
            Unique identifier for a specific version of a product.

            The id is made of of these four attributes:

            <ul class="attribute-list">
              <li>
                <em>source</em>
                The product contributor, usually a <a href="#net">network code</a>.
              </li>
              <li>
                <em>type</em>
                The type of product.
                See <a href="http://ehppdl1.cr.usgs.gov/userguide/products/"
                  >http://ehppdl1.cr.usgs.gov/userguide/products/</a>
                  for a list of product types.
              </li>
              <li>
                <em>code</em>
                A unique identifier from the product <code>source</code>, for
                this <code>type</code> of product.
              </li>
              <li>
                <em>updateTime</em>
                A millisecond timestamp that indicates when this version of the
                product was created.
                <br />
                Two products with the same <code>source</code>,
                <code>type</code>, and <code>code</code>, with different
                <code>updateTime</code>s indicate different versions of the
                same product. The latest updateTime for a product supersedes
                any earlier updateTime for the same product.
              </li>
            </ul>
          </dd>
        </dl>
      </dd>

      <dt id="product_link">link</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Description</dt>
          <dd>
            Products have zero or more links, which consist of:

            <ul class="attribute-list">
              <li>
                <em>&lt;relation&gt;</em>
                Relation describes how the link is related to the product.
              </li>
              <li>
                <em>href</em>
                Link is a URI, and may be a URL or a URN depending on product
                type and <code>relation</code>.
              </li>
            </ul>

            Links vary depending on <a href="#product_id">product type</a>.
          </dd>
        </dl>
      </dd>

      <dt id="product_property">property</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Description</dt>
          <dd>
            Products have zero or more key/value properties, which are both
            Strings. Properties vary depending on
            <a href="#product_id">product type</a>.
          </dd>
        </dl>
      </dd>

      <dt id="product_preferredWeight">preferredWeight</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">Integer</dd>
          <dt>Description</dt>
          <dd>
            Relative weight of product.
            When multiple products of the same type are associated to an event,
            one product of each type is considered &ldquo;most preferred&rdquo;
            This is defined as the product of that type with the largest
            <code>preferredWeight</code>, and when two products have equal
            preferredWeight the most recent updateTime is more preferred.
          </dd>
        </dl>
      </dd>

      <dt id="product_status">status</dt>
      <dd>
        <dl>
          <dt>Data Type</dt><dd class="datatype">String</dd>
          <dt>Description</dt>
          <dd>
            Status of the product.
            There is only one reserved status <code>DELETE</code>, which
            indicates the product has been deleted. Any other value indicates
            a product update, and may vary depending on <a href="#product_id">
            product type</a>.
          </dd>
        </dl>
      </dd>
    </dl>
  </section>
</div>
