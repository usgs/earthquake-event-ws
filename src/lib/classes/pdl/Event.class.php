<?php

class Event {

	const PROPERTY_CONFIG_PREFIX = "property_";

	public $indexId;
	public $products = array();

	private $config = null;


	public function __construct($indexId = null) {
		global $APP_DIR;
		$this->indexId = $indexId;
		$this->config = parse_ini_file($APP_DIR . "/conf/config.ini");
	}

	public function getIndexId() { return $this->indexId; }
	public function setIndexId($id) { $this->indexId = $id; }



	/**
	 * Return an associative array of all products, including those that are deleted.
	 *
	 * array(type1 => array(product1, product2), type2 => array(...))
	 */
	public function getAllProducts() {
		return $this->products;
	}

	public function getLastModified() {
		// TODO: should this filter products down to non-superseded?
		// those by definition have an older update time anyways
		$allproducts = $this->getAllProducts();

		$lastModified = -1;
		foreach ($allproducts as $type => $products) {
			foreach ($products as $product) {
				$modified = $product->getUpdateTime();
				if ($modified > $lastModified) {
					$lastModified = $modified;
				}
			}
		}

		return $lastModified;
	}

	public function setProducts($products) { $this->products = $products; }


	public function addProduct($summary) { 
		$this->products[$summary->getId()->getType()][] = $summary;
	}

	public function removeProduct($summary) {
		$type = $summary->getId()->getType();
		$array = $this->products[$type];
		if (($index = array_search($summary, $array)) !== FALSE) {
			//remove from array
			unset($array[$index]);
			$array = array_values($array);
			$this->products[$type] = $array;
		}
	}

	/**
	 * Return an associative array of all event codes that make up this event.
	 *
	 * array(source1 => source1code, sourc2, source2code)
	 */
	public function getEventCodes() {
		$eventIds = array();

		foreach ($this->getAllProducts() as $type => $products) {
			foreach ($products as $product) {
				$source = $product->getEventSource();
				$sourceCode = $product->getEventSourceCode();
				
				if ($source != null && $sourceCode != null) {
					$eventIds[$source] = $sourceCode;
				}
			}
		}

		return $eventIds;
	}

	public function getEventIdProduct() {
		$products = Event::productTypeMapToList($this->getAllProducts());

		//check for most preferred origin
		$preferredSource = Event::getMostPreferred(
				Event::getWithoutSuperseded($products),
				"origin",
				"eventsource"
			);

		if ($preferredSource == null || $preferredSource->getEventId() == null) {
			//origin may be deleted, remove deletes and check again
			$preferredSource = Event::getMostPreferred(
					Event::getWithoutSuperseded(Event::getWithoutDeleted($products)),
					"origin",
					"eventsource"
				);
		}

		return $preferredSource;
	}


	/**
	 * Return the preferred source.
	 */
	public function getSource() {
		$product = $this->getEventIdProduct();
		if ($product != null) {
			return $product->getEventSource();
		}
		return null;
	}

	/**
	 * Return the preferred source code.
	 */
	public function getSourceCode() {
		$product = $this->getEventIdProduct();
		if ($product != null) {
			return $product->getEventSourceCode();
		}
		return null;
	}


	public function getOriginProduct() {
		$products = Event::productTypeMapToList($this->getAllProducts());

		$product = null;

		foreach (array('latitude', 'longitude', 'eventtime') as $property) {
			$product = Event::getMostPreferred(
					Event::getWithoutDeleted(
							Event::getWithoutSuperseded($products)
						),
					"origin",
					$property
				);

			if ($product == null 
					|| $product->getEventLatitude() == null
					|| $product->getEventLongitude() == null
					|| $product->getEventTime() == null
			) {
				// no non-deleted product with all properties, check deleted
				
				// THIS LOOKS WRONG, shouldn't call getWithoutDeleted when looking for a deleted product
				$product = Event::getMostPreferred(
						Event::getWithoutSuperseded(
								Event::getWithoutDeleted($products)
							),
						"origin",
						$property
					);
			}

			if ($product != null) {
				break;
			}
		}

		return $product;
	}


	public function getCapAlertProduct() {
		$product = Event::getMostPreferred(
			Event::getWithoutDeleted(
				Event::getWithoutSuperseded(Event::productTypeMapToList($this->getAllProducts()))
			),
			"cap",
			null
		);

		return $product;
	}

	/**
	 * Return the preferred time.
	 */
	public function getTime() {
		$product = $this->getOriginProduct();
		if ($product != null) {
			return $product->getEventTime();
		}
		return null;
	}

