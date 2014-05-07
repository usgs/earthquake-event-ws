DROP TRIGGER IF EXISTS on_event_update_trigger;
-- table
DROP TABLE IF EXISTS focalMechanismSummary;
CREATE TABLE focalMechanismSummary (productid BIGINT(20) REFERENCES productSummary(id) ON DELETE CASCADE,review_status VARCHAR(255),beachball_source VARCHAR(255),nodal_plane_1_strike DOUBLE,nodal_plane_1_dip DOUBLE,nodal_plane_1_rake DOUBLE,nodal_plane_2_strike DOUBLE,nodal_plane_2_dip DOUBLE,nodal_plane_2_rake DOUBLE,num_stations_used INT,UNIQUE(productid),INDEX(review_status),INDEX(beachball_source),INDEX(nodal_plane_1_strike),INDEX(nodal_plane_1_dip),INDEX(nodal_plane_1_rake),INDEX(nodal_plane_2_strike),INDEX(nodal_plane_2_dip),INDEX(nodal_plane_2_rake),INDEX(num_stations_used));
-- procedure
delimiter //
DROP PROCEDURE IF EXISTS addFocalMechanismSummary//
CREATE PROCEDURE addFocalMechanismSummary(IN in_productid INT) MODIFIES SQL DATA
BEGIN
  DECLARE review_status VARCHAR(255);
  DECLARE beachball_source VARCHAR(255);
  DECLARE nodal_plane_1_strike DOUBLE;
  DECLARE nodal_plane_1_dip DOUBLE;
  DECLARE nodal_plane_1_rake DOUBLE;
  DECLARE nodal_plane_2_strike DOUBLE;
  DECLARE nodal_plane_2_dip DOUBLE;
  DECLARE nodal_plane_2_rake DOUBLE;
  DECLARE num_stations_used INT;

  CALL getProductProperty(in_productid, 'review-status', review_status);
  CALL getProductProperty(in_productid, 'beachball-source', beachball_source);
  CALL getProductProperty(in_productid, 'nodal-plane-1-strike', nodal_plane_1_strike);
  CALL getProductProperty(in_productid, 'nodal-plane-1-dip', nodal_plane_1_dip);
  CALL getProductProperty(in_productid, 'nodal-plane-1-rake', nodal_plane_1_rake);
  CALL getProductProperty(in_productid, 'nodal-plane-2-strike', nodal_plane_2_strike);
  CALL getProductProperty(in_productid, 'nodal-plane-2-dip', nodal_plane_2_dip);
  CALL getProductProperty(in_productid, 'nodal-plane-2-rake', nodal_plane_2_rake);
  CALL getProductProperty(in_productid, 'num-stations-used', num_stations_used);

 INSERT INTO focalMechanismSummary VALUES (in_productid,review_status,beachball_source,nodal_plane_1_strike,nodal_plane_1_dip,nodal_plane_1_rake,nodal_plane_2_strike,nodal_plane_2_dip,nodal_plane_2_rake,num_stations_used) ON DUPLICATE KEY UPDATE productid=productid;
END;
//
delimiter ;

