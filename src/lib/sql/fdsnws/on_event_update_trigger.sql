-- potentially repetitive, but does not hurt
DROP TRIGGER IF EXISTS on_event_update_trigger;

delimiter //
CREATE TRIGGER on_event_update_trigger AFTER UPDATE ON event FOR EACH ROW
BEGIN
  CALL updateEventSummary(NEW.id);
  CALL summarizeEventProducts(NEW.id);
  CALL updateProductSummaryEventStatus(NEW.id);
END;
//
delimiter ;