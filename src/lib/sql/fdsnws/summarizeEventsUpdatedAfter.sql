delimiter //
DROP PROCEDURE IF EXISTS summarizeEventsUpdatedAfter//
CREATE PROCEDURE summarizeEventsUpdatedAfter(
  IN in_threshold BIGINT
)
  MODIFIES SQL DATA
BEGIN
  DECLARE l_eventid VARCHAR(255);

  -- find events with products that were indexed at or after in_threshold
  DECLARE done INT DEFAULT 0;
  DECLARE cur_eventids CURSOR FOR
    SELECT DISTINCT eventid
    FROM productSummary
    WHERE created >= in_threshold
      AND eventid IS NOT NULL;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur_eventids;
  cur_eventids_loop: LOOP
    FETCH cur_eventids INTO l_eventid;
    IF done = 1 THEN
      CLOSE cur_eventids;
      LEAVE cur_eventids_loop;
    END IF;
    -- call summarize stored procedures with eventid
    CALL updateEventSummary(l_eventid);
    CALL summarizeEventProducts(l_eventid);
  END LOOP cur_eventids_loop;
END;
//
delimiter ;
