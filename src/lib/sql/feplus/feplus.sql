DROP TABLE IF EXISTS feplus;
CREATE TABLE feplus (
  s VARCHAR(255),
  m VARCHAR(255),
  l VARCHAR(255),
  e VARCHAR(255),
  h VARCHAR(255),
  area INT,
  shape GEOMETRY,
  feregion INT,
  priority INT,
  dataset VARCHAR(255)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci;