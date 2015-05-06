<?php

/**
 * An event is a collection of ProductSummary objects.
 */
class Event {

  /** The ProductIndex database id for this event. */
  public $indexId;

  /** Array of products associated to this event. */
  public $products;

  /**
   * Construct a new event object.
   *
   * @param $indexId {Integer} default null.
   *        the product index database id.
   * @param $products {Array<String => Array<ProductSummary>>} default null.
   *        array of products, keyed by type.
   */
  public function __construct($indexId = null, $products = null) {
    $this->indexId = $indexId;
    $this->products = ($products === null ? array() : $products);
  }

  /**
   * Get the index id.
   *
   * @return {Integer} the index id if set, or null.
   */
  public function getIndexId () {
    return $this->indexId;
  }

  /**
   * Set the index id.
   *
   * @param $id {Integer} the index id.
   */
  public function setIndexId ($id) {
    $this->indexId = $id;
  }

  /**
   * Get the array of all products, keyed by type.
   *
   * @return {Array<String => Array<ProductSummary>>}
   *         array of arrays of products, keyed by type.
   */
  public function getAllProducts() {
    return $this->products;
  }

  /**
   * Set the array of all products, keyed by type.
   */
  public function setProducts ($products) {
    $this->allProducts = $products;
  }

  /**
   * Add a product to this event.
   *
   * @param $summary {ProductSummary}
   *        the summary to remove.
   */
  public function addProduct ($summary) {
    $this->products[$summary->getId()->getType()][] = $summary;
  }

  /**
   * Remove a product from this event.
   *
   * @param $summary {ProductSummary}
   *        the summary to remove.
   */
  public function removeProduct ($summary) {
    $type = $summary->getId()->getType();
    if (isset($this->products[$type])) {
      $this->products[$type] = array_diff(
          $this->products[$type],
          // remove $summary from products array
          array($summary));
    }
  }

  /**
   * Check when this event was last modified.
   *
   * This method finds the most recent product updateTime.
   *
   * @return {Integer} millisecond timestamp when event was last updated.
   */
  public function getLastModified () {
    $allProducts = $this->getAllProducts();
    $lastModified = -1;
    foreach ($allProducts as $type => $products) {
      foreach ($products as $p) {
        $modified = $p->getUpdateTime();
        if ($modified > $lastModified) {
          $lastModified = $modified;
        }
      }
    }
    return $lastModified;
  }

  /**
   * Get preferred id for this event.
   *
   * @return preferred id, or null if none set.
   */
  public function getEventId () {
    $product = $this->getEventIdProduct();
    if ($product !== null) {
      return $product->getEventId();
    }
    return null;
  }

  /**
   * Get preferred source for this event.
   *
   * @return preferred source, or null if none set.
   */
  public function getSource () {
    $product = $this->getEventIdProduct();
    if ($product !== null) {
      return $product->getEventSource();
    }
    return null;
  }

  /**
   * Get preferred source code for this event.
   *
   * @return preferred source code, or null if none set.
   */
  public function getSourceCode () {
    $product = $this->getEventIdProduct();
    if ($product !== null) {
      return $product->getEventSourceCode();
    }
    return null;
  }


  /**
   * Get the product used for eventsource and eventsourcecode.
   *
   * @return {ProductSummary} the most preferred product summary used to define
   *         eventsource and eventsource code.
   */
  protected function getEventIdProduct () {
    $product = $this->getPreferredOriginProduct();
    if ($product === null) {
      $product = $this->getProductWithOriginProperties();
    }
    return $product;
  }

  /**
   * Get the event type.
   *
   * @return event type.
   */
  public function getType () {
    $product = $this->getProductWithOriginProperties();
    if ($product !== null) {
      return $product->getEventType();
    }
    return null;
  }

  /**
   * Get the time of this event.
   *
   * @return event time.
   */
  public function getTime () {
    $product = $this->getProductWithOriginProperties();
    if ($product !== null) {
      return $product->getEventTime();
    }
    return null;
  }

  /**
   * Get the latitude value for this event.
   *
   * @return event latitude.
   */
  public function getLatitude () {
    $product = $this->getProductWithOriginProperties();
    if ($product !== null) {
      return $product->getEventLatitude();
    }
    return null;
  }

