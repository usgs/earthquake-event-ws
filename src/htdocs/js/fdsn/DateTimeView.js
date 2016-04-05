'use strict';

var FDSNFormatter = require('fdsn/FDSNFormatter'),
    Util = require('util/Util'),
    View = require('mvc/View');

var DateTimeView = function (options) {
  var _this,
      _initialize,

      _customTime,
      _endtime,
      _monthAgo,
      _now,
      _pastSevenDays,
      _pastThirtyDays,
      _starttime,
      _weekAgo,

      _createDateTimeContent,
      _getTimes,
      _onCustomClick,
      _onPastSevenDaysClick,
      _onPastThirtyDaysClick,
      _onTimeChange;

  _this = View(options);

  _initialize = function (/*options*/) {
    _getTimes();
    _createDateTimeContent(options);

    if (_this.model.get('starttime') === null &&
        _this.model.get('endtime') === null) {
      _this.model.set({
        starttime: FDSNFormatter.formatDateTime(_weekAgo),
        endtime: FDSNFormatter.formatDateTime(_now)
      });
        }
  };

  _getTimes = function () {
    var now,
        year,
        month,
        day,
        thirtyDays,
        today,
        sevenDays;

    // Break down current date into year, month, day
    now = new Date();
    year = now.getFullYear();
    month = now.getMonth() + 1;
    day = now.getDate();

    // calculate time ranges in epoch milliseconds
    today = Date.UTC(year, month, day);
    sevenDays = today - (7 * 86400000);
    thirtyDays = today - (30 * 86400000);
    today = today + 86399999;

    // set time variables for current, 7 past days, and 30 past days
    _now = FDSNFormatter.formatDateTime(today);
    _weekAgo = FDSNFormatter.formatDateTime(sevenDays);
    _monthAgo = FDSNFormatter.formatDateTime(thirtyDays);
  };

  _createDateTimeContent = function () {
     var listWrapper;

    listWrapper = _this.el.querySelector('.basictime-list') ||
        document.createElement('ul');

    listWrapper.innerHTML = [
      '<li>',
        '<input id="basictime-weekago" type="radio" name="basictime"',
            'value="7" checked="checked"',
            'aria-labelledby="basictime-weekago"/>',
        '<label for="basictime-weekago" class="label-checkbox">',
          'Past 7 Days',
        '</label>',
      '</li>',
      '<li>',
        '<input id="basictime-monthago" type="radio" name="basictime"',
            'value="30" aria-labelledby="basictime-monthago"/>',
        '<label for="basictime-monthago" class="label-checkbox">',
          'Past 30 Days',
        '</label>',
      '</li>',
      '<li>',
        '<input id="basictime-custom" type="radio" name="basictime"',
            'value="custom" aria-labelledby="basictime-custom"/>',
        '<label for="basictime-custom" class="label-checkbox">',
          'Custom',
        '</label>',
      '</li>'
    ].join('');

    _customTime = listWrapper.querySelector('#basictime-custom');
    _pastSevenDays = listWrapper.querySelector('#basictime-weekago');
    _pastThirtyDays = listWrapper.querySelector('#basictime-monthago');

    _starttime = _this.el.querySelector('#starttime');
    _endtime = _this.el.querySelector('#endtime');

    _customTime.addEventListener('click', _onCustomClick);
    _pastSevenDays.addEventListener('click', _onPastSevenDaysClick);
    _pastThirtyDays.addEventListener('click', _onPastThirtyDaysClick);

    _starttime.addEventListener('change', _onTimeChange);
    _endtime.addEventListener('change', _onTimeChange);
  };

  _onCustomClick = function () {
    _starttime.focus();
    _starttime.select();
  };

  _onPastSevenDaysClick = function () {
    _this.model.set({
      starttime: FDSNFormatter.formatDateTime(_weekAgo),
      endtime: FDSNFormatter.formatDateTime(_now)
    });
  };

  _onPastThirtyDaysClick = function () {
    _this.model.set({
      starttime: FDSNFormatter.formatDateTime(_monthAgo),
      endtime: FDSNFormatter.formatDateTime(_now)
    });
  };

  _onTimeChange = function () {
    _customTime.checked = true;
    _this.model.set({
      starttime: FDSNFormatter.formatDateTime(_starttime.value),
      endtime: FDSNFormatter.formatDateTime(_endtime.value)
    });
  };

  _this.render = function () {
    var end,
        start;

    end = _this.model.get('endtime');
    start = _this.model.get('starttime');

    // update time inputs
    if (end !== null) {
      _endtime.value = end;
    } else {
      _endtime.value = '';
    }

    if (start !== null) {
      _starttime.value = start;
     } else {
      _starttime.value = '';
    }

    // update radio button selection
    if (start === _weekAgo && end === _now) {
      _pastSevenDays.checked = true;
    } else if (start === _monthAgo && end === _now) {
      _pastThirtyDays.checked = true;
    } else {
      _customTime.checked = true;
    }
  };

  _this.destroy = Util.compose(function () {
    _customTime.removeEventListener('click', _onCustomClick);
    _pastSevenDays.removeEventListener('click', _onPastSevenDaysClick);
    _pastThirtyDays.removeEventListener('click', _onPastThirtyDaysClick);
    _starttime.removeEventListener('change', _onTimeChange);
    _endtime.removeEventListener('change', _onTimeChange);

    _customTime = null;
    _endtime = null;
    _monthAgo = null;
    _now = null;
    _pastSevenDays = null;
    _pastThirtyDays = null;
    _starttime = null;
    _weekAgo = null;

    _createDateTimeContent = null;
    _getTimes = null;
    _onCustomClick = null;
    _onPastSevenDaysClick = null;
    _onPastThirtyDaysClick = null;
    _onTimeChange = null;

    _initialize = null;
    _this = null;
  }, _this.destroy);

  _initialize(options);
  options = null;
  return _this;
};

module.exports = DateTimeView;
