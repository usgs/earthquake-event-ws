<?php

//Stores all the information that can be queried for a product
class ProductQuery {
  
  public $source;
  public $type;
  public $code;
  public $updateTime;

  public $startTime;
  public $endTime;
  public $maxLatitude;
  public $maxLongitude;
  public $minLatitude;
  public $minLongitude;
  public $latitude;
  public $longitude;
  public $time;

  //path search term (to be implemented)
  //public $path = null; 

  /*
  public function __construct(){
    $source = null;
    $type = null;
    $code = null;
    $updateTime = null;
  }
  */

  //Depricated as of 281b
  public function toString() {
    $ret = "Parameters: (" . $this->source . ", " . $this->type . ", " . $this->code . ", " . $this->updateTime . ")\n\r";
    return $ret;
  }

}

?>