CREATE OR REPLACE VIEW currentProducts AS
SELECT
    ps.*
FROM
    productSummary ps
WHERE
    eventId IS NOT NULL
    -- newer version of same product
    AND NOT EXISTS (
  SELECT * FROM productSummary
  WHERE source=ps.source
    AND type=ps.type
    AND code=ps.code
    AND updateTime>ps.updateTime
    );
