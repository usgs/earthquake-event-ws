delimiter //
DROP PROCEDURE IF EXISTS updateEventSummary //
CREATE PROCEDURE updateEventSummary(IN in_eventid INT)
  MODIFIES SQL DATA
BEGIN
  DECLARE maxmmi DOUBLE;
  DECLARE alertlevel TEXT CHARACTER SET utf8;
  DECLARE review_status TEXT CHARACTER SET utf8;
  DECLARE event_type TEXT CHARACTER SET utf8;
  DECLARE azimuthal_gap DOUBLE;
  DECLARE magnitude_type TEXT CHARACTER SET utf8;
  DECLARE region TEXT CHARACTER SET utf8;
  DECLARE types TEXT CHARACTER SET utf8;
  DECLARE eventids TEXT CHARACTER SET utf8;
  DECLARE eventsources TEXT CHARACTER SET utf8;
  DECLARE productsources TEXT CHARACTER SET utf8;
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