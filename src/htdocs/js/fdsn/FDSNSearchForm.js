/* global window, document */

'use strict';

var FDSNModel = require('fdsn/FDSNModel'),
    FDSNModelValidator = require('fdsn/FDSNModelValidator'),
    SelectField = require('fdsn/SelectField'),
    EventTypeField = require('fdsn/EventTypeField'),
    UrlBuilderFormatter = require('fdsn/UrlBuilderFormatter'),
    ManagedModelView = require('fdsn/ManagedModelView'),
    ToggleSection = require('fdsn/ToggleSection'),
    UrlManager = require('fdsn/UrlManager'),
    Util = require('util/Util'),
    Xhr = require('util/Xhr'),
    ModalView = require('mvc/ModalView'),
    RegionView = require('locationview/RegionView');

// Static counter to increment each time a JSONP callback is created. This
// allows unique callback to be generated and executed in global scope
//var CALLBACK_COUNT = 0;



var FDSNSearchForm = function (options) {
  var _this,
      _initialize,
      
      _addSubmitHandler,
      _bindInput,
      _bindModel,
      _bindRadio,
      _enableOutputDetailsToggle,
      _enableRegionControl,
      _enableToggleFields,
      _fetchFieldData,
      _formatSearchErrors,
      _serializeFormToUrl,
      _trimSearch,

      onModelChange,
      onModelFormatChange,
      onSubmit;

    _this = {};


  // --------------------------------------------------
  // "Private" methods
  // --------------------------------------------------


  /**
   * Initializes the form.
   *
   */
  _initialize = function () {

    // Pull conf options off the options and store as instance variables
    _this.el = options.el || document.createElement('div');
    _this.fieldDataUrl = options.fieldDataUrl || null;
    _this.model = options.model || FDSNModel();

    if (options.hasOwnProperty('fdsnHost')) {
      _this.fdsnHost = options.fdsnHost;
    } else {
      _this.fdsnHost = 'http://' + window.location.host;
    }

    if (options.hasOwnProperty('fdsnPath')) {
      _this.fdsnPath = options.fdsnPath;
    } else {
      _this.fdsnPath = '/fdsnws/event/1';
    }

    // Formatting field display text (catalogs, contributors, etc...)
    _this.formatter = UrlBuilderFormatter();

    // Validator to auto-validate the model
    _this.validator = FDSNModelValidator({model: _this.model});

    // Create the form
    _enableToggleFields();

    _enableRegionControl();
    _enableOutputDetailsToggle();

    _bindModel();
    _addSubmitHandler();

    _fetchFieldData();
  };

  _formatSearchErrors = function (errors) {
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
          field = _this.el.querySelector('#' + fields[i]);
          if (field !== null) {
            field.classList.add('error');
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
  };

  _trimSearch = function (params) {

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
  };

  _serializeFormToUrl = function () {
    var url = _this.fdsnHost + _this.fdsnPath + '/query',
        search = _trimSearch(_this.model.getNonEmpty()),
        maplistsort = 'newest', searchsort = _this.model.get('orderby'),
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
  };

  _bindModel = function () {
    var nonEmptyParams = null, parsedUrl = null;

    _this.model.on('change', onModelChange, _this);
    _this.model.on('change:format', onModelFormatChange, _this);

    // Bind the form fields to the model
    _bindInput('starttime');
    _bindInput('endtime');

    _bindInput('minmagnitude');
    _bindInput('maxmagnitude');

    _bindInput('maxlatitude');
    _bindInput('minlongitude');
    _bindInput('maxlongitude');
    _bindInput('minlatitude');

    _bindInput('latitude');
    _bindInput('longitude');
    _bindInput('minradiuskm');
    _bindInput('maxradiuskm');


    _bindInput('mindepth');
    _bindInput('maxdepth');

    _bindInput('mingap');
    _bindInput('maxgap');

    _bindRadio('reviewstatus');
    // TODO :: Conform magnitude type to FDSN spec
    //_bindInput('magnitudetype');
    _bindInput('eventtype');

    _bindInput('minsig');
    _bindInput('maxsig');

    _bindRadio('alertlevel');

    _bindInput('minmmi');
    _bindInput('maxmmi');

    _bindInput('mincdi');
    _bindInput('maxcdi');
    _bindInput('minfelt');

    _bindInput('catalog');
    _bindInput('contributor');
    _bindInput('producttype');


    _bindRadio('format');
    _bindRadio('output-quakeml');
    _bindRadio('output-kml');

    _bindRadio('orderby');

    _bindInput('callback');
    _bindInput('limit');
    _bindInput('offset');


    if (window.location.hash !== '') {
      // Update the model with information from the hash
      parsedUrl = UrlManager.parseUrl();
      parsedUrl = parsedUrl.search || {};
      parsedUrl = parsedUrl.params || null;

      // If parsing a hash, that contains an existing search
      // want to clear default values (if not specified)
      if (parsedUrl !== null) {
        if (!parsedUrl.hasOwnProperty('starttime')) {
          parsedUrl.starttime = '';
        }
        if (!parsedUrl.hasOwnProperty('endtime')) {
          parsedUrl.endtime = '';
        }
        if (!parsedUrl.hasOwnProperty('minmagnitude')) {
          parsedUrl.minmagnitude = '';
        }

        _this.model.setAll(parsedUrl);
      }
    }

    // Expand collapsed sections if any of their parameters are set
    nonEmptyParams = _this.model.getNonEmpty();
    // TODO :: Conform magnitude type to FDSN spec
    //if (nonEmptyParams.hasOwnProperty('magnitudetype')) {
      //Util.addClass(_this.el.querySelector('#magtype').parentNode,
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
      _this.el.querySelector('#impact').parentNode.classList.add(
          'toggle-visible');
    }

  };

  _this._bindModelUpdate = function () {
    var nonEmptyParams = null;

    _bindRadio('eventtype');

    _bindRadio('catalog');
    _bindRadio('contributor');
    _bindRadio('producttype');

    // Expand collapsed sections if any of their parameters are set
    nonEmptyParams = _this.model.getNonEmpty();

    if (nonEmptyParams.hasOwnProperty('eventtype')) {
      _this.el.querySelector('#evttype').parentNode.classList.add(
          'toggle-visible');
    }
    if (nonEmptyParams.hasOwnProperty('catalog')) {
      _this.el.querySelector('#cat').parentNode.classList.add(
          'toggle-visible');
    }
    if (nonEmptyParams.hasOwnProperty('contributor')) {
      _this.el.querySelector('#contrib').parentNode.classList.add(
          'toggle-visible');
    }
    if (nonEmptyParams.hasOwnProperty('producttype')) {
      _this.el.querySelector('#prodtype').parentNode.classList.add(
          'toggle-visible');
    }
  };

  _bindInput = function (inputId) {
    var form = _this,
        eventName = 'change:' + inputId,
        input = _this.el.querySelector('#' + inputId),
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
    _this.model.on(eventName, onModelChange, _this);

    // Update the model when the view changes
    input.addEventListener('change', onViewChange);

    // Update the model with value in the form
    onViewChange();
  };

  _bindRadio = function (inputId) {
    var form = _this,
        eventName = 'change:' + inputId,
        list = _this.el.querySelector('.' + inputId + '-list'),
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
    _this.model.on(eventName, onModelChange, _this);

    // Update the model when the view changes
    for (; i < numInputs; i++) {
      input = inputs.item(i);
      // Only radios and checkboxes here
      if (input.type === 'checkbox' || input.type === 'radio') {
        inputs.item(i).addEventListener('change', onViewChange);
      }
    }

    // Update model with current information in form
    onViewChange();
  };

  _fetchFieldData = function () {
    var form = _this;

    Xhr.ajax({
      url: _this.fieldDataUrl,
      success: function (data) {
        form._enhanceField(data.catalogs || [],
            'catalog', form.formatter.formatCatalog);
        form._enhanceField(data.contributors || [],
            'contributor', form.formatter.formatContributor);
        form._enhanceField(data.producttypes || [],
            'producttype', form.formatter.formatProductType);
        form._enhanceEventType(data.eventtypes || []);

        form._bindModelUpdate();
      }
    });
  };

  /**
   * Looks for all toggle-able elements in the form and makes them as such.
   *
   */
  _enableToggleFields = function () {
    var toggles = _this.el.querySelectorAll('.toggle-control'),
        i = 0, len = toggles.length,
        t = null, s = null;

    for (; i < len; i++) {
      t = toggles[i];
      s = t.parentNode;

      new ToggleSection({control: t, section: s});
    }
  };


  _addSubmitHandler = function () {
    var form = _this;

    // Prevent early submission through <enter> key
    _this.el.addEventListener('keydown', function (evt) {
      var code = evt.keyCode || evt.charCode;
      if (code === 13 && _this.id !== 'fdsn-submit') {
        evt.preventDefault();
        return false;
      }
    });

    // Add event handler for form submission
    _this.el.addEventListener('submit', (function () {
      return function (evt) {
        form.onSubmit();
        evt.preventDefault();
        return false;
      };
    })(this));
  };

  _this._enhanceField = function (fields, name, format, classes) {
    var textInput = _this.el.querySelector('#' + name),
        parentNode = textInput.parentNode,
        list = parentNode.appendChild(document.createElement('ul')),
        inputModel,
        element,
        selectField,
        i, len;

    inputModel  = _this.model.get(name).split(',');
    classes = classes || [];
    list.classList.add(name + '-list');
    // IE 11 bug, doesnt support multiple tokens
    list.classList.add('no-style');
    parentNode.removeChild(textInput);

    for (i = 0, len = classes.length; i < len; i++) {
      list.classList.addClass(classes[i]);
    }

    selectField = new SelectField({
      el: list,
      id: name,
      fields: fields,
      type: 'radio',
      formatDisplay: format
    });

    len = inputModel.length;
    for (i = 0; i < len; i++) {
      element = _this.el.querySelector('#' +
        selectField._getFieldId(inputModel[i]));
      if (element !== null) {
        element.checked = true;
      }
    }
  };

  _this._enhanceEventType = function (fields) {
    var textInput = _this.el.querySelector('#eventtype'),
        parentNode = textInput.parentNode,
        list = parentNode.appendChild(document.createElement('ul')),
        inputModel,
        element,
        eventType,
        i, len;

    inputModel = _this.model.get('eventtype').split(',');
    list.classList.add('eventtype-list');
    // IE 11 bug, doesnt support multiple tokens
    list.classList.add('no-style');
    parentNode.removeChild(textInput);

    eventType = new EventTypeField({
      el: list,
      id: 'eventtype',
      fields: fields,
      type: 'checkbox',
      formatDisplay: _this.formatter.formatEventType
    });

    len = inputModel.length;
    for (i = 0; i < len; i++) {
      element = _this.el.querySelector('#' +
          eventType._getFieldId(inputModel[i]));
      if (element !== null) {
        element.checked = true;
      }
    }
  };

  _enableRegionControl = function () {
    var drawRectangleButton = _this.el.querySelector('.draw'),
        maxLatitude = document.querySelector('#maxlatitude'),
        minLatitude = document.querySelector('#minlatitude'),
        maxLongitude = document.querySelector('#maxlongitude'),
        minLongitude = document.querySelector('#minlongitude'),
        regionView,
        _onRegionCallback,
        _model;

    _model = _this.model;

    _this._regionControl = ManagedModelView({
      clearedText: 'Currently searching entire world',
      filledText: 'Currently searching custom region',
      controlText: 'Clear Region',
      el: _this.el.querySelector('.region-description'),
      model: _model,
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

    // set form values on callback from regionview
    _onRegionCallback = function (region) {
      _model.set({
        maxlatitude: region.get('north'),
        minlatitude: region.get('south'),
        maxlongitude: region.get('east'),
        minlongitude: region.get('west')
      });
    };

    // Initialize RegionView
    regionView = new RegionView({
      onRegionCallback: _onRegionCallback
    });

    // Add rectangle controls for drawing on map
    drawRectangleButton.addEventListener('click', function () {
      var region = null,
          north,
          south,
          east,
          west;

      north = (maxLatitude.value === '') ? null : parseFloat(maxLatitude.value);
      south = (minLatitude.value === '') ? null : parseFloat(minLatitude.value);
      east = (maxLongitude.value === '') ? null : parseFloat(maxLongitude.value);
      west = (minLongitude.value === '') ? null : parseFloat(minLongitude.value);

      if (north === null &&south === null && east === null && west === null ) {
        regionView.show({region: null});
      } else {
        region = {
          north: north,
          south: south,
          east:  east,
          west:  west
        };
        regionView.show({region: region});
      }
    });
  };

  _enableOutputDetailsToggle = function () {
    var list = _this.el.querySelector('.format-list'),
        map = document.createElement('li'),
        csv = _this.el.querySelector('#output-format-csv'),
        kml = _this.el.querySelector('#output-format-kml'),
        quakeml = _this.el.querySelector('#output-format-quakeml'),
        geojson = _this.el.querySelector('#output-format-geojson'),
        kmlD = _this.el.querySelector('#output-format-kml-details'),
        quakemlD = _this.el.querySelector('#output-format-quakeml-details'),
        geojsonD = _this.el.querySelector('#output-format-geojson-details'),
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
        kmlD.classList.remove('hidden');
        quakemlD.classList.add('hidden');
        geojsonD.classList.add('hidden');
      } else if (quakeml.checked) {
        kmlD.classList.add('hidden');
        quakemlD.classList.remove('hidden');
        geojsonD.classList.add('hidden');
      } else if (geojson.checked) {
        kmlD.classList.add('hidden');
        quakemlD.classList.add('hidden');
        geojsonD.classList.remove('hidden');
      } else {
        kmlD.classList.add('hidden');
        quakemlD.classList.add('hidden');
        geojsonD.classList.add('hidden');
      }
    };

    map.querySelector('input').addEventListener('change', handler);
    csv.addEventListener('change', handler);
    kml.addEventListener('change', handler);
    quakeml.addEventListener('change', handler);
    geojson.addEventListener('change', handler);

    handler(); // Ensure proper visibility of output format details
  };

  // --------------------------------------------------
  // Event handlers
  // --------------------------------------------------

  onSubmit = function () {
    var form = _this;

    if (_this.validator.isValid()) {
      window.location = _serializeFormToUrl();
    } else {

      // Some errors during submission, show them
      _this.el.classList.add('show-errors');

      (new ModalView(this._formatSearchErrors(
          _this.validator.getErrors()), {
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

                if (section !== null && section.classList.contains('toggle') &&
                    !section.classList.contains('toggle-visible')) {
                  section.classList.add('toggle-visible');
                }
              }

              // Focus first error field
              error.focus();
            },
          }
        ]
      })).show();
    }
  };

  onModelChange = function () {
    var fields = _this.el.querySelectorAll('.error'),
        searchError = _this.el.querySelector('.search-error'),
        i = 0,
        len = fields.length;

    // Clear any previously marked error fields
    for (; i < len; i++) {
      fields.item(i).classList.remove('error');
    }

    if (!_this.validator.isValid()) {
      searchError.innerHTML =
        _formatSearchErrors(_this.validator.getErrors());
    } else {
      searchError.innerHTML = '';
      _this.el.classList.remove('show-errors');
    }
  };

  onModelFormatChange = function () {
    var format = _this.model.get('format'),
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

    _this.el.querySelector('.output-descriptor').innerHTML =
        'Output Format: ' + text;
  };


  _initialize();
  return _this;
};


module.exports = FDSNSearchForm;
