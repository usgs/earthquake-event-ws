CREATE TABLE IF NOT EXISTS extentSummary(
    id BIGINT PRIMARY KEY,
    starttime BIGINT DEFAULT NULL,
    endtime BIGINT DEFAULT NULL,
    minimum_latitude DOUBLE DEFAULT NULL,
    maximum_latitude DOUBLE DEFAULT NULL,
    minimum_longitude DOUBLE DEFAULT NULL,
    maximum_longitude DOUBLE DEFAULT NULL
) ENGINE = INNODB;