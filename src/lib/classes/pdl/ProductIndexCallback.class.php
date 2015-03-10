<?php


/**
 * Class used for larger searches of the product index.
 * Results are passed back via method calls.
 */
class ProductIndexCallback {

  public function __construct() {
  }

  public function onEventSummary($summary) {
  }

}


class JSONProductIndexCallback extends ProductIndexCallback {

  private $first = true;

  public function __construct() {
  }

  public function onEventSummary($summary) {
    // comma between summaries
    if (!$this->first) {
      print ',';
    } else {
      $this->first = false;
    }

    $json = json_encode($summary->toArray());
    $json = str_replace('\\/', '/', $json);
    print $json;
  }

}


class FeedCallback extends ProductIndexCallback {

  private $feed;
  private $firstEntry = true;


  public function __construct($feed) {
    $this->feed = $feed;
  }

  public function start() {
    header('Content-type: ' . $this->feed->getMimeType());
    echo $this->feed->getHeader();
  }

  public function onEventSummary($event) {
    echo $this->feed->getEntry($event);
  }

  public function finish() {
    echo $this->feed->getFooter();
  }
}


