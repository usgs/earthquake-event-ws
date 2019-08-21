<?php


//Can query database for products based on source, type, and code.
class ProductWebService extends WebService {

  //ProductIndex used to query ComCat database
  public $index;

  private $url;

  /**
   * @param $index {ProductIndex}
   *    The ProductIndex used to search for products and query the database
   */
  public function __construct($index, $CONFIG, $url) {
    parent::__construct($index);

    $this->serviceLimit = array_key_exists('PRODUCT_MAX_SEARCH', $CONFIG) ? $CONFIG['PRODUCT_MAX_SEARCH'] : 1000;

    $this->url = $url;
  }

  /**
   * Forward-facing query
   * 
   * @param $params {Array}
   *    Parameters passed through $_GET
   *  
   * */
  public function query($params) {
    $query = $this->parseQuery($params);

    if (isset($query->source) && isset($query->type) && isset($query->code)) {
      $this->handleDetailQuery($query);
    }
    else {
      $this->handleSummaryQuery($query);
    }
  }

  /**
   * Gets all products satisfying query
   * 
   * @param $query {ProductQuery}
   *    The ProductQuery storing necessary query information for table search
   */
  protected function handleSummaryQuery($query) {
    global $APP_DIR;
    global $HOST_URL_PREFIX;

    //Make sure count to be returned is not over the maximum
    $count = $this->index->getProductCount($query);
    if ($count > $this->serviceLimit){
      //Toss error (robbed from FDSNEventWebService for consistency)
      $this->error(self::BAD_REQUEST, $count . ' matching products exceeds ' .
          'search limit of ' . $this->serviceLimit . '. Modify the search ' .
          'to match fewer products.',false,true);
    }
    
    //Query based on parameters
    $summaryArr = $this->index->getProductSummaryArray($query);

    //Output (In FDSNEventWebService, output is handled by query as well)
    $medatata = array();
    $metadata['generated'] = time() . "000";
    $metadata['url'] = $HOST_URL_PREFIX . $_SERVER['REQUEST_URI'];
    $metadata['status'] = 200;
    $metadata['api'] = $this->version;
    $metadata['count'] = $count;

    //Cache for 60 seconds regardless of age
    $CACHE_MAXAGE = 60;
    include $APP_DIR . '/lib/cache.inc.php';

    //Build array of summary information
    $productArr = array();
    foreach($summaryArr as $id=>$product) {
      $tmpArr = $product->toArray();
      $tmpArr['url'] = $this->getDetailUrl($product);
      $productArr[] = $tmpArr;
    }

    //Output
    header('Content-type: application/json');
    $json = safe_json_encode(array("metadata"=>$metadata, "products"=>$productArr));
    $json = str_replace('\/', '/', $json);
    echo $json;

    exit;
  }

