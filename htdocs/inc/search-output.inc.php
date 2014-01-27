<section role="group" aria-labelledby="output-format">
	<h3 id="output-format" class="label">Format</h3>

	<ul class="format-list">
		<!-- Note: Map/List requires JS -->
		<li>
			<label for="output-format-csv" class="label-checkbox">
				<input id="output-format-csv" type="radio" name="format"
						value="csv" checked/>
				CSV
			</label>
		</li>
		<li>
			<label for="output-format-kml" class="label-checkbox">
				<input id="output-format-kml" type="radio" name="format"
						value="kml"/>
				KML
			</label>
		</li>
		<li>
			<label for="output-format-quakeml" class="label-checkbox">
				<input id="output-format-quakeml" type="radio" name="format"
						value="quakeml"/>
				QuakeML
			</label>
		</li>
		<li>
			<label for="output-format-geojson" class="label-checkbox">
				<input id="output-format-geojson" type="radio" name="format"
						value="geojson"/>
				GeoJSON
			</label>
		</li>
	</ul>

	<div id="output-format-quakeml-details"
			role="group" aria-labelledby="output-quakeml">
	<h4 id="output-quakeml" class="label">QuakeML-Specific Options</h4>

	<ul class="output-quakeml-list">
		<li>
			<label for="includeallorigins" class="label-checkbox">
				<input id="includeallorigins" type="checkbox" value="true"
						name="includeallorigins"/>
				Include all origins
			</label>
		</li>
		<li>
			<label for="includeallmagnitudes" class="label-checkbox">
				<input id="includeallmagnitudes" type="checkbox" value="true"
						name="includeallmagnitudes"/>
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

	<ul class="output-kml-list">
		<li>
			<label for="kmlcolorby-age" class="label-checkbox">
				<input id="kmlcolorby-age" type="radio" name="kmlcolorby"
						value="age" checked/>
				Color by age
			</label>
		</li>
		<li>
			<label for="kmlcolorby-depth" class="label-checkbox">
			<input id="kmlcolorby-depth" type="radio" name="kmlcolorby"
					value="depth"/>
				Color by depth
			</label>
		</li>
		<li style="margin-top:8px;">
			<label for="kmlanimated" class="label-checkbox">
				<input id="kmlanimated" type="checkbox" value="true"
						name="kmlanimated"/>
				Animated
			</label>
		</li>
	</ul>
	</div>

	<div id="output-format-geojson-details"
			role="group" aria-labelledby="output-geojson">
	<h4 id="output-geojson" class="label">GeoJSON-Specific Options</h4>

	<ul class="output-geojson-list">
		<li>
			<label for="callback" class="label">Callback Option</label>
			<input type="text" name="callback" id="callback"/>
		</li>
		<li>
			<label for="jsonerror" class="label-checkbox">
				<input id="jsonerror" type="checkbox" value="true"
						name="jsonerror" />
				Format errors as JSON(P)
			</label>
		</li>
	</ul>
	</div>


		<h3 class="label">Order By</h3>
		<ul class="orderby-list">
			<li>
				<label for="orderby-time" class="label-checkbox">
					<input id="orderby-time" type="radio" name="orderby"
							value="time" aria-labelledby="adv-orderby" checked/>
					Time - Newest First
				</label>
			</li>
			<li>
				<label for="orderby-time-asc" class="label-checkbox">
					<input id="orderby-time-asc" type="radio" name="orderby"
							value="time-asc" aria-labelledby="adv-orderby"/>
					Time - Oldest First
				</label>
			</li>
			<li style="margin-top:8px;">
				<label for="orderby-magnitude" class="label-checkbox">
					<input id="orderby-magnitude" type="radio" name="orderby"
							value="magnitude" aria-labelledby="adv-orderby"/>
					Magnitude - Largest First
				</label>
			</li>
			<li>
				<label for="orderby-magnitude-asc" class="label-checkbox">
					<input id="orderby-magnitude-asc" type="radio" name="orderby"
							value="magnitude-asc" aria-labelledby="adv-orderby"/>
					Magnitude - Smallest First
				</label>
			</li>
		</ul>

	<h3 class="label">Limit Results</h3>
	<ul class="two-up">
		<li>
			<label for="limit" class="label">Number of Events</label>
			<input type="number" step="any" name="limit" id="limit" min="1"
					max="<?php echo $MAX_SEARCH; ?>"/>
		</li>
		<li>
			<label for="offset">Offset</label>
			<input type="number" step="any" name="offset" id="offset" min="1"/>
		</li>
	</ul>


</section>
