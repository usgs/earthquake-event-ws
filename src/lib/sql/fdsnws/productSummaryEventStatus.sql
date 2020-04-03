CREATE TABLE IF NOT EXISTS productSummaryEventStatus (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  eventId BIGINT NOT NULL,
  eventUpdated BIGINT NOT NULL,
  productSummaryId BIGINT NOT NULL,
  eventPreferred INT DEFAULT 0,

  KEY preferredEventProductIndex (eventId, eventPreferred),
  FOREIGN KEY (eventId) REFERENCES event(id) ON DELETE CASCADE,
  FOREIGN KEY (productSummaryId) REFERENCES productSummary(id) ON DELETE CASCADE
) ENGINE=InnoDB;


delimiter //
DROP PROCEDURE IF EXISTS updateProductSummaryEventStatus //
CREATE PROCEDURE updateProductSummaryEventStatus(IN in_eventid INT)
  MODIFIES SQL DATA
BEGIN
  DECLARE preferredId INT;

  -- remove existing summary status
  DELETE FROM productSummaryEventStatus WHERE eventId = in_eventid;

  -- add catalog preferred origin products
  INSERT INTO productSummaryEventStatus
    (eventId, eventUpdated, productSummaryId, eventPreferred)
    SELECT
      e.id as eventId,
      e.updated as eventUpdated,
      ps.id as productSummaryId,
      0 as eventPreferred
    FROM event e
    JOIN preferredProduct ps ON (ps.eventid = e.id)
    WHERE e.id = in_eventid
    AND ps.type in ('origin', 'origin-scenario');

  -- find preferred origin
  SELECT id into preferredId FROM productSummary
  WHERE eventid = in_eventid
  AND id IN (
    SELECT productSummaryId
    FROM productSummaryEventStatus
    WHERE eventId = in_eventid
  )
  ORDER BY preferred DESC, updateTime DESC
  LIMIT 1;

  -- set preferred origin
  UPDATE productSummaryEventStatus
  SET eventPreferred = 1
  WHERE eventId = in_eventid
  AND productSummaryId = preferredId;
END;
//
delimiter ;


delimiter //
DROP PROCEDURE IF EXISTS summarizeProductSummaryEventStatus //
CREATE PROCEDURE summarizeProductSummaryEventStatus(
  IN in_starttime BIGINT,
  IN in_endtime BIGINT
)
  MODIFIES SQL DATA
BEGIN
  DECLARE eventid INT;
  DECLARE done INT DEFAULT 0;

  DECLARE cur_events CURSOR FOR
    SELECT DISTINCT e.id
    FROM event e
    JOIN productSummary ps ON (ps.eventid = e.id)
    WHERE ps.type in ('origin', 'origin-scenario')
    AND e.eventTime >= in_starttime and e.eventTime < in_endtime;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  -- loop over all events, updating productSummaryEventStatus table
  START TRANSACTION;
  OPEN cur_events;
  cur_events_loop: LOOP
    FETCH cur_events INTO eventid;
    IF done = 1 THEN
      CLOSE cur_events;
      LEAVE cur_events_loop;
    END IF;

    CALL updateProductSummaryEventStatus(eventid);
  END LOOP cur_events_loop;
  COMMIT;

END;
//
delimiter ;
