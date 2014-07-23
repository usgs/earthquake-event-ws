<?php

// pdl classes

	include_once('classes/pdl/Content.class.php');
	include_once('classes/pdl/ProductId.class.php');
	include_once('classes/pdl/Product.class.php');
	include_once('classes/pdl/ProductSummary.class.php');
	include_once('classes/pdl/Event.class.php');
	include_once('classes/pdl/EventSummary.class.php');

	include_once('classes/pdl/ProductStorage.class.php');
	include_once('classes/pdl/ProductIndexCallback.class.php');
	include_once('classes/pdl/ProductIndexQuery.class.php');
	include_once('classes/pdl/ProductIndex.class.php');


// fdsn classes
	include_once ('classes/fdsn/Formatter.class.php');
	include_once ('classes/fdsn/AbstractFeed.class.php');
	include_once ('classes/fdsn/FDSNQuery.class.php');
	include_once ('classes/fdsn/FDSNIndex.class.php');
	include_once ('classes/fdsn/FDSNIndexCallback.class.php');
	include_once ('classes/fdsn/FDSNEventWebService.class.php');

	include_once ('classes/fdsn/AtomFeed.class.php');
	include_once ('classes/fdsn/CSVFeed.class.php');
	include_once ('classes/fdsn/TextFeed.class.php');
	include_once ('classes/fdsn/GeoJSONFeed.class.php');
	include_once ('classes/fdsn/KMLFeed.class.php');
	include_once ('classes/fdsn/QuakemlFeed.class.php');


// functions

	if (!function_exists("safefloatval")) {
		function safefloatval($value=null) {
			if ($value === null) {
				return null;
			} else {
				return floatval($value);
			}
		}
	}

	if (!function_exists("safeintval")) {
		function safeintval($value=null) {
			if ($value === null) {
				return null;
			} else {
				return intval($value);
			}
		}
	}
