<html><head><title>database test</title></head>
<body bgcolor=white>
<?
  include_once dirname(__FILE__)."/database_parameters.php";
  
  echo "<h1>ARE YOU SURE YOU WANT TO DO THIS</h1>";
  die("goodbye");
    
  mysql_connect(SQL_SERVER,SQL_USERNAME,SQL_PASSWORD);
  mysql_select_db(SQL_DATABASE) or die( "Unable to select database");

  // common elements to my primary tables
  $generic = "Id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,";
  $generic .= "Reviewed       BOOLEAN NOT NULL DEFAULT 0,";
  $generic .= "Followup       VARCHAR(100),";
  $generic .= "FollowupDetail VARCHAR(100),";
  $generic .= "Note           MEDIUMTEXT,";

  // document
  $query = "CREATE TABLE Documents (";
  $query .= "Document" . $generic;
  $query .= "Title         VARCHAR(100) NOT NULL DEFAULT 'New Document',";
  $query .= "Citation      MEDIUMTEXT,";
  $query .= "PRIMARY KEY (DocumentId),";
  $query .= "INDEX (Title))";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());

  // page
  $query = "CREATE TABLE Pages (";
  $query .= "Page" . $generic;
  $query .= "Title         VARCHAR(100) NOT NULL DEFAULT 'New Page',";
  $query .= "Transcription MEDIUMTEXT,";
  $query .= "Citation      MEDIUMTEXT,";
  $query .= "DocumentId    MEDIUMINT UNSIGNED,";
  $query .= "ImageFile     VARCHAR(100),";
  $query .= "PRIMARY KEY (PageId),";
  $query .= "INDEX (Title),";
  $query .= "INDEX (DocumentId),";
  $query .= "INDEX (ImageFile),";
  $query .= "FOREIGN KEY (DocumentId) REFERENCES Documents(DocumentId) ON DELETE SET NULL)";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());

  // event
  $query = "CREATE TABLE Events (";
  $query .= "Event" . $generic;
  $query .= "Title        VARCHAR(100) NOT NULL DEFAULT 'New Event',";
  $query .= "Text         MEDIUMTEXT,";
  $query .= "DatePrefix   ENUM('normal', 'about', 'unknown') NOT NULL DEFAULT 'unknown',";
  $query .= "Date         DATE NOT NULL DEFAULT '1900-06-15',";
  $query .= "YearExact    BOOLEAN NOT NULL DEFAULT 0,";
  $query .= "MonthExact   BOOLEAN NOT NULL DEFAULT 0,";
  $query .= "DayExact     BOOLEAN NOT NULL DEFAULT 0,";
  $query .= "LocationName VARCHAR(100),";
  $query .= "LocationDescription  MEDIUMTEXT,";
  $query .= "MapEnable    BOOLEAN NOT NULL DEFAULT 0,";
  $query .= "MapLatitude  FLOAT(10,6),";
  $query .= "MapLongitude FLOAT(10,6),";
  $query .= "MapZoom      TINYINT NOT NULL DEFAULT -1,";
  $query .= "INDEX (Title),";
  $query .= "PRIMARY KEY (EventId))";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());

  // people
  $query = "CREATE TABLE People (";
  $query .= "Person" . $generic;
  $query .= "FirstName     VARCHAR(50) NOT NULL DEFAULT 'Unknown',";
  $query .= "LastName      VARCHAR(50) NOT NULL DEFAULT 'Unknown',";
  $query .= "Gender        ENUM('unknown', 'male', 'female')  NOT NULL DEFAULT 'unknown',";
  $query .= "BirthEventId  MEDIUMINT UNSIGNED,";
  $query .= "DeathEventId  MEDIUMINT UNSIGNED,";
  $query .= "PRIMARY KEY (PersonId),";
  $query .= "INDEX (FirstName),";
  $query .= "INDEX (LastName),";
  $query .= "INDEX (BirthEventId),";
  $query .= "INDEX (DeathEventId),";
  $query .= "FOREIGN KEY (BirthEventId) REFERENCES Events(EventID) ON DELETE SET NULL,";
  $query .= "FOREIGN KEY (DeathEventId) REFERENCES Events(EventID) ON DELETE SET NULL)";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());
  
  // family
  $query = "CREATE TABLE Families (";
  $query .= "Family" . $generic;
  $query .= "FatherPersonId   MEDIUMINT UNSIGNED,";
  $query .= "MotherPersonId   MEDIUMINT UNSIGNED,";
  $query .= "MarriageEventId  MEDIUMINT UNSIGNED,";
  $query .= "DivorceEventId   MEDIUMINT UNSIGNED,";
  $query .= "INDEX (FatherPersonId),";
  $query .= "INDEX (MotherPersonId),";
  $query .= "INDEX (MarriageEventId),";
  $query .= "INDEX (DivorceEventId),";
  $query .= "PRIMARY KEY (FamilyId),";
  $query .= "FOREIGN KEY (FatherPersonId)  REFERENCES People(PersonId) ON DELETE SET NULL,";
  $query .= "FOREIGN KEY (MotherPersonId)  REFERENCES People(PersonId) ON DELETE SET NULL,";
  $query .= "FOREIGN KEY (MarriageEventId) REFERENCES Events(EventID) ON DELETE SET NULL,";
  $query .= "FOREIGN KEY (DivorceEventId)  REFERENCES Events(EventID) ON DELETE SET NULL)";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());
  
  // people <-> events link
  $query = "CREATE TABLE PeopleEvents (";
  $query .= "PersonId         MEDIUMINT UNSIGNED,";
  $query .= "EventId          MEDIUMINT UNSIGNED,";
  $query .= "PRIMARY KEY (PersonId,EventId),";
  $query .= "INDEX (PersonId),";
  $query .= "INDEX (EventId),";
  $query .= "FOREIGN KEY (PersonId)  REFERENCES People(PersonId) ON DELETE CASCADE,";
  $query .= "FOREIGN KEY (EventId)   REFERENCES Events(EventId) ON DELETE CASCADE)";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());

  // family <-> events link for parents
  $query = "CREATE TABLE FamiliesEventsParents (";
  $query .= "FamilyId         MEDIUMINT UNSIGNED,";
  $query .= "EventId          MEDIUMINT UNSIGNED,";
  $query .= "PRIMARY KEY (FamilyId,EventId),";
  $query .= "INDEX (FamilyId),";
  $query .= "INDEX (EventId),";
  $query .= "FOREIGN KEY (FamilyId)  REFERENCES Families(FamilyId) ON DELETE CASCADE,";
  $query .= "FOREIGN KEY (EventId)   REFERENCES Events(EventId) ON DELETE CASCADE)";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());

  // family <-> events link for children
  $query = "CREATE TABLE FamiliesEventsChildren (";
  $query .= "FamilyId         MEDIUMINT UNSIGNED,";
  $query .= "EventId          MEDIUMINT UNSIGNED,";
  $query .= "PRIMARY KEY (FamilyId,EventId),";
  $query .= "INDEX (FamilyId),";
  $query .= "INDEX (EventId),";
  $query .= "FOREIGN KEY (FamilyId)  REFERENCES Families(FamilyId) ON DELETE CASCADE,";
  $query .= "FOREIGN KEY (EventId)   REFERENCES Events(EventId) ON DELETE CASCADE)";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());

  // family <-> people link (children)
  $query = "CREATE TABLE FamiliesPeopleChildren (";
  $query .= "FamilyId         MEDIUMINT UNSIGNED,";
  $query .= "PersonId         MEDIUMINT UNSIGNED,";
  $query .= "PRIMARY KEY (FamilyId,PersonId),";
  $query .= "INDEX (FamilyId),";
  $query .= "INDEX (PersonId),";
  $query .= "FOREIGN KEY (FamilyId)  REFERENCES Families(FamilyId) ON DELETE CASCADE,";
  $query .= "FOREIGN KEY (PersonId)   REFERENCES People(PersonId) ON DELETE CASCADE)";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());
  
  // event <-> page link
  $query = "CREATE TABLE EventsPages (";
  $query .= "EventId          MEDIUMINT UNSIGNED,";
  $query .= "PageId           MEDIUMINT UNSIGNED,";
  $query .= "PRIMARY KEY (EventId,PageId),";
  $query .= "INDEX (EventId),";
  $query .= "INDEX (PageId),";
  $query .= "FOREIGN KEY (EventId)   REFERENCES Events(EventId) ON DELETE CASCADE,";
  $query .= "FOREIGN KEY (PageId)    REFERENCES Pages(PageId) ON DELETE CASCADE)";
  $query .= " ENGINE=InnoDB";
  echo "<p>$query</p>";
  mysql_query($query) or die(mysql_error());

  mysql_close();
  
?>

</body></html>