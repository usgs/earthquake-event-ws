-- Note: This script can only be run after the productSummary table is created.

CREATE TABLE IF NOT EXISTS productSummaryLink (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  productSummaryIndexId BIGINT,
  relation VARCHAR(255),
  url TEXT,
  FOREIGN KEY (productSummaryIndexId) REFERENCES productSummary(id)
    ON DELETE CASCADE
) ENGINE=INNODB CHARACTER SET utf8 COLLATE utf8_general_ci;
