CREATE TABLE IF NOT EXISTS productSummary(
  id BIGINT PRIMARY KEY AUTO_INCREMENT ,
  created BIGINT NOT NULL,
  productId VARCHAR(255) NOT NULL,
  eventId BIGINT DEFAULT NULL,
  `type` VARCHAR(255) NOT NULL,
  source VARCHAR(255) NOT NULL,
  code VARCHAR(255) NOT NULL,
  updateTime BIGINT NOT NULL,
  eventSource VARCHAR(255) DEFAULT NULL,
  eventSourceCode VARCHAR(255) DEFAULT NULL,
  eventTime BIGINT DEFAULT NULL,
  eventLatitude DOUBLE DEFAULT NULL,
  eventLongitude DOUBLE DEFAULT NULL,
  eventDepth DOUBLE DEFAULT NULL,
  eventMagnitude DOUBLE DEFAULT NULL,
  version VARCHAR(255) DEFAULT NULL,
  status VARCHAR(255) NOT NULL,
  trackerURL VARCHAR(255) NOT NULL,
  preferred BIGINT NOT NULL
) ENGINE = INNODB;


CREATE UNIQUE INDEX summaryIdIndex
  ON productSummary (source, `type`, code, updateTime);

CREATE INDEX summaryEventIdIndex
  ON productSummary (eventSource, eventSourceCode);

CREATE INDEX summaryTimeLatLonIdx
  ON productSummary (eventTime, eventLatitude, eventLongitude);

CREATE INDEX preferredEventProductIndex
  ON productSummary (eventId, type, preferred, updateTime);


-- The following indexes are frequently used by user queries,
-- but may be commented out if desired (affects processing speed)

CREATE INDEX summaryUpdateTimeIdx
  ON productSummary (updateTime);

CREATE INDEX summaryCodeIdx
  ON productSummary (code);

CREATE INDEX summaryVersionIdx
  ON productSummary (version);

CREATE INDEX summaryStatusIdx
  ON productSummary (status);