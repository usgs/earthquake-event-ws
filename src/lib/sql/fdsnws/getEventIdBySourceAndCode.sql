--
-- Look up a product index event id (primary key) using indexes.
-- @author jmfee
--
-- Event ids consist of
--    event source - an event id creator, effectively a namespace for uniqueness
--    event source code - an event code assigned by an event source
--
-- These attributes are stored in separate columns, but are frequently merged
-- together when referring to an event.  This file provides two stored functions
-- that can be used to efficiently look up the primary key in the event table.
--



-- getEventIdBySourceAndCode(eventSource, eventSourceCode)
-- Example Usage:
--     SELECT * FROM event WHERE id=getEventIdBySourceAndCode('us', 'b000bupa');
--
-- NOTE: This method is the most efficient way to look up an event id.
delimiter //
DROP FUNCTION IF EXISTS getEventIdBySourceAndCode//
CREATE FUNCTION getEventIdBySourceAndCode(
    _eventSource VARCHAR(255),
    _eventSourceCode VARCHAR(255)
)
RETURNS INT
READS SQL DATA
COMMENT 'Get event primary key based on eventSource and eventSourceCode'
BEGIN
    DECLARE l_event_id INT;
    DECLARE l_done INT DEFAULT 0;
    DECLARE cur_event_id CURSOR FOR
	SELECT eventId
	FROM productSummary
	WHERE eventSource=_eventSource
	AND eventSourceCode=_eventSourceCode
	LIMIT 1;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_done = 1;

    -- open cursor
    OPEN cur_event_id;
    FETCH cur_event_id INTO l_event_id;
    IF l_done = 1 THEN
	-- no matching results
	SET l_event_id = NULL;
    END IF;
    -- free cursor
    CLOSE cur_event_id;

    RETURN l_event_id;
END;
//
delimiter ;