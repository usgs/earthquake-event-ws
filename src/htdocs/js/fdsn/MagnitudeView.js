'use strict';

var Util = require('util/Util'),
    View = require('mvc/View');

var MagnitudeView = function (options) {
  var _this,
      _initialize,

      _custommag,
      _fourfivemag,
      _maxmag,
      _minmag,
      _twofivemag,

      _createMagnitudeContent,
      _onCustomClick,
      _onFourFiveClick,
      _onMagChange,
      _onTwoFiveClick;

  _this = View(options);

  _initialize = function (/*options*/) {
    _createMagnitudeContent();
    if (_this.model.get('minmagnitude') === null &&
        _this.model.get('maxmagnitude') === null) {
      _this.model.set({
        minmagnitude: 2.5,
        maxmagnitude: null
      });
    } else {
      _this.render();
    }
  };

  _createMagnitudeContent = function () {
    var listWrapper;

    listWrapper = _this.el.querySelector('.basicmagnitude-list') ||
        document.createElement('ul');

    listWrapper.innerHTML = [
      '<li>',
        '<input id="two-five-plus" type="radio" name="basicmagnitude"',
            'value="2.5" checked="checked" aria-labelledby="two-five-plus"/>',
        '<label for="two-five-plus" class="label-checkbox">',
          '2.5+',
        '</label>',
      '</li>',
      '<li>',
        '<input id="four-five-plus" type="radio" name="basicmagnitude"',
            'value="4.5" aria-labelledby="four-five-plus"/>',
        '<label for="four-five-plus" class="label-checkbox">',
          '4.5+',
        '</label>',
      '</li>',
      '<li>',
        '<input id="custom-mag" type="radio" name="basicmagnitude"',
            'value="" aria-labelledby="custom-mag"/>',
        '<label for="custom-mag" class="label-checkbox">',
          'Custom',
        '</label>',
      '</li>'
    ].join('');

    _custommag = listWrapper.querySelector('#custom-mag');
    _fourfivemag = listWrapper.querySelector('#four-five-plus');
    _twofivemag = listWrapper.querySelector('#two-five-plus');

    _maxmag = _this.el.querySelector('#maxmagnitude');
    _minmag = _this.el.querySelector('#minmagnitude');

    _custommag.addEventListener('click', _onCustomClick);
    _fourfivemag.addEventListener('click', _onFourFiveClick);
    _twofivemag.addEventListener('click', _onTwoFiveClick);

    _maxmag.addEventListener('change', _onMagChange);
    _minmag.addEventListener('change', _onMagChange);
  };

  _onCustomClick = function () {
    _minmag.focus();
    _minmag.select();
  };

  _onFourFiveClick = function () {
    _this.model.set({
      minmagnitude: 4.5,
      maxmagnitude: null
    });
  };

  _onTwoFiveClick = function() {
    _this.model.set({
      minmagnitude: 2.5,
      maxmagnitude: null
    });
  };

  _onMagChange = function () {
    _custommag.checked = true;
    _this.model.set({
      minmagnitude: parseFloat(_minmag.value),
      maxmagnitude: parseFloat(_maxmag.value)
    });
  };

  _this.render = function () {
    var max,
        min;

    max = _this.model.get('maxmagnitude');
    min = _this.model.get('minmagnitude');

    if (max !== null) {
      _maxmag.value = max;
    } else {
      _maxmag.value = '';
    }

    if (min !== null) {
      _minmag.value = min;
    } else {
      _minmag.value = '';
    }
  };

  _this.destroy = Util.compose(function () {
    _twofivemag.removeEventListener('click', _onTwoFiveClick);
    _minmag.removeEventListener('change', _onMagChange);
    _maxmag.removeEventListener('change', _onMagChange);
    _fourfivemag.removeEventListener('click', _onFourFiveClick);
    _custommag.removeEventListener('click', _onCustomClick);

    _twofivemag = null;
    _minmag = null;
    _maxmag = null;
    _fourfivemag = null;
    _custommag = null;

    _onTwoFiveClick = null;
    _onMagChange = null;
    _onFourFiveClick = null;
    _onCustomClick = null;

    _initialize = null;
    _this = null;

  }, _this.destroy);

  _initialize(options);
  options = null;
  return _this;
};

module.exports = MagnitudeView;
