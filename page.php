<?php

  include_once dirname(__FILE__)."/phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/phplibraries/genealogy/gsql-includes.php";
  include_once dirname(__FILE__)."/phplibraries/auth.php";

  class DisplaySourcePageWebPage extends DisplayWebPage
  {
    const DEFAULT_TITLE = "Genealogy Page Viewer";
    
    public function getBodyContentItem()
    {
      $output = "<h1>" . $this->getItem()->getTitle() . "</h1>\n";
      if (isAuthorized())
      {
        $output .= "<a href=\"" . $this->getItem()->getEditURL() . "\">e</a>\n";
      }
      $output .= "<p>" . $this->getItem()->FormatForHTML($this->getItem()->getTranscription()) . "</p>\n";
      $output .= "<p><i>" . $this->getItem()->FormatForHTML($this->getItem()->getCitation()) . "</i></p>\n";
      $output .= $this->getItem()->getImage();
      return $output;
    }
  }
  
  $page = new DisplaySourcePageWebPage ('Page');
  echo $page->getWebPage();
?>