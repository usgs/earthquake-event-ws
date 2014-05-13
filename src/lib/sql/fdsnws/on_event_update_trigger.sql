delimiter //
CREATE TRIGGER on_event_update_trigger AFTER UPDATE ON event FOR EACH ROW
BEGIN
  CALL updateEventSummary(NEW.id);
  CALL summarizeEventProducts(NEW.id);
END;
//
delimiter ;