<?php

  include_once dirname(__FILE__)."/../phplibraries/html/editor_html_class.php";

  class EditorEventWebPage extends EditorWebPage
  {
    const DISPLAY_NAME = 'Event';
    private $map;
    
    public function __construct($class)
    {
      parent::__construct($class);
      $event = $this->getItem();
      if (isset($event))
      {
        if ($event->hasMap())
        {
          $this->map = $event->getMap('map','100%','200px', true);
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
    
    public function getItemCustomContent()
    {
      $event = $this->getItem();
      $output = "<input name=title size=70 value=\"" . htmlentities($event->getTitle(), ENT_QUOTES) . "\">\n";
      $output .= "<input type=submit name=action value=\"save\"></h2>\n";      
      $output .= "<p />\n";
      $output .= "<table border=0 width=\"100%\">\n";

      //date
      $output .= "<tr><td width=\"50%\" valign=top>\n";
      $output .= "<select name=\"date_prefix\">\n";
      $output .= "<option value=\"normal\"";
      if ($event->getDatePrefix() == 'normal')
      {
        $output .= " selected";
      }
      $output .= ">normal</option>\n";
      $output .= "<option value=\"about\"";
      if ($event->getDatePrefix() == 'about')
      {
        $output .= " selected";
      }
      $output .= ">about</option>\n";
      $output .= "<option value=\"unknown\"";
      if ($event->getDatePrefix() == 'unknown')
      {
        $output .= " selected";
      }
      $output .= ">unknown</option>\n";
      $output .= "</select>\n";
      $output .= "month <input name=\"date_month_exact\" type=\"checkbox\" value=\"enabled\"";
      if ($event->isMonthExact())
      {
        $output .= " checked";
      }
      $output .= " />\n";
      $output .= "<select name=\"date_month\">\n";
      for ($i = 1; $i <= 12; $i++)
      {
        $output .= "<option value=\"$i\"";
        if ($i == $event->getMonthNumber())
        {
          $output .= " selected";
        }
        $output .= ">" . Event::GetMonthNameFromNumber($i) . "</option>\n";
      }
      $output .= "</select>\n";

      $output .= "day <input name=\"date_day_exact\" type=\"checkbox\" value=\"enabled\"";
      if ($event->isDayExact())
      {
        $output .= " checked";
      }
      $output .= " />\n";
      $output .= "<select name=\"date_day\">\n";
      for ($i = 1; $i <= 31; $i++)
      {
        $output .= "<option value=\"$i\"";
        if ($i == $event->getDay())
        {
          $output .= " selected";
        }
        $output .= ">$i</option>\n";
      }
      $output .= "</select>\n";
      
      $output .= "year <input name=\"date_year_exact\" type=\"checkbox\" value=\"enabled\"";
      if ($event->isYearExact())
      {
        $output .= " checked";
      }
      $output .= " />\n";
      $output .= "<input name=\"date_year\" maxlength=\"4\" size=\"4\" value=\"" . htmlentities($event->getYear(), ENT_QUOTES) . "\">\n";
      $output .= "</td>";
      $output .= "<td width=\"50%\" valign=top>\n";
      $output .= $event->FormatForHTML($event->getDateString());
      $output .= "</td></tr>\n";

      //location
      $output .= "<tr><td width=\"50%\" valign=top>\n";
      $output .= "location <input name=\"location_name\" style=\"width:80%;\" value=\"" . htmlentities($event->getLocationName(), ENT_QUOTES) . "\"><br \>\n";
      $output .= "latitude <input name=\"map_latitude\" style=\"width:40%;\" value=\"" . htmlentities($event->getMapLatitude(), ENT_QUOTES) . "\"><br \>\n";
      $output .= "longitude <input name=\"map_longitude\" style=\"width:40%;\" value=\"" . htmlentities($event->getMapLongitude(), ENT_QUOTES) . "\"><br \>\n";
      $output .= "map enabled <input name=\"map_enable\" type=\"checkbox\" value=\"enabled\"";
      if ($event->isMapEnable())
      {
        $output .= " checked";
      }
      $output .= " />\n";
      $output .= "zoom <select name=\"map_zoom\">\n";
      if ($event->getMapZoom() == Event::MAP_ZOOM_AUTO)
      {
        $output .= "<option value=\"auto\" selected>auto</option>\n";
      } 
      else
      {
        $output .= "<option value=\"auto\">auto</option>\n";
      }
      for ($i = 21; $i >= 0; $i--)
      {
        $output .= "<option value=\"$i\"";
        if ($i == $event->getMapZoom())
        {
          $output .= " selected";
        }
        $output .= ">$i</option>\n";
      } 
      $output .= "</select><br \>\n";
      $output .= "<textarea name=\"location_description\" rows=5 style=\"width:100%;\">\n";
      $output .= htmlentities($event->getLocationDescription(), ENT_QUOTES);
      $output .= "</textarea>\n";
      $output .= "</td><td width=\"50%\" valign=top>\n";
      if (isset($this->map))
      {
        $output .= $this->map->getOnLoad();
        $output .= $this->map->getMap();
      }
      else
      {
        $output .= $event->getLocationName();
      }
      $output .= "</td></tr>\n";

      //text
      $output .= "<tr><td colspan=2>description</td></tr>\n";
      $output .= "<tr><td width=\"50%\" valign=top>\n";
      $output .= "<textarea name=\"text\" rows=5 style=\"width:100%;\">\n";
      $output .= htmlentities($event->getText(), ENT_QUOTES);
      $output .= "</textarea>\n";
      $output .= "</td>";
      $output .= "<td width=\"50%\" valign=top>\n";
      $output .= $event->FormatForHTML($event->getText());
      $output .= "</td></tr>\n";
    
      $output .= "</table>\n";
    
      $output .= $this->editList("Sources:", "source" , "Page", $event->getPages());
      $output .= $this->displayEditList("Event for these People:", "Person", $event->getPeople());
      $output .= $this->displayEditList("Event for these Families:", "Family", $event->getFamilies());
      return $output;
    }
    
    public function processActionSave($event)
    {
      $event->updateTitle($_REQUEST['title']);
      $event->updateDatePrefix($_REQUEST['date_prefix']);
      $event->updateDate(
        $_REQUEST['date_year'],
        $_REQUEST['date_month'],
        $_REQUEST['date_day']);
      if (isset($_REQUEST['date_year_exact']) && $_REQUEST['date_year_exact'] == 'enabled')
      {
        $event->enableYearExact();
      }
      else
      {
        $event->disableYearExact();
      }
      if (isset($_REQUEST['date_month_exact']) && $_REQUEST['date_month_exact'] == 'enabled')
      {
        $event->enableMonthExact();
      }
      else
      {
        $event->disableMonthExact();
      }
      if (isset($_REQUEST['date_day_exact']) && $_REQUEST['date_day_exact'] == 'enabled')
      {
        $event->enableDayExact();
      }
      else
      {
        $event->disableDayExact();
      }
      $enable = false;
      if (isset($_REQUEST['map_enable']) && $_REQUEST['map_enable'] == 'enabled')
      {
        $enable = true;
      }
      // lat and lon need to be set before enable and name
      // for auto locator to properly update lat and lon!
      $event->updateMapData(
        $enable,
        $_REQUEST['map_latitude'],
        $_REQUEST['map_longitude'],
        $_REQUEST['map_zoom']);
      $event->updateLocationName($_REQUEST['location_name']);
      $event->updateLocationDescription($_REQUEST['location_description']);
      $event->updateText($_REQUEST['text']);
      $event->saveUpdates();
    }

    public function processAction()
    {
      $event = NULL;
      list($action, $type, $id) = preg_split("/:/" , $_REQUEST['action']) + array('', '', '');
      switch ($action) 
      {
        case "delete":
          $event = Event::Get($_REQUEST['save']);
          switch ($type)
          {
            case "source":
              $event->deletePages(Page::Get($id));
              break;
          }
          break;            
        case "add":
          $event = Event::Get($_REQUEST['save']);
          switch ($type)
          {
            case "source":
              $event->addPages(Page::Get($_REQUEST['addsource']));
              break;
          }
          break;            
        case "new":
          $event = Event::Get($_REQUEST['save']);
          switch ($type)
          {
            case "source":
              $page = Page::Make();
              $event->addPages($page);
              header("Location: ".$page->getEditURL(), true, 303);
              exit();
              break;
          }
          break;            
      }
      return $event;
    }
  }
  
  $page = new EditorEventWebPage('Event');
  echo $page->getWebPage();
?>