  /**
   * Get the longitude for this event.
   *
   * @return event longitude.
   */
  public function getLongitude () {
    $product = $this->getProductWithOriginProperties();
    if ($product !== null) {
      return $product->getEventLongitude();
    }
    return null;
  }

  /**
   * Get the depth for this event.
   *
   * @return event depth.
   */
  public function getDepth () {
    $product = $this->getProductWithOriginProperties();
    if ($product !== null) {
      return $product->getEventDepth();
    }
    return null;
  }

  /**
   * Get the magnitude for this event.
   *
   * @return event magnitude.
   */
  public function getMagnitude () {
    $product = $this->getPreferredMagnitudeProduct();
    if ($product === null) {
      // fall back to origin product if no preferred magnitude product
      $product = $this->getProductWithOriginProperties();
    }
    if ($product !== null) {
      return $product->getEventMagnitude();
    }
    return null;
  }

  /**
   * Check whether event is deleted.
   *
   * @return {Boolean} true if event is deleted, false otherwise.
   */
  public function isDeleted () {
    $preferred = $this->getPreferredOriginProduct();
    if ($preferred !== null &&
        !$preferred->isDeleted() &&
        self::productHasOriginProperties($preferred)) {
      // have "origin" type product, that isn't deleted,
      // and has origin properties
      return false;
    }
    return true;
  }

  /**
   * Check whether event is reviewed.
   *
   * @return {Boolean} true if event is reviewed, false otherwise.
   */
  public function isReviewed () {
    $product = $this->getPreferredOriginProduct();
    if ($product !== null) {
      if (isset($product->properties['review-status'])) {
        $reviewStatus = strtoupper($product->properties['review-status']);
        if ($reviewStatus === 'REVIEWED' || $reviewStatus === 'PUBLISHED') {
          // reviewed
          return true;
        }
      }
    }
    // default is not reviewed
    return false;
  }

  /**
   * Return an associative array of event codes that make up this event.
   *
   * @deprecated
   * @see #getAllEventCodes()
   */
  public function getEventCodes() {
    // process all products, least preferred first
    // in case a source has multiple codes, the most recent
    // will be processed last.
    $allProducts = array_reverse(self::getSortedMostPreferredFirst(
        self::getWithoutSuperseded(self::productMapToList(
            $this->getAllProducts()))));

    $eventIds = array();
    foreach ($allProducts as $product) {
      $source = $product->getEventSource();
      $sourceCode = $product->getEventSourceCode();
      if ($source != null && $sourceCode != null) {
        $eventIds[$source] = $sourceCode;
      }
    }

    return $eventIds;
  }

  /**
   * Get all event codes associated with event.
   *
   * Generally each source has only one code per event, however sources
   * make mistakes.  This method accounts for the possibility of multiple
   * codes per source from forced associations.
   *
   * @return {Array<String => Array<String>>} all event codes associated
   *         to event.  Key is source, value is array of event codes from
   *         that source.
   */
  public function getAllEventCodes () {
    $eventIds = array();
    foreach ($this->getAllProducts() as $type => $products) {
      foreach ($products as $p) {
        $source = $product->getEventSource();
        $sourceCode = $product->getEventSourceCode();
        if ($source !== null && $sourceCode !== null) {
          $eventIds[$source][$sourceCode] = 1;
        }
      }
    }
    // convert codes to list instead of associative array
    foreach ($eventIds as $source => $codes) {
      $eventIds[$source] = array_keys($codes);
    }
    return $eventIds;
  }

  /**
   * Get EventSummary object for this event.
   *
   * @return {EventSummary} summarizing this event.
   */
  public function getEventSummary () {
    $summary = new EventSummary();
    $summary->setEventIndexId($this->getIndexId());
    $summary->setStatus($this->isDeleted() ? 'DELETE' : 'UPDATE');

    $idProduct = $this->getEventIdProduct();
    if ($idProduct !== null) {
      $summary->setSource($idProduct->getEventSource());
      $summary->setSourceCode($idProduct->getEventSourceCode());
    }

    $originProduct = $this->getProductWithOriginProperties();
    if ($originProduct !== null) {
      $summary->setLatitude($originProduct->getEventLatitude());
      $summary->setLongitude($originProduct->getEventLongitude());
      $summary->setTime($originProduct->getEventTime());
      $summary->setDepth($originProduct->getEventDepth());
    }

    $magnitudeProduct = $this->getPreferredMagnitudeProduct();
    if ($magnitudeProduct !== null) {
      $summary->setMagnitude($magnitudeProduct->getEventMagnitude());
      $summary->setMagnitudeType(
          isset($magnitudeProduct->properties['magnitude-type']) ?
          $magnitudeProduct->properties['magnitude-type'] : null);
    }

    $summary->setEventCodes($this->getEventCodes());

    return $summary;
  }

