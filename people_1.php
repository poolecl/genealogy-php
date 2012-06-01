<?php

  include_once dirname(__FILE__)."/phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/phplibraries/html/family_table_class.php";
  include_once dirname(__FILE__)."/phplibraries/genealogy/gsql-includes.php";
  include_once dirname(__FILE__)."/phplibraries/auth.php";

  class DisplayPersonWebPage extends DisplayWebPage
  {
    const DEFAULT_TITLE = "Genealogy Person Viewer";
    
    public function getBodyContentItem()
    {
      $person = $this->getItem();
      $output = "<h1>" . $person->getName() . "</h1>";
      $output .= "<p><a href=\"".$person->getDisplayMapURL()."\">see map</a></p>\n";
      switch ($person->getGender())
      {
        case Person::GENDER_MALE:
          $output .= "male ";
          break;
        case Person::GENDER_FEMALE:
          $output .= "female ";
          break;
      }
      if (isAuthorized())
      {
        $output .= "<a href=\"" . $person->getEditURL() . "\">e</a>\n";
      }

      $left = "<h3>" . $person->getBirthDateString() . " - " . $person->getDeathDateString() . "</h3>\n";
      $left .= $this->displayList("", "Event", $person->getEvents());
  
      $familytable = new FamilyTable($person);
      $right = $familytable->getTable();

      $output .= "<div class=body><div class=events>\n";
      $output .= $left;
      $output .= "</div><div class=stats>\n";
      $output .= $right;
      $output .= "</div></div>\n";
 
      return $output;
    }
  }
  
  $page = new DisplayPersonWebPage('Person');
  echo $page->getWebPage();
?>