<?php


class FDSNIndex {

  // the database connectin to use
  public $pdo;


  // for radius search
  const KILOMETERS_PER_DEGREE = 111.2;


  public function __construct($pdo=null) {
    $this->pdo = $pdo;
  }

  public function getCatalogs() {
    $rs = $this->pdo->query(
        'select distinct eventSource ' .
        'from productSummary ' .
        "where eventSource is not null and eventSource != '' " .
        'order by eventSource');
    $catalogs = array();
    while ($row = $rs->fetch()) {
      $catalogs[] = $row[0];
    }
    $rs->closeCursor();
    return $catalogs;
  }

  public function getContributors() {
    $rs = $this->pdo->query(
        'select distinct source ' .
        'from productSummary ' .
        'order by source');
    $contributors = array();
    while ($row = $rs->fetch()) {
      $contributors[] = $row[0];
    }
    $rs->closeCursor();
    return $contributors;
  }

  public function getProductTypes () {
    $rs = $this->pdo->query(
      'select distinct types.type ' .
      'from ( ' .
        'select distinct type, status ' .
        'from productSummary ' .
        "where type is not null and type != '' and " .
        "status is not null and status != 'DELETE' " .
      ') types ' .
      'order by types.type');
    $producttypes = array();
    while ($row = $rs->fetch()) {
      $producttypes[] = $row[0];
    }
    $rs->closeCursor();
    return $producttypes;
  }

  public function getEventTypes () {
    $rs = $this->pdo->query(
      'select distinct event_type ' .
      'from originSummary ' .
      "where event_type is not null and event_type != '' " .
      'order by event_type');
    $eventtypes = array();
    while ($row = $rs->fetch()) {
      $eventtypes[] = $row[0];
    }
    $rs->closeCursor();
    return $eventtypes;
  }

  public function getMagnitudeTypes () {
    $rs = $this->pdo->query(
      'select distinct magnitude_type ' .
      'from originSummary ' .
      "where magnitude_type is not null and magnitude_type != '' " .
      'order by magnitude_type');
    $magnitudetypes = array();
    while ($row = $rs->fetch()) {
      $magnitudetypes[] = $row[0];
    }
    $rs->closeCursor();
    return $magnitudetypes;
  }

  public function getEvents($query, $callback=null, $objects=false) {
    // build sql
    $where = $this->getWhere($query);
    $sql = 'select distinct ' .
        implode(',', array(
          'e.id as eventid',
          'e.source as preferredSource',
          'e.sourceCode as preferredSourceCode',
          'e.eventTime as preferredEventTime',
          'e.latitude as preferredLatitude',
          'e.longitude as preferredLongitude',
          'e.magnitude as preferredMagnitude',
          'e.depth as preferredDepth',
          'e.status as eventStatus',
          'es.event_type',
          'es.lastModified as eventUpdateTime',
          'es.maxmmi',
          'es.alertlevel',
          'es.region',
          'es.tsunami',
          'es.offset',
          'es.num_responses',
          'es.maxcdi',
          'es.significance',
          'es.types as producttypes',
          'es.eventids',
          'es.eventsources',
          'es.productsources',
          'ps.*',
          'os.*'
        )) . ' ' . $where['sql'];

    // prepare
    $statement = $this->pdo->prepare($sql);
    // bind parameters
    for ($i=0, $len=count($where['params']); $i<$len; $i++) {
      $statement->bindValue($i+1, $where['params'][$i]);
    }

    // execute
    if ($statement->execute() === FALSE) {
      // something went wrong
      $errorInfo = $statement->errorInfo();
      if ($callback === null) {
        throw new Exception($errorInfo[2]);
      }
      $callback->onError($errorInfo);
    } else {
      if ($callback === null) {
        $events = $statement->fetchAll(PDO::FETCH_ASSOC);
        $statement->closeCursor();
        $events = utf8_encode_array($events);
        return $events;
      }

      // iterate over results
      $callback->onStart($query);
      while($row = $statement->fetch(PDO::FETCH_ASSOC)) {

        if ($query->format === 'quakeml' || $query->format === 'xml') {
          if ($query->eventid !== null || $query->includeallorigins || $query->includeallmagnitudes) {
            // when querying a specific event, include all information
            $row['origin'] = $this->getOrigins($row['eventid']);
            $row['moment-tensor'] = $this->getMomentTensors($row['eventid']);
            $row['focal-mechanism'] = $this->getFocalMechanisms($row['eventid']);
          }
        }

        $row['event_type'] = str_replace('_',' ', $row['event_type']);
        $event = utf8_encode_array($row);

        if ($objects) {
          $event = new EventSummary();
          $event->eventIndexId = $row['eventid'];
          $event->source = $row['eventSource'];
          $event->sourceCode = $row['eventSourceCode'];
          $event->time = $row['eventTime'];
          $event->latitude = $row['eventLatitude'];
          $event->longitude = $row['eventLongitude'];
          $event->depth = $row['eventDepth'];
          $event->magnitude = $row['eventMagnitude'];
          $event->magnitudeType = $row['magnitude_type'];
          $event->status = $row['review_status'];
          $event->eventType = $row['event_type'];
          $event->azimuthalGap = $row['azimuthal_gap'];
          $event->tsunami = $row['tsunami'];
          $event->offset = $row['offset'];
          $event->significance = $row['significance'];
          $event->numStationsUsed = $row['num_stations_used'];
          $event->minimumDistance = $row['minimum_distance'];
          $event->standardError = $row['standard_error'];
          $event->properties = $row;
          $event->eventCodes = $row['eventids'];
          $event->lastModified = $row['eventUpdateTime'];
        }

        $callback->onEvent($event, $this);
      }
      $callback->onEnd();
    }

    // free resources
    $statement->closeCursor();
  }