  /**
   * Get the most recent product with origin properties (id, lat, lon, time).
   *
   * <strong>NOTE</strong>: this product may have been superseded by a delete.
   * When an event has not been deleted, this method should be consistent with
   *  {@link #getPreferredOriginProduct()}.
   *
   * Products are checked in the following order, sorted most preferred first
   * within each group.  The first matching product is returned:
   * <ol>
   * <li>"origin" products not superseded or deleted,
   *    that have origin properties</li>
   * <li>"origin" products superseded by a delete,
   *    that have origin properties</li>
   * <li>products not superseded or deleted,
   *    that have origin properties</li>
   * <li>products superseded by a delete,
   *    that have origin properties</li>
   * </ol>
   *
   * @return {ProductSummary} the most recent product with origin properties.
   * @see #productHasOriginProperties(ProductSummary)
   */
  public function getProductWithOriginProperties() {
    $allProducts = $this->getAllProducts();

    if (isset($allProducts['origin'])) {
      // "origin" products not superseded or deleted
      $products = self::getSortedMostPreferredFirst(
          self::getWithoutDeleted(self::getWithoutSuperseded(
              $allProducts['origin'])));
      foreach ($products as $p) {
        if (self::productHasOriginProperties($p)) {
          return $p;
        }
      }

      // "origin" products superseded by a delete
      $products = self::getSortedMostPreferredFirst(
          self::getWithoutSuperseded(self::getWithoutDeleted(
              $allProducts['origin'])));
      foreach ($products as $p) {
        if (self::productHasOriginProperties($p)) {
          return $p;
        }
      }
    }

    // products not superseded or deleted,
    $products = self::getSortedMostPreferredFirst(
        self::getWithoutDeleted(self::getWithoutSuperseded(
            self::productMapToList($allProducts))));
    foreach ($products as $p) {
      if (self::productHasOriginProperties($p)) {
        return $p;
      }
    }

    // products superseded by a delete
    $products = self::getSortedMostPreferredFirst(
        self::getWithoutSuperseded(self::getWithoutDeleted(
            self::productMapToList($allProducts))));
    foreach ($products as $p) {
      if (self::productHasOriginProperties($p)) {
        return $p;
      }
    }

    // didn't find anything
    return null;
  }

  /**
   * Get the most preferred origin-like product for this event.
   *
   * The event is considered deleted if the returned product is null, deleted,
   * or does not have origin properties.  Information about the event
   * may still be available using {@link #getProductWithOriginProperties()}.
   *
   * Products are checked in the following order, sorted most preferred first
   * within each group.  The first matching product is returned:
   * <ul>
   * <li>If any "origin" products exist:
   *    <ol>
   *    <li>"origin" products not superseded or deleted,
   *        that have origin properties.</li>
   *    <li>"origin" products not superseded,
   *        that have an event id.</li>
   *    </ol>
   * </li>
   * <li>If no "origin" products exist:
   *    <ol>
   *    <li>products not superseded or deleted,
   *        that have origin properties.</li>
   *    <li>products not superseded,
   *        that have an event id.</li>
   *    </ol>
   * </li>
   * </ul>
   *
   * @return {ProductSummary} the most recent product with origin properties.
   * @see #productHasOriginProperties(ProductSummary)
   */
  public function getPreferredOriginProduct () {
    $allProducts = $this->getAllProducts();

    if (isset($allProducts['origin'])) {
      // "origin" products not superseded or deleted,
      // that have origin properties
      $products = self::getSortedMostPreferredFirst(
          self::getWithoutDeleted(self::getWithoutSuperseded(
              $allProducts['origin'])));
      foreach ($products as $p) {
        if (self::productHasOriginProperties($p)) {
          return $p;
        }
      }

      // "origin" products not superseded,
      // that have event id
      $products = self::getSortedMostPreferredFirst(
          self::getWithoutSuperseded($allProducts['origin']));
      foreach ($products as $p) {
        if ($p->getEventSource() !== null
            && $p->getEventSourceCode() !== null) {
          return $p;
        }
      }

      // an origin exists for this event, but is incomplete.
      return null;
    }

    // products not superseded or deleted,
    // that have origin properties
    $products = self::getSortedMostPreferredFirst(
        self::getWithoutDeleted(self::getWithoutSuperseded(
            self::productMapToList($allProducts))));
    foreach ($products as $p) {
      if (self::productHasOriginProperties($p)) {
        return $p;
      }
    }

    // products not superseded,
    // that have event id
    $products = self::getSortedMostPreferredFirst(
        self::getWithoutSuperseded(
            self::productMapToList($allProducts)));
    foreach ($products as $p) {
      if ($p->getEventSource() !== null
          && $p->getEventSourceCode() !== null) {
        return $p;
      }
    }

    // didn't find anything
    return null;
  }

