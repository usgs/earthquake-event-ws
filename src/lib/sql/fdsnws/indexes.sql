-- Drops an index if it exists. Assumes table/index are in current schema.
DELIMITER //
DROP PROCEDURE IF EXISTS `drop_index_if_exists`//
CREATE PROCEDURE `drop_index_if_exists` (IN in_table VARCHAR(50),
    IN in_index VARCHAR(50))
SQL SECURITY INVOKER
BEGIN

IF EXISTS (
  SELECT table_schema, table_name, index_name
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE table_schema = SCHEMA()
    AND table_name = in_table
    AND index_name = in_index
) THEN
  -- Must use a session variable (@variable) to create prepared statement
  SET @drop_sql = CONCAT('ALTER TABLE `', SCHEMA(), '`.`', in_table,
      '` DROP INDEX `', in_index, '`');

  PREPARE drop_stmt FROM @drop_sql;
  EXECUTE drop_stmt;
  DEALLOCATE PREPARE drop_stmt;
END IF;

END//
DELIMITER ;

-- Creates a simple index if it does not already exist
DELIMITER //
DROP PROCEDURE IF EXISTS `create_simple_index_if_not_exists`//
CREATE PROCEDURE `create_simple_index_if_not_exists` (IN in_table VARCHAR(50),
    IN in_index VARCHAR(50), IN in_columns VARCHAR(50))
SQL SECURITY INVOKER
BEGIN

IF NOT EXISTS (
  SELECT table_schema, table_name, index_name
  FROM INFORMATION_SCHEMA.STATISTICS
  WHERE table_schema = SCHEMA()
    AND table_name = in_table
    AND index_name = in_index
) THEN
  SET @create_sql = CONCAT('ALTER TABLE `', SCHEMA(), '`.`', in_table,
      'ADD INDEX `', in_index, '` (', in_columns, ')');

  PREPARE create_stmt FROM @create_sql;
  EXECUTE create_stmt;
  DEALLOCATE PREPARE create_stmt;
END IF;

END//
DELIMITER ;

-- Note: Use drop/create rather than conditionally create because indexes
--       can get complex and it is more flexible to not wrap the entire create
--       syntax within a stored procedure. This approach is less efficient
--       but more universal.


-- Indexes for event table used by FDSN
CALL drop_index_if_exists('event', 'eventLongitudeIdx');
CREATE INDEX eventLongitudeIdx ON event (longitude);

CALL drop_index_if_exists('event', 'eventDepthIdx');
CREATE INDEX eventDepthIdx ON event (depth);

CALL drop_index_if_exists('event', 'eventMagnitudeIdx');
CREATE INDEX eventMagnitudeIdx ON event (magnitude);

CALL drop_index_if_exists('event', 'eventStatusIdx');
CREATE INDEX eventStatusIdx ON event (status);


-- Indexes for productSummary table used by FDSN
CALL drop_index_if_exists('productSummary', 'summaryUpdateTimeIdx');
CREATE INDEX summaryUpdateTimeIdx ON productSummary (updateTime);

CALL drop_index_if_exists('productSummary', 'summaryCodeIdx');
CREATE INDEX summaryCodeIdx ON productSummary (code);

CALL drop_index_if_exists('productSummary', 'summaryVersionIdx');
CREATE INDEX summaryVersionIdx ON productSummary (version);

CALL drop_index_if_exists('productSummary', 'summaryStatusIdx');
CREATE INDEX summaryStatusIdx ON productSummary (status);

CALL drop_index_if_exists('productSummary', 'summaryTypeStatusIdx');
CREATE INDEX summaryTypeStatusIdx ON productSummary (type, status);


-- Don't need these anymore
DROP PROCEDURE IF EXISTS `drop_index_if_exists`;
DROP PROCEDURE IF EXISTS `create_simple_index_if_not_exists`;
