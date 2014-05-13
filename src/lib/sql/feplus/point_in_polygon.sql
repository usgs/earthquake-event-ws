delimiter //
DROP FUNCTION IF EXISTS point_in_polygon//
CREATE FUNCTION point_in_polygon(p POINT, poly GEOMETRY) RETURNS INT(1)
COMMENT 'Should be combined with MBRContains as a prefilter.'
-- poly can be POLYGON or MULTIPOLYGON
DETERMINISTIC
BEGIN
  DECLARE n INT DEFAULT 0;
  DECLARE i INT DEFAULT 1; -- 1 based
  DECLARE result INT(1) DEFAULT 0;
  DECLARE type VARCHAR(255);

  SET type = GeometryType(poly);

  IF type = 'POLYGON' THEN
    SET result = point_in_one_polygon(p, poly);
  ELSEIF type = 'MULTIPOLYGON' THEN
    -- test each sub polygon
    SET n = NumGeometries(poly);
    WHILE i <= n DO
      SET result = point_in_one_polygon(p, GeometryN(poly, i));
      IF result = 1 THEN
	RETURN result;
      END IF;

      SET i = i + 1;
    END WHILE;
  END IF;

  RETURN result;
END;
//
delimiter ;