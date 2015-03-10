/* global chai, describe, it */
'use strict';

var expect = chai.expect,
    FDSNSearchForm = require('fdsn/FDSNSearchForm');


describe('FDSNSearchForm test suite.', function () {
  describe('Constructor', function () {
    it('Can be defined.', function () {
      /* jshint -W030 */
      expect(FDSNSearchForm).not.to.be.undefined;
      /* jshint +W030 */
    });
  });
});


