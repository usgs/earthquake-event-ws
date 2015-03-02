delimiter //
DROP PROCEDURE IF EXISTS get_feregion//
CREATE PROCEDURE get_feregion(
  IN in_point POINT,
  OUT out_s VARCHAR(255),
  OUT out_m VARCHAR(255),
  OUT out_l VARCHAR(255),
  OUT out_e VARCHAR(255),
  OUT out_h VARCHAR(255),
  OUT out_area INT,
  OUT out_shape POLYGON,
  OUT out_feregion INT,
  OUT out_priority INT,
  OUT out_dataset VARCHAR(255)
)
  COMMENT 'Find the best FERegion polygon for in_point'
  READS SQL DATA
BEGIN
  DECLARE done INT DEFAULT 0;
  DECLARE cur_points CURSOR FOR
    SELECT s, m, l, e, h, area, shape, feregion, priority, dataset
    FROM feplus
    WHERE MBRContains(shape, in_point) = 1
    ORDER BY
      priority ASC,
      area ASC;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur_points;
  cur_points_loop: LOOP
    FETCH cur_points INTO out_s, out_m, out_l, out_e, out_h, out_area,
  out_shape, out_feregion, out_priority, out_dataset;
    IF done = 1 THEN
      CLOSE cur_points;
      LEAVE cur_points_loop;

      -- not found, set outputs to null
      SET out_s = NULL;
      SET out_m = NULL;
      SET out_l = NULL;
      SET out_e = NULL;
      SET out_h = NULL;
      SET out_area = NULL;
      SET out_shape = NULL;
      SET out_feregion = NULL;
      SET out_priority = NULL;
      SET out_dataset = NULL;
    END IF;

    IF point_in_polygon(in_point, out_shape) = 1 THEN
      -- exit loop at first "precise" match
      CLOSE cur_points;
      LEAVE cur_points_loop;
    END IF;
  END LOOP cur_points_loop;
END;
//
delimiter ;
