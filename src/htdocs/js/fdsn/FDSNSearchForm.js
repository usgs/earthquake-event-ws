/* global define, window, document */
define([
	'fdsn/FDSNModel',
	'fdsn/FDSNModelValidator',
	'fdsn/SelectField',
	'fdsn/EventTypeField',
	'fdsn/UrlBuilderFormatter',
	'fdsn/ManagedModelView',
	'fdsn/ToggleSection',
	'fdsn/UrlManager',
	'util/Util',
	'util/Xhr',
	'mvc/ModalView'
], function (
	FDSNModel,
	FDSNModelValidator,
	SelectField,
	EventTypeField,
	UrlBuilderFormatter,
	ManagedModelView,
	ToggleSection,
	UrlManager,
	Util,
	Xhr,
	ModalView
) {
	'use strict';

	// Static counter to increment each time a JSONP callback is created. This
	// allows unique callback to be generated and executed in global scope
	//var CALLBACK_COUNT = 0;

	var FDSNSearchForm = function (options) {
		// Pull conf options off the options and store as instance variables
		this._el = options.el || document.createElement('div');
		this.fieldDataUrl = options.fieldDataUrl || null;
		this.model = options.model || new FDSNModel();
		this._parsedUrl = UrlManager.parseUrl();

		if (options.hasOwnProperty('fdsnHost')) {
			this.fdsnHost = options.fdsnHost;
		} else {
			this.fdsnHost = 'http://' + window.location.host;
		}

		if (options.hasOwnProperty('fdsnPath')) {
			this.fdsnPath = options.fdsnPath;
		} else {
			this.fdsnPath = '/fdsnws/event/1';
		}

		// Formatting field display text (catalogs, contributors, etc...)
		this.formatter = new UrlBuilderFormatter();

		// Validator to auto-validate the model
		this.validator = new FDSNModelValidator({model: this.model});

		// Initialize the FDSNSearchForm
		this._initialize();
	};

	FDSNSearchForm.prototype = {
		// --------------------------------------------------
		// Public methods
		// --------------------------------------------------

		// --------------------------------------------------
		// Event handlers
		// --------------------------------------------------

		onSubmit: function () {
			var form = this;

			if (this.validator.isValid()) {
				window.location = this._serializeFormToUrl();
			} else {

				// Some errors during submission, show them
				Util.addClass(this._el, 'show-errors');

				(new ModalView(this._formatSearchErrors(
						this.validator.getErrors()), {
					title: 'Form Errors',
					classes: ['modal-error'],
					buttons: [
						{
							text: 'Edit Search',
							title: 'Go back to form and edit search',
							callback: function (evt, dialog) {
								var error = form._el.querySelector('input.error'),
								    fields = null,
								    field = null,
								    section = null,
								    i = 0, len = 0;

								dialog.hide();

								// Make error fields visible (might be in collapsed secion)
								fields = Array.prototype.slice.call(
										form._el.querySelectorAll('.error'), 0);
								len = fields.length;

								for (i = 0; i < len; i++) {
									field = fields[i];
									section = Util.getParentNode(field, 'SECTION', form._el);

									if (section !== null && Util.hasClass(section, 'toggle') &&
											!Util.hasClass(section, 'toggle-visible')) {
										Util.addClass(section, 'toggle-visible');
									}
								}

								// Focus first error field
								error.focus();
							},
						}
					]
				})).show();
			}
		},

		onModelChange: function () {
			var fields = this._el.querySelectorAll('.error'),
			    searchError = this._el.querySelector('.search-error'),
			    i = 0,
			    len = fields.length;

			// Clear any previously marked error fields
			for (; i < len; i++) {
				Util.removeClass(fields.item(i), 'error');
			}

			if (!this.validator.isValid()) {
				searchError.innerHTML =
					this._formatSearchErrors(this.validator.getErrors());
			} else {
				searchError.innerHTML = '';
				Util.removeClass(this._el, 'show-errors');
			}
		},

		onModelFormatChange: function () {
			var format = this.model.get('format'),
			    fmtMap = null,
			    text = null;

			fmtMap = {
					maplist: 'Map &amp; List',
					csv: 'CSV',
					kml: 'KML',
					quakeml: 'QuakeML',
					geojson: 'GeoJSON'
				};

			text = (fmtMap.hasOwnProperty(format)) ? fmtMap[format] : format;

			this._el.querySelector('.output-descriptor').innerHTML =
					'Output Format: ' + text;
		},

		// --------------------------------------------------
		// "Private" methods
		// --------------------------------------------------

		/**
		 * Initializes the form.
		 *
		 */
		_initialize: function () {
			if (this._INITIALIZED_) {
				return;
			} else {
				this._enableToggleFields();

				this._enableRegionControl();
				this._enableOutputDetailsToggle();

				this._bindModel();
				this._addSubmitHandler();

				this._fetchFieldData();
			}
		},

		_formatSearchErrors: function (errors) {
			var errorMarkup = [],
			    fields = null,
			    field = null,
			    key = null,
			    message = null,
			    i = null;

			for (key in errors) {
				errorMarkup.push('<li>', key, '<ul>');
				for (message in errors[key]) {
					errorMarkup.push('<li>', message, '</li>');

					// Mark the field in the UI
					fields = errors[key][message];
					for (i = 0; i < fields.length; i++) {
						field = this._el.querySelector('#' + fields[i]);
						if (field !== null) {
							Util.addClass(field, 'error');
						}
					}
				}
				errorMarkup.push('</ul></li>');

			}

			return [
				'<p class="error">',
					'The current combination of search parameters contains one or ',
					'more errors.',
				'</p>',
				'<ul class="search-errors">', errorMarkup.join(''), '</ul>'
			].join('');
		},

		_trimSearch: function (params) {

			// --------------------------------------------------
			// Not errors, but clear these out for clean searches
			// --------------------------------------------------

			if (params.format !== 'kml') {
				if (params.hasOwnProperty('kmlcolorby')) {
					// Not an error, but strip from serialized data
					delete params.kmlcolorby;
				}
				if (params.hasOwnProperty('kmlanimated')) {
					// Not an error, but strip from serialized data
					delete params.kmlanimated;
				}
			}

			if (params.format !== 'quakeml') {
				if (params.hasOwnProperty('includeallorigins')) {
					delete params.includeallorigins;
				}
				if (params.hasOwnProperty('includeallmagnitudes')) {
					delete params.includeallmagnitudes;
				}
				// TODO :: Implement includearrivals
				//if (params.hasOwnProperty('includearrivals')) {
					//delete params.includearrivals;
				//}
			}

			if (params.format !== 'geojson') {
				if (params.hasOwnProperty('callback')) {
					delete params.callback;
				}
				if (params.hasOwnProperty('jsonerror')) {
					delete params.jsonerror;
				}
			}

			return params;
		},

		_serializeFormToUrl: function () {
			var url = this.fdsnHost + this.fdsnPath + '/query',
			    search = this._trimSearch(this.model.getNonEmpty()),
			    maplistsort = 'newest', searchsort = this.model.get('orderby'),
					mapposition = [[], []],
			    searchString = [], key = null,
			    format = search.format;

			delete search.format;

			if (format === 'maplist') {
				// TODO :: Streamline this mapping
				if (searchsort === 'time-asc') {
					maplistsort = 'oldest';
				} else if (searchsort === 'magnitude') {
					maplistsort = 'largest';
				} else if (searchsort === 'magnitude-asc') {
					maplistsort = 'smallest';
				}

				// Set map position based on search extent, or use full world
				// TODO :: Parse cirle extent as well
				if (search.hasOwnProperty('minlatitude')) {
					mapposition[0].push(parseFloat(search.minlatitude));
				} else {
					mapposition[0].push(-85.0);
				}

				if (search.hasOwnProperty('minlongitude')) {
					mapposition[0].push(parseFloat(search.minlongitude));
				} else {
					mapposition[0].push(0.0);
				}

				if (search.hasOwnProperty('maxlatitude')) {
					mapposition[1].push(parseFloat(search.maxlatitude));
				} else {
					mapposition[1].push(85.0);
				}

				if (search.hasOwnProperty('maxlongitude')) {
					mapposition[1].push(parseFloat(search.maxlongitude));
				} else {
					mapposition[1].push(360.0);
				}


				url = window.location.protocol + '//' + window.location.host +
						'/earthquakes/map/#' + window.escape(UrlManager.parseSettings({
							viewModes: {help: false, list: true, map: true, settings: false},
							sort: maplistsort,
							mapposition: mapposition
						}, {
							id: '' + (new Date()).getTime(),
							name: 'Search Results',
							isSearch: true,
							params: search
						}).substring(1));

			} else {

				for (key in search) {
					if (search.hasOwnProperty(key)) {
						searchString.push(key + '=' + search[key]);
					}
				}

				url += '.' + format + '?' + searchString.join('&');
			}

			return url;
		},

		_bindModel: function () {
			var nonEmptyParams = null;

			this.model.on('change', this.onModelChange, this);
			this.model.on('change:format', this.onModelFormatChange, this);

			// Bind the form fields to the model
			this._bindInput('starttime');
			this._bindInput('endtime');

			this._bindInput('minmagnitude');
			this._bindInput('maxmagnitude');

			this._bindInput('maxlatitude');
			this._bindInput('minlongitude');
			this._bindInput('maxlongitude');
			this._bindInput('minlatitude');

			this._bindInput('latitude');
			this._bindInput('longitude');
			this._bindInput('minradiuskm');
			this._bindInput('maxradiuskm');


			this._bindInput('mindepth');
			this._bindInput('maxdepth');

			this._bindInput('mingap');
			this._bindInput('maxgap');

			this._bindInput('reviewstatus');
			// TODO :: Conform magnitude type to FDSN spec
			//this._bindInput('magnitudetype');
			this._bindInput('evttype');

			this._bindInput('minsig');
			this._bindInput('maxsig');

			this._bindRadio('alertlevel');

			this._bindInput('minmmi');
			this._bindInput('maxmmi');

			this._bindInput('mincdi');
			this._bindInput('maxcdi');
			this._bindInput('minfelt');

			this._bindInput('catalog');
			this._bindInput('contributor');
			this._bindInput('producttype');


			this._bindRadio('format');
			this._bindRadio('output-quakeml');
			this._bindRadio('output-kml');

			this._bindRadio('orderby');

			this._bindInput('callback');
			this._bindInput('limit');
			this._bindInput('offset');


			if (window.location.hash !== '') {
				// Update the model with information from the hash
				this._parsedUrl = this._parsedUrl.search || {};
				this._parsedUrl = this._parsedUrl.params || null;

				// If parsing a hash, that contains an existing search
				// want to clear default values (if not specified)
				if (this._parsedUrl !== null) {
					if (!this._parsedUrl.hasOwnProperty('starttime')) {
						this._parsedUrl.starttime = '';
					}
					if (!this._parsedUrl.hasOwnProperty('endtime')) {
						this._parsedUrl.endtime = '';
					}
					if (!this._parsedUrl.hasOwnProperty('minmagnitude')) {
						this._parsedUrl.minmagnitude = '';
					}

					this.model.setAll(this._parsedUrl);
				}
			}

			// Expand collapsed sections if any of their parameters are set
			nonEmptyParams = this.model.getNonEmpty();
			// TODO :: Conform magnitude type to FDSN spec
			//if (nonEmptyParams.hasOwnProperty('magnitudetype')) {
				//Util.addClass(this._el.querySelector('#magtype').parentNode,
						//'toggle-visible');
			//}

			if (nonEmptyParams.hasOwnProperty('minsig') ||
					nonEmptyParams.hasOwnProperty('maxsig') ||
					nonEmptyParams.hasOwnProperty('alertlevel') ||
					nonEmptyParams.hasOwnProperty('minmmi') ||
					nonEmptyParams.hasOwnProperty('maxmmi') ||
					nonEmptyParams.hasOwnProperty('mincdi') ||
					nonEmptyParams.hasOwnProperty('maxcdi') ||
					nonEmptyParams.hasOwnProperty('minfelt')) {
				Util.addClass(this._el.querySelector('#impact').parentNode,
						'toggle-visible');
			}

		},

		_bindModelUpdate: function () {
			var nonEmptyParams = null;

			this._bindRadio('reviewstatus');
			// TODO :: Conform magnitude type to FDSN spec
			//this._bindRadio('magnitudetype');
			this._bindRadio('eventtype');

			this._bindRadio('catalog');
			this._bindRadio('contributor');
			this._bindRadio('producttype');

			if (window.location.hash !== '') {
				// Update the model with information from the hash

				// If parsing a hash, that contains an existing search
				// want to clear default values (if not specified)
				if (this._parsedUrl !== null) {
					if (!this._parsedUrl.hasOwnProperty('starttime')) {
						this._parsedUrl.starttime = '';
					}
					if (!this._parsedUrl.hasOwnProperty('endtime')) {
						this._parsedUrl.endtime = '';
					}
					if (!this._parsedUrl.hasOwnProperty('minmagnitude')) {
						this._parsedUrl.minmagnitude = '';
					}

					this.model.setAll(this._parsedUrl);
				}
			}

			// Expand collapsed sections if any of their parameters are set
			nonEmptyParams = this.model.getNonEmpty();

			if (nonEmptyParams.hasOwnProperty('eventtype')) {
				Util.addClass(this._el.querySelector('#evttype').parentNode,
						'toggle-visible');
			}
			if (nonEmptyParams.hasOwnProperty('catalog')) {
				Util.addClass(this._el.querySelector('#cat').parentNode,
						'toggle-visible');
			}
			if (nonEmptyParams.hasOwnProperty('contributor')) {
				Util.addClass(this._el.querySelector('#contrib').parentNode,
						'toggle-visible');
			}
			if (nonEmptyParams.hasOwnProperty('producttype')) {
				Util.addClass(this._el.querySelector('#prodtype').parentNode,
						'toggle-visible');
			}
		},

		_bindInput: function (inputId) {
			var form = this,
			    eventName = 'change:' + inputId,
			    input = this._el.querySelector('#' + inputId),
			    onModelChange = null, onViewChange = null;

			onModelChange = function (newValue) {
				input.value = newValue;
			};

			onViewChange = function () {
				var o = {};
				o[inputId] = input.value;

				form.model.set(o);
			};

			// Update the view when the model changes
			this.model.on(eventName, onModelChange, this);

			// Update the model when the view changes
			Util.addEvent(input, 'change', onViewChange);

			// Update the model with value in the form
			onViewChange();
		},

		_bindRadio: function (inputId) {
			var form = this,
			    eventName = 'change:' + inputId,
			    list = this._el.querySelector('.' + inputId + '-list'),
			    inputs = list.querySelectorAll('input'),
			    input = null, i = 0, numInputs = inputs.length,
			    onModelChange = null, onViewChange = null;


			onModelChange = function (newValue) {
				var values = newValue.split(','), // TODO :: Handle commas in values
				    numValues = values.length,
				    inputs = list.querySelectorAll('input'),
				    numInputs = inputs.length,
				    i = 0, j = 0, input = null;

				for (; i < numInputs; i++) {
					input = inputs.item(i);

					for (j = 0; j < numValues; j++) {
						/* jshint eqeqeq: false */
						if (input.value == values[j]) {
							input.checked = true;
							break;
						}
						/* jshint eqeqeq: true */
					}
				}

			};

			onViewChange = function () {
				var values = {}, valueName = null, input = null,
				    inputs = list.querySelectorAll('input'),
				    numInputs = inputs.length,
				    i = 0, key = null;

				for (; i < numInputs; i++) {
					input = inputs.item(i);
					valueName = input.getAttribute('name');

					// Skip unnamed inputs. These are UI convenience controls
					if (valueName) {
						if (!values.hasOwnProperty(valueName)) {
							values[valueName] = [];
						}

						if (input.checked) {
							values[valueName].push(input.value);
						}
					}

				}

				for (key in values) {
					if (values.hasOwnProperty(key)) {
						values[key] = values[key].join(',');
					}
				}

				form.model.set(values);
			};

			// Update the view when the model changes
			this.model.on(eventName, onModelChange, this);

			// Update the model when the view changes
			for (; i < numInputs; i++) {
				input = inputs.item(i);
				// Only radios and checkboxes here
				if (input.type === 'checkbox' || input.type === 'radio') {
					Util.addEvent(inputs.item(i), 'change', onViewChange);
				}
			}

			// Update model with current information in form
			onViewChange();
		},

		_fetchFieldData: function () {
			var form = this;

			Xhr.ajax({
				url: this.fieldDataUrl,
				success: function (data) {
					form._enhanceField(data.catalogs || [],
							'catalog', form.formatter.formatCatalog);
					form._enhanceField(data.contributors || [],
							'contributor', form.formatter.formatContributor);
					form._enhanceField(data.producttypes || [],
							'producttype', form.formatter.formatProductType, ['two-up']);
					form._enhanceEventType(data.eventtypes || []);

					form._bindModelUpdate();
				}
			});
		},

		/**
		 * Looks for all toggle-able elements in the form and makes them as such.
		 *
		 */
		_enableToggleFields: function () {
			var toggles = this._el.querySelectorAll('.toggle-control'),
			    i = 0, len = toggles.length,
			    t = null, s = null;

			for (; i < len; i++) {
				t = toggles[i];
				s = t.parentNode;

				new ToggleSection({control: t, section: s});
			}
		},


		_addSubmitHandler: function () {
			var form = this;

			// Prevent early submission through <enter> key
			Util.addEvent(this._el, 'keydown', function (evt) {
				var code = evt.keyCode || evt.charCode;
				if (code === 13 && this.id !== 'fdsn-submit') {
					evt.preventDefault();
					return false;
				}
			});

			// Add event handler for form submission
			Util.addEvent(this._el, 'submit', (function () {
				return function (evt) {
					form.onSubmit();
					evt.preventDefault();
					return false;
				};
			})(this));
		},

		_enhanceField: function (fields, name, format, classes) {
			var textInput = this._el.querySelector('#' + name),
			    parentNode = textInput.parentNode,
			    list = parentNode.appendChild(document.createElement('ul')),
			    i, len;

			classes = classes || [];
			Util.addClass(list, name + '-list');
			parentNode.removeChild(textInput);

			for (i = 0, len = classes.length; i < len; i++) {
				Util.addClass(list, classes[i]);
			}

			new SelectField({
				el: list,
				id: name,
				fields: fields,
				type: 'radio',
				formatDisplay: format
			});
		},

		_enhanceEventType: function (fields) {
			var textInput = this._el.querySelector('#eventtype'),
			    parentNode = textInput.parentNode,
			    list = parentNode.appendChild(document.createElement('ul'));

			Util.addClass(list, 'eventtype-list');
			// Util.addClass(list, 'two-up');
			parentNode.removeChild(textInput);

			new EventTypeField({
				el: list,
				id: 'eventtype',
				fields: fields,
				type: 'checkbox',
				formatDisplay: this.formatter.formatEventType
			});
		},

		_enableRegionControl: function () {
			this._regionControl = new ManagedModelView({
				clearedText: 'Currently searching entire world',
				filledText: 'Currently searching custom region',
				controlText: 'Clear Region',
				el: this._el.querySelector('.region-description'),
				model: this.model,
				fields: {
					'maxlatitude': '',
					'minlatitude': '',
					'maxlongitude': '',
					'minlongitude': '',
					'latitude': '',
					'longitude': '',
					'minradiuskm': '',
					'maxradiuskm': ''
				}
			});
		},

		_enableOutputDetailsToggle: function () {
			var list = this._el.querySelector('.format-list'),
			    map = document.createElement('li'),
			    csv = this._el.querySelector('#output-format-csv'),
			    kml = this._el.querySelector('#output-format-kml'),
			    quakeml = this._el.querySelector('#output-format-quakeml'),
			    geojson = this._el.querySelector('#output-format-geojson'),
			    kmlD = this._el.querySelector('#output-format-kml-details'),
			    quakemlD = this._el.querySelector('#output-format-quakeml-details'),
			    geojsonD = this._el.querySelector('#output-format-geojson-details'),
			    handler = null;

			/* jshint -W015 */
			map.innerHTML = [
				'<label for="output-format-maplist" class="label-checkbox">',
					'<input id="output-format-maplist" type="radio" name="format" ',
							'value="maplist" checked/> ',
					'Map &amp; List',
				'</label>'
			].join('');
			/* jshint +W015 */
			list.insertBefore(map, list.firstChild);


			handler = function () {
				if (kml.checked) {
					Util.removeClass(kmlD, 'hidden');
					Util.addClass(quakemlD, 'hidden');
					Util.addClass(geojsonD, 'hidden');
				} else if (quakeml.checked) {
					Util.addClass(kmlD, 'hidden');
					Util.removeClass(quakemlD, 'hidden');
					Util.addClass(geojsonD, 'hidden');
				} else if (geojson.checked) {
					Util.addClass(kmlD, 'hidden');
					Util.addClass(quakemlD, 'hidden');
					Util.removeClass(geojsonD, 'hidden');
				} else {
					Util.addClass(kmlD, 'hidden');
					Util.addClass(quakemlD, 'hidden');
					Util.addClass(geojsonD, 'hidden');
				}
			};

			Util.addEvent(map.querySelector('input'), 'change', handler);
			Util.addEvent(csv, 'change', handler);
			Util.addEvent(kml, 'change', handler);
			Util.addEvent(quakeml, 'change', handler);
			Util.addEvent(geojson, 'change', handler);

			handler(); // Ensure proper visibility of output format details
		}
	};

	return FDSNSearchForm;
});
