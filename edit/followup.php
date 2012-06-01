<?php

  include_once dirname(__FILE__)."/../phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/../phplibraries/genealogy/gsql-includes.php";

  class FollowupWebPage extends WebPageAbstract
  {    
    public function getTitle()
    {
      return "Genealogy Editor: Items for Followup";
    }

    public function getBodyContent()
    {
      $output = "<h1>".$this->getTitle()."</h1>\n";
      $for_followup_types = array();
      $for_followup_items = array();
      foreach (array_merge(Person::GetAll(), Family::GetAll(), Event::GetAll(), Document::GetAll(), Page::GetAll()) as $item)
      {
        $followup = $item->getFollowup();
        if ($followup == '' && $item->getFollowupDetail() != '')
        {
          $followup = 'Other';
        }
        if ($followup != '')
        {
          if (!(isset($for_followup_items[$followup])))
          {
            $for_followup_items[$followup] = array();
            array_push($for_followup_types, $followup);
          }
          array_push($for_followup_items[$followup], $item);
        }
      }
  
      natcasesort($for_followup_types);

      foreach ($for_followup_types as $type)
      {
        $output .= "<p> \n";
        $output .= $type.":<ul>\n";
        foreach ($for_followup_items[$type] as $item)
        {
          $output .= "<li>\n";
          $output .= get_class($item);
          $output .= ": " . $item->getEditLink() . "<br />\n";
          $output .= "<i>".$item->getFollowupDetail()."</i>\n";
          $output .= "</li>\n";
        }
        $output .= "</ul></p>\n";
      }
      return $output;
    }    
  }
  
  $page = new FollowupWebPage();
  echo $page->getWebPage();
?>