  /**
   * Gets product from ProductIndex, encodes into GeoJSON
   * 
   * @param $query {ProductQuery}
   *    The ProductQuery storing necessary query information for table search
   */
  protected function handleDetailQuery($query) {
    global $APP_DIR;
    global $storage;

    //Gets product ID
    $productId = $this->index->getProductIdByQuery($query);

    //Get product from storage
    $product = null;
    if (isset($productId)) {
      $product = $storage->getProduct(ProductId::parse($productId));
    }

    //Error if product not found
    if ($product == null) {
      $this->error(self::NOT_FOUND,self::$statusMessage[self::NOT_FOUND],true,true);
    }

    //Check if product has been modified
    $MODIFIED = $product->getID()->getUpdateTime();
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) > $MODIFIED) {
      header('HTTP/1.0 304 Not Modified');
      exit;
    }

    //Send caching headers
    $CACHE_MAXAGE;
    if ($query->updateTime == null) {
      //If updateTime not included, cache for a minute
      $CACHE_MAXAGE = 60;
    } else {
      //Cache for a year if updateTime included
      $CACHE_MAXAGE = 60*60*24*365;
    }
    include $APP_DIR . '/lib/cache.inc.php';

    //Output JSON
    header('Content-type: application/json');
    $json = safe_json_encode($product->toArray());
    echo $json;

    exit;
    
  }

  /**
   * Constructs query object based on parameters in $_GET
   * 
   * @param $params {Array}
   *    Parameters passed through $_GET
   */
  protected function parseQuery($params) {
    $query = new ProductQuery();

    //Parse fields
    foreach ($params as $name=>$value) {
      if ($value === '') {
        continue;
      } elseif ($name == 'source') {
        $query->source = $value;
      } elseif ($name == 'type') {
        $query->type = $value;
      } elseif ($name == 'code') {
        $query->code = $value;
      } elseif ($name == 'updatetime' || $name == 'updateTime') {
        $query->updateTime = $this->validateFloat($name,$value); //Not validated for time so you can easily use known updatetime
      } elseif ($name == 'maxupdatetime' || $name == 'maxUpdateTime') {
        $query->maxUpdateTime = $this->validateTime($name,$value);
      } elseif ($name == 'minupdatetime' || $name == 'minUpdateTime') {
        $query->minUpdateTime = $this->validateTime($name,$value);
      } elseif ($name == 'time') {
        $query->time = $this->validateTime($name,$value);
      } elseif ($name == 'starttime' || $name == 'startTime') {
        $query->startTime = $this->validateTime($name,$value);
      } elseif ($name == 'endtime' || $name == 'endTime') {
        $query->endTime = $this->validateTime($name,$value);
      } elseif ($name == 'latitude') {
        $query->latitude = $this->validateFloat($name,$value,-90,90);
      } elseif ($name == 'longitude') {
        $query->longitude = $this->validateFloat($name,$value,-180,180);
      } elseif ($name == 'maxlatitude' || $name == 'maxLatitude') {
        $query->maxLatitude = $this->validateFloat($name,$value,-90,90);
      } elseif ($name == 'minlatitude' || $name == 'minLatitude') {
        $query->minLatitude = $this->validateFloat($name,$value,-90,90);
      } elseif ($name == 'maxlongitude' || $name == 'maxLongitude') {
        $query->maxLongitude = $this->validateFloat($name,$value,-360,360);
      } elseif ($name == 'minlongitude' || $name == 'minLongitude') {
        $query->minLongitude = $this->validateFloat($name,$value,-360,360);
      } elseif ($name == 'minmagnitude' || $name == 'minMagnitude') {
        $query->minMagnitude = $this->validateFloat($name,$value);
      } elseif ($name == 'maxmagnitude' || $name == 'maxMagnitude') {
        $query->maxMagnitude = $this->validateFloat($name,$value);
      } elseif ($name == 'includedeleted' || $name == 'includeDeleted') {
        $query->includeDeleted = $this->validateBoolean($name,$value);
      } elseif ($name == 'includesuperseded' || $name == 'includeSuperseded') {
        $query->includeSuperseded = $this->validateBoolean($name,$value);
      } elseif ($name == 'orderby' || $name=='orderBy') {
        $query->orderBy = $value;
      } elseif ($name == 'limit') {
        $query->limit = $this->validateInteger($name,$value,0);
      } elseif ($name == 'offset') {
        $query->offset = $this->validateInteger($name,$value,0);
      } else {
        $this->error(self::BAD_REQUEST, $name . ' is not a supported parameter',false,true);
      }
    }

    //Combination validation
    if ((isset($query->latitude) && !isset($query->longitude)) || (isset($query->longitude) && !isset($query->latitude))) {
      $this->error(self::BAD_REQUEST, 'must provide both latitude and longitude',false,true);
    }
    if (isset($query->startTime) && isset($query->endTime) && $query->startTime > $query->endTime) {
      $this->error(self::BAD_REQUEST, 'starttime must be less than endtime',false,true);
    }
    if (isset($query->minUpdateTime) && isset($query->maxUpdateTime) && $query->minUpdateTime > $query->maxUpdateTime) {
      $this->error(self::BAD_REQUEST, 'minupdatetime must be less than maxupdatetime',false,true);
    }
    if (isset($query->minLatitude) && isset($query->maxLatitude) && $query->minLatitude > $query->maxLatitude) {
      $this->error(self::BAD_REQUEST, 'minlatitude must be less than maxlatitude',false,true);
    }
    if (isset($query->minLongitude) && isset($query->maxLongitude) && $query->minLongitude > $query->maxLongitude) {
      $this->error(self::BAD_REQUEST, 'minlongitude must be less than maxlongitude',false,true);
    }
    if (isset($query->minLongitude) && isset($query->maxLongitude) && ($query->maxLongitude - $query->minLongitude) > 360) {
      $this->error(self::BAD_REQUEST, 'Searches cannot span more than 360 degrees of longitude.',false,true);
    }
    if (isset($query->minMagnitude) && isset($query->maxMagnitude) && $query->minMagnitude > $query->maxMagnitude) {
      $this->error(self::BAD_REQUEST, 'minmagnitude must be less than maxmagnitude',false,true);
    }

    return $query;
  }

  protected function getDetailUrl($product) {
    $id = $product->getId();
    return $this->url . '?' . 
      'source=' . $id->getSource() . 
      '&type=' . $id->getType() . 
      '&code=' . $id->getCode() . 
      '&updateTime=' . $id->getUpdateTime();
  }

}

?>