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
  var _el,
      _fdsnHost,
      _fdsnPath,
      _fieldDataUrl,
      _formatter,
      _model,
      _regionControl,
      _this,
      _validator,
      
      _addSubmitHandler,
      _bindInput,
      _bindModel,
      _bindModelUpdate,
      _bindRadio,
      _enableOutputDetailsToggle,
      _enableRegionControl,
      _enableToggleFields,
      _enhanceEventType,
      _enhanceField,
      _fetchFieldData,
      _formatSearchErrors,
      _initialize,
      _onModelChange,
      _onModelFormatChange,
      _onSubmit,
      _serializeFormToUrl,
      _trimSearch;

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
    _el = options.el || document.createElement('div');
    _fieldDataUrl = options.fieldDataUrl || null;
    _model = options.model || FDSNModel();

    if (options.hasOwnProperty('fdsnHost')) {
      _fdsnHost = options.fdsnHost;
    } else {
      _fdsnHost = 'http://' + window.location.host;
    }

    if (options.hasOwnProperty('fdsnPath')) {
      _fdsnPath = options.fdsnPath;
    } else {
      _fdsnPath = '/fdsnws/event/1';
    }

    // Formatting field display text (catalogs, contributors, etc...)
    _formatter = UrlBuilderFormatter();

    // Validator to auto-validate the model
    _validator = FDSNModelValidator({model: _model});

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
          field = _el.querySelector('#' + fields[i]);
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
    var url = _fdsnHost + _fdsnPath + '/query',
        search = _trimSearch(_model.getNonEmpty()),
        maplistsort = 'newest', searchsort = _model.get('orderby'),
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

    _model.on('change', _onModelChange);
    _model.on('change:format', _onModelFormatChange);

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

        _model.setAll(parsedUrl);
      }
    }

    // Expand collapsed sections if any of their parameters are set
    nonEmptyParams = _model.getNonEmpty();
    // TODO :: Conform magnitude type to FDSN spec
    //if (nonEmptyParams.hasOwnProperty('magnitudetype')) {
      //Util.addClass(_el.querySelector('#magtype').parentNode,
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
      _el.querySelector('#impact').parentNode.classList.add(
          'toggle-visible');
    }

  };

  _bindModelUpdate = function () {
    var nonEmptyParams = null;

    _bindRadio('eventtype');

    _bindRadio('catalog');
    _bindRadio('contributor');
    _bindRadio('producttype');

    // Expand collapsed sections if any of their parameters are set
    nonEmptyParams = _model.getNonEmpty();

    if (nonEmptyParams.hasOwnProperty('eventtype')) {
      _el.querySelector('#evttype').parentNode.classList.add(
          'toggle-visible');
    }
    if (nonEmptyParams.hasOwnProperty('catalog')) {
      _el.querySelector('#cat').parentNode.classList.add(
          'toggle-visible');
    }
    if (nonEmptyParams.hasOwnProperty('contributor')) {
      _el.querySelector('#contrib').parentNode.classList.add(
          'toggle-visible');
    }
    if (nonEmptyParams.hasOwnProperty('producttype')) {
      _el.querySelector('#prodtype').parentNode.classList.add(
          'toggle-visible');
    }
  };

  _bindInput = function (inputId) {
    var eventName = 'change:' + inputId,
        input = _el.querySelector('#' + inputId),
        onModelChange = null, onViewChange = null;

    onModelChange = function (newValue) {
      input.value = newValue;
    };

    onViewChange = function () {
      var o = {};
      o[inputId] = input.value;

      _model.set(o);
    };

    // Update the view when the model changes
    _model.on(eventName, onModelChange);

    // Update the model when the view changes
    input.addEventListener('change', onViewChange);

    // Update the model with value in the form
    onViewChange();
  };

  _bindRadio = function (inputId) {
    var eventName = 'change:' + inputId,
        list = _el.querySelector('.' + inputId + '-list'),
        inputs = list.querySelectorAll('input'),
        input = null, i = 0, numInputs = inputs.length,
        onModelChange = null, onViewChange = null;


    onModelChange = function (newValue) {
      var values = (newValue || '').split(','), // TODO :: Handle commas in values
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

      _model.set(values);
    };

    // Update the view when the model changes
    _model.on(eventName, onModelChange);

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

    Xhr.ajax({
      url: _fieldDataUrl,
      success: function (data) {
        _enhanceField(data.catalogs || [],
            'catalog', _formatter.formatCatalog);
        _enhanceField(data.contributors || [],
            'contributor', _formatter.formatContributor);
        _enhanceField(data.producttypes || [],
            'producttype', _formatter.formatProductType);
        _enhanceEventType(data.eventtypes || []);

        _bindModelUpdate();
      }
    });
  };

  /**
   * Looks for all toggle-able elements in the form and makes them as such.
   *
   */
  _enableToggleFields = function () {
    var toggles = _el.querySelectorAll('.toggle-control'),
        i = 0, len = toggles.length,
        t = null, s = null;

    for (; i < len; i++) {
      t = toggles[i];
      s = t.parentNode;

      ToggleSection({control: t, section: s});
    }
  };


  _addSubmitHandler = function () {
    // Prevent early submission through <enter> key
    _el.addEventListener('keydown', function (evt) {
      var code = evt.keyCode || evt.charCode;
      if (code === 13 && evt.target.id !== 'fdsn-submit') {
        evt.preventDefault();
        return false;
      }
    });

    // Add event handler for form submission
    _el.addEventListener('submit',
      function (evt) {
        _onSubmit();
        evt.preventDefault();
        return false;
      }
    );
  };

  _enhanceField = function (fields, name, format, classes) {
    var textInput = _el.querySelector('#' + name),
        parentNode = textInput.parentNode,
        list = parentNode.appendChild(document.createElement('ul')),
        inputModel,
        element,
        selectField,
        i, len;

    inputModel  = (_model.get(name) || '').split(',');
    classes = classes || [];
    list.classList.add(name + '-list');
    // IE 11 bug, doesnt support multiple tokens
    list.classList.add('no-style');
    parentNode.removeChild(textInput);

    for (i = 0, len = classes.length; i < len; i++) {
      list.classList.addClass(classes[i]);
    }

    selectField = SelectField({
      el: list,
      id: name,
      fields: fields,
      type: 'radio',
      formatDisplay: format
    });

    len = inputModel.length;
    for (i = 0; i < len; i++) {
      element = _el.querySelector('#' +
        selectField._getFieldId(inputModel[i]));
      if (element !== null) {
        element.checked = true;
      }
    }
  };

  _enhanceEventType = function (fields) {
    var textInput = _el.querySelector('#eventtype'),
        parentNode = textInput.parentNode,
        list = parentNode.appendChild(document.createElement('ul')),
        inputModel,
        element,
        eventType,
        i, len;

    inputModel = (_model.get('eventtype') || '').split(',');
    list.classList.add('eventtype-list');
    // IE 11 bug, doesnt support multiple tokens
    list.classList.add('no-style');
    parentNode.removeChild(textInput);

    eventType = EventTypeField({
      el: list,
      id: 'eventtype',
      fields: fields,
      type: 'checkbox',
      formatDisplay: _formatter.formatEventType
    });

    len = inputModel.length;
    for (i = 0; i < len; i++) {
      element = _el.querySelector('#' +
          eventType._getFieldId(inputModel[i]));
      if (element !== null) {
        element.checked = true;
      }
    }
  };

  _enableRegionControl = function () {
    var drawRectangleButton = _el.querySelector('.draw'),
        maxLatitude = document.querySelector('#maxlatitude'),
        minLatitude = document.querySelector('#minlatitude'),
        maxLongitude = document.querySelector('#maxlongitude'),
        minLongitude = document.querySelector('#minlongitude'),
        regionView,
        _onRegionCallback;

    _regionControl = ManagedModelView({
      clearedText: 'Currently searching entire world',
      filledText: 'Currently searching custom region',
      controlText: 'Clear Region',
      el: _el.querySelector('.region-description'),
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
      var maxlat = region.get('north'),
          minlat = region.get('south'),
          maxlng = region.get('east'),
          minlng = region.get('west');
      
      if ((maxlat || maxlat === 0.0) && !isNaN(maxlat)) {
        maxlat = parseFloat(maxlat.toFixed(3));
      }
      if ((minlat || minlat === 0.0) && !isNaN(minlat)) {
        minlat = parseFloat(minlat.toFixed(3));
      }
      if ((maxlng || maxlng === 0.0) && !isNaN(maxlng)) {
        maxlng = parseFloat(maxlng.toFixed(3));
      }
      if ((minlng || minlng === 0.0) && !isNaN(minlng)) {
        minlng = parseFloat(minlng.toFixed(3));
      }

      _model.set({
        maxlatitude: maxlat,
        minlatitude: minlat,
        maxlongitude: maxlng,
        minlongitude: minlng
      });
    };

    // Initialize RegionView
    regionView = RegionView({
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
    var list = _el.querySelector('.format-list'),
        map = document.createElement('li'),
        csv = _el.querySelector('#output-format-csv'),
        kml = _el.querySelector('#output-format-kml'),
        quakeml = _el.querySelector('#output-format-quakeml'),
        geojson = _el.querySelector('#output-format-geojson'),
        kmlD = _el.querySelector('#output-format-kml-details'),
        quakemlD = _el.querySelector('#output-format-quakeml-details'),
        geojsonD = _el.querySelector('#output-format-geojson-details'),
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

  _onSubmit = function () {

    if (_validator.isValid()) {
      window.location = _serializeFormToUrl();
    } else {

      // Some errors during submission, show them
      _el.classList.add('show-errors');

      (ModalView(_formatSearchErrors(
          _validator.getErrors()), {
        title: 'Form Errors',
        classes: ['modal-error'],
        buttons: [
          {
            text: 'Edit Search',
            title: 'Go back to form and edit search',
            callback: function (evt, dialog) {
              var error = _el.querySelector('input.error'),
                  fields = null,
                  field = null,
                  section = null,
                  i = 0, len = 0;

              dialog.hide();

              // Make error fields visible (might be in collapsed secion)
              fields = Array.prototype.slice.call(
                  _el.querySelectorAll('.error'), 0);
              len = fields.length;

              for (i = 0; i < len; i++) {
                field = fields[i];
                section = Util.getParentNode(field, 'SECTION', _el);

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

  _onModelChange = function () {
    var fields = _el.querySelectorAll('.error'),
        searchError = _el.querySelector('.search-error'),
        i = 0,
        len = fields.length;

    // Clear any previously marked error fields
    for (; i < len; i++) {
      fields.item(i).classList.remove('error');
    }

    if (!_validator.isValid()) {
      searchError.innerHTML =
        _formatSearchErrors(_validator.getErrors());
    } else {
      searchError.innerHTML = '';
      _el.classList.remove('show-errors');
    }
  };

  _onModelFormatChange = function () {
    var format = _model.get('format'),
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

    _el.querySelector('.output-descriptor').innerHTML =
        'Output Format: ' + text;
  };


  _initialize();
  return _this;
};


module.exports = FDSNSearchForm;
