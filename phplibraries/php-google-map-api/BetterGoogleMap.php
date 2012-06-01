<?php
  include_once dirname(__FILE__)."/GoogleMap.php";
  class BetterGoogleMapAPI extends GoogleMapAPI
  {

    private $listeners = array();
    private $onloadcode = array();
    const YAHOO_APP_ID = 'ViwA7P74';
  
    public function geoGetCoords($address,$depth=0)
    {
      $coords = array();
      $url = sprintf('http://where.yahooapis.com/geocode?appid=%s&q=%s',self::YAHOO_APP_ID, rawurlencode($address));
      $result = false;
      if($result = $this->fetchURL($url))
      {
        preg_match('!<latitude>(.*)</latitude><longitude>(.*)</longitude>!U', $result, $match);
        $coords['lon'] = $match[2];
        $coords['lat'] = $match[1];
      }
      return $coords;       
    }

    public function addListener($type, $function)
    {
      array_push($this->listeners, array('type' => $type, 'function' => $function));
    }

    public function addOnLoadCode($code)
    {
      array_push($this->onloadcode, $code);
    }
  
    public function getMapJS()
    {
      $output = parent::getMapJS();
      $output .= "<script type=\"text/javascript\" charset=\"utf-8\">\n";
      $output .= "//<![CDATA[\n";
      $output .= "function ".$this->getOnLoadFunction()."() {\n";
      $output .= "  ".parent::getOnLoadFunction()."();\n";
      foreach ($this->listeners as $listener)
      {
        $output .= "  google.maps.event.addListener(map".$this->map_id.", '".$listener['type']."', function(event) {\n";
        $output .= $listener['function'];
        $output .= "  });\n";
      }
      foreach ($this->onloadcode as $onloadcode)
      {
        $output .= $onloadcode;
        $output .= "\n";
      }
      $output .= "}\n";
      $output .= "//]]>\n";
      $output .= "</script>\n";
      return $output;
    }
  
    function getOnLoad()
    {
        return '<script language="javascript" type="text/javascript" charset="utf-8">window.onload='.$this->getOnLoadFunction().';</script>';
    }

    function getOnLoadFunction()
    {
      return parent::getOnLoadFunction() . 'better';
    }  
  }
?>