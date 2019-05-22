<?php


//Can query database for products based on source, type, and code.
class ProductWebService extends WebService {

  //ProductIndex used to query ComCat database
  public $index;

  /**
   * @param $index {ProductIndex}
   *    The ProductIndex used to search for products and query the database
   */
  public function __construct($index) {
    parent::__construct($index);

    if ($redirect) {
      //Do nothing for now, don't know where this service is pointing
      $redirect = '';
    }
  }

  //Forward-facing query
  public function query() {
    $query = $this->parseQuery();
    $this->handleDetailQuery($query);
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
    $product;
    if (isset($productId)) {
      $product = $storage->getProduct(ProductId::parse($productId));
    }

    //Error if product not found
    if ($product == null) {
      $this->error(self::NOT_FOUND,self::$statusMessage[self::NOT_FOUND]);
    }

    //Send caching headers
    $MODIFIED;
    $CACHE_MAXAGE;
    if ($query->updateTime == null) {
      //If updateTime not included, cache for a minute
      $MODIFIED = $product->getID()->getUpdateTime();
      $CACHE_MAXAGE = 60;

      //Check if product has been modified
      if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) > $MODIFIED) {
        header('HTTP/1.0 304 Not Modified');
        exit;
      }
    } else {
      $CACHE_MAXAGE = 60*60*24*365; //Cache for a year if updateTime included
    }
    include $APP_DIR . '/lib/cache.inc.php';

    //Output JSON
    header('Content-type: application/json');
    $json = safe_json_encode($product->toArray());
    echo $json;

    exit;
    
  }

  //Constructs query object based on parameters in $_GET
  protected function parseQuery() {
    $query = new ProductQuery();

    //Parse fields
    foreach ($_GET as $name=>$value) {
      if ($value === '') {
        $this->error(self::BAD_REQUEST, self::$statusMessage[self::BAD_REQUEST]);
      } elseif ($name == 'source') {
        $query->source = $value;
      } elseif ($name == 'type') {
        $query->type = $value;
      } elseif ($name == 'code') {
        $query->code = $value;
      } elseif ($name == 'updatetime' || $name == 'updateTime') {
        $query->updateTime = $value;
      } else {
        $this->error(self::BAD_REQUEST, self::$statusMessage[self::BAD_REQUEST]);
      }

    }

    //Validate for required parameters
    if ($query->source == null || $query->type == null || $query->code == null) {
      $this->error(self::BAD_REQUEST, self::$statusMessage[self::BAD_REQUEST]);
    }

    return $query;
  }
  
}

?>