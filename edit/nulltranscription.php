<?php

  include_once dirname(__FILE__)."/../phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/../phplibraries/genealogy/gsql-includes.php";

  class EditorNullTranscriptionWebPage extends WebPageAbstract
  {
    public function getTitle()
    {
      return "Genealogy Editor: Untranscribed Items";
    }

    public function getBodyContent()
    {
      $output = "<h1>".$this->getTitle()."</h1>\n";
      $output .= "<ul>\n";
      $all = Page::GetUntranscribed();
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
      $output .= "</ul>\n";
      return $output;
    }
  }
  
  $page = new EditorNullTranscriptionWebPage();
  echo $page->getWebPage();
?>