-- table
DROP TABLE IF EXISTS momentTensorSummary;
CREATE TABLE momentTensorSummary (productid BIGINT(20) REFERENCES productSummary(id) ON DELETE CASCADE,review_status VARCHAR(255),beachball_source VARCHAR(255),nodal_plane_1_strike DOUBLE,nodal_plane_1_dip DOUBLE,nodal_plane_1_rake DOUBLE,nodal_plane_2_strike DOUBLE,nodal_plane_2_dip DOUBLE,nodal_plane_2_rake DOUBLE,num_stations_used INT,percent_double_couple DOUBLE,scalar_moment DOUBLE,beachball_type VARCHAR(255),tensor_mrr DOUBLE,tensor_mtt DOUBLE,tensor_mpp DOUBLE,tensor_mtp DOUBLE,tensor_mrt DOUBLE,tensor_mrp DOUBLE,derived_latitude DOUBLE,derived_longitude DOUBLE,derived_depth DOUBLE,derived_eventtime BIGINT,derived_magnitude DOUBLE,derived_magnitude_type VARCHAR(255),UNIQUE(productid),INDEX(review_status),INDEX(beachball_source),INDEX(nodal_plane_1_strike),INDEX(nodal_plane_1_dip),INDEX(nodal_plane_1_rake),INDEX(nodal_plane_2_strike),INDEX(nodal_plane_2_dip),INDEX(nodal_plane_2_rake),INDEX(num_stations_used),INDEX(percent_double_couple),INDEX(scalar_moment),INDEX(beachball_type),INDEX(tensor_mrr),INDEX(tensor_mtt),INDEX(tensor_mpp),INDEX(tensor_mtp),INDEX(tensor_mrt),INDEX(tensor_mrp),INDEX(derived_latitude),INDEX(derived_longitude),INDEX(derived_depth),INDEX(derived_eventtime),INDEX(derived_magnitude),INDEX(derived_magnitude_type));
-- procedure
delimiter //
DROP PROCEDURE IF EXISTS addMomentTensorSummary//
CREATE PROCEDURE addMomentTensorSummary(IN in_productid INT) MODIFIES SQL DATA
BEGIN
  DECLARE review_status VARCHAR(255);
  DECLARE beachball_source VARCHAR(255);
  DECLARE nodal_plane_1_strike DOUBLE;
  DECLARE nodal_plane_1_dip DOUBLE;
  DECLARE nodal_plane_1_rake DOUBLE;
  DECLARE nodal_plane_2_strike DOUBLE;
  DECLARE nodal_plane_2_dip DOUBLE;
  DECLARE nodal_plane_2_rake DOUBLE;
  DECLARE num_stations_used INT;
  DECLARE percent_double_couple DOUBLE;
  DECLARE scalar_moment DOUBLE;
  DECLARE beachball_type VARCHAR(255);
  DECLARE tensor_mrr DOUBLE;
  DECLARE tensor_mtt DOUBLE;
  DECLARE tensor_mpp DOUBLE;
  DECLARE tensor_mtp DOUBLE;
  DECLARE tensor_mrt DOUBLE;
  DECLARE tensor_mrp DOUBLE;
  DECLARE derived_latitude DOUBLE;
  DECLARE derived_longitude DOUBLE;
  DECLARE derived_depth DOUBLE;
  DECLARE derived_eventtime BIGINT;
  DECLARE derived_magnitude DOUBLE;
  DECLARE derived_magnitude_type VARCHAR(255);

  CALL getProductProperty(in_productid, 'review-status', review_status);
  CALL getProductProperty(in_productid, 'beachball-source', beachball_source);
  CALL getProductProperty(in_productid, 'nodal-plane-1-strike', nodal_plane_1_strike);
  CALL getProductProperty(in_productid, 'nodal-plane-1-dip', nodal_plane_1_dip);
  CALL getProductProperty(in_productid, 'nodal-plane-1-rake', nodal_plane_1_rake);
  CALL getProductProperty(in_productid, 'nodal-plane-2-strike', nodal_plane_2_strike);
  CALL getProductProperty(in_productid, 'nodal-plane-2-dip', nodal_plane_2_dip);
  CALL getProductProperty(in_productid, 'nodal-plane-2-rake', nodal_plane_2_rake);
  CALL getProductProperty(in_productid, 'num-stations-used', num_stations_used);
  CALL getProductProperty(in_productid, 'percent-double-couple', percent_double_couple);
  CALL getProductProperty(in_productid, 'scalar-moment', scalar_moment);
  CALL getProductProperty(in_productid, 'beachball-type', beachball_type);
  CALL getProductProperty(in_productid, 'tensor-mrr', tensor_mrr);
  CALL getProductProperty(in_productid, 'tensor-mtt', tensor_mtt);
  CALL getProductProperty(in_productid, 'tensor-mpp', tensor_mpp);
  CALL getProductProperty(in_productid, 'tensor-mtp', tensor_mtp);
  CALL getProductProperty(in_productid, 'tensor-mrt', tensor_mrt);
  CALL getProductProperty(in_productid, 'tensor-mrp', tensor_mrp);
  CALL getProductProperty(in_productid, 'derived-latitude', derived_latitude);
  CALL getProductProperty(in_productid, 'derived-longitude', derived_longitude);
  CALL getProductProperty(in_productid, 'derived-depth', derived_depth);
  CALL getProductProperty(in_productid, 'derived_eventtime', derived_eventtime);
  CALL getProductProperty(in_productid, 'derived-magnitude', derived_magnitude);
  CALL getProductProperty(in_productid, 'derived-magnitude-type', derived_magnitude_type);

 INSERT INTO momentTensorSummary VALUES (in_productid,review_status,beachball_source,nodal_plane_1_strike,nodal_plane_1_dip,nodal_plane_1_rake,nodal_plane_2_strike,nodal_plane_2_dip,nodal_plane_2_rake,num_stations_used,percent_double_couple,scalar_moment,beachball_type,tensor_mrr,tensor_mtt,tensor_mpp,tensor_mtp,tensor_mrt,tensor_mrp,derived_latitude,derived_longitude,derived_depth,derived_eventtime,derived_magnitude,derived_magnitude_type) ON DUPLICATE KEY UPDATE productid=productid;
