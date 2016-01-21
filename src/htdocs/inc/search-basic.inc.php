<div class="one-of-three column">
  <section class="magnitude-view">
    <h3 id="magnitude" class="label">Magnitude</h3>
    <ul class="no-style basicmagnitude-list"></ul>
    <ul class="vertical no-style">
      <li>
        <label for="minmagnitude" class="label" id="magnitude-minmagnitude">
          Minimum
        </label>
        <input type="number" step="any" name="minmagnitude" id="minmagnitude"
            min="-1" max="10" step="0.1" value="2.5"
            aria-labelledby="magnitude magnitude-minmagnitude"/>
      </li>
      <li>
        <label for="maxmagnitude" class="label" id="magnitude-maxmagnitude">
          Maximum
        </label>
        <input type="number" step="any" name="maxmagnitude" id="maxmagnitude"
            min="-1" max="10" step="0.1" value=""
            aria-labelledby="magnitude magnitude-maxmagnitude"/>
      </li>
    </ul>
  </section>
</div>

<div class="one-of-three column">
  <section class="date-time-view">
    <h3 id="datetime" class="label">Date &amp; Time</h3>
    <ul class="no-style basictime-list"></ul>
    <ul class="vertical no-style">
      <li>
        <label for="starttime" class="label" id="datetime-starttime">
          Start (UTC)
        </label>
        <input type="text" name="starttime" id="starttime"
            placeholder="yyyy-mm-dd hh:mm:ss"
            aria-labelledby="datetime datetime-starttime"/>
      </li>
      <li>
        <label for="endtime" class="label" id="datetime-endtime">
          End (UTC)
        </label>
        <input type="text" name="endtime" id="endtime"
            placeholder="yyyy-mm-dd hh:mm:ss"
            aria-labelledby="datetime datetime-endtime"/>
      </li>
    </ul>
  </section>
</div>

<div class="one-of-three column">
  <section class="location-view">
    <h3 id="region" class="label">Geographic Region</h3>
    <ul class="no-style basiclocation-list"></ul>
    <div class="region-description"></div>
    <div class="fieldset" role="group" aria-labelledby="region-rectangle">
      <button type="button" class="draw orange">Draw Rectangle on Map</button>
    </div>
  </section>
</div>
