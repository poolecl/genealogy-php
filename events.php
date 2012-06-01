<?php

  include_once dirname(__FILE__)."/phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/phplibraries/genealogy/gsql-includes.php";
  include_once dirname(__FILE__)."/phplibraries/auth.php";

  class DisplayEventWebPage extends DisplayWebPage
  {
    const DEFAULT_TITLE = "Genealogy Event Viewer";
    private $map;
    
    public function getBodyContentItem()
    {
      $event = $this->getItem();
      $output = "<h1>" . $event->getTitle() . "</h1>\n";
      if (isAuthorized())
      {
        $output .= "<a href=\"" . $event->getEditURL() . "\">e</a>\n";
      }

      $left = "<h3>" . $event->getDateString() . "</h3>\n";
      $left .= "<p>" . $event->getText() . "</p>\n";
      $left .= $this->displayList("Citations:", "Page", $event->getPages());
      if (isset($this->map))
      {
        $right = $this->map->getOnLoad();
        $right .= $this->map->getMap();
        $output .= "<table width=\"100%\"><tr><td valign=\"top\" halign=\"left\">\n";
        $output .= $left;
        $output .= "</td><td valign=\"top\" halign=\"right\">\n";
        $output .= $right;
        $output .= "</td></tr></table>\n";
      }
      else
      {
        $output .= $left;
      }
      return $output;
    }

    public function __construct($class)
    {
      parent::__construct($class);
      $event = $this->getItem();
      if (!is_null($event))
      {
        if ($event->hasMap())
        {
          $this->map = $event->getMap('map','500px','500px', false);
        }
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
  }
  
  $page = new DisplayEventWebPage('Event');
  echo $page->getWebPage();
?>