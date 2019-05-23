<?php

$productsToSummarize = array(

  'focalMechanism' => array(
    'types' => array('focal-mechanism', 'focal-mechanism-scenario'),
    'properties' => array(
      // similar to fm
      'review-status' => 'VARCHAR(255)',
      'beachball-source' => 'VARCHAR(255)',
      'nodal-plane-1-strike' => 'DOUBLE',
      'nodal-plane-1-dip' => 'DOUBLE',
      'nodal-plane-1-rake' => 'DOUBLE',
      'nodal-plane-2-strike' => 'DOUBLE',
      'nodal-plane-2-dip' => 'DOUBLE',
      'nodal-plane-2-rake' => 'DOUBLE',
      'num-stations-used' => 'INT'
    )
  ),

  "momentTensor" => array(
    'types' => array( 'moment-tensor', 'moment-tensor-scenario'),
    'properties' => array(
      // similar to fm
      'review-status' => 'VARCHAR(255)',
      'beachball-source' => 'VARCHAR(255)',
      'nodal-plane-1-strike' => 'DOUBLE',
      'nodal-plane-1-dip' => 'DOUBLE',
      'nodal-plane-1-rake' => 'DOUBLE',
      'nodal-plane-2-strike' => 'DOUBLE',
      'nodal-plane-2-dip' => 'DOUBLE',
      'nodal-plane-2-rake' => 'DOUBLE',
      'num-stations-used' => 'INT',
      // mt properties
      'percent-double-couple' => 'DOUBLE',
      'scalar-moment' => 'DOUBLE',
      'beachball-type' => 'VARCHAR(255)',
      'tensor-mrr' => 'DOUBLE',
      'tensor-mtt' => 'DOUBLE',
      'tensor-mpp' => 'DOUBLE',
      'tensor-mtp' => 'DOUBLE',
      'tensor-mrt' => 'DOUBLE',
      'tensor-mrp' => 'DOUBLE',
      'derived-latitude' => 'DOUBLE',
      'derived-longitude' => 'DOUBLE',
      'derived-depth' => 'DOUBLE',
      'derived-eventtime' => 'VARCHAR(255)',
      'derived-magnitude' => 'DOUBLE',
      'derived-magnitude-type' => 'VARCHAR(255)'
    )
  ),

  'origin' => array(
    'types' => array('origin', 'origin-scenario'),
    'properties' => array(
      'event-type' => 'VARCHAR(255)',
      'azimuthal-gap' => 'DOUBLE',
      'horizontal-error' => 'DOUBLE',
      'vertical-error' => 'DOUBLE',
      'minimum-distance' => 'DOUBLE',
      'num-stations-used' => 'INT',
      'num-phases-used' => 'INT',
      'review-status' => 'VARCHAR(255)',
      'standard-error' => 'DOUBLE',
      'origin-source' => 'VARCHAR(255)',
      'magnitude-source' => 'VARCHAR(255)',
      'magnitude-type' => 'VARCHAR(255)',
      'magnitude-error' => 'DOUBLE',
      'magnitude-num-stations-used' => 'INT'
    )
    ),

  'extent' => array(
    'types' => array('shakemap', 'event-sequence'),
    'properties' => array(
      'starttime' => 'BIGINT',
      'endtime' => 'BIGINT',
      'maximum-latitude' => 'DOUBLE',
      'maximum-longitude' => 'DOUBLE',
      'minimum-latitude' => 'DOUBLE',
      'minimum-longitude' => 'DOUBLE'
    )
  )

);


$TRIGGER_NAME = "on_event_update_trigger";


// drop trigger before updates
echo 'DROP TRIGGER IF EXISTS ' . $TRIGGER_NAME . ';' . "\n";


// used in event update trigger
$event_procedureNotexists = array();
$event_procedureCalls = array();


// output summary table, procedure to update summary table
foreach ($productsToSummarize as $summaryName => $info) {

  $tableName = $summaryName . 'Summary';
  $tableColumns = array();
  $tableIndexes = array();

  $procedureName = 'add' . ucfirst($tableName);
  $procedureDeclares = array();
  $procedureCalls = array();
  $procedureColumns = array();

  // build table and procedure to update

  // foreign key to productSummary table
  $tableColumns[] = 'productid BIGINT(20) REFERENCES productSummary(id) ON DELETE CASCADE';
  $tableIndexes[] = 'UNIQUE(productid)';
  $procedureColumns[] = 'in_productid';

  // generate columns and indexes
  foreach ($info['properties'] as $propertyName => $type) {
    $columnName = str_replace('-', '_', $propertyName);
    $tableColumns[] = $columnName . ' ' . $type;
    $tableIndexes[] = 'INDEX(' . $columnName . ')';

    $procedureDeclares[] = 'DECLARE ' . $columnName . ' ' . $type . ';';
    $procedureCalls[] = 'CALL getProductProperty(in_productid, \'' . $propertyName . '\', ' . $columnName . ');';
    $procedureColumns[] = $columnName;
  }

  // output sql
  echo '-- table' . "\n";
  echo 'DROP TABLE IF EXISTS ' . $tableName . ';' . "\n";
  echo 'CREATE TABLE ' . $tableName . ' (' .
    implode(',', $tableColumns) . ',' .
    implode(',', $tableIndexes) .
  ') ENGINE=INNODB;' . "\n";

  echo '-- procedure' . "\n";
  echo 'delimiter //' . "\n";
  echo 'DROP PROCEDURE IF EXISTS ' . $procedureName . '//' . "\n";
  echo 'CREATE PROCEDURE ' . $procedureName . '(' .
    'IN in_productid INT' .
    ') MODIFIES SQL DATA' . "\n";
  echo 'BEGIN';
  echo "\n  " . implode("\n  ", $procedureDeclares) . "\n";
  echo "\n  " . implode("\n  ", $procedureCalls) . "\n";
  echo "\n" . ' INSERT INTO ' . $tableName . ' VALUES (' . implode(',', $procedureColumns) . ') ON DUPLICATE KEY UPDATE productid=productid;' . "\n";
  echo 'END;' . "\n";
  echo '//' . "\n";
  echo 'delimiter ;' . "\n\n";


  $event_procedureNotexists[] = '(p.type=\'' . implode('\' OR p.type=\'', $info['types']) . '\') AND' .
      ' NOT EXISTS (SELECT * FROM ' . $tableName . ' WHERE productid=p.id)';

  $event_procedureCalls[] = 'l_type=\'' . implode('\' OR l_type=\'', $info['types']) . '\' THEN' .
      "\n  CALL " . $procedureName . '(l_id);' . "\n";


}



// output trigger to call procedures above


?>

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
      ((<?php

  echo implode(') OR (', $event_procedureNotexists);

?>));
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

  OPEN cur_toupdate;
  cur_toupdate_loop: LOOP
    FETCH cur_toupdate INTO l_id, l_type;
    IF done = 1 THEN
      CLOSE cur_toupdate;
      LEAVE cur_toupdate_loop;
    END IF;

    IF <?php

  echo implode('ELSEIF ', $event_procedureCalls);

?>
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


CALL resummarizeEventProducts();
