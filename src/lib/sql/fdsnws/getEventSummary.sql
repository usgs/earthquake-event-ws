delimiter //
DROP PROCEDURE IF EXISTS getEventSummary//
CREATE PROCEDURE getEventSummary(
  IN in_eventid INT,
  OUT out_maxmmi DOUBLE,
  OUT out_alertlevel TEXT,
  OUT out_review_status TEXT,
  OUT out_event_type TEXT,
  OUT out_azimuthal_gap DOUBLE,
  OUT out_magnitude_type TEXT,
  OUT out_region TEXT,
  OUT out_producttypes TEXT,
  OUT out_eventids TEXT,
  OUT out_eventsources TEXT,
  OUT out_productsources TEXT,
  OUT out_tsunami INT,
  OUT out_offset INT,
  OUT out_num_responses INT,
  OUT out_maxcdi DOUBLE,
  OUT out_magnitude DOUBLE,
  OUT out_lastmodified BIGINT,
  OUT out_significance INT,
  OUT out_num_stations_used INT,
  OUT out_minimum_distance DOUBLE,
  OUT out_standard_error DOUBLE
)
  READS SQL DATA
BEGIN
  DECLARE pager_id INT DEFAULT -1;
  DECLARE origin_id INT DEFAULT -1;
  DECLARE dyfi_id INT DEFAULT -1;
  DECLARE shakemap_id INT DEFAULT -1;
  DECLARE geoserve_id INT DEFAULT -1;
  DECLARE significance_id INT DEFAULT -1;
  DECLARE l_latitude DOUBLE;
  DECLARE l_longitude DOUBLE;
  DECLARE summary_type VARCHAR(255);
  DECLARE summary_id INT;
  DECLARE tmp_tsunami VARCHAR(255);

  DECLARE mag_sig DOUBLE;
  DECLARE pager_sig DOUBLE;
  DECLARE dyfi_sig DOUBLE;

  DECLARE done INT DEFAULT 0;
  DECLARE cur_summary_products CURSOR FOR
    SELECT ps.id, ps.type
    FROM preferredProduct ps
    WHERE ps.eventId=in_eventid
    AND ps.type IN (
      'losspager', 'losspager-scenario',
      'origin', 'origin-scenario',
      'shakemap', 'shakemap-scenario',
      'dyfi', 'dyfi-scenario',
      'geoserve', 'geoserve-scenario',
      'significance', 'significance-scenario'
    );

  -- used to look up event location for region name
  DECLARE cur_location CURSOR FOR
    SELECT latitude, longitude
    FROM event
    WHERE id=in_eventid;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  -- find product ids for products used to generate summary
  OPEN cur_summary_products;
  cur_summary_products_loop: LOOP
    FETCH cur_summary_products INTO summary_id, summary_type;
    IF done = 1 THEN
      CLOSE cur_summary_products;
      LEAVE cur_summary_products_loop;
    END IF;

    -- save relevant product ids for fetching properties
    IF summary_type = 'losspager' THEN SET pager_id = summary_id;
    ELSEIF summary_type = 'losspager-scenario' THEN SET pager_id = summary_id;
    ELSEIF summary_type = 'origin' THEN SET origin_id = summary_id;
    ELSEIF summary_type = 'origin-scenario' THEN SET origin_id = summary_id;
    ELSEIF summary_type = 'shakemap' THEN SET shakemap_id = summary_id;
    ELSEIF summary_type = 'shakemap-scenario' THEN SET shakemap_id = summary_id;
    ELSEIF summary_type = 'dyfi' THEN SET dyfi_id = summary_id;
    ELSEIF summary_type = 'dyfi-scenario' THEN SET dyfi_id = summary_id;
    ELSEIF summary_type = 'geoserve' THEN SET geoserve_id = summary_id;
    ELSEIF summary_type = 'geoserve-scenario' THEN SET geoserve_id = summary_id;
    ELSEIF summary_type = 'significance' THEN SET significance_id = summary_id;
    ELSEIF summary_type = 'significance-scenario' THEN SET significance_id = summary_id;
    END IF;
  END LOOP cur_summary_products_loop;

  -- load shakemap properties
  IF shakemap_id <> -1 THEN
    CALL getProductProperty(shakemap_id, 'maxmmi', out_maxmmi);
  END IF;

  -- load pager properties
  IF pager_id <> -1 THEN
    IF out_maxmmi IS NULL THEN
      -- shakemap didn't specify, check losspager
      CALL getProductProperty(pager_id, 'maxmmi', out_maxmmi);
    END IF;
    CALL getProductProperty(pager_id, 'alertlevel', out_alertlevel);
  END IF;

  -- load dyfi properties
  IF dyfi_id <> -1 THEN
    CALL getProductProperty(dyfi_id, 'maxmmi', out_maxcdi);
    CALL getProductProperty(dyfi_id, 'num-responses', out_num_responses);

    IF out_num_responses IS NULL THEN
      CALL getProductProperty(dyfi_id, 'numResp', out_num_responses);
    END IF;
  END IF;

  -- load origin properties
  IF origin_id <> -1 THEN
    CALL getProductProperty(origin_id, 'magnitude', out_magnitude);
    CALL getProductProperty(origin_id, 'magnitude-type', out_magnitude_type);
    CALL getProductProperty(origin_id, 'title', out_region);
    CALL getProductProperty(origin_id, 'azimuthal-gap', out_azimuthal_gap);
    CALL getProductProperty(origin_id, 'review-status', out_review_status);
    CALL getProductProperty(origin_id, 'event-type', out_event_type);
    CALL getProductProperty(origin_id, 'num-stations-used',
  out_num_stations_used);
    CALL getProductProperty(origin_id, 'minimum-distance',
  out_minimum_distance);
    CALL getProductProperty(origin_id, 'standard-error', out_standard_error);
    IF out_event_type IS NULL THEN
      SET out_event_type = 'earthquake';
    END IF;
  END IF;

  -- improved location information from geoserve
  IF geoserve_id <> -1 THEN
    CALL getProductProperty(geoserve_id, 'utcOffset', out_offset);

    CALL getProductProperty(geoserve_id, 'tsunamiFlag', tmp_tsunami);
    IF tmp_tsunami IS NOT NULL AND tmp_tsunami = 'true' THEN
      SET out_tsunami = 1;
    END IF;

    IF out_region IS NULL THEN
      CALL getProductProperty(geoserve_id, 'location', out_region);
    END IF;
  END IF;

  IF out_region IS NULL THEN
    -- only use feregion if not already set by origin or geoserve
    SET done = 0;
    OPEN cur_location;
    FETCH cur_location INTO l_latitude, l_longitude;
    CLOSE cur_location;
    IF done <> 1 THEN
      -- found lat/lon, uses feplus get_region_name function
      SET out_region = get_region_name(l_latitude, l_longitude, 'L');
    END IF;
  END IF;

  IF out_tsunami IS NULL OR out_tsunami = 0 THEN
    CALL getTsunamiLinkProduct(in_eventid, out_tsunami);
    IF out_tsunami IS NOT NULL AND out_tsunami <> 0 THEN
      SET out_tsunami = 1;
    END IF;
  END IF;

  IF significance_id <> -1 THEN
    CALL getProductProperty(significance_id, 'significance', out_significance);
  ELSE
    -- calculate significance
    IF out_magnitude IS NOT NULL THEN
      -- Use ABS when scaling magnitude to prevent negative magnitudes from
      -- resulting in a positive significance contribution from magnitude
      SET mag_sig = out_magnitude * 100 * (ABS(out_magnitude) / 6.5);
    ELSE
      SET mag_sig = 0;
    END IF;

    SET pager_sig = 0;
    IF out_alertlevel IS NOT NULL THEN
      IF out_alertlevel = 'red' THEN
  SET pager_sig = 2000;
      ELSEIF out_alertlevel = 'orange' THEN
  SET pager_sig = 1000;
      ELSEIF out_alertlevel = 'yellow' THEN
  SET pager_sig = 650;
      END IF;
    END IF;

    IF out_num_responses IS NOT NULL THEN
      SET dyfi_sig = (LEAST(out_num_responses, 1000) * out_maxcdi / 10);
    ELSE
      SET dyfi_sig = 0;
    END IF;

    SET out_significance = GREATEST(mag_sig, pager_sig) + dyfi_sig;
  END IF;

  -- get event ids and sources
  CALL getEventIds(in_eventid, out_eventids, out_eventsources);
  -- get product sources
  CALL getEventProductSources(in_eventid, out_productsources);
  -- get product types
  CALL getEventProductTypes(in_eventid, out_producttypes);
  -- event last modified is most recent product update time
  CALL getEventLastModified(in_eventid, out_lastmodified);
END;
//
delimiter ;
