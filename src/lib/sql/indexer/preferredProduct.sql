CREATE VIEW preferredProduct AS
SELECT ps.eventId, ps.id, ps.type
FROM productSummary ps
WHERE ps.eventId IS NOT NULL
  AND NOT EXISTS (
    SELECT *
    FROM productSummary
    WHERE source = ps.source
      AND type = ps.type
      AND code = ps.code
      AND updateTime > ps.updateTime
  )
  AND NOT EXISTS (
    SELECT *
    FROM productSummary
    WHERE eventId = ps.eventId
    AND type = ps.type
    AND (
      preferred > ps.preferred
      OR (
	preferred = ps.preferred
	AND updateTime > ps.updateTime
      )
    )
  );