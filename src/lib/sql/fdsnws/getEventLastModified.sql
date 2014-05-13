delimiter //
DROP PROCEDURE IF EXISTS getEventLastModified//
CREATE PROCEDURE getEventLastModified(
  IN in_eventid INT,
  OUT out_lastmodified BIGINT
)
  READS SQL DATA
BEGIN
  DECLARE l_lastmodified BIGINT;

  DECLARE done INT DEFAULT 0;
  DECLARE cur_lastmodified CURSOR FOR
    SELECT MAX(updateTime) as updateTime
    FROM productSummary
    WHERE eventid = in_eventid;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  SET out_lastmodified = NULL;

  OPEN cur_lastmodified;
  FETCH cur_lastmodified INTO out_lastmodified;
  CLOSE cur_lastmodified;

  IF done = 1 THEN
    SET out_lastmodified = NULL;
  END IF;
END;
//
delimiter ;