  public function getEventCount($query) {
    // build sql
    $where = $this->getWhere($query, true);
    $sql = 'select count(*) from (select distinct ps.* ' . $where['sql'] . ') x';

    //prepare statement
    $statement = $this->pdo->prepare($sql);
    // bind parameters
    for ($i=0, $len=count($where['params']); $i<$len; $i++) {
      $statement->bindValue($i+1, $where['params'][$i]);
    }

    // execute
    $statement->execute();

    // get result
    $count = $statement->fetch();

    //free resources
    $statement->closeCursor();

    // Properly report what would match
    if ($query->limit !== null) {
      $count = min(intval($count[0]), intval($query->limit));
    } else {
      $count = intval($count[0]);
    }

    return $count;
  }

  public function getOrigins($eventid) {
    $statement = $this->pdo->prepare(
        'select ps.*, os.*' .
        ' from event e' .
        ' join productSummary ps on (ps.eventId = e.id)' .
        ' join currentProducts cp on (cp.id = ps.id)' .
        ' join originSummary os on (cp.id = os.productid)' .
        ' where e.id=?' .
        ' order by ps.eventId, ps.preferred desc, ps.updateTime desc');
    $statement->bindValue(1, $eventid);
    $statement->execute();
    $origins = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $origins;
  }

  public function getMomentTensors($eventid) {
    $statement = $this->pdo->prepare(
        'select ps.*, mts.*' .
        ' from event e' .
        ' join productSummary ps on (ps.eventId = e.id)' .
        ' join currentProducts cp on (cp.id = ps.id)' .
        ' join momentTensorSummary mts on (cp.id = mts.productid)' .
        ' where e.id=?' .
        ' order by ps.preferred desc, ps.updateTime desc');
    $statement->bindValue(1, $eventid);
    $statement->execute();
    $tensors = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $tensors;
  }

  public function getFocalMechanisms($eventid) {
    $statement = $this->pdo->prepare(
        'select ps.*, fms.*' .
        ' from event e' .
        ' join productSummary ps on (ps.eventId = e.id)' .
        ' join currentProducts cp on (cp.id = ps.id)' .
        ' join focalMechanismSummary fms on (cp.id = fms.productid)' .
        ' where e.id=?' .
        ' order by ps.preferred desc, ps.updateTime desc');
    $statement->bindValue(1, $eventid);
    $statement->execute();
    $tensors = $statement->fetchAll(PDO::FETCH_ASSOC);
    $statement->closeCursor();
    return $tensors;
  }


