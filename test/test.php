<?php
  try{
    //Importing config, starting database connection, etc
    include_once '../src/conf/feeds.inc.php';

    $service = new ProductWebService($index);

    $service->query();
  }
  catch (Exception $e){
    if ($service !== null) {
      $service->error(503, $e->getMessage());
    } else {
      header('HTTP/1.0 503 Service Unavailable');
      print_r($e);
    }
  }
?>