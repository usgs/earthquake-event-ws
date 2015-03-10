CREATE OR REPLACE VIEW preferredProduct AS
SELECT ps.eventId, ps.id, ps.type
FROM currentProducts ps
WHERE ps.eventId IS NOT NULL
  AND ps.status <> 'DELETE'
  AND NOT EXISTS (
    SELECT *
    FROM currentProducts
    WHERE eventId = ps.eventId
    AND type = ps.type
    AND status <> 'DELETE'
    AND (
      preferred > ps.preferred
      OR (
  preferred = ps.preferred
  AND updateTime > ps.updateTime
      )
    )
  );