END;
//
delimiter ;

-- table
DROP TABLE IF EXISTS originSummary;
CREATE TABLE originSummary (productid BIGINT(20) REFERENCES productSummary(id) ON DELETE CASCADE,event_type VARCHAR(255),azimuthal_gap DOUBLE,horizontal_error DOUBLE,vertical_error DOUBLE,minimum_distance DOUBLE,num_stations_used INT,num_phases_used INT,review_status VARCHAR(255),standard_error DOUBLE,origin_source VARCHAR(255),magnitude_source VARCHAR(255),magnitude_type VARCHAR(255),magnitude_error DOUBLE,magnitude_num_stations_used INT,UNIQUE(productid),INDEX(event_type),INDEX(azimuthal_gap),INDEX(horizontal_error),INDEX(vertical_error),INDEX(minimum_distance),INDEX(num_stations_used),INDEX(num_phases_used),INDEX(review_status),INDEX(standard_error),INDEX(origin_source),INDEX(magnitude_source),INDEX(magnitude_type),INDEX(magnitude_error),INDEX(magnitude_num_stations_used));
-- procedure
delimiter //
DROP PROCEDURE IF EXISTS addOriginSummary//
CREATE PROCEDURE addOriginSummary(IN in_productid INT) MODIFIES SQL DATA
BEGIN
  DECLARE event_type VARCHAR(255);
  DECLARE azimuthal_gap DOUBLE;
  DECLARE horizontal_error DOUBLE;
  DECLARE vertical_error DOUBLE;
  DECLARE minimum_distance DOUBLE;
  DECLARE num_stations_used INT;
  DECLARE num_phases_used INT;
  DECLARE review_status VARCHAR(255);
  DECLARE standard_error DOUBLE;
  DECLARE origin_source VARCHAR(255);
  DECLARE magnitude_source VARCHAR(255);
  DECLARE magnitude_type VARCHAR(255);
  DECLARE magnitude_error DOUBLE;
  DECLARE magnitude_num_stations_used INT;

  CALL getProductProperty(in_productid, 'event-type', event_type);
  CALL getProductProperty(in_productid, 'azimuthal-gap', azimuthal_gap);
  CALL getProductProperty(in_productid, 'horizontal-error', horizontal_error);
  CALL getProductProperty(in_productid, 'vertical-error', vertical_error);
  CALL getProductProperty(in_productid, 'minimum-distance', minimum_distance);
  CALL getProductProperty(in_productid, 'num-stations-used', num_stations_used);
  CALL getProductProperty(in_productid, 'num-phases-used', num_phases_used);
  CALL getProductProperty(in_productid, 'review-status', review_status);
  CALL getProductProperty(in_productid, 'standard-error', standard_error);
  CALL getProductProperty(in_productid, 'origin-source', origin_source);
  CALL getProductProperty(in_productid, 'magnitude-source', magnitude_source);
  CALL getProductProperty(in_productid, 'magnitude-type', magnitude_type);
  CALL getProductProperty(in_productid, 'magnitude-error', magnitude_error);
  CALL getProductProperty(in_productid, 'magnitude-num-stations-used', magnitude_num_stations_used);

 INSERT INTO originSummary VALUES (in_productid,event_type,azimuthal_gap,horizontal_error,vertical_error,minimum_distance,num_stations_used,num_phases_used,review_status,standard_error,origin_source,magnitude_source,magnitude_type,magnitude_error,magnitude_num_stations_used) ON DUPLICATE KEY UPDATE productid=productid;
