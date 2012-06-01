<?php

  include_once dirname(__FILE__)."/../phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/../phplibraries/genealogy/gsql-includes.php";

  class EditorNullTranscriptionWebPage extends WebPageAbstract
  {
    public function __construct()
    {
      if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save')
      {
        $page = Page::Get($_REQUEST['page']);
        $page->updateTranscription($_REQUEST['text']);
        $page->saveUpdates();
        header("Location: ".$page->getEditURL(), true, 303);
        exit();
      }
    }

    public function getTitle()
    {
      return "Genealogy Editor: Random Transcription";
    }

    public function getBodyContent()
    {
      $output = "<h1>".$this->getTitle()."</h1>\n";
      
      $output .= "<form name=editor action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";

      $all = Page::GetUntranscribed();
      $item = $all[array_rand($all)];
      
      $output .= "<input name=\"page\" type=\"hidden\" value=\"".$item->getID()."\" />\n";

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
      $output .= "<input type=submit name=action value=\"save\"><p />\n";      

      $output .= "<table width=\"100%\"><tr><td width=\"50%\" valign=\"top\">";
      //transcription
      $output .= "<textarea name=\"text\" rows=8 style=\"width:100%;\">\n";
      $output .= htmlentities($item->getTranscription(), ENT_QUOTES);
      $output .= "</textarea>\n";

      $output .= "</td><td width=\"50%\" valign=\"top\">";
     
      $output .= $item->getImage();
      $output .= "</td></tr></table>";
            
      return $output;
    }
  }
  
  $page = new EditorNullTranscriptionWebPage();
  echo $page->getWebPage();
?>