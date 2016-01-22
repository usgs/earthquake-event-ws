'use strict';

var RegionView = require('locationview/RegionView'),
    Util = require('util/Util'),
    View = require('mvc/View');

var LocationView = function (options) {
  var _this,
      _initialize,

      _circleCenterLatitude,
      _circleCenterLongitude,
      _circleOuterRadius,
      _east,
      _locationCustom,
      _locationUS,
      _locationWorld,
      _north,
      _south,
      _west,

      _createLocationContent,
      _isSet,
      _onLocationCustomClick,
      _onLocationUsClick,
      _onLocationWorldClick,
      _onLocationChange,
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
  };

  _createLocationContent = function () {
    var listWrapper;

    listWrapper = _this.el.querySelector ('.basiclocation-list') ||
        document.createElement('ul');

    listWrapper.innerHTML = [
      '<li>',
        '<input id="basiclocation-world" type="radio" name="basiclocation"',
            'value="basiclocationworld" checked="checked"',
            'aria-labelledby="basiclocationworld"/>',
        '<label for="basiclocation-world" class="label-checkbox">',
          'World',
        '</label>',
      '</li>',
      '<li>',
        '<input id="basiclocation-us" type="radio" name="basiclocation"',
            'value="basiclocationus" aria-labelledby="basiclocationus"/>',
        '<label for="basiclocation-us" class="label-checkbox">',
          'Conterminous US',
        '</label>',
      '</li>',
      '<li>',
        '<input id="basiclocation-custom" type="radio" name="basiclocation"',
            'value="basiclocationcustom" aria-labelledby="basiclocationcustom"/>',
        '<label for="basiclocation-custom" class="label-checkbox">',
          'Custom',
        '</label>',
      '</li>',
      '<ul class="search-text help">',
      '</ul>'
    ].join('');

    _locationWorld = listWrapper.querySelector('#basiclocation-world');
    _locationUS = listWrapper.querySelector('#basiclocation-us');
    _locationCustom = listWrapper.querySelector('#basiclocation-custom');

    _north = document.querySelector('#maxlatitude');
    _west = document.querySelector('#minlongitude');
    _east = document.querySelector('#maxlongitude');
    _south = document.querySelector('#minlatitude');
    _circleCenterLatitude = document.querySelector('#latitude');
    _circleCenterLongitude = document.querySelector('#longitude');
    _circleOuterRadius = document.querySelector('#maxradiuskm');

    _locationWorld.addEventListener('click', _onLocationWorldClick);
    _locationUS.addEventListener('click', _onLocationUsClick);
    _locationCustom.addEventListener('click', _onLocationCustomClick);

    _north.addEventListener('change', _onLocationChange);
    _west.addEventListener('change', _onLocationChange);
    _east.addEventListener('change', _onLocationChange);
    _south.addEventListener('change', _onLocationChange);
    _circleCenterLatitude.addEventListener('change', _onLocationChange);
    _circleCenterLongitude.addEventListener('change', _onLocationChange);
    _circleOuterRadius.addEventListener('change', _onLocationChange);
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
      minlongitude: -125
    });
  };

  _onLocationCustomClick = function () {
    _north.focus();
    _north.select();
    _showRegionView();
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

    RegionView().show({
      region:{
        north: north,
        south: south,
        east: east,
        west: west
      },
      enableRectangleControl: true
    });
  };

  _onLocationChange = function () {
    _locationCustom.checked = true;
    _this.model.set({
      maxlatitude: _north.value,
      minlatitude: _south.value,
      maxlongitude: _east.value,
      minlongitude: _west.value,
      latitude: _circleCenterLatitude.value,
      longitude: _circleCenterLongitude.value,
      maxradiuskm: _circleOuterRadius.value
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
        markup.push('<h3>Custom Rectangle</h3>',
          '<ul class="search-text help">',
            '<li>[',
              hasSouth ? south : '&ndash;', ', ',
              hasNorth ? north : '&ndash;',
            ']</li>',
            '<li>[',
              hasWest ? west : '&ndash;', ', ',
              hasEast ? east : '&ndash;',
            ']</li>',
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
      }
    }

    document.querySelector('.region-description').innerHTML = markup.join('');
  };

  _this.destroy = Util.compose(function () {
    _locationWorld.removeEventListener('click', _onLocationWorldClick);
    _locationUS.removeEventListener('click', _onLocationUsClick);
    _locationCustom.removeEventListener('click', _onLocationCustomClick);
    _north.removeEventListener('change', _onLocationChange);
    _west.removeEventListener('change', _onLocationChange);
    _east.removeEventListener('change', _onLocationChange);
    _south.removeEventListener('change', _onLocationChange);
    _circleCenterLatitude.removeEventListener('change', _onLocationChange);
    _circleCenterLongitude.removeEventListener('change', _onLocationChange);
    _circleOuterRadius.removeEventListener('change', _onLocationChange);

    _circleCenterLatitude = null;
    _circleCenterLongitude = null;
    _circleOuterRadius = null;
    _east = null;
    _locationCustom = null;
    _locationUS = null;
    _locationWorld = null;
    _north = null;
    _south = null;
    _west = null;

    _createLocationContent = null;
    _isSet = null;
    _onLocationCustomClick = null;
    _onLocationUsClick = null;
    _onLocationWorldClick = null;
    _onLocationChange = null;
    _showRegionView = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);

  _initialize(options);
  options = null;
  return _this;
};

module.exports = LocationView;
