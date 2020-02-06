CREATE OR REPLACE VIEW currentProducts AS
SELECT
    ps.*
FROM
    productSummary ps
WHERE
    ps.eventId IS NOT NULL
    -- newer version of same product
    AND ps.is_current = 1;
