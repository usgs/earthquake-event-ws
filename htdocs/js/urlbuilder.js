(function() {


	// elements of form
	var _form = document.getElementById('search_form');
	var _format = document.getElementById('format');
	var _button = document.getElementById('search_button');
	var _url = document.getElementById('search_url');
	var _advanced = document.getElementById('advanced');


	// utility event handler function
	var _addEvent = function(el, name, callback) {
		try {
			el.addEventListener(name, callback);
		} catch (e) {
			el.attachEvent('on'+name, callback);
		}
	};

	// read form fields that aren't empty
	var _getData = function() {
		var data = {};
		for (var i=0, len=_form.elements.length; i<len; i++) {
			var el = _form.elements[i];
			if (el.type === 'checkbox') {
				if (el.checked) {
					data[el.name] = el.value;
				}
			} else if (el.value) {
				data[el.name] = el.value;
			}
		}
		return data;
	};

	// convert form fields that aren't empty into search url
	var _getUrl = function() {
		var url = _form.getAttribute("action"),
			data = _getData(),
			params = [];
		for (var name in data) {
			params.push(name + '=' + escape(data[name]));
		}
		if (params.length == 0) {
			return url;
		} else {
			return url + '?' + params.join('&');
		}
	};


	// toggle advanced section
	_addEvent(_advanced, 'click', function() {
		if (_advanced.className == 'showAdvanced') {
			_advanced.className = '';
		} else {
			_advanced.className = 'showAdvanced';
		}
	});


	// show and hide custom formats using a form class
	var _updateFormatOptions = function() {
		var classname = '';
		if (_format.value === '') {
			classname = 'quakeml';
		} else {
			classname = _format.value;
		}
		_form.className = classname + '-format';
	};
	// change form class when format is changed
	_addEvent(_format, 'change', _updateFormatOptions);
	// and on form load
	_updateFormatOptions();


	// update url displayed to user
	var _updateUrl = function (e) {
		var url = _getUrl().replace(/&/g, '&amp;'),
			html = ['<a href="', url, '">', url, '</a>'].join('');
		// only update if changed, otherwise requires double click
		if (_url.innerHTML !== html) {
			_url.innerHTML = html;
		}
	};
	// on change, update url displayed to user
	_addEvent(_form, 'change', _updateUrl);

	var _keyupTimeout = null;
	// also on keyup, in case user hasn't blurred input
	_addEvent(_form, 'keyup', function(e) {
		if (_keyupTimeout !== null) {
			clearTimeout(_keyupTimeout);
		}
		_keyupTimeout = setTimeout(function() {
				_updateUrl(e);
				_keyupTimeout = null;
			}, 50);
	});

	// update on form load, in case user hit back
	_updateUrl();


	// on submit, use same url displayed to user (without empty fields)
	_addEvent(_form, 'submit', function(e) {
		e.preventDefault();
		window.location = _getUrl();
		return false;
	});


})();
