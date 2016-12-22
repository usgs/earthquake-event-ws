<section class="one-of-two column" aria-labelledby="output-format">
  <h3 id="output-format" class="label">Format</h3>

  <ul class="no-style format-list">
    <!-- Note: Map/List requires JS -->
    <li>
      <input id="output-format-csv" type="radio" name="format"
          value="csv" checked/>
      <label for="output-format-csv" class="label-checkbox">
        CSV
      </label>
    </li>
    <li>
      <input id="output-format-kml" type="radio" name="format"
          value="kml"/>
      <label for="output-format-kml" class="label-checkbox">
        KML
      </label>
    </li>
    <li>
      <input id="output-format-quakeml" type="radio" name="format"
          value="quakeml"/>
      <label for="output-format-quakeml" class="label-checkbox">
        QuakeML
      </label>
    </li>
    <li>
      <input id="output-format-geojson" type="radio" name="format"
          value="geojson"/>
      <label for="output-format-geojson" class="label-checkbox">
        GeoJSON
      </label>
    </li>
  </ul>

  <div id="output-format-quakeml-details"
      role="group" aria-labelledby="output-quakeml">
  <h4 id="output-quakeml" class="label">QuakeML-Specific Options</h4>

  <ul class="no-style output-quakeml-list">
    <li>
      <input id="includeallorigins" type="checkbox" value="true"
          name="includeallorigins"/>
      <label for="includeallorigins" class="label-checkbox">
        Include all origins
      </label>
    </li>
    <li>
      <input id="includeallmagnitudes" type="checkbox" value="true"
          name="includeallmagnitudes"/>
      <label for="includeallmagnitudes" class="label-checkbox">
        Include all magnitudes
      </label>
    </li>
    <!-- TODO : Implement includearrivals
    <li>
      <label for="includearrivals">
        <input id="includearrivals" type="checkbox" value="true"
            name="includearrivals"/>
        Include all phase arrival times
      </label>
    </li>
    -->
  </ul>
  </div>

  <div id="output-format-kml-details"
      role="group" aria-labelledby="output-kml">
  <h4 id="output-kml" class="label">KML-Specific Options</h4>

  <ul class="no-style output-kml-list">
    <li>
      <input id="kmlcolorby-age" type="radio" name="kmlcolorby"
          value="age" checked/>
      <label for="kmlcolorby-age" class="label-checkbox">
        Color by age
      </label>
    </li>
    <li>
      <input id="kmlcolorby-depth" type="radio" name="kmlcolorby"
          value="depth"/>
      <label for="kmlcolorby-depth" class="label-checkbox">
        Color by depth
      </label>
    </li>
    <li>
      <input id="kmlanimated" type="checkbox" value="true"
          name="kmlanimated"/>
      <label for="kmlanimated" class="label-checkbox">
        Animated
      </label>
    </li>
  </ul>
  </div>

  <div id="output-format-geojson-details"
      role="group" aria-labelledby="output-geojson">
  <h4 id="output-geojson" class="label">GeoJSON-Specific Options</h4>

  <ul class="no-style output-geojson-list">
    <li>
      <label for="callback" class="label">Callback Option</label>
      <input type="text" name="callback" id="callback"/>
    </li>
    <li>
      <input id="jsonerror" type="checkbox" value="true"
          name="jsonerror" />
      <label for="jsonerror" class="label-checkbox">
        Format errors as JSON(P)
      </label>
    </li>
  </ul>
  </div>
</section>

<section class="one-of-two column" aria-labelledby="output-orderby">
  <h3 id="output-orderby" class="label">Order By</h3>
  <ul class="no-style orderby-list">
  <?php
    if (!$SCENARIO_MODE) {
      echo '
        <li>
          <input id="orderby-time" type="radio" name="orderby"
              value="time" aria-labelledby="output-orderby" checked/>
          <label for="orderby-time" class="label-checkbox">
            Time - Newest First
          </label>
        </li>
        <li>
          <input id="orderby-time-asc" type="radio" name="orderby"
              value="time-asc" aria-labelledby="output-orderby"/>
          <label for="orderby-time-asc" class="label-checkbox">
            Time - Oldest First
          </label>
        </li>
        <li>
          <input id="orderby-magnitude" type="radio" name="orderby"
              value="magnitude" aria-labelledby="output-orderby"/>
          <label for="orderby-magnitude" class="label-checkbox">
            Magnitude - Largest First
          </label>
        </li>';
    } else {
      echo '
        <li>
          <input id="orderby-magnitude" type="radio" name="orderby"
              value="magnitude" aria-labelledby="output-orderby" checked/>
          <label for="orderby-magnitude" class="label-checkbox">
            Magnitude - Largest First
          </label>
        </li>';
    }
  ?>
    <li>
      <input id="orderby-magnitude-asc" type="radio" name="orderby"
          value="magnitude-asc" aria-labelledby="output-orderby"/>
      <label for="orderby-magnitude-asc" class="label-checkbox">
        Magnitude - Smallest First
      </label>
    </li>
  </ul>

  <h3 class="label">Limit Results</h3>
  <ul class="vertical no-style two-up">
    <li>
      <label for="limit" class="label">Number of Events</label>
      <input type="number" step="any" name="limit" id="limit" min="1"
          max="<?php echo $MAX_SEARCH; ?>"/>
    </li>
    <li>
      <label for="offset" class="label">Offset</label>
      <input type="number" step="any" name="offset" id="offset" min="1"/>
    </li>
  </ul>
</section>