	/**
	 * Return the preferred latitude.
	 */
	public function getLatitude() {
		$product = $this->getOriginProduct();
		if ($product != null) {
			return $product->getEventLatitude();
		}
		return null;


		$products = Event::productTypeMapToList($this->getAllProducts());

		$product = Event::getMostPreferred(
				Event::getWithoutDeleted(
						Event::getWithoutSuperseded($products)
					),
				"origin",
				"latitude"
			);

		if ($product == null) {
			// no non-deleted product with property, check deleted
			$product = Event::getMostPreferred(
					Event::getWithoutSuperseded(
							Event::getWithoutDeleted($products)
						),
					"origin",
					"latitude"
				);
		}

		if ($product != null) {
			return $product->getEventLatitude();
		}
		return null;
	}
	
	/**
	 * Return the event type.
	 */
	public function getType() {
		$product = $this->getOriginProduct();
		if ($product != null) {
			return $product->getEventType();
		}
		return null;
	}

	/**
	 * Return the preferred longitude.
	 */
	public function getLongitude() {
		$product = $this->getOriginProduct();
		if ($product != null) {
			return $product->getEventLongitude();
		}
		return null;


		$products = Event::productTypeMapToList($this->getAllProducts());

		$product = Event::getMostPreferred(
				Event::getWithoutDeleted(
						Event::getWithoutSuperseded($products)
					),
				"origin",
				"longitude"
			);

		if ($product == null) {
			// no non-deleted product with property, check deleted
			$product = Event::getMostPreferred(
					Event::getWithoutSuperseded(
							Event::getWithoutDeleted($products)
						),
					"origin",
					"longitude"
				);
		}

		if ($product != null) {
			return $product->getEventLongitude();
		}
		return null;
	}

	/**
	 * Return the preferred depth.
	 */
	public function getDepth() {
		$products = Event::productTypeMapToList($this->getAllProducts());

		$product = Event::getMostPreferred(
				Event::getWithoutDeleted(
						Event::getWithoutSuperseded($products)
					),
				"origin",
				"depth"
			);

		if ($product == null) {
			// no non-deleted product with property, check deleted
			$product = Event::getMostPreferred(
					Event::getWithoutSuperseded(
							Event::getWithoutDeleted($products)
						),
					"origin",
					"depth"
				);
		}

		if ($product != null) {
			return $product->getEventDepth();
		}
		return null;
	}

	public function getMagnitudeProduct() {
		$products = Event::productTypeMapToList($this->getAllProducts());

		$product = Event::getMostPreferred(
				Event::getWithoutDeleted(
						Event::getWithoutSuperseded($products)
					),
				"magnitude",
				"magnitude"
			);

		if ($product == null) {
			// no non-deleted product with property, check deleted
			$product = Event::getMostPreferred(
					Event::getWithoutSuperseded(
							Event::getWithoutDeleted($products)
						),
					"magnitude",
					"magnitude"
				);
		}

		return $product;
	}

	/**
	 * Return the preferred magnitude.
	 */
	public function getMagnitude() {
		$product = $this->getMagnitudeProduct();
		if ($product != null) {
			return $product->getEventMagnitude();
		}
		return null;
	}

	public function getMagnitudeType() {
		$product = $this->getMagnitudeProduct();
		if ($product != null) {
			$props = $product->getProperties();
			if (array_key_exists('magnitude-type', $props)) {
				return $props['magnitude-type'];
			}
		}
		return null;
	}

	public function isDeleted() {
		$originProduct = $this->getOriginProduct();
		if ($originProduct == null) {
			return true;
		}
		return false;

		$products = Event::productTypeMapToList($this->getAllProducts());
		$products = Event::getWithoutDeleted(
				Event::getWithoutSuperseded($products)
			);

		$latitude = Event::getMostPreferred($products, "origin", "latitude");
		$longitude = Event::getMostPreferred($products, "origin", "longitude");
		$eventtime = Event::getMostPreferred($products, "origin", "eventtime");

		if ($latitude == null || $longitude == null || $eventtime == null) {
			return true;
		}
		return false;
	}

