CREATE TABLE IF NOT EXISTS event  (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  created BIGINT NOT NULL,
  updated BIGINT DEFAULT NULL,
  source VARCHAR(255) DEFAULT NULL,
  sourceCode VARCHAR(255) DEFAULT NULL,
  eventTime BIGINT DEFAULT NULL,
  latitude DOUBLE DEFAULT NULL,
  longitude DOUBLE DEFAULT NULL,
  depth DOUBLE DEFAULT NULL,
  magnitude DOUBLE DEFAULT NULL,
  status VARCHAR(255) DEFAULT NULL,

  UNIQUE KEY eventIdIdx (source, sourceCode),

  KEY eventLatLonIdx (latitude, longitude),
  KEY eventTimeLatLonIdx (eventTime, latitude, longitude),

  KEY eventLongitudeIdx (longitude),
  KEY eventDepthIdx (depth),
  KEY eventMagnitudeIdx (magnitude),
  KEY eventStatusIdx (status)
) ENGINE=INNODB;