<?php
/**
 * event_class.php
 * 
 * @author Christopher Poole <poolecl@yahoo.ca>
 * @version .01
 * @package genealogy
 */
  include_once dirname(__FILE__)."/gsql-includes.php";
  include_once dirname(__FILE__)."/../php-google-map-api/BetterGoogleMap.php";

  /**
   * @author Christopher Poole <poolecl@yahoo.ca>
   * @version .01
   * @package genealogy
   */
  class Event extends GenealogyAbstract
  {
    const DISPLAY_URL = "/events.php?";
    const EDIT_URL = "/edit/events.php?action=open&open=";

    const TABLE_NAME = 'Events';
    const TABLE_KEY = 'EventId';

    const DATE_PREFIX_NORMAL = 'normal';
    const DATE_PREFIX_ABOUT = 'about';
    const DATE_PREFIX_UNKNOWN = 'unknown';

    const MAP_ZOOM_AUTO = -1;

    protected $cache_fields = array('Title',
      'DatePrefix',
      'Date',
      'YearExact',
      'MonthExact',
      'DayExact',
      'LocationName',
      'MapEnable',
      'MapLatitude',
      'MapLongitude',
      'MapZoom');
    private $data = array();
    
    protected static function GetNew($id)
    {
      return new Event($id);
    }

    public function getTitle()
    {
      return $this->getField('Title');
    }
    public function updateTitle($title)
    {
      $this->updateField('Title', $title);
    }

    public function getText()
    {
      return $this->getField('Text');
    }
    public function updateText($text)
    {
      $this->updateField('Text', $text);
    }

    public function getDatePrefix()
    {
      return $this->getField('DatePrefix');
    }
    public function updateDatePrefix($dateprefix)
    {
      $this->updateField('DatePrefix', $dateprefix);
    }

    public function getYear()
    {
      return intval(substr($this->getField('Date'), 0, 4));
    }
    public function isYearExact()
    {
      return $this->getField('YearExact');
    }
    public function enableYearExact()
    {
      $this->updateField('YearExact', true);
    }
    public function disableYearExact()
    {
      $this->updateField('YearExact', false);
    }

    public function getMonthNumber()
    {
      return intval(substr($this->getField('Date'), 5, 2));
    }
    public static function GetMonthNameFromNumber($number)
    {
      $monthnames = array(
         1 => 'Jan',
         2 => 'Feb',
         3 => 'Mar',
         4 => 'Apr',
         5 => 'May',
         6 => 'Jun',
         7 => 'Jul',
         8 => 'Aug',
         9 => 'Sep',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dec');
      return $monthnames[$number];
    }
    public function getMonthName()
    {
      return $this->GetMonthNameFromNumber[$this->getMonthNumber()];
    }
    public function isMonthExact()
    {
      return $this->getField('MonthExact');
    }
    public function enableMonthExact()
    {
      $this->updateField('MonthExact', true);
    }
    public function disableMonthExact()
    {
      $this->updateField('MonthExact', false);
    }

    public function getDay()
    {
      return intval(substr($this->getField('Date'), 8, 2));
    }
    public function isDayExact()
    {
      return $this->getField('DayExact');
    }
    public function enableDayExact()
    {
      $this->updateField('DayExact', true);
    }
    public function disableDayExact()
    {
      $this->updateField('DayExact', false);
    }
    
    public function updateDate($year, $month, $day)
    {
      $this->updateField('Date', sprintf("%04d-%02d-%02d", $year, $month, $day));
    }

    public function getDateString()
    {
      return Event::BuildDateString($this->getDatePrefix(),
        $this->getYear(), $this->getMonthNumber(), $this->getDay(),
        $this->isYearExact(), $this->isMonthExact(), $this->isDayExact());
    }
    public static function BuildDateString($prefix, $year, $month, $day, $year_exact = true, $month_exact = true, $day_exact = true)
    {
      $class = get_called_class();
      $output = "";
      if ($day_exact)
      {
        $output .= $day." ";
      }
      if ($month_exact)
      {
        $output .= $class::GetMonthNameFromNumber($month)." ";
      }
      if ($year_exact)
      {
        $output .= $year." ";
      }

      switch ($prefix)
      {
        case Event::DATE_PREFIX_UNKNOWN:
          return 'unknown';
          break;
        case Event::DATE_PREFIX_ABOUT:
          return 'about ' . $output;
          break;
        default:
          return $output;
          break;
      }
    }


    public function getDateValue()
    {
      return gregoriantojd($this->getMonthNumber(), $this->getDay(), $this->getYear());
    }

    public function getLocationName()
    {
      return $this->getField('LocationName');
    }
    public function updateLocationName($name)
    {
      $this->updateField('LocationName', $name);
      $this->checkForLatitudeLongitue();
    }
    public function getLocationDescription()
    {
      return $this->getField('LocationDescription');
    }
    public function updateLocationDescription($description)
    {
      $this->updateField('LocationDescription', $description);
    }

    public function isMapEnable()
    {
      return $this->getField('MapEnable');
    }
    public function getMapLatitude()
    {
      return $this->getField('MapLatitude');
    }
    public function getMapLongitude()
    {
      return $this->getField('MapLongitude');
    }
    public function getMapZoom()
    {
      return $this->getField('MapZoom');
    }

    public function updateMapData($enable, $latitude, $longitude, $zoom)
    {
      $this->updateMapCoordinates($latitude, $longitude);
      $this->updateFieldBoolean('MapEnable', $enable);
      $this->updateMapZoom($zoom);
      $this->checkForLatitudeLongitue();

    }
    public function enableMap()
    {
      $this->updateFieldBoolean('MapEnable', true);
      $this->checkForLatitudeLongitue();
    }
    public function disableMap()
    {
      $this->updateFieldBoolean('MapEnable', false);
    }
    public function updateMapCoordinates($latitude, $longitude)
    {
      $this->updateField('MapLatitude', $latitude, NULL);
      $this->updateField('MapLongitude', $longitude, NULL);
    }
    public function updateMapZoom($zoom)
    {
      if ($zoom == 'auto')
      {
        $this->updateField('MapZoom', Event::MAP_ZOOM_AUTO);
      }
      else
      {
        $this->updateField('MapZoom', $zoom, Event::MAP_ZOOM_AUTO);
      }
    }

    private function checkForLatitudeLongitue()
    {
      if ($this->isMapEnable() && $this->getLocationName() != "" && ($this->getMapLatitude() == "" || $this->getMapLongitude() == ""))
      {
        // if we have a location name and are enabled but no lat/lon coordinates, lets fetch some!
        $map = new GoogleMapAPI();
        $coord = $map->getGeocode($this->getLocationName());
        if (isset($coord['lat']) && isset($coord['lon']))
        {
          $this->updateMapCoordinates($coord['lat'], $coord['lon']);
        }
      }     
    }
    public function hasMap()
    {
      return ($this->isMapEnable() && ($this->getLocationName() != "" || ($this->getMapLatitude() == "" && $this->getMapLongitude() == "")));
    }

    public static function GetEventListMap($list, $name='map', $width="500px", $height="500px")
    {
      $map = new BetterGoogleMapAPI($name);
      
      $map->setMapType('ROADMAP');
      $map->setWidth($width);
      $map->setHeight($height);
      $list = Event::SortList($list);
      foreach ($list as $item)
      {
        if ($item->hasMap())
        {
          if ($item->getMapLatitude() == "" || $item->getMapLongitude() == "")
          {
            $map->addMarkerByAddress($item->getLocationName(), "<p><b>".$item->getDisplayLink()."</b><br />".$item->getLocationName()."</p>");
          }
          else
          {
            $map->addMarkerByCoords($item->getMapLongitude(), $item->getMapLatitude(), "<p><b>".$item->getDisplayLink()."</b><br />".$item->getLocationName()."</p>");
          }
        }
      }
      $map->disableStreetViewControls();
      return $map;
    }

    public function getMap($name='map', $width="500px", $height="500px", $is_editor = false)
    {
      $map = new BetterGoogleMapAPI($name);
      
      $map->setMapType('ROADMAP');
      $map->setWidth($width);
      $map->setHeight($height);
      if ($this->getMapZoom() != Event::MAP_ZOOM_AUTO)
      {
        $map->setZoomLevel($this->getMapZoom());
      }
      if ($this->getMapLatitude() == "" || $this->getMapLongitude() == "")
      {
        $map->addMarkerByAddress($this->getLocationName(), "<b>".$this->getLocationName()."</b><p />".$this->getLocationDescription());
      }
      else
      {
        $map->addMarkerByCoords($this->getMapLongitude(), $this->getMapLatitude(), 
          str_replace("\n", "", str_replace("\r", "", 
            "<b>".$this->getLocationName()."</b><p />".Event::FormatForHTML($this->getLocationDescription())
          ))
        );
      }
      $map->disableStreetViewControls();
      if ($is_editor)
      {
        $jsfunc  = "    document.editor.".$name."_latitude.value = event.latLng.lat();\n";
        $jsfunc .= "    document.editor.".$name."_longitude.value = event.latLng.lng();\n";
        $map->addListener("click", $jsfunc);
        $jsfunc  = "    document.editor.".$name."_zoom.value = map".$name.".getZoom();\n";
        $map->addListener("zoom_changed", $jsfunc);
      }
      return $map;
    }
    
    public function getPages()
    {
      if (!isset($this->data['Pages']))
      {
        $query = "SELECT PageId FROM EventsPages WHERE EventId=".$this->getID();
        $this->data['Pages'] = $this->GetQueryClassList($query, 'Page', 'PageId');
      }
      return $this->data['Pages'];
    }
    public function containsPages($page)
    {
      return in_array($page, $this->getPages(), true);
    }
    public function addPages($page)
    {
      if (isset($this->data['Pages'])) $this->data['Pages'][] = $page;
      $query = "INSERT INTO EventsPages (EventId, PageId) VALUES (".$this->getID().", ".$page->getID().")";
      $this->GetQuery($query);
    }
    public function deletePages($page)
    {
      if (isset($this->data['Pages'])) array_diff($this->data['Pages'], array($page));
      $query = "DELETE FROM EventsPages WHERE EventId=".$this->getID()." AND PageId=".$page->getID();
      $this->GetQuery($query);
    }
   
    public function getPeople()
    {
      if (!isset($this->data['People']))
      {
        $query = "SELECT PersonId FROM PeopleEvents WHERE EventId=".$this->getID();
        $events = $this->GetQueryClassList($query, 'Person', 'PersonId');

        $query = "SELECT PersonId FROM People WHERE BirthEventId=".$this->getID()." OR DeathEventId=".$this->getID();
        $birthdeath = $this->GetQueryClassList($query, 'Person', 'PersonId');

        $this->data['People'] = array_unique(array_merge($events, $birthdeath));
      }
      return $this->data['People'];
    }
    public function getFamilies()
    {
      if (!isset($this->data['Families']))
      {
        $query = "SELECT FamilyId FROM FamiliesEventsChildren WHERE EventId=".$this->getID();
        $child = $this->GetQueryClassList($query, 'Family', 'FamilyId');

        $query = "SELECT FamilyId FROM FamiliesEventsParents WHERE EventId=".$this->getID();
        $parent = $this->GetQueryClassList($query, 'Family', 'FamilyId');

        $query = "SELECT FamilyId FROM Families WHERE MarriageEventId=".$this->getID()." OR DivorceEventId=".$this->getID();
        $family = $this->GetQueryClassList($query, 'Family', 'FamilyId');

        $this->data['Families'] = array_unique(array_merge($child, $parent, $family));
      }
      return $this->data['Families'];
    }
    
    public function getDisplayLink()
    {
      return "<i>".$this->getDateString()."</i> - ".parent::getDisplayLink();
    }
    public function getEditLink()
    {
      return "<i>".$this->getDateString()."</i> - ".parent::getEditLink();
    }

    protected static function ClassCompare($a, $b)
    {
      $adatevalue = $a->getDateValue();
      $bdatevalue = $b->getDateValue();
      if ($adatevalue == $bdatevalue) {
        return 0;
      }
      return ($adatevalue < $bdatevalue) ? -1 : 1;
    }
  }
?>