END;
//
delimiter ;


delimiter //
DROP PROCEDURE IF EXISTS summarizeEventProducts //
CREATE PROCEDURE summarizeEventProducts(
  IN in_eventid INT
) MODIFIES SQL DATA
BEGIN
	DECLARE l_id INT;
	DECLARE l_type VARCHAR(255);
	DECLARE done INT DEFAULT 0;
	DECLARE cur_toupdate CURSOR FOR
		SELECT p.id, p.type
		FROM productSummary p
		WHERE p.eventId=in_eventid AND
			(((p.type='focal-mechanism') AND NOT EXISTS (SELECT * FROM focalMechanismSummary WHERE productid=p.id)) OR ((p.type='moment-tensor') AND NOT EXISTS (SELECT * FROM momentTensorSummary WHERE productid=p.id)) OR ((p.type='origin') AND NOT EXISTS (SELECT * FROM originSummary WHERE productid=p.id)));
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	OPEN cur_toupdate;
	cur_toupdate_loop: LOOP
		FETCH cur_toupdate INTO l_id, l_type;
		IF done = 1 THEN
			CLOSE cur_toupdate;
			LEAVE cur_toupdate_loop;
		END IF;

		IF l_type='focal-mechanism' THEN
  CALL addFocalMechanismSummary(l_id);
ELSEIF l_type='moment-tensor' THEN
  CALL addMomentTensorSummary(l_id);
ELSEIF l_type='origin' THEN
  CALL addOriginSummary(l_id);
		END IF;
	END LOOP cur_toupdate_loop;
END;
//
delimiter ;


delimiter //
DROP PROCEDURE IF EXISTS resummarizeEventProducts //
CREATE PROCEDURE resummarizeEventProducts()
	MODIFIES SQL DATA
BEGIN
	DECLARE eventid INT;

	DECLARE done INT DEFAULT 0;
	DECLARE cur_events CURSOR FOR SELECT id FROM event;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

	START TRANSACTION;

	-- loop over all events, updating eventSummary table
	OPEN cur_events;
	cur_events_loop: LOOP
		FETCH cur_events INTO eventid;
		IF done = 1 THEN
			CLOSE cur_events;
			LEAVE cur_events_loop;
		END IF;

		CALL summarizeEventProducts(eventid);
	END LOOP cur_events_loop;

	COMMIT;

END;
//
delimiter ;


delimiter //
CREATE TRIGGER on_event_update_trigger AFTER UPDATE ON event FOR EACH ROW
BEGIN
	CALL updateEventSummary(NEW.id);
	CALL summarizeEventProducts(NEW.id);
END;
//
delimiter ;


CALL resummarizeEventProducts();
