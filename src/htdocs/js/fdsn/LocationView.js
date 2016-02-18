'use strict';

var RegionView = require('locationview/RegionView'),
    Util = require('util/Util'),
    View = require('mvc/View');

var LocationView = function (options) {
  var _this,
      _initialize,

      _locationCustom,
      _locationUS,
      _locationWorld,
      _regionButton,
      _regionDescription,

      _createLocationContent,
      _isSet,
      _onLocationCustomClick,
      _onLocationUsClick,
      _onLocationWorldClick,
      _onLocationChange,
      _onRegionButtonClick,
      _onRegionCallback,
      _showRegionView;

  _this = View(options);

  _initialize = function (/*options*/) {
    _createLocationContent();

    if (_this.model.get('maxlatitude') === null &&
        _this.model.get('minlatitude') === null &&
        _this.model.get('maxlongitude') === null &&
        _this.model.get('minlongitude') === null) {
      _this.model.set({
        maxlatitude: null,
        minlatitude: null,
        maxlongitude: null,
        minlongitude: null
      });
    }

    _this.render();
  };

  _createLocationContent = function () {
    var el,
        regionDisclaimer;

    el = _this.el;

    el.innerHTML = [
      '<h3 class="label">Geographic Region</h3>',
      '<ul class="no-style basiclocation-list">',
        '<li>',
          '<input type="radio" name="basic-location" ',
              'id="basic-location-world" value="basic-location-world"/>',
          '<label for="basic-location-world" class="label-checkbox">',
              'World</label>',
        '</li>',
        '<li>',
          '<input type="radio" name="basic-location" ',
              'id="basic-location-us" value="basic-location-us"/>',
          '<label for="basic-location-us" class="label-checkbox">',
              'Conterminous U.S.<sup>1</sup></label>',
        '</li>',
        '<li>',
          '<input type="radio" name="basic-location" ',
              'id="basic-location-custom" value="basic-location-custom"/>',
          '<label for="basic-location-custom" class="label-checkbox">',
              'Custom</label>',
        '</li>',
      '</ul>',
      '<div class="region-description"></div>',
      '<button type="button" class="draw orange">',
        'Draw Rectangle on Map',
      '</button>'
    ].join('');

    _locationWorld = el.querySelector('#basic-location-world');
    _locationUS = el.querySelector('#basic-location-us');
    _locationCustom = el.querySelector('#basic-location-custom');
    _regionDescription = el.querySelector('.region-description');
    _regionButton = el.querySelector('button');

    _locationWorld.addEventListener('click', _onLocationWorldClick);
    _locationUS.addEventListener('click', _onLocationUsClick);
    _locationCustom.addEventListener('click', _onLocationCustomClick);
    _regionButton.addEventListener('click', _onRegionButtonClick);

    regionDisclaimer = document.createElement('small');

    regionDisclaimer.innerHTML = ['<sup>1</sup></a> Conterminous U.S. refers ',
      'to a rectangular region including the lower 48 states and surrounding ',
      'areas which are outside the Conterminous U.S.'].join('');

    document.querySelector('.page-content').appendChild(regionDisclaimer);
  };

  _isSet = function (value) {
    return (typeof value !== 'undefined' && value !== '' && value !== null);
  };

  _onLocationWorldClick = function () {
    _this.model.set({
      maxlatitude: null,
      minlatitude: null,
      maxlongitude: null,
      minlongitude: null,
      latitude: null,
      longitude: null,
      maxradiuskm: null
    });
  };

  _onLocationUsClick = function () {
    _this.model.set({
      maxlatitude: 50,
      minlatitude: 24.6,
      maxlongitude: -65,
      minlongitude: -125,
      latitude: null,
      longitude: null,
      maxradiuskm: null
    });
  };

  _onLocationCustomClick = function () {
    _showRegionView();
  };

  _onRegionButtonClick = function () {
    _showRegionView();
  };

  _onRegionCallback = function (region) {
    var maxlat,
        maxlng,
        minlat,
        minlng;

    maxlat = region.get('north');
    minlat = region.get('south');
    maxlng = region.get('east');
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

    _this.model.set({
      maxlatitude: maxlat,
      minlatitude: minlat,
      maxlongitude: maxlng,
      minlongitude: minlng
    });
  };

  _showRegionView = function () {
    var north,
        south,
        east,
        west;

    north = _this.model.get('maxlatitude');
    south = _this.model.get('minlatitude');
    east = _this.model.get('maxlongitude');
    west = _this.model.get('minlongitude');

    if (north === '') {
      north = null;
    }
    if (south === '') {
      south = null;
    }
    if (east === '') {
      east = null;
    }
    if (west === '') {
      west = null;
    }

    RegionView({
      onRegionCallback: _onRegionCallback
    }).show({
      region:{
        north: north,
        south: south,
        east: east,
        west: west
      },
      enableRectangleControl: true
    });
  };

  _this.render = function () {
    var east,
        hasCircle,
        hasEast,
        hasLat,
        hasLng,
        hasNorth,
        hasRadius,
        hasRect,
        hasSouth,
        hasWest,
        latitude,
        longitude,
        markup,
        maxradiuskm,
        north,
        south,
        west;

    markup = [];

    north = _this.model.get('maxlatitude');
    south = _this.model.get('minlatitude');
    east = _this.model.get('maxlongitude');
    west = _this.model.get('minlongitude');
    latitude = _this.model.get('latitude');
    longitude = _this.model.get('longitude');
    maxradiuskm = _this.model.get('maxradiuskm');

    hasNorth = _isSet(north);
    hasSouth = _isSet(south);
    hasEast = _isSet(east);
    hasWest = _isSet(west);
    hasLat = _isSet(latitude);
    hasLng = _isSet(longitude);
    hasRadius = _isSet(maxradiuskm);

    hasRect = hasNorth || hasSouth || hasEast || hasWest;
    hasCircle = hasLat && hasLng && hasRadius;

    if (!hasRect && !hasCircle) {
      markup.push('<h3>Worldwide</h3>');
      _locationWorld.checked = true;
    } else {
      if (hasRect) {
        if (north === 50.0 && south === 24.6 && east === -65.0 &&
            west === -125.0) {
          markup.push('<h3>Conterminous U.S.</h3>');
          _locationUS.checked = true;
        } else {
          markup.push('<h3>Custom Rectangle</h3>');
          _locationCustom.checked = true;
        }

        markup.push('<ul class="search-text help">',
            '<li>[',
              hasSouth ? south : '&ndash;', ', ',
              hasNorth ? north : '&ndash;',
            '] Latitude</li>',
            '<li>[',
              hasWest ? west : '&ndash;', ', ',
              hasEast ? east : '&ndash;',
            '] Longitude</li>',
          '</ul>'
        );
      }

      if (hasCircle) {
        markup.push('<h3>Custom Circle</h3>',
          '<ul class="search-text help">',
            '<li>', latitude, ' Latitude</li>',
            '<li>', longitude, ' Longitude</li>',
            '<li>', maxradiuskm, ' Radius (km)</li>',
          '</ul>'
        );
        _locationCustom.checked = true;
      }
    }

    _regionDescription.innerHTML = markup.join('');
  };

  _this.destroy = Util.compose(function () {
    _locationWorld.removeEventListener('click', _onLocationWorldClick);
    _locationUS.removeEventListener('click', _onLocationUsClick);
    _locationCustom.removeEventListener('click', _onLocationCustomClick);
    _regionButton.removeEventListener('click', _onRegionButtonClick);


    _locationCustom = null;
    _locationUS = null;
    _locationWorld = null;
    _regionButton = null;
    _regionDescription = null;

    _createLocationContent = null;
    _isSet = null;
    _onLocationCustomClick = null;
    _onLocationUsClick = null;
    _onLocationWorldClick = null;
    _onLocationChange = null;
    _onRegionButtonClick = null;
    _onRegionCallback = null;
    _showRegionView = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);

  _initialize(options);
  options = null;
  return _this;
};

module.exports = LocationView;
