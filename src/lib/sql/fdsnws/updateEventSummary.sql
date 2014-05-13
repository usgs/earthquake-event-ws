delimiter //
DROP PROCEDURE IF EXISTS updateEventSummary //
CREATE PROCEDURE updateEventSummary(IN in_eventid INT)
  MODIFIES SQL DATA
BEGIN
  DECLARE maxmmi DOUBLE;
  DECLARE alertlevel TEXT;
  DECLARE review_status TEXT;
  DECLARE event_type TEXT;
  DECLARE azimuthal_gap DOUBLE;
  DECLARE magnitude_type TEXT;
  DECLARE region TEXT;
  DECLARE types TEXT;
  DECLARE eventids TEXT;
  DECLARE eventsources TEXT;
  DECLARE productsources TEXT;
  DECLARE tsunami INT;
  DECLARE offset INT;
  DECLARE num_responses INT;
  DECLARE maxcdi DOUBLE;
  DECLARE magnitude DOUBLE;
  DECLARE lastmodified BIGINT;
  DECLARE significance INT;
  DECLARE num_stations_used INT;
  DECLARE minimum_distance DOUBLE;
  DECLARE standard_error DOUBLE;

  CALL getEventSummary(in_eventid, maxmmi, alertlevel, review_status,
      event_type, azimuthal_gap, magnitude_type, region, types, eventids,
      eventsources, productsources, tsunami, offset, num_responses, maxcdi,
      magnitude, lastmodified, significance, num_stations_used,
      minimum_distance, standard_error);

  -- update table
  DELETE FROM eventSummary WHERE eventid=in_eventid;
  INSERT INTO eventSummary VALUES (in_eventid, lastmodified, maxmmi, alertlevel,
      review_status, event_type, azimuthal_gap, magnitude_type, region, types,
      eventids, eventsources, productsources, tsunami, offset, num_responses,
      maxcdi, significance, num_stations_used, minimum_distance,
      standard_error);
END;
//
delimiter ;