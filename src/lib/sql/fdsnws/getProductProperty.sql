delimiter //
DROP PROCEDURE IF EXISTS getProductProperty//
CREATE PROCEDURE getProductProperty(IN in_productid INT,
  IN in_name VARCHAR(255), OUT out_value TEXT CHARACTER SET 'utf8')
  READS SQL DATA
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE cur_property CURSOR FOR
    SELECT value
    FROM productSummaryProperty
    WHERE productSummaryIndexId=in_productid
    AND name=in_name;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done=1;

  OPEN cur_property;
  FETCH cur_property INTO out_value;

  IF done = 1 THEN
    SET out_value = NULL;
  END IF;

  CLOSE cur_property;
END;
//
delimiter ;
