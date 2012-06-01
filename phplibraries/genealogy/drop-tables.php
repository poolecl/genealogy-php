<html><head><title>database test</title></head>
<body bgcolor=white>

<?
  include_once dirname(__FILE__)."/database_parameters.php";
  /*
   these constants should be in your database_parameters.php file
   const SQL_USERNAME = "username";
   const SQL_PASSWORD = "password";
   const SQL_DATABASE = "database name";
   const SQL_SERVER = 'database server';
  */

  echo "<h1>ARE YOU SURE YOU WANT TO DO THIS</h1>";
  die("goodbye");

  mysql_connect(SQL_SERVER,SQL_USERNAME,SQL_PASSWORD);
  mysql_select_db(SQL_DATABASE) or die( "Unable to select database");
  
  // people <-> events link
  $query = "DROP TABLE PeopleEvents";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";

  // family <-> events link for parents
  $query = "DROP TABLE FamiliesEventsParents";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";

  // family <-> events link for children
  $query = "DROP TABLE FamiliesEventsChildren";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";

  // family <-> people link for children
  $query = "DROP TABLE FamiliesPeopleChildren";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";
  
  // event <-> page link
  $query = "DROP TABLE EventsPages";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";

  // page
  $query = "DROP TABLE Pages";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";

  // document
  $query = "DROP TABLE Documents";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";

  // family
  $query = "DROP TABLE Families";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";

  // people
  $query = "DROP TABLE People";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";

  // event
  $query = "DROP TABLE Events";
  echo "<p>$query</p>";
  mysql_query($query); //or die(mysql_error());
  echo "<p>".mysql_error()."</p>\n";

  
  mysql_close();

?>

</body></html>