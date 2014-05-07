delimiter //
DROP FUNCTION IF EXISTS get_point//
CREATE FUNCTION get_point(lat DECIMAL(9,6), lon DECIMAL(9,6)) RETURNS POINT
COMMENT 'Convenience method to convert lat and lon to POINT.'
DETERMINISTIC
BEGIN
  DECLARE result POINT;
  SET result = GeomFromText(CONCAT('POINT(', lon, ' ', lat, ')'));
  RETURN result;
END;
//
delimiter ;