  protected function getWhere($query, $dontIncludeLimit=false) {

    $from = 'FROM event e' .
        ' JOIN eventSummary es on (es.eventid = e.id)' .
        ' JOIN productSummary ps on (ps.eventId = e.id)' .
        ' JOIN originSummary os on (os.productid = ps.id)';

    $where = array();
    $params = array();

    // query preferred attributes in event table
    $timeColumn = 'e.eventTime';
    $latitudeColumn = 'e.latitude';
    $longitudeColumn = 'e.longitude';
    $depthColumn = 'e.depth';
    $magnitudeColumn = 'e.magnitude';
    $updatedColumn = 'es.lastModified';

    if (!$query->includedeleted && !$query->includesuperseded) {
      // hide deleted events
      $where[] = "upper(e.status) <> 'DELETE'";
      $where[] = "upper(ps.status) <> 'DELETE'";
    }

    if ($query->eventid !== null) {
      // function call gives horrible performance in where,
      // but uses indexes when subselect and join
      $from .= ' JOIN (select getEventIdByFullEventId(?) as id) eventid on (e.id=eventid.id)';
      $params[] = $query->eventid;
    }

    // latest version of product
    $where[] = ' NOT EXISTS (' .
        'SELECT * FROM productSummary' .
        ' WHERE source=ps.source' .
        ' AND type=ps.type' .
        ' AND code=ps.code' .
        ' AND updateTime>ps.updateTime' .
        ')';

    // limit join to most preferred origin product for event
    $where[] = ' NOT EXISTS (' .
      'SELECT * FROM productSummary mp' .
      // in same event
      ' WHERE mp.eventId=ps.eventId' .
      // with same product type
      ' AND mp.type=ps.type' .
      // and not deleted
      " AND upper(mp.status)<>'DELETE'" .
      // limit to same catalog if searching by catalog
      ($query->catalog !== null ? ' AND mp.eventSource=ps.eventSource' : '') .
      // and more preferred
      ' AND (' .
        'mp.preferred > ps.preferred' .
        ' OR (mp.preferred=ps.preferred and mp.updateTime>ps.updateTime)' .
      ')' .
      // and is the latest version of itself
      ' AND NOT EXISTS (' .
        'select * from productSummary' .
        ' where source=mp.source' .
        ' and type=mp.type' .
        ' and code=mp.code' .
        ' and updateTime>mp.updateTime' .
      ')' .
    ')';

    if ($query->catalog !== null) {
      // additional parameters need summary tables
      $timeColumn = 'ps.eventTime';
      $latitudeColumn = 'ps.eventLatitude';
      $longitudeColumn = 'ps.eventLongitude';
      $depthColumn = 'ps.eventDepth';
      $magnitudeColumn = 'ps.eventMagnitude';
      $updatedColumn = 'ps.updateTime';

      $where[] = 'ps.eventSource = ?';
      $params[] = $query->catalog;
    }


    // ignore all other parameters when eventid present
    if ($query->eventid === null) {

      // normalize longitudes that are entirely outside the [-180,180] range
      if ($query->minlongitude !== null && $query->maxlongitude !== null) {
        if ($query->minlongitude < -180 && $query->maxlongitude < -180) {
          $query->minlongitude = $query->minlongitude + 360;
          $query->maxlongitude = $query->maxlongitude + 360;
        } else if ($query->minlongitude > 180 && $query->maxlongitude > 180) {
          $query->minlongitude = $query->minlongitude - 360;
          $query->maxlongitude = $query->maxlongitude - 360;
        }
      }

      if ($query->maxradius !== null && $query->latitude !== null && $query->longitude !== null) {
        // use maxradius to create bounding box so query uses indexes
        $latradius = $query->maxradius;

        $query->maxlatitude = min($query->latitude+$latradius,
            ($query->maxlatitude === null ? 90 : $query->maxlatitude));
        $query->minlatitude = max($query->latitude-$latradius,
            ($query->minlatitude === null ? -90 : $query->minlatitude));

        // adjust longitude radius based on latitude when far from equator
        $alat = abs($query->latitude);
        if ($alat < 89) {
          // only filter longitude when not at poles
          $lonradius = $latradius / cos(deg2rad($alat));
          $query->maxlongitude = min($query->longitude+$lonradius,
              ($query->maxlongitude === null ? 360 : $query->maxlongitude));
          $query->minlongitude = max($query->longitude-$lonradius,
              ($query->minlongitude === null ? -360 : $query->minlongitude));
        }

        //haversine from: http://www.movable-type.co.uk/scripts/gis-faq-5.1.html
        //great circle distance in radians is c
        //dlon = lon2 - lon1
        //dlat = lat2 - lat1
        //a = sin^2(dlat/2) + cos(lat1) * cos(lat2) * sin^2(dlon/2)
        //c = 2 * arcsin(min(1,sqrt(a)))

        $where[] = 'degrees(2 * asin(least(1,sqrt(' .
            // a
            'pow(sin(radians(('. $latitudeColumn . ' - ?' ./*lat*/')/2)), 2)' .
            '+ cos(radians(?' . /*lat*/ '))*cos(radians(' . $latitudeColumn .
            '))*pow(sin(radians(('. $longitudeColumn . ' - ?' ./*lon*/')/2)), 2)' .
            // end a
            ')))) BETWEEN ?' . /*minradius*/ ' AND ?' /*maxradius*/;
        $params[] = $query->latitude;
        $params[] = $query->latitude;
        $params[] = $query->longitude;
        $params[] = $query->minradius;
        $params[] = $query->maxradius;
      }

      if ($query->updatedafter !== null) {
        $where[] = $updatedColumn . ' >= ?';
        $params[] = $query->updatedafter;
      }
      if ($query->starttime !== null) {
        $where[] = $timeColumn . ' >= ?';
        $params[] = $query->starttime;
      }
      if ($query->endtime !== null) {
        $where[] = $timeColumn . ' <= ?';
        $params[] = $query->endtime;
      }
      if ($query->minlatitude !== null) {
        $where[] = $latitudeColumn . ' >= ?';
        $params[] = $query->minlatitude;
      }
      if ($query->maxlatitude !== null) {
        $where[] = $latitudeColumn . ' <= ?';
        $params[] = $query->maxlatitude;
      }

      if (
          ($query->minlongitude !== null && $query->maxlongitude !== null)
          && ($query->minlongitude < -180 || $query->maxlongitude > 180)
      ) {
        // cross the date line
        if ($query->minlongitude < -180) {
          $left_min = $query->minlongitude + 360;
          $left_max = 180;
          $right_min = -180;
          $right_max = $query->maxlongitude;
        } else {
          // maxlongitude > 180
          $left_min = $query->minlongitude;
          $left_max = 180;
          $right_min = -180;
          $right_max = $query->maxlongitude - 360;
        }

        $where[] = '(' .
            '(' . $longitudeColumn . ' BETWEEN ? AND ?)' .
            ' OR ' .
            '(' . $longitudeColumn . ' BETWEEN ? AND ?)' .
          ')';
        $params[] = $left_min;
        $params[] = $left_max;
        $params[] = $right_min;
        $params[] = $right_max;
      } else {
        if ($query->minlongitude !== null) {
          $where[] = $longitudeColumn . ' >= ?';
          $params[] = $query->minlongitude;
        }
        if ($query->maxlongitude !== null) {
          $where[] = $longitudeColumn . ' <= ?';
          $params[] = $query->maxlongitude;
        }
      }

      if ($query->mindepth !== null) {
        $where[] = $depthColumn . ' >= ?';
        $params[] = $query->mindepth;
      }
      if ($query->maxdepth !== null) {
        $where[] = $depthColumn . ' <= ?';
        $params[] = $query->maxdepth;
      }
      if ($query->minmagnitude !== null) {
        $where[] = $magnitudeColumn . ' >= ?';
        $params[] = $query->minmagnitude;
      }
      if ($query->maxmagnitude !== null) {
        $where[] = $magnitudeColumn . ' <= ?';
        $params[] = $query->maxmagnitude;
      }
      if ($query->magnitudetype !== null) {
        $where[] = 'upper(os.magnitude_type) = upper(?)';
        $params[] = $query->magnitudetype;
      }

      // extensions

      if ($query->eventtype !== null) {
        $types = array();
        foreach ($query->eventtype as $eventtype) {
          $types[] = 'upper(os.event_type) = upper(?)';
          $params[] =  $eventtype;
        }
        $where[] = "( " . implode(" OR ", $types) . " )";
      }

      if ($query->reviewstatus !== null) {
        if ($query->reviewstatus == 'automatic') {
          $where[] = "(upper(os.review_status) = upper(?) OR os.review_status='')";
        } else {
          $where[] = 'upper(os.review_status) = upper(?)';
        }
        $params[] = $query->reviewstatus;
      }

      if ($query->minmmi !== null) {
        $where[] = 'es.maxmmi >= ?';
        $params[] = $query->minmmi;
      }
      if ($query->maxmmi !== null) {
        $where[] = 'es.maxmmi <= ?';
        $params[] = $query->maxmmi;
      }

      if ($query->mincdi !== null) {
        $where[] = 'es.maxcdi >= ?';
        $params[] = $query->mincdi;
      }
      if ($query->maxcdi !== null) {
        $where[] = 'es.maxcdi <= ?';
        $params[] = $query->maxcdi;
      }
      if ($query->minfelt !== null) {
        $where[] = 'es.num_responses >= ?';
        $params[] = $query->minfelt;
      }

      if ($query->alertlevel !== null) {
        if ($query->alertlevel === '*') {
          $where[] = "(es.alertlevel <> '' AND es.alertlevel IS NOT NULL)";
        } else {
          $where[] = 'upper(es.alertlevel) = upper(?)';
          $params[] = $query->alertlevel;
        }
      }

      if ($query->mingap !== null) {
        $where[] = 'os.azimuthal_gap >= ?';
        $params[] = $query->mingap;
      }
      if ($query->maxgap !== null) {
        $where[] = 'os.azimuthal_gap <= ?';
        $params[] = $query->maxgap;
      }

      if ($query->minsig !== null) {
        $where[] = 'es.significance >= ?';
        $params[] = $query->minsig;
      }
      if ($query->maxsig !== null) {
        $where[] = 'es.significance <= ?';
        $params[] = $query->maxsig;
      }

      if ($query->producttype !== null ||
          $query->contributor !== null ||
          $query->productcode !== null ) {

        $from .= ' JOIN productSummary contributed on (e.id=contributed.eventid)';
        // latest version of product
        $where[] = ' NOT EXISTS (' .
            'SELECT * FROM productSummary' .
            ' WHERE source=contributed.source' .
            ' AND type=contributed.type' .
            ' AND code=contributed.code' .
            ' AND updateTime>contributed.updateTime' .
            ')';
        // and not deleted
        if (!$query->includedeleted && !$query->includesuperseded) {
          $where[] = "upper(contributed.status) <> 'DELETE'";
        }

        if ($query->producttype !== null) {
          $where[] = 'contributed.type=?';
          $params[] = $query->producttype;
        }

        if ($query->contributor !== null) {
          $where[] = 'contributed.source=?';
          $params[] = $query->contributor;
        }

        if ($query->productcode !== null) {
          $where[] = 'contributed.code=?';
          $params[] = $query->productcode;
        }
      }

    } // end ($query->eventid === null)


    // assemble sql
    $sql = $from;
    if (count($where) > 0) {
      $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    // order by
    $sql .= ' ORDER BY ';
    if ($query->orderby === 'time') {
      $sql .= $timeColumn . ' desc';
    } else if ($query->orderby === 'time-asc') {
      $sql .= $timeColumn . ' asc';
    } else if ($query->orderby === 'magnitude') {
      $sql .= $magnitudeColumn . ' desc';
    } else if ($query->orderby === 'magnitude-asc') {
      $sql .= $magnitudeColumn . ' asc';
    }

    // limit
    if ($query->limit !== null && !$dontIncludeLimit) {
      $sql .= ' LIMIT ' . intval($query->limit);
      if ($query->offset !== null) {
        $sql .= ' OFFSET ' . (intval($query->offset)-1);
      }
    }

    return array(
      'sql' => $sql,
      'params' => $params
    );
  }

}
