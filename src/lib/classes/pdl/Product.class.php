<?php


class Product {

  public static $STATUS_DELETE = "DELETE";
  public static $STATUS_UPDATE = "UPDATE";

  private $id;
  private $status;
  private $properties = array();
  private $links = array();
  private $contents = array();
  private $trackerURL;
  private $signature;

  public function __construct($productid) {
    $this->id = $productid;
  }

  public function getId() { return $this->id; }
  public function setId($id) { $this->id = $id; }
  public function getStatus() { return $this->status; }
  public function setStatus($status) { $this->status = $status; }

  public function getProperties() { return $this->properties; }
  public function setProperties($props) { $this->properties = $props; }

  public function getLinks() { return $this->links; }
  public function setLinks($links) { $this->links = $links; }

  public function getContents() { return $this->contents; }
  public function setContents($contents) { $this->contents = $contents; }

  public function getTrackerURL() { return $this->trackerURL; }
  public function setTrackerURL($url) { $this->trackerURL = $url; }

  public function getSignature() { return $this->signature; }
  public function setSignature($signature) { $this->signature = $signature; }


  public function addLink($relation, $uri) {
    if (!array_key_exists($relation, $this->links)) {
      $this->links[$relation] = array();
    }

    $this->links[$relation][] = $uri;
  }

  public function addContent($path, $content) {
    $this->contents[$path] = $content;
  }

  public function getEventId() {
    $source = $this->getEventSource();
    $code = $this->getEventSourceCode();

    if ($source == null || $code == null) {
      return null;
    } else {
      return $source . $code;
    }
  }

  public function getEventSource() {
    if (array_key_exists('eventsource', $this->properties)) {
      return $this->properties['eventsource'];
    } else {
      return null;
    }
  }

  public function getEventSourceCode() {
    if (array_key_exists('eventsourcecode', $this->properties)) {
      return $this->properties['eventsourcecode'];
    } else {
      return null;
    }
  }

  public function getEventTime() {
    if (array_key_exists('eventtime', $this->properties)) {
      return $this->properties['eventtime'];
    } else {
      return null;
    }
  }

  public function getMagnitude() {
    if (array_key_exists('magnitude', $this->properties)) {
      return $this->properties['magnitude'];
    } else {
      return null;
    }
  }

  public function getLatitude() {
    if (array_key_exists('latitude', $this->properties)) {
      return $this->properties['latitude'];
    } else {
      return null;
    }
  }

  public function getLongitude() {
    if (array_key_exists('longitude', $this->properties)) {
      return $this->properties['longitude'];
    } else {
      return null;
    }
  }

  public function getDepth() {
    if (array_key_exists('depth', $this->properties)) {
      return $this->properties['depth'];
    } else {
      return null;
    }
  }

  public function getVersion() {
    if (array_key_exists('version', $this->properties)) {
      return $this->properties['version'];
    } else {
      return null;
    }
  }

  public function toArray() {
    //leaving out trackerURL, and signature to save space
    $r = array(
      'id' => $this->getId()->toString(),
      'status' => $this->getStatus(),
      'properties' => $this->getProperties(),
      'links' => $this->getLinks(),
      'contents' => array()
    );

    // add contents
    foreach ($this->getContents() as $path => $content) {
      $r['contents'][$path] = $content->toArray();
    }

    return $r;
  }

}