  /**
   * Get the most preferred magnitude product for event.
   *
   * Currently calls getPreferredOriginProduct().
   *
   * @return {ProductSummary} most preferred magnitude product for event.
   */
  public function getPreferredMagnitudeProduct () {
    return $this->getPreferredOriginProduct();
  }

  /**
   * Get products from this event.
   *
   * @param $type {String}
   *        type of product to return, or null for all types.
   * @return {Array<ProductSummary>}, when type is not null;
   *         or {Array<String => Array<ProductSummary>>}, when type is null.
   */
  public function getProducts ($type = null) {
    if ($type === null) {
      // all products
      $map = array();
      foreach ($this->products as $type => $products) {
        $map[$type] = self::getSortedMostPreferredFirst(
            self::getWithoutDeleted(self::getWithoutSuperseded(
                $products)));
      }
      return $map;
    } else {
      $list = array();
      if (isset($this->products[$type])) {


        $list = self::getSortedMostPreferredFirst(
            self::getWithoutDeleted(self::getWithoutSuperseded(
                $this->products[$type])));
      }
      return $list;
    }
  }

  public function getPreferredProduct ($type) {
    $typeProducts = $this->getProducts($type);
    if (count($typeProducts) > 0) {
      return $typeProducts[0];
    }
    return null;
  }

  /**
   * Get preferred products of all types.
   */
  public function getPreferredProducts () {
    $preferred = array();
    $allProducts = $this->getProducts();
    foreach ($allProducts as $type => $products) {
      $preferred[$type] = $products[0];
    }
    return $preferred;
  }

  /**
   * Encode event information in an associative array.
   *
   * @param $storage {ProductStorage} default null.
   *        ProductStorage object used to load product contents.
   * @param $includeDeleted {Boolean} default true.
   *        Whether to include deleted products.
   * @param $includeSuperseded {Boolean} default false.
   *        Whether to include superseded products.
   * @return {Array} event information encoded as an associative array.
   */
  public function toArray($storage = null, $includeDeleted = true,
      $includeSuperseded = false) {
    $r = array(
      'summary' => $this->getEventSummary()->toArray(),
      'products' => array()
    );
    $products = self::productMapToList($this->getAllProducts());
    // filters
    if (!$includeSuperseded) {
      $products = self::getWithoutSuperseded($products);
    }
    if (!$includeDeleted && !$includeSuperseded) {
      $products = self::getWithoutDeleted($products);
    }

    // add non-superseded to array first
    $withoutSuperseded = self::getWithoutSuperseded($products);
    // sort by weight
    $withoutSuperseded = self::getSortedMostPreferredFirst($withoutSuperseded);
    foreach ($withoutSuperseded as $summary) {
      $array = $summary->toArray();
      if ($storage !== null) {
        // load product contents
        $product = $storage->getProduct($summary->getId());
        if ($product !== null) {
          $productArray = $product->toArray();
          $array['contents'] = $productArray['contents'];
        }
      }
      $r['products'][$summary->getId()->getType()][] = $array;
    }

    // output superseded at end of arrays
    if (count($withoutSuperseded) < count($products)) {
      // remove already output products from array
      $superseded = array_udiff($products, $withoutSuperseded,
          function ($a, $b) {
            return $a->getIndexId() - $b->getIndexId();
          });
      // sort remaining in preferred order
      $superseded = self::getSortedMostPreferredFirst($superseded);
      foreach ($superseded as $summary) {
        $array = $summary->toArray();
        if ($storage !== null) {
          // load product contents
          $product = $storage->getProduct($summary->getId());
          if ($product !== null) {
            $productArray = $product->toArray();
            $array['contents'] = $productArray['contents'];
          }
        }
        $r['products'][$summary->getId()->getType()][] = $array;
      }
    }

    // sort product types alphabetically
    ksort($r['products']);
    // return array representation
    return $r;
  }

