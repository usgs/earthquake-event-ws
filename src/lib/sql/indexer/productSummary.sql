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
  preferred BIGINT NOT NULL,

  UNIQUE KEY summaryIdIndex (source, `type`, code, updateTime),

  KEY summaryEventIdIndex (eventSource, eventSourceCode),
  KEY summaryTimeLatLonIdx (eventTime, eventLatitude, eventLongitude),
  KEY preferredEventProductIndex (eventId, type, preferred, updateTime),
  KEY productIdIndex (productId)
) ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;

-- Note :: Additional may indexes added by FDSN later
