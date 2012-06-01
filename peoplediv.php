<?php
  include_once dirname(__FILE__)."/phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/phplibraries/html/family_table_class.php";
  include_once dirname(__FILE__)."/phplibraries/genealogy/gsql-includes.php";
  include_once dirname(__FILE__)."/phplibraries/auth.php";

  $person = Person::Get($_REQUEST{"person"});
 
  switch ($_REQUEST{"div"})
  {
    case "personName":
      echo $person->getName();
      break;
    case "mapLink":
      echo "<p><a href=\"".$person->getDisplayMapURL()."\">see map</a></p>\n";      break;
    case "personGender":
      switch ($person->getGender())
      {
        case Person::GENDER_MALE:
          echo "male";
          break;
        case Person::GENDER_FEMALE:
          echo "female";
          break;
      }
      break;
    case "personStats":
      echo $person->getBirthDateString() . " - " . $person->getDeathDateString();
      break;
    case "personTimeline":
      echo DisplayWebPage::displayList("", "Event", $person->getEvents());
      break;
    case "personFamilytree":
      $familytable = new FamilyTable($person);
      echo $familytable->getTable();
      break;
  }

?>