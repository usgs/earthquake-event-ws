delimiter //
DROP PROCEDURE IF EXISTS getTsunamiLinkProduct//
CREATE PROCEDURE getTsunamiLinkProduct(
  IN in_eventid INT,
  OUT out_summaryid INT
)
  READS SQL DATA
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE cur_tsunamilink CURSOR FOR
    SELECT s.id
    FROM productSummary s
    WHERE s.eventid = in_eventid AND
      s.`type`='impact-link' AND
      EXISTS (
	SELECT * from productSummaryProperty
	WHERE productSummaryIndexId=s.id AND
	  name='addon-code' AND
	  UPPER(value) LIKE 'TSUNAMILINK%'
      );
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  SET out_summaryid = NULL;

  OPEN cur_tsunamilink;
  FETCH cur_tsunamilink INTO out_summaryid;
  CLOSE cur_tsunamilink;

  IF done = 1 THEN
    SET out_summaryid = NULL;
  END IF;
END;
//
delimiter ;