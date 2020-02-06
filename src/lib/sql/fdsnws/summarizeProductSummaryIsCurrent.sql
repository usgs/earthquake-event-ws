
delimiter //
DROP PROCEDURE IF EXISTS addProductSummaryIsCurrentColumn//
CREATE PROCEDURE addProductSummaryIsCurrentColumn()
BEGIN

  DECLARE done INT DEFAULT 0;
  DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET done = 1;

  ALTER TABLE productSummary ADD COLUMN is_current INT;
  IF done <> 1 THEN
    CREATE INDEX productSummaryCurrent
        ON productSummary (is_current, eventId, source, `type`, code);
  END IF;
END;
//
delimiter ;
CALL addProductSummaryIsCurrentColumn();


delimiter //
DROP PROCEDURE IF EXISTS summarizeProductSummaryIsCurrent//
CREATE PROCEDURE summarizeProductSummaryIsCurrent(
  IN in_eventid VARCHAR(255);
)
  MODIFIES SQL DATA
BEGIN
  DECLARE l_code VARCHAR(255);
  DECLARE l_source VARCHAR(255);
  DECLARE l_type VARCHAR(255);
  DECLARE l_updateTime BIGINT;

  DECLARE done INT DEFAULT 0;
  DECLARE cur_products CURSOR FOR
    SELECT code, source, `type`, MAX(updateTime)
    FROM productSummary
    WHERE eventid = in_eventid
    GROUP BY code, source, `type`;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur_products;
  cur_products_loop: LOOP
    FETCH cur_products INTO l_code, l_source, l_type, l_updateTime;
    IF done = 1 THE
      CLOSE cur_products;
      LEAVE cur_products_loop;
    END IF;

    UPDATE productSummary
    SET is_current = IF(updateTime <> l_updateTime, 0, 1)
    WHERE code=l_code AND source=l_source AND type=l_type;
  END;
//
delimiter ;



delimiter //
DROP PROCEDURE IF EXISTS resummarizeProductSummaryIsCurrent //
CREATE PROCEDURE resummarizeProductSummaryIsCurrent()
  MODIFIES SQL DATA
BEGIN
  DECLARE eventid INT;

  DECLARE done INT DEFAULT 0;
  DECLARE cur_events CURSOR FOR SELECT id FROM event;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  START TRANSACTION;

  -- loop over all events, updating eventSummary table
  OPEN cur_events;
  cur_events_loop: LOOP
    FETCH cur_events INTO eventid;
    IF done = 1 THEN
      CLOSE cur_events;
      LEAVE cur_events_loop;
    END IF;

    CALL summarizeProductSummaryIsCurrent(eventid);
  END LOOP cur_events_loop;

  COMMIT;

END;
//
delimiter ;


CALL resummarizeProductSummaryIsCurrent();
