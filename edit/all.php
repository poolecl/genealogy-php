<?php

  include_once dirname(__FILE__)."/../phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/../phplibraries/genealogy/gsql-includes.php";

  class EditorAllWebPage extends WebPageAbstract
  {
    public function getTitle()
    {
      return "Genealogy Editor: All Items";
    }

    public function getBodyContent()
    {
      $output = "<h1>".$this->getTitle()."</h1>\n";
      foreach(array("Person", "Family", "Event", "Document", "Page") as $class)
      {
        $output .= "<h2>".$class."</h2>\n";
        $output .= "<p><ul>\n";
    
        $all = $class::GetAll();
        natcasesort($all);
        foreach ($all as $item)
        {
          $output .= "<li>";
          $output .= "<a href=\"" . $item->getEditURL() . "\">" . $item->getTitle() . "</a>";
          if ($item->getFollowup() != '' || $item->getFollowupDetail() != '')
          {
            $output .= "\n<br /><i>";
            if ($item->getFollowup() != '')
            {
              $output .= $item->getFollowup() . ": ";
            }
            $output .= $item->getFollowupDetail()."</i>\n";
          }
          $output .= "</li>\n";
        }
        $output .= "</ul></p>\n";
      }
      return $output;
    }
  }
  
  $page = new EditorAllWebPage();
  echo $page->getWebPage();
?>