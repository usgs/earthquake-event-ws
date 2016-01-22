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
      _regionDescription,

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
    _regionDescription = _this.el.querySelector('.region-description');

    _locationWorld.addEventListener('click', _onLocationWorldClick);
    _locationUS.addEventListener('click', _onLocationUsClick);
    _locationCustom.addEventListener('click', _onLocationCustomClick);
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

        if (north === 50.0 && south === 24.6 && east === -65.0 &&
            west === -125.0) {
          _locationUS.checked = true;
        } else {
          _locationCustom.checked = true;
        }
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


    _locationCustom = null;
    _locationUS = null;
    _locationWorld = null;
    _regionDescription = null;

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
