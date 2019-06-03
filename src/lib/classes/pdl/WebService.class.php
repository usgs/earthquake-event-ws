<?php

//Parent WebService class to ProductWebService and FDSNEventWebService
class WebService {

  // the FDSNProductIndex to use
  public $index;

  // service version number
  public $version;

  // maximum number of search results
  public $serviceLimit;

  public $redirect;

  //Error codes
  const NO_DATA = 204;
  const BAD_REQUEST = 400;
  const NOT_FOUND = 404;
  const CONFLICT = 409;
  const NOT_IMPLEMENTED = 501;
  const SERVICE_UNAVAILABLE = 503;

  // status message text
  public static $statusMessage = array(
    self::NO_DATA => 'No Data',
    self::BAD_REQUEST => 'Bad Request',
    self::NOT_FOUND => 'Not Found',
    self::CONFLICT => 'Conflict',
    self::NOT_IMPLEMENTED => 'Not Implemented',
    self::SERVICE_UNAVAILABLE => 'Service Unavailable'
  );

  /**
   * @param $index {ProductIndex}
   *    ProductIndex used to query database, perform searches
   */
  public function __construct($index) {
    global $CONFIG;
    
    $this->index = $index;

    $this->serviceLimit = $CONFIG['MAX_SEARCH'];

    global $FDSN_VERSION;
    $this->version = $FDSN_VERSION;
  }

  //Error for all other requests
  public function httpError ($code, $message, $isProductWebservice=false) {

    if (isset(self::$statusMessage[$code])) {
      $codeMessage = ' ' . self::$statusMessage[$code];
    } else {
      $codeMessage = '';
    }

    header('HTTP/1.0 ' . $code . $codeMessage);
    if ($code < 400) {
      exit();
    }

    global $HOST_URL_PREFIX;
    global $FDSN_PATH;
    global $PRODUCT_URL;

    $docsExtension = ($isProductWebservice) ? $PRODUCT_URL : $FDSN_PATH;

    // error message for 400 or 500
    header('Content-type: text/plain');
    echo implode("\n", array(
      'Error ' . $code . ': ' . self::$statusMessage[$code],
      '',
      $message,
      '',
      'Usage details are available from ' . $HOST_URL_PREFIX . $docsExtension,
      '',
      'Request:',
      $_SERVER['REQUEST_URI'],
      '',
      'Request Submitted:',
      gmdate('c'),
      '',
      'Service version:',
      $this->version
    ));
    exit();
  }

  /**
   * Validate a time parameter.
   *
   * @param $param parameter name, for error message.
   * @param $value parameter value
   * @return value as epoch millisecond timestamp, exit with error if invalid.
   */
  protected function validateTime($param, $value) {
    $parsed = strtotime($value);
    if ($parsed === false) {
      $this->error(self::BAD_REQUEST,
        'Bad ' . $param . ' value "' . $value . '".' .
        ' Valid values are ISO-8601 timestamps.');
    }
    return $parsed . '000';
  }

  /**
   * Validate a boolean parameter.
   *
   * @param $param parameter name, for error message
   * @param $value parameter value
   * @return value as boolean if valid ("true" or "false", case insensitively), exit with error if invalid.
   */
  protected function validateBoolean($param, $value) {
    $val = strtolower($value);
    if ($val !== 'true' && $val !== 'false') {
      $this->error(self::BAD_REQUEST,
          'Bad ' . $param . ' value "' . $value . '".' .
          ' Valid values are (case insensitive): "TRUE", "FALSE".');
    }
    return ($val === 'true');
  }

  /**
   * Validate an integer parameter.
   *
   * @param $param parameter name, for error message
   * @param $value parameter value
   * @param $min minimum valid value for parameter, or null if no minimum.
   * @param $max maximum valid value for parameter, or null if no maximum.
   * @return value as integer if valid (integer and in range), exit with error if invalid.
   */
  protected function validateInteger($param, $value, $min, $max) {
    if (
        !ctype_digit($value)
        || ($min !== null && intval($value) < $min)
        || ($max !== null && intval($value) > $max)
    ) {
      $message = '';
      if ($min === null && $max === null) {
        $message = 'integers';
      } else {
        $message = '';
        if ($min !== null) {
          $message .= $min . ' <= ';
        }
        $message .= $param;
        if ($max !== null) {
          $message .= ' <= ' . $max;
        }
      }
      $this->error(self::BAD_REQUEST, 'Bad ' . $param . ' value "' . $value . '".' .
          ' Valid values are ' . $message);
    }
    return intval($value);
  }

  /**
   * Validate a float parameter.
   *
   * @param $param parameter name, for error message
   * @param $value parameter value
   * @param $min minimum valid value for parameter, or null if no minimum.
   * @param $max maximum valid value for parameter, or null if no maximum.
   * @return value as float if valid (float and in range), exit with error if invalid.
   */
  protected function validateFloat($param, $value, $min=null, $max=null) {
    if (
        !is_numeric($value)
        || ($min !== null && floatval($value) < $min)
        || ($max !== null && floatval($value) > $max)
    ) {
      if ($min === null && $max === null) {
        $message = 'numeric';
      } else {
        $message = '';
        if ($min !== null) {
          $message .= $min . ' <= ';
        }
        $message .= $param;
        if ($max !== null) {
          $message .= ' <= ' . $max;
        }
      }

      $this->error(self::BAD_REQUEST, 'Bad ' . $param . ' value "' . $value . '".' .
          ' Valid values are ' . $message);
    }
    return floatval($value);
  }

  /**
   * Validate a parameter that has an enumerated list of valid values.
   *
   * @param $param parameter name, for error message
   * @param $value parameter value
   * @param $enum array of valid parameter values.
   * @return value if valid (in array), exit with error if invalid.
   */
  protected function validateEnumerated($param, $value, $enum) {
    if (!in_array($value, $enum)) {
      $this->error(self::BAD_REQUEST, 'Bad ' . $param . ' value "' . $value . '".' .
        ' Valid values are: "' . implode('", "', $enum) . '".');
    }
    return $value;
  }
}

?>