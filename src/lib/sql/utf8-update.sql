## Update existing tables used by earthquake-event-ws.

## Change character encoding to UTF-8. If run in a mysql replication
## environment, run the script on the "master" host and changes will be
## replicated to the "slave" instance via the statement based
## replication strategy.

ALTER TABLE event CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE eventSummary CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE extentSummary CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE feplus CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE focalMechanismSummary CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE momentTensorSummary CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE originSummary CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE productSummary CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE productSummaryEventStatus CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE productSummaryLink CHARACTER SET utf8 COLLATE utf8_general_ci;
ALTER TABLE productSummaryProperty CHARACTER SET utf8 COLLATE utf8_general_ci;
