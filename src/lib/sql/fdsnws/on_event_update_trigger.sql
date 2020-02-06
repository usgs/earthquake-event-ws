delimiter //
CREATE TRIGGER on_event_update_trigger AFTER UPDATE ON event FOR EACH ROW
BEGIN
  -- update is_current before following procedures
  CALL summarizeProductSummaryIsCurrent(NEW.id);
  CALL updateEventSummary(NEW.id);
  CALL summarizeEventProducts(NEW.id);
END;
//
delimiter ;