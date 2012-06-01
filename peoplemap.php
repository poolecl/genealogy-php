<?php

  include_once dirname(__FILE__)."/phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/phplibraries/genealogy/gsql-includes.php";
  include_once dirname(__FILE__)."/phplibraries/globals_oo.php";
  include_once dirname(__FILE__)."/phplibraries/auth.php";

  class DisplayPersonMapWebPage extends DisplayWebPage
  {
    const DEFAULT_TITLE = "Genealogy Person Map Viewer";
    const DISPLAY_URL = "/peoplemap.php?";
    
    public function getBodyContentItem()
    {
      $person = $this->getItem();
      $output = "<h1>" . $person->getName() . "</h1>";
      if (isAuthorized())
      {
        $output .= "<a href=\"" . $person->getEditURL() . "\">e</a>\n";
      }
      $output .= "<p><a href=\"".$person->getDisplayURL()."\">return to regular view</a></p>\n";

      $output .= "<table width=\"100%\"><tr><td width=\"75%\" valign=\"top\" halign=\"left\">\n";
      $output .= $this->map->getOnLoad();
      $output .= $this->map->getMap();
      $output .= "</td><td valign=\"top\" halign=\"right\">\n";
      $output .= "<div style=\"overflow:auto;height:600px;\">\n";
      $output .= $this->map->getSidebar();
      $output .= "</div>\n";
      $output .= "</td></tr></table>\n";
  //    $output .= "<table width=\"100%\"><tr><td valign=\"top\" halign=\"left\">\n";

  //    $output .= "</td></tr></table>\n";

      //$left = "<h3>" . $person->getBirthDateString() . " - " . $person->getDeathDateString() . "</h3>\n";
      //$left .= $this->displayList("", "Event", $person->getEvents());
  
      //$right = familyTable($person);

      //$output .= "<table width=\"100%\"><tr><td valign=\"top\" halign=\"left\">\n";
      //$output .= $left;
      //$output .= "</td><td valign=\"top\" halign=\"right\">\n";
      //$output .= $right;
      //$output .= "</td></tr></table>\n";
 
      return $output;
    }

    public function __construct($class)
    {
      parent::__construct($class);
      $person = $this->getItem();
      if (!is_null($person))
      {
        $this->map = Event::GetEventListMap($person->getEvents(), 'map','100%','600px');
      }
    }

    public function getHeaderContent()
    {
      $output = parent::getHeaderContent();
      if (isset($this->map))
      {
        $output .= $this->map->getHeaderJS();
        $output .= $this->map->getMapJS();
      }
      return $output;
    }

    public function displayList($heading, $class, $items)
    {
      $output = "$heading<ul>";
      $items = $class::SortList($items);
      foreach ($items as $item)
      {
        $output .= "<li>\n";
        $output .= $item->getDisplayMapLink();
        $output .= "</li>\n";
      }
      $output .= "</ul>\n";
      return $output;
    }    
  }
  
  $page = new DisplayPersonMapWebPage('Person');
  echo $page->getWebPage();
?>