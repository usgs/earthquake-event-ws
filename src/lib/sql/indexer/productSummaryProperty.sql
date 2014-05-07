-- Note: This script can only be run after the productSummary table is created.

CREATE TABLE IF NOT EXISTS productSummaryProperty (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  productSummaryIndexId BIGINT,
  name VARCHAR(255),
  value TEXT,
  FOREIGN KEY (productSummaryIndexId) REFERENCES productSummary(id)
    ON DELETE CASCADE
) ENGINE=INNODB;


CREATE UNIQUE INDEX propertyIdNameIndex
  ON productSummaryProperty (productSummaryIndexId, name);