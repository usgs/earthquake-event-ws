<?php

// pdl classes

  include_once('classes/pdl/WebService.class.php');

  include_once('classes/pdl/Content.class.php');
  include_once('classes/pdl/ProductId.class.php');
  include_once('classes/pdl/Product.class.php');
  include_once('classes/pdl/ProductSummary.class.php');
  include_once('classes/pdl/Event.class.php');
  include_once('classes/pdl/EventSummary.class.php');

  include_once('classes/pdl/ProductStorage.class.php');
  include_once('classes/pdl/ProductIndexCallback.class.php');
  include_once('classes/pdl/ProductIndexQuery.class.php');
  include_once('classes/pdl/ProductIndex.class.php');

  include_once('classes/pdl/ProductWebService.class.php'); //Product WebService - here for the time being
  include_once('classes/pdl/ProductQuery.class.php');


// fdsn classes
  include_once ('classes/fdsn/Formatter.class.php');
  include_once ('classes/fdsn/AbstractFeed.class.php');
  include_once ('classes/fdsn/FDSNQuery.class.php');
  include_once ('classes/fdsn/FDSNIndex.class.php');
  include_once ('classes/fdsn/FDSNIndexCallback.class.php');
  include_once ('classes/fdsn/FDSNEventWebService.class.php');

  include_once ('classes/fdsn/AtomFeed.class.php');
  include_once ('classes/fdsn/CSVFeed.class.php');
  include_once ('classes/fdsn/TextFeed.class.php');
  include_once ('classes/fdsn/GeoJSONFeed.class.php');
  include_once ('classes/fdsn/KMLFeed.class.php');
  include_once ('classes/fdsn/QuakemlFeed.class.php');


// functions

  if (!function_exists("safefloatval")) {
    function safefloatval($value=null) {
      if ($value === null) {
        return null;
      } else {
        return floatval($value);
      }
    }
  }

  if (!function_exists("safeintval")) {
    function safeintval($value=null) {
      if ($value === null) {
        return null;
      } else {
        return intval($value);
      }
    }
  }

  if (!function_exists('utf8_encode_array')) {
    /**
     * UTF8 encode a data structure.
     *
     * from http://stackoverflow.com/questions/10199017/how-to-solve-json-error-utf8-error-in-php-json-decode
     *
     * @param $mixed {Mixed}
     *        value to utf8 encode.
     * @return {Mixed}
     *         utf8 encoded value.
     */
    function utf8_encode_array($mixed) {
      if (is_array($mixed)) {
          foreach ($mixed as $key => $value) {
              $mixed[$key] = utf8_encode_array($value);
          }
      } else if (is_string ($mixed)) {
          return utf8_encode($mixed);
      }
      return $mixed;
    }
  }

  if (!function_exists('safe_json_encode')) {
    /**
     * Safely json_encode values.
     *
     * Handles malformed UTF8 characters better than normal json_encode.
     * from http://stackoverflow.com/questions/10199017/how-to-solve-json-error-utf8-error-in-php-json-decode
     *
     * @param $value {Mixed}
     *        value to encode as json.
     * @return {String}
     *         json encoded value.
     * @throws Exception when unable to json encode.
     */
    function safe_json_encode($value){
      $encoded = json_encode($value);
      $lastError = json_last_error();
      switch ($lastError) {
        case JSON_ERROR_NONE:
          return $encoded;
        case JSON_ERROR_UTF8:
          return safe_json_encode(utf8_encode_array($value));
        default:
          throw new Exception('json_encode error (' . $lastError . ')');
      }
    }
  }
