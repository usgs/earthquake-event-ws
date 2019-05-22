<?php

//Parent WebService class to ProductWebService and FDSNEventWebService
class WebService{

  // the FDSNProductIndex to use
  public $index;

  // service version number
  public $version;

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
  public function __construct($index){
    $this->index = $index;

    global $FDSN_VERSION;
    $this->version = $FDSN_VERSION;
  }

  //Error handling
  public function error($code, $message, $isDetail = false) {
    global $APP_DIR;

    // only cache errors for 60 seconds
    $CACHE_MAXAGE = 60;
    include $APP_DIR . '/lib/cache.inc.php';

    if ($this->redirect !== false && $isDetail &&
        ($code === self::NO_DATA || $code === self::NOT_FOUND)) {
      $this->doRedirect();
    }

    if (isset($_GET['jsonerror']) && $_GET['jsonerror'] == 'true' &&
        isset($_GET['format']) && $_GET['format'] == 'geojson') {
      // For geojson requests, user wants 'jsonerror' output
      $this->jsonError($code, $message, $isDetail);
    } else {
      $this->httpError($code, $message);
    }
  }

  //Redirect (Only used by FDSNEventWebService)
  public function doRedirect () {
    $redirect = $this->redirect . '/query?' . $_SERVER['QUERY_STRING'];

    header('HTTP/1.0 302 Found');
    header('Location: ' . $redirect);
    exit();
  }

  //Prints error if user expecting JSON
  public function jsonError ($code, $message, $isDetail = false) {
    global $HOST_URL_PREFIX;
    $callback = false;
    if (isset($_GET['callback'])) {
      $callback = $_GET['callback'];
      // restrict allowed callback names
      if (!preg_match('/^[A-Za-z0-9\._]+$/', $callback)) {
        header('HTTP/1.0 400 Bad Request');
        echo 'Bad callback value, valid characters include [A-Za-z0-9\._]';
        exit();
      }
      header('Content-type: text/javascript');
    } else {
      header('Content-type: application/json');
    }

    // Does this need to look fully like GeoJSON format?
    $response = array(
      'type' => $isDetail ? 'Feature' : 'FeatureCollection',
      'metadata' => array(
        'status' => $code,
        'generated' => time() . '000',
        'url' => $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'],
        'title' => 'Search Error',
        'api' => $this->version,
        'count' => 0,
        'error' => $message
      )
    );

    if ($isDetail) {
      $response['properties'] = null;
    } else {
      $response['features'] = array();
    }

    if ($callback) {
      echo $callback . '(';
    }
    echo preg_replace('/"(generated)":"([\d]+)"/', '"$1":$2',
        str_replace('\/', '/', safe_json_encode($response)));

    if ($callback) {
      echo ');';
    }

    exit();
  }

  //Error for all other requests
  public function httpError ($code, $message) {

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

    // error message for 400 or 500
    header('Content-type: text/plain');
    echo implode("\n", array(
      'Error ' . $code . ': ' . self::$statusMessage[$code],
      '',
      $message,
      '',
      'Usage details are available from ' . $HOST_URL_PREFIX . $FDSN_PATH,
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
}

?>