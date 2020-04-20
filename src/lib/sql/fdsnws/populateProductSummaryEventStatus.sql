
delimiter //
DROP PROCEDURE IF EXISTS populateProductSummaryEventStatus //
CREATE PROCEDURE populateProductSummaryEventStatus(
)
  MODIFIES SQL DATA
BEGIN
  DECLARE starttime BIGINT;
  DECLARE endtime BIGINT;
  DECLARE done INT DEFAULT 0;

  DECLARE cur_events CURSOR FOR
    SELECT DISTINCT min(e.eventTime), max(e.eventTime) + 1
    FROM event e;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  -- loop over all events, updating productSummaryEventStatus table
  START TRANSACTION;
  OPEN cur_events;
  cur_events_loop: LOOP
    FETCH cur_events into starttime, endtime;
    IF done = 1 THEN
      CLOSE cur_events;
      LEAVE cur_events_loop;
    END IF;

    CALL summarizeProductSummaryEventStatus(starttime, endtime);
  END LOOP cur_events_loop;
  COMMIT;

END;
//
delimiter ;


CALL populateProductSummaryEventStatus();
