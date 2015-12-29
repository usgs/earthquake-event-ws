<?php
  date_default_timezone_set('UTC');
  $now = time();
  $dayago = $now - 86400; // 24 hours ago
  $dayago = date('Y-m-d H:i:s', $dayago);
  $starttime = $now - 604800; // 1 week ago
  $basictime = $starttime;
  $basictime = date('Y-m-d H:i:s', $basictime);
  $starttime -= ($starttime % 86400); // Round to start of day
  $starttime = date('Y-m-d H:i:s', $starttime);

  $endtime = $now + 86400; // Tomorrow
  $endtime -= ($endtime % 86400); // Round to start of day (i.e. end of today)
  $endtime = date('Y-m-d H:i:s', $endtime - 1);
?>


<div class="column">
  <section aria-labelledby="region">
    <h3 id="region" class="label">Geographic Region</h3>
    <div class="region-description"></div>

    <div class="fieldset" role="group" aria-labelledby="region-rectangle">
      <h4 id="region-rectangle" class="label">Rectangle</h4>

      <button type="button" class="draw orange">Draw Rectangle on Map</button>

    </div>
  </section>
</div>

<div class="one-of-two column">
  <section aria-labelledby="basictime">
    <h3 id="basictime" class="label">Select Time</h3>

    <ul class="no-style basictime-list">
      <li>
        <input id="basictime-weekago" type="radio" name="basictime"
            value="<?php print $basictime ?>" checked="checked"
            aria-labelledby="weekago"/>
        <label for="basictime-weekago" class="label-checkbox">
          Past 7 Days
        </label>
      </li>
      <li>
        <input id="basictime-dayago" type="radio" name="basictime"
            value="<?php print $dayago ?>" aria-labelledby="dayago"/>
        <label for="basictime-dayago" class="label-checkbox">
          Past 24 Hours
        </label>
      </li>
      <li>
        <input id="basictime-custom" type="radio" name="basictime"
            value="" aria-labelledby="custom"/>
        <label for="basictime-custom" class="label-checkbox">
          Custom (set in advanced options)
        </label>
      </li>
    </ul>
  </section>
</div>

<div class="one-of-two column">
  <section aria-labelledby="basicmagnitude">
    <h3 id="magnitude" class="label">Magnitude</h3>

    <ul class="no-style basicmagnitude-list">
      <li>
        <input id="greaterfour" type="radio" name="basicmagnitude"
            value="greaterfour" checked="checked" aria-labelledby="greaterfour"/>
        <label for="greaterfour" class="label-checkbox">
          > 4 Stronger
        </label>
      </li>
      <li>
        <input id="lessfour" type="radio" name="basicmagnitude"
            value="lessfour" aria-labelledby="lessfour"/>
        <label for="lessfour" class="label-checkbox">
          < 4 Weaker
        </label>
      </li>
      <li>
        <input id="custommag" type="radio" name="basicmagnitude"
            value="" aria-labelledby="custommag"/>
        <label for="custommag" class="label-checkbox">
          Custom (set in advanced options)
        </label>
      </li>
    </ul>
  </section>
</div>
