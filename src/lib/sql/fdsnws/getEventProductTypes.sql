-- build a distinct list of product types associated with an event
delimiter //
DROP PROCEDURE IF EXISTS getEventProductTypes//
CREATE PROCEDURE getEventProductTypes(
  IN in_eventid INT,
  OUT out_producttypes TEXT
)
  READS SQL DATA
BEGIN
  DECLARE l_type VARCHAR(255);

  DECLARE done INT DEFAULT 0;
  DECLARE cur_products CURSOR FOR
    SELECT type
    FROM preferredProduct
    WHERE eventid = in_eventid
    AND status <> 'DELETE';
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  SET out_producttypes = NULL;
  OPEN cur_products;
  cur_products_loop: LOOP
    FETCH cur_products INTO l_type;
    IF done = 1 THEN
      CLOSE cur_products;
      LEAVE cur_products_loop;
    END IF;

    SET out_producttypes = CONCAT(COALESCE(out_producttypes, ''), ',', l_type);
  END LOOP cur_products_loop;

  IF out_producttypes <> '' THEN
    SET out_producttypes = CONCAT(out_producttypes, ',');
  END IF;
END;
//
delimiter ;
