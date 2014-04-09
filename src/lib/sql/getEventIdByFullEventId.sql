
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


-- getEventIdByFulleventid(fullEventId)
-- Example Usage:
--     SELECT * FROM event WHERE id=getEventIdByFulleventid('usb000bupa');
-- 
-- NOTE: This method checks all known event sources in an attempt to split a full
-- event id into its components event source and event source code, before using
-- getEventIdBySourceAndCode to find the actual id.
-- 
-- This method returns the first match.  Although unlikely, it is possible
-- for an event source and code collision given one event source that is the 
-- prefix of a second event source:
--    AT LAS1234
--    ATLAS 1234
delimiter //
DROP FUNCTION IF EXISTS getEventIdByFullEventId//
CREATE FUNCTION getEventIdByFullEventId(
    _fullEventId VARCHAR(255)
) 
RETURNS INT 
READS SQL DATA 
COMMENT 'Get event primary key based on a fullEventId'
BEGIN
    DECLARE l_event_id INT;
    DECLARE l_eventSource VARCHAR(255);
    DECLARE l_source VARCHAR(255);
    DECLARE l_code VARCHAR(255);
    DECLARE l_done INT DEFAULT 0;

    DECLARE cur_sources CURSOR FOR
        SELECT DISTINCT eventSource
        FROM productSummary
        -- ignore empty sources; only looking for non-empty
        WHERE eventSource IS NOT NULL
        AND eventSource <> '';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET l_done = 1;

    -- loop over all known event sources
    OPEN cur_sources;
    cur_sources_loop: LOOP
        FETCH cur_sources INTO l_eventSource;
        IF l_done = 1 THEN
            -- no more sources
            CLOSE cur_sources;
            LEAVE cur_sources_loop;
        END IF;

        -- check if _fullEventId starts with l_eventSource
        SET l_source = SUBSTRING(_fullEventId, 1, LENGTH(l_eventSource));
        IF l_source = l_eventSource THEN
            -- _fullEventId starts with this source, now check if there is an event with this code
            SET l_code = SUBSTRING(_fullEventId, LENGTH(l_eventSource) + 1);
            SET l_event_id = getEventIdBySourceAndCode(l_source, l_code);
            IF l_event_id IS NOT NULL THEN
                -- found event id
                CLOSE cur_sources;
                LEAVE cur_sources_loop;
            END IF;
        END IF;
    END LOOP cur_sources_loop;

    RETURN l_event_id;
END;
//
delimiter ;

