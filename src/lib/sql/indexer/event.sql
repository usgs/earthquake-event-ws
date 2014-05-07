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
  status VARCHAR(255) DEFAULT NULL
) ENGINE=INNODB;


CREATE UNIQUE INDEX eventIdIdx
  ON event (source, sourceCode);

CREATE INDEX eventLatLonIdx
  ON event (latitude, longitude);

CREATE INDEX eventTimeLatLonIdx
  ON event (eventTime, latitude, longitude);


-- The following indexes are frequently used by user queries,
-- but may be commented out if desired (affects processing speed)

CREATE INDEX eventLongitudeIdx
  ON event (longitude);

CREATE INDEX eventDepthIdx
  ON event (depth);

CREATE INDEX eventMagnitudeIdx
  ON event (magnitude);

CREATE INDEX eventStatusIdx
  ON event (status);