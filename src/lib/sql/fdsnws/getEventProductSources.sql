-- build a distinct list of product sources associated with an event
delimiter //
-- remove the old name for this procedure too
DROP PROCEDURE IF EXISTS getEventProducts//
DROP PROCEDURE IF EXISTS getEventProductSources//
CREATE PROCEDURE getEventProductSources(
  IN in_eventid INT,
  OUT out_productsources TEXT
)
  READS SQL DATA
BEGIN
  DECLARE l_source VARCHAR(255);

  DECLARE done INT DEFAULT 0;
  DECLARE cur_products CURSOR FOR
    SELECT DISTINCT source
    FROM currentProducts
    WHERE eventid = in_eventid
    AND status <> 'DELETE';
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  SET out_productsources = NULL;
  OPEN cur_products;
  cur_products_loop: LOOP
    FETCH cur_products INTO l_source;
    IF done = 1 THEN
      CLOSE cur_products;
      LEAVE cur_products_loop;
    END IF;

    SET out_productsources = CONCAT(COALESCE(out_productsources, ''), ',',
	l_source);
  END LOOP cur_products_loop;

  IF out_productsources <> '' THEN
    SET out_productsources = CONCAT(out_productsources, ',');
  END IF;
END;
//
delimiter ;