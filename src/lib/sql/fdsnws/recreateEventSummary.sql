delimiter //
DROP PROCEDURE IF EXISTS recreateEventSummary //
CREATE PROCEDURE recreateEventSummary()
  MODIFIES SQL DATA
BEGIN
  DECLARE eventid INT;

  DECLARE done INT DEFAULT 0;
  DECLARE cur_events CURSOR FOR SELECT id FROM event;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  START TRANSACTION;

  -- recreate eventSummary table
  DROP TABLE IF EXISTS eventSummary;
  CREATE TABLE eventSummary (
      eventid BIGINT(20) REFERENCES event(id),
      lastmodified BIGINT,
      maxmmi DOUBLE,
      alertlevel VARCHAR(255),
      review_status VARCHAR(255),
      event_type VARCHAR(255),
      azimuthal_gap DOUBLE,
      magnitude_type VARCHAR(255),
      region VARCHAR(255),
      types TEXT,
      eventids TEXT,
      eventsources TEXT,
      productsources TEXT,
      tsunami INT,
      offset INT,
      num_responses INT,
      maxcdi DOUBLE,
      significance INT,
      num_stations_used INT,
      minimum_distance DOUBLE,
      standard_error DOUBLE,

      UNIQUE INDEX(eventid),
      INDEX(lastmodified),
      INDEX(maxmmi),
      INDEX(alertlevel),
      INDEX(review_status),
      INDEX(event_type),
      INDEX(azimuthal_gap),
      INDEX(magnitude_type),
      INDEX(region),
      INDEX(types(255)),
      INDEX(eventids(255)),
      INDEX(eventsources(255)),
      INDEX(productsources(255)),
      INDEX(tsunami),
      INDEX(num_responses),
      INDEX(maxcdi),
      INDEX(significance),
      INDEX(num_stations_used),
      INDEX(minimum_distance),
      INDEX(standard_error)
    ) ENGINE = InnoDB;

  -- loop over all events, updating eventSummary table
  OPEN cur_events;
  cur_events_loop: LOOP
    FETCH cur_events INTO eventid;
    IF done = 1 THEN
      CLOSE cur_events;
      LEAVE cur_events_loop;
    END IF;

    CALL updateEventSummary(eventid);
  END LOOP cur_events_loop;

  COMMIT;
END;
//
delimiter ;

CALL recreateEventSummary();