	public function isReviewed() {
		$originProduct = $this->getOriginProduct();
		if ($originProduct != null) {
			$props = $originProduct->getProperties();
			if (array_key_exists("review-status", $props)) {
				$review_status = $props['review-status'];
				if (strstr($review_status, 'Reviewed') || strstr($review_status, 'Published')) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Return an associative array, or array of non-deleted products of the specified type.
	 *
	 * When type is null:
	 * 	array(type1 => array(product1, product2), type2 => array(...))
	 * When type is not null:
	 * 	array(product1, product2)
	 */
	public function getProducts($type = null) {
		$notDeleted = array();

		if ($type == null) {
			foreach ($this->products as $type => $summaries) {
				$typeNotDeleted = $this->getProducts($type);
				if (count($typeNotDeleted) > 0) {
					$notDeleted[$type] = $typeNotDeleted;
				}
			}
		} else {
			if (isset($this->products[$type])) {
				foreach ($this->products[$type] as $summary) {
					if (strtoupper($summary->getStatus()) != strtoupper(Product::$STATUS_DELETE)) {
						$notDeleted[] = $summary;
					}
				}

				usort($notDeleted, "Event::comparePreferredWeightDescending");
			}
		}

		return $notDeleted;
	}


	/**
	 * Return an associative array of preferred, non-deleted products of each type.
	 *
	 * array(type1 => product1, type2 => product2)
	 */
	public function getPreferredProducts() {
		$preferred = array();

		$notDeleted = $this->getProducts();
		foreach ($notDeleted as $type => $products) {
			$preferred[$type] = $this->getPreferredProduct($type);
		}

		return $preferred;
	}

	/**
	 * Return the most preferred product of the specified type, or null
	 * if no product of that type is available.
	 */
	public function getPreferredProduct($type) {
		return $this->choosePreferredProduct($this->getProducts($type));
	}
	
	/**
	 * Return an array of all preferred, non-deleted products, sorted by preferredWeight.
	 */
	public function getPreferredProductsSorted() {
		// get all the preferred products, outside an associative array
		$preferred = array_values($this->getPreferredProducts());

		// sort by preferred weight, descending
		usort($preferred, "Event::comparePreferredWeightDescending");

		return $preferred;
	}
	
	public function getEventSummary() {
		$summary = new EventSummary();

		$summary->setDepth($this->getDepth());
		$summary->setLatitude($this->getLatitude());
		$summary->setLongitude($this->getLongitude());
		$summary->setMagnitude($this->getMagnitude());
		$summary->setSource($this->getSource());
		$summary->setSourceCode($this->getSourceCode());
		$summary->setTime($this->getTime());
		$summary->setEventCodes($this->getEventCodes());

		// For all the properties defined in the config file, lets set them
		foreach( array_keys( $this->config ) as $key ) {
			if( strpos( $key, Event::PROPERTY_CONFIG_PREFIX) !== false ) {
				$property = substr( $key, strlen(Event::PROPERTY_CONFIG_PREFIX) );
				if( $this->getProperty($property) != null ) {
					$summary->setProperty( $property, $this->getProperty($property) );
				}
			}
		}

		return $summary;
	}

	/**
	 * Get the preferred product from an array of products.
	 *
	 * Chosen by greatest preferredWeight, then most recent updateTime.
	 */
	protected function choosePreferredProduct($products) {
		$preferred = null;

		foreach ($products as $product) {
			if ($preferred == null) {
				// first product
				$preferred = $product;
			} else {
				$summaryWeight = $product->getPreferredWeight();
				$preferredWeight = $preferred->getPreferredWeight();

				if ($summaryWeight > $preferredWeight 
					|| ($summaryWeight == $preferredWeight 
					&& gmp_cmp($product->getId()->getUpdateTime(),  
						$preferred->getId()->getUpdateTime()) == 1)
					//&& ($product->getId()->getUpdateTime() >   
					//	$preferred->getId()->getUpdateTime()))
				) {
					// summary has higher preferred weight
					// or weights are equal, but summary is more recent
					$preferred = $product;
				}
			}
		}

		return $preferred;
	}

	/**
	 * Comparison function for preferred ProductSummary weights.
	 */
	public static function comparePreferredWeightDescending($a, $b) {
		return $a->compareToPreferred($b);
	}

	/**
	 * Get the specified property from this event's product properties. The config.ini
	 * file specifies a list of products to search for this property
	 */	
	public function getProperty( $property ) {
		// Get Preferred, non-deleted products with the property
		$productList = Event::productTypeMapToList($this->getAllProducts());
	
		$productPropertyList = $this->config[Event::PROPERTY_CONFIG_PREFIX . $property];
		
		foreach( $productPropertyList as $productProperty ) {
			$pieces = explode('.', $productProperty);

			$preferred = $this->getMostPreferred( Event::getWithoutDeleted(
					Event::getWithoutSuperseded( $productList )), 
					$pieces[0], $pieces[1]);

			if ( $preferred != null ) {
				$properties =  $preferred->getProperties();
				return $properties[$property];
			}

			// Get Preferred, non-deleted products with the property
			$preferred = $this->getMostPreferred( Event::getWithoutSuperseded(
					Event::getWithoutDeleted( $productList )), 
					$pieces[0], $pieces[1]);

			if ( $preferred != null ) {
				$properties =  $preferred->getProperties();
				return $properties[$property];
			}
		}
		return null;
	}

	/**
	 * Serialize the event into an array for easy use by the search pages. 
	 */
	public function toArray($storage = null, $includeDeleted = true) {
		$r = array(
			'summary' => $this->getEventSummary()->toArray(),
			'products' => array()
		);

		$allproducts = array();
		if ($includeDeleted) {
			$allproducts = $this->getAllProducts();
		} else {
			$allproducts = $this->getProducts();
		}

		// TODO: get product contents urls, and merge with summary info
		foreach ($allproducts as $type => $products) {
			$r['products'][$type] = array();

			$sortedProducts = $products;
			usort($sortedProducts, "Event::comparePreferredWeightDescending");
			
			foreach ($sortedProducts as $summary) {
				$array = $summary->toArray();
				if ($storage != null) {
					// now get product contents...
					$product = $storage->getProduct($summary->getId());
					if ($product != null) {
						$productArray = $product->toArray();
						$array['contents'] = $productArray['contents'];
					}
				}
				$r['products'][$type][] = $array;
			}
		}
		
		return $r;
	}

	/**
	 * Convert the multidimensional product type map into a standard array containing all products
	 */
	public static function productTypeMapToList( $products ) {
		$allProducts = array();
		foreach( $products as $productType ) {
			foreach( $productType as $product ) {
				$allProducts[] = $product;
			}
		}
		return $allProducts;
	}

	/**
	 * Find the most preferred product.
	 * 
	 * If preferredType is not null, products of this type are favored over
	 * those not of this type.
	 * 
	 * If preferredNotNullProperty is not null, products that have this property
	 * set are favored over those without this property set.
	 * 
	 * @param products
	 *            the list of products to search.
	 * @param preferredType
	 *            the preferred product type, if available.
	 * @param preferredNotNullProperty
	 *            the preferred property name, if available.
	 * @return
	 */
	public static function getMostPreferred($products, $preferredType,
			$preferredNotNullProperty) {
		
		$mostPreferred = null;

		foreach( $products as $product ) {
			if ($preferredNotNullProperty != null) {
				// ignore products that don't have the preferredNotNullProperty
				if ( !array_key_exists( $preferredNotNullProperty, 
					$product->getProperties()) ) {
					continue;
				}
			}

			if ($mostPreferred == null) {
				// first product is most preferred so far
				$mostPreferred = $product;
				continue;
			}

			if ($preferredType != null) {
				if ($product->getId()->getType() == $preferredType) {
					if ($mostPreferred->getId()->getType() != $preferredType) {
						// prefer products of this type
						$mostPreferred = $product;
					}
				} else if ($mostPreferred->getId()->getType() == $preferredType) {
					// already have preferred product of preferred type
					continue;
				}
			}

			if ($product->getPreferredWeight() > $mostPreferred->getPreferredWeight()) {
				// higher preferred weight
				$mostPreferred = $product;
			} else if ($product->getPreferredWeight() == $mostPreferred->getPreferredWeight()) {

				$ptime = $product->getUpdateTime();
				$mtime = $mostPreferred->getUpdateTime();

				if (function_exists("gmp_cmp")) {
					if (gmp_cmp($ptime, $mtime) == 1) {
						$mostPreferred = $product;
					}
				} else if (floatval($ptime) > floatval($mtime)) {
					// floats have larger range than ints
					$mostPreferred = $product;
				}
			}
			
		}

		return $mostPreferred;
	}
	
	/**
	 * Remove deleted products from the list.
	 * 
	 * @param products
	 *            list of products to filter.
	 * @return copy of the products list with deleted products removed.
	 */
	public static function getWithoutDeleted($products) {
		$withoutDeleted = array();

		foreach( $products as $product ) {
			if ( strtoupper(Product::$STATUS_DELETE) != strtoupper($product->getStatus()) ) {
				$withoutDeleted[] = $product;
			}
		}

		return $withoutDeleted;
	}
	
	/**
	 * Remove old versions of products from the list.
	 * 
	 * @param products
	 *            list of products to filter.
	 * @return a copy of the products list with products of the same
	 *         source+type+code but with older updateTimes (superseded) removed.
	 */
	public static function getWithoutSuperseded($products) {
		// place product into latest, keyed by source+type+code,
		// keeping only most recent update for each key
		$latest = array();
		foreach ( $products as $summary ) {
			$id = $summary->getId();

			// key is combination of source, type, and code
			// since none of these may contain ":", it is used as a delimiter to
			// prevent collisions.
			$key = $id->getSource() . ":" . $id->getType() . ":" . $id->getCode();
			if (!array_key_exists($key, $latest)) {
				// first product
				$latest[$key] = $summary;
			} else {
				// keep latest product
				$other = $latest[$key];
				// Check if $other's update time is less than $summary's
				if ( gmp_cmp($other->getId()->getUpdateTime(), 
					$id->getUpdateTime()) == -1
				//if ( $other->getId()->getUpdateTime() <  
				//	$id->getUpdateTime()

				) {
					$latest[$key] = $summary;
				}
			}
		}

		// those that are in the latest map have not been superseded
		$withoutSuperseded = array_values($latest);
		return $withoutSuperseded;
	}

}

