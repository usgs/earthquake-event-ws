<?php


/**
 * A Query object for an FDSNIndex.
 */
class FDSNQuery {


// fdsn api parameters

	// time
	public $starttime = null;     // millisecond epoch time
	public $endtime = null;       // millisecond epoch time
	public $updatedafter = null;  // millisecond epoch time

	// rectangle
	public $minlatitude = null;  // degrees [-90,90]
	public $maxlatitude = null;  // degrees [-90,90]
	public $minlongitude = null; // degrees [-180,180]
	public $maxlongitude = null; // degrees [-180,180]

	// circle
	public $latitude = null;    // degrees [-90,90]
	public $longitude = null;   // degrees [-180,180]
	public $minradius = 0;      // degrees [0,180]
	public $maxradius = null;   // degrees [0,180]
	public $minradiuskm = null; // kilometers [0, 20001.6]
	public $maxradiuskm = null; // kilometers [0, 20001.6]

	// other
	public $mindepth = null;   // kilometers [-100,1000]
	public $maxdepth = null;   // kilometers [-100,1000]
	public $minmagnitude = null;
	public $maxmagnitude = null;
	public $magnitudetype = null;
	public $includeallorigins = false;
	public $includeallmagnitudes = false;
	public $includearrivals = false;
	public $eventid = null;
	public $limit = null;
	public $offset = 1;
	public $orderby = 'time';     // 'time', 'time-asc', 'magnitude', 'magnitude-asc'
	public $catalog = null;       // eventSource
	public $contributor = null;   // productSource


// extensions


	// TODO: should this default to earthquake? or all?
	public $eventtype = null;     // 'earthquake', 'quarry', ???
	public $reviewstatus = null;  // 'automatic', 'manual'

	// shakemap
	public $minmmi = null;
	public $maxmmi = null;

	// dyfi
	public $mincdi = null;
	public $maxcdi = null;
	public $minfelt = null;

	// pager
	public $alertlevel = null;    // 'green', 'yellow', 'orange', 'red'

	// azimuthal gap
	public $mingap = null;
	public $maxgap = null;

	// significance
	public $minsig = null;
	public $maxsig = null;

	// associated information
	public $producttype = null;
	public $productcode = null;

	public $includedeleted = false;
	public $includesuperseded = false;

	// formatting parameters

	// output format (extension to fdsn, which only specifies quakeml)
	public $format = 'quakeml';   // 'quakeml', 'csv', 'geojson', 'kml'

	// callback name for geojson format
	public $callback = null;

	// kml options
	public $kmlcolorby = 'age'; // 'age', 'depth'
	public $kmlanimated = false;

	// error code options
	public $nodata = 204;


// non-query parameters

	// used by summary feeds to determine feed "title"
	public $resultTitle = 'USGS Earthquakes';

	// number of matching events, populated by service
	public $resultCount = null;

}

