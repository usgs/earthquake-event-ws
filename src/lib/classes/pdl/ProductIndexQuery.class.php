<?php
class ProductIndexQuery {

	public function __construct() {
		$this->resultType = self::RESULT_TYPE_CURRENT;
	}


	// These types omit deleted products

	const RESULT_TYPE_CURRENT = 1;
	const RESULT_TYPE_SUPERSEDED = 2;
	const RESULT_TYPE_PRODUCT_SOURCE = 3; // Not implemented

	// With deleted products too

	const RESULT_TYPE_CURRENT_WITH_DELETE = 101;
	const RESULT_TYPE_SUPERSEDED_WITH_DELETE = 102;
	const RESULT_TYPE_PRODUCT_SOURCE_WITH_DELETE = 103; // Not implemented

	// All means all. Yes deleted, yes superceded, yes all.
	const RESULT_TYPE_ALL = 999; // Not implemented

	/** Search results max limit */
	private $searchMax;

	/** Include previous versions? */
	private $resultType;

	/** Event id */
	private $eventId;

	/** Preferred and non-preferred event source */
	private $eventSource;

	/** Preferred event source code */
	private $eventSourceCode;

	/** Minimum event time, inclusive. */
	private $minEventTime;

	/** Maximum event time, inclusive. */
	private $maxEventTime;

	/** Minimum event latitude. */
	private $minEventLatitude;

	/** Maximum event latitude. */
	private $maxEventLatitude;

	/** Minimum event longitude. */
	private $minEventLongitude;

	/** Maximum event longitude. */
	private $maxEventLongitude;

	/** Minimum event depth. */
	private $minEventDepth;

	/** Maximum event depth. */
	private $maxEventDepth;

	/** Minimum event magnitude. */
	private $minEventMagnitude;

	/** Maximum event magnitude. */
	private $maxEventMagnitude;

	/** A list of product ids to search. */
	private $productIds;

	/** Minimum product update time. */
	private $minProductUpdateTime;

	/** Maximum product update time. */
	private $maxProductUpdateTime;

	/** The product source. */
	private $productSources;

	/** The product type. */
	private $productTypes;

	/** The product code. */
	private $productCode;

	/** The product version */
	private $productVersion;

	/** The product status */
	private $productStatus;

	/** Some EventSummary preferred properties */

	/** Maximum Max MMI value */
	private $maxMaxmmi;

	/** Minimum Max MMI value */
	private $minMaxmmi;

	/** Pager alert level */
	private $alertlevel;

	/** Event review status */
	private $review_status;

	/** Minimum Azimuthal gap property */
	private $minAzimuthal_gap;

	/** Maximum Azimuthal gap property */
	private $maxAzimuthal_gap;

	/** Type of the preferred magnitude */
	private $magnitude_type;

	/** Minimum event significance to include */
	private $min_significance;

	/** Maximum event significance to include */
	private $max_significance;

	/** Event type (quarry blast, nuke, etc) */
	private $event_type;

	/** Event Status **/
	private $showDeletedEvent;

	/** Product Status **/
	private $showDeletedProduct;

	/** Catalog Source -- same as eventSource **/
	private $catalogSource;

	/** Display certain products on the event page **/
	private $showProductBySource;

	/**
	 * A lot of getters and setters...
	*/

	public function setSearchMax($searchMax) {
		$this->searchMax = $searchMax;
	}

	public function getSearchMax() {
		return $this->searchMax;
	}

	public function setResultType($resultType) {
		$this->resultType = $resultType;
	}

	public function getResultType() {
		return $this->resultType;
	}

	public function setEventId( $eventId ) {
		$this->eventId = $eventId;
	}

	public function getEventId() {
		return $this->eventId;
	}

	public function setEventSource($eventSource) {
		$this->eventSource = $eventSource;
	}

	public function getEventSource() {
		return $this->eventSource;
	}

	public function setEventSourceCode($eventSourceCode) {
		$this->eventSourceCode = $eventSourceCode;
	}

	public function getEventSourceCode() {
		return $this->eventSourceCode;
	}

	public function getMinEventTime() {
		return $this->minEventTime;
	}

	public function setMinEventTime($minEventTime) {
		$this->minEventTime = $minEventTime;
	}

	public function getMaxEventTime() {
		return  $this->maxEventTime;
	}

	public function setMaxEventTime($maxEventTime) {
		$this->maxEventTime = $maxEventTime;
	}

	public function getMinEventLatitude() {
		return  $this->minEventLatitude;
	}

	public function setMinEventLatitude($minEventLatitude) {
		$this->minEventLatitude = $minEventLatitude;
	}

	public function getMaxEventLatitude() {
		return $this->maxEventLatitude;
	}

	public function setMaxEventLatitude($maxEventLatitude) {
		$this->maxEventLatitude = $maxEventLatitude;
	}

	public function getMinEventLongitude() {
		return  $this->minEventLongitude;
	}

	public function setMinEventLongitude($minEventLongitude) {
		$this->minEventLongitude = $minEventLongitude;
	}

	public function getMaxEventLongitude() {
		return  $this->maxEventLongitude;
	}

