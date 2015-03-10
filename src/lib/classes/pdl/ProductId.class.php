<?php


/**
 * Unique identifier for a product.
 */
class ProductId {

  /** Prefix of ProductId URNs. */
  public static $URN_PREFIX = "urn:usgs-product:";

  /** Who sent the product. */
  private $source;
  /** The type of product. */
  private $type;
  /** Unique code assigned by source. */
  private $code;
  /** When this version was sent. */
  private $updateTime;


  public function __construct($source, $type, $code, $updateTime) {
    $this->source = $source;
    $this->type = $type;
    $this->code = $code;
    $this->updateTime = $updateTime;
  }

  public function getSource() {
    return $this->source;
  }

  public function getType() {
    return $this->type;
  }

  public function getCode() {
    return $this->code;
  }

  public function getUpdateTime() {
    return $this->updateTime;
  }

  public function equals($that) {
    return (($this->compareTo($that)) == 0);
  }

  public function compareTo($that) {
    // Subtract $that's time from $this's time
    $r = gmp_sub( $that->getUpdateTime(), $this->getUpdateTime() );
    //$r = $that->getUpdateTime() - $this->getUpdateTime();
    if ($r != 0) {
      return $r;
    }

    $r = strcmp($this->getSource(), $that->getSource());
    if ($r != 0) {
      return $r;
    }

    $r = strcmp($this->getType(), $that->getType());
    if ($r != 0) {
      return $r;
    }

    $r = strcmp($this->getCode(), $that->getCode());
    if ($r != 0) {
      return $r;
    }

    // must be equal
    return 0;
  }

  public function isSameProduct($that) {
    if (
         $this->getSource() == $that->getSource() 
      && $this->getType() == $that->getType() 
      && $this->getCode() == $that->getCode()
    ) {
      return true;
    }
    return false;
  }

  public function toString() {
    $urn = self::$URN_PREFIX;

    $urn .= $this->getSource();
    $urn .= ':';
    $urn .= $this->getType();
    $urn .= ':';
    $urn .= $this->getCode();
    $urn .= ':';
    $urn .= $this->getUpdateTime();

    return $urn;
  }

  public static function parse($urn) {
    if (!strpos($urn, self::$URN_PREFIX) == 0) {
      //doesn't start with urn prefix
      return null;
    }

    $parts = str_replace(self::$URN_PREFIX, "", $urn);
    $parts = explode(":", $parts);
    if (count($parts) != 4) {
      //incomplete urn
      return null;
    }

    $id = new ProductId($parts[0], $parts[1], $parts[2], $parts[3]);
    return $id;
  }

}
