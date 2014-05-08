delimiter //
DROP FUNCTION IF EXISTS get_region_name//
CREATE FUNCTION get_region_name(
    lat DECIMAL(9,6), lon DECIMAL(9,6), type CHAR(1)) RETURNS VARCHAR(255)
COMMENT 'Get a name for a location. Type is one of M, S, L, E, H'
  -- M - "basic" 40-character name
  -- S - short 32-character name
  -- L - long 64-character name (mixed case)
  -- E - spanish name
  -- H - HDS (continent;country;region)
DETERMINISTIC
BEGIN
  DECLARE l_s VARCHAR(255);
  DECLARE l_m VARCHAR(255);
  DECLARE l_l VARCHAR(255);
  DECLARE l_e VARCHAR(255);
  DECLARE l_h VARCHAR(255);
  DECLARE l_area INT;
  DECLARE l_shape POLYGON;
  DECLARE l_feregion INT;
  DECLARE l_priority INT;
  DECLARE l_dataset VARCHAR(255);

  CALL get_feregion(get_point(lat, lon), l_s, l_m, l_l, l_e, l_h, l_area,
      l_shape, l_feregion, l_priority, l_dataset);

  IF type = 'S' THEN
    RETURN l_s;
  ELSEIF type = 'M' THEN
    RETURN l_m;
  ELSEIF type = 'L' THEN
    RETURN l_l;
  ELSEIF type = 'E' THEN
    RETURN l_e;
  ELSEIF type = 'H' THEN
    RETURN l_h;
  END IF;

  RETURN NULL;
END;
//
delimiter ;
