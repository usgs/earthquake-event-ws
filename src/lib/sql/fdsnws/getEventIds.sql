delimiter //
DROP PROCEDURE IF EXISTS getEventIds//
CREATE PROCEDURE getEventIds(
  IN in_eventid INT,
  OUT out_eventids TEXT,
  OUT out_eventsources TEXT
)
  READS SQL DATA
BEGIN
  DECLARE l_source VARCHAR(255);
  DECLARE l_code VARCHAR(255);

  DECLARE done INT DEFAULT 0;
  DECLARE cur_ids CURSOR FOR
    SELECT DISTINCT eventSource, eventSourceCode
    FROM productSummary
    WHERE eventid = in_eventid
      AND eventSource IS NOT NULL
      AND eventSourceCode IS NOT NULL;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  SET out_eventids = NULL;
  SET out_eventsources = NULL;
  OPEN cur_ids;
  cur_ids_loop: LOOP
    FETCH cur_ids INTO l_source, l_code;
    IF done = 1 THEN
      CLOSE cur_ids;
      LEAVE cur_ids_loop;
    END IF;

    -- build list of product types
    SET out_eventids = CONCAT(COALESCE(out_eventids, ''), ',', l_source,
	l_code);
    SET out_eventsources = CONCAT(COALESCE(out_eventsources, ''), ',',
	l_source);
  END LOOP cur_ids_loop;

  IF out_eventids <> '' THEN
    SET out_eventids = CONCAT(out_eventids, ',');
    SET out_eventsources = CONCAT(out_eventsources, ',');
  END IF;
END;
//
delimiter ;