	public function setMaxEventLongitude($maxEventLongitude) {
		$this->maxEventLongitude = $maxEventLongitude;
	}

	public function getMinEventDepth() {
		return  $this->minEventDepth;
	}

	public function setMinEventDepth($minEventDepth) {
		$this->minEventDepth = $minEventDepth;
	}

	public function getMaxEventDepth() {
		return  $this->maxEventDepth;
	}

	public function setMaxEventDepth($maxEventDepth) {
		$this->maxEventDepth = $maxEventDepth;
	}

	public function getMinEventMagnitude() {
		return  $this->minEventMagnitude;
	}

	public function setMinEventMagnitude($minEventMagnitude) {
		$this->minEventMagnitude = $minEventMagnitude;
	}

	public function getMaxEventMagnitude() {
		return  $this->maxEventMagnitude;
	}

	public function setMaxEventMagnitude($maxEventMagnitude) {
		$this->maxEventMagnitude = $maxEventMagnitude;
	}

	public function getEventType() {
		return  $this->event_type;
	}

	public function setEventType($event_type) {
		$this->event_type = $event_type;
	}

	public function getProductIds() {
		return  $this->productIds;
	}

	public function setProductIds($productIds) {
		$this->productIds = $productIds;
	}

	public function getMinProductUpdateTime() {
		return  $this->minProductUpdateTime;
	}

	public function setMinProductUpdateTime($minProductUpdateTime) {
		$this->minProductUpdateTime = $minProductUpdateTime;
	}

	public function getMaxProductUpdateTime() {
		return  $this->maxProductUpdateTime;
	}

	public function setMaxProductUpdateTime($maxProductUpdateTime) {
		$this->maxProductUpdateTime = $maxProductUpdateTime;
	}

	public function getProductSources() {
		return  $this->productSources;
	}

	public function setProductSources($productSources) {
		$this->productSources = $productSources;
	}

	// Product types should be an array
	public function getProductTypes() {
		return  $this->productTypes;
	}

	public function setProductTypes($productTypes) {
		$this->productTypes = $productTypes;
	}

	public function getProductCode() {
		return  $this->productCode;
	}

	public function setProductCode($productCode) {
		$this->productCode = $productCode;
	}

	public function setProductVersion($productVersion) {
		$this->productVersion = $productVersion;
	}

	public function getProductVersion() {
		return  $this->productVersion;
	}

	public function setProductStatus($productStatus) {
		$this->productStatus = $productStatus;
	}

	public function getProductStatus() {
		return  $this->productStatus;
	}

	/** Yes these should be MaxMaxMMI */
	public function setMaxMaxMMI($maxMaxmmi) {
		$this->maxMaxmmi = $maxMaxmmi;
	}

	public function getMaxMaxMMI() {
		return  $this->maxMaxmmi;
	}

	public function setMinMaxMMI($minMaxmmi) {
		$this->minMaxmmi = $minMaxmmi;
	}

	public function getMinMaxMMI() {
		return  $this->minMaxmmi;
	}


	public function setAlertLevel($alertlevel) {
		$this->alertlevel = $alertlevel;
	}

	public function getAlertLevel() {
		return  $this->alertlevel;
	}

	public function setReviewStatus($review_status) {
		$this->review_status = $review_status;
	}

	public function getReviewStatus() {
		return  $this->review_status;
	}

	public function setMinAzimuthalGap($minAzimuthal_gap) {
		$this->minAzimuthal_gap = $minAzimuthal_gap;
	}

	public function getMinAzimuthalGap() {
		return  $this->minAzimuthal_gap;
	}

	public function setMaxAzimuthalGap($maxAzimuthal_gap) {
		$this->maxAzimuthal_gap = $maxAzimuthal_gap;
	}

	public function getMaxAzimuthalGap() {
		return  $this->maxAzimuthal_gap;
	}

	public function setMagnitudeType($magnitude_type) {
		$this->magnitude_type = $magnitude_type;
	}

	public function getMagnitudeType() {
		return  $this->magnitude_type;
	}

	public function setMinSignificance ($min_significance) {
		$this->min_significance = $min_significance;
	}

	public function getMinSignificance () {
		return $this->min_significance;
	}

	public function setMaxSignificance ($max_significance) {
		$this->max_significance = $max_significance;
	}

	public function getMaxSignificance () {
		return $this->max_significance;
	}

	public function getShowDeletedEvent() {
		return $this->showDeletedEvent;
	}

	public function setShowDeletedEvent($showDeletedEvent) {
		$this->showDeletedEvent = $showDeletedEvent;
	}

	public function getShowDeletedProduct() {
		return $this->showDeletedProduct;
	}

	public function setShowDeletedProduct($showDeletedProduct) {
		$this->showDeletedProduct = $showDeletedProduct;
	}

	public function getCatalogSource() {
		return $this->catalogSource;
	}

	public function setCatalogSource($catalogSource) {
		$this->catalogSource = $catalogSource;
	}

	public function getShowProductBySource() {
		return $this->showProductBySource;
	}

	public function setShowProductBySource($showProductBySource) {
		$this->showProductBySource = $showProductBySource;
	}
}
?>