  /**
   * Convert a product "map" to list.
   *
   * @param $map {Array<String => Array<ProductSummary>}
   *        product map keyed by type.
   * @return {Array<ProductSummary>} list of products.
   */
  public static function productMapToList ($map) {
    $list = array();
    foreach ($map as $type => $products) {
      foreach ($products as $p) {
        $list[] = $p;
      }
    }
    return $list;
  }

  /**
   * Convert a product list to "map".
   *
   * @param $list {Array<ProductSummary>}
   *        list of products.
   * @return {Array<String => Array<ProductSummary>>}
   *         product map keyed by type.
   */
  public static function productListToMap ($list) {
    $map = array();
    foreach ($list as $product) {
      $map[$product->getId()->getType()][] = $product;
    }
    return $map;
  }

  /**
   * Sort a list of products in most-preferred-first order.
   *
   * @param $list {Array<ProductSummary>}
   *        list of products.
   * @return {Array<ProductSummary>} sorted list.
   */
  public static function getSortedMostPreferredFirst ($list) {
    // make a copy
    $sorted = array_merge($list);
    // now sort
    usort($sorted, function ($p1, $p2) {
      $diff = $p2->getPreferredWeight() - $p1->getPreferredWeight();
      if ($diff !== 0) {
        return $diff;
      }
      // same preferred weight, check update time
      $diff = $p2->getUpdateTime() - $p1->getUpdateTime();
      if ($diff !== 0) {
        return $diff;
      }
      // same update time, sort alphabetically by URN
      return strcmp($p1->getId()->toString(), $p2->getId()->toString());
    });
    return $sorted;
  }

  /**
   * Remove old versions of products from a list.
   *
   * @param $list {Array<ProductSummary>}
   *        list of products.
   * @return {Array<ProductSummary>} list without old versions.
   */
  public static function getWithoutSuperseded ($list) {
    $unique = array();
    foreach ($list as $product) {
      $id = $product->getId();
      // unique key for product
      $key = $id->getSource() . '_' . $id->getType() . '_' . $id->getCode();
      // get current latest version
      $modified = -1;
      if (isset($unique[$key])) {
        $modified = $unique[$key]->getUpdateTime();
      }
      // keep latest version
      if ($id->getUpdateTime() > $modified) {
        $unique[$key] = $product;
      }
    }
    return array_values($unique);
  }

  /**
   * Remove old versions of products from a list.
   *
   * @param $list {Array<ProductSummary>}
   *        list of products.
   * @return {Array<ProductSummary>} list without old versions.
   */
  public static function getWithoutDeleted ($list) {
    $withoutDeleted = array();
    foreach ($list as $product) {
      if (strtoupper($product->getStatus()) !== 'DELETE') {
        $withoutDeleted[] = $product;
      }
    }
    return $withoutDeleted;
  }

  /**
   * Check whether a product can define an event (id, lat, lon, time).
   *
   * @param $product {ProductSummary}
   *        product to check.
   * @return {Boolean} true if product has all origin properties,
   *         false otherwise.
   */
  public static function productHasOriginProperties ($product) {
    return ($product->getEventSource() !== null
        && $product->getEventSourceCode() !== null
        && $product->getEventLatitude() !== null
        && $product->getEventLongitude() !== null
        && $product->getEventTime() !== null);
  }

}
