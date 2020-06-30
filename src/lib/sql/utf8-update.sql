-- Update existing tables used by earthquake-event-ws.

-- Change character encoding to UTF-8. If run in a mysql replication
-- environment, run the script on the "master" host and changes will be
-- replicated to the "slave" instance via the statement based
-- replication strategy.

SET NAMES utf8;

-- Database character set
ALTER DATABASE product_index
DEFAULT CHARACTER SET = utf8;

-- Column character set
ALTER TABLE event CONVERT TO CHARACTER SET utf8;
ALTER TABLE eventSummary CONVERT TO CHARACTER SET utf8;
ALTER TABLE extentSummary CONVERT TO CHARACTER SET utf8;
ALTER TABLE feplus CONVERT TO CHARACTER SET utf8;
ALTER TABLE focalMechanismSummary CONVERT TO CHARACTER SET utf8;
ALTER TABLE momentTensorSummary CONVERT TO CHARACTER SET utf8;
ALTER TABLE originSummary CONVERT TO CHARACTER SET utf8;
ALTER TABLE productSummary CONVERT TO CHARACTER SET utf8;
ALTER TABLE productSummaryEventStatus CONVERT TO CHARACTER SET utf8;
ALTER TABLE productSummaryLink CONVERT TO CHARACTER SET utf8;
ALTER TABLE productSummaryProperty CONVERT TO CHARACTER SET utf8;

-- Stored procedure region/title/place character set
SOURCE fdsnws/getEventSummary.sql;
SOURCE fdsnws/updateEventSummary.sql;
SOURCE fdsnws/getProductProperty.sql;
