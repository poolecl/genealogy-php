<?php

  include_once dirname(__FILE__)."/../phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/../phplibraries/genealogy/gsql-includes.php";

  class EditorAllWebPage extends WebPageAbstract
  {        
    public function __construct()
    {
      foreach($_REQUEST as $pagestr => $documentid)
      {
        if(preg_match('/^page:(?P<page>.*)$/', $pagestr, $matches))
        {
          $pageid = $matches['page'];          
          $page = Page::Get($pageid);
          $page->updateDocument(Document::Get($documentid));
          $page->saveUpdates();
        }
      }
    }
 
    public function getTitle()
    {
      return "Genealogy Editor: Bulk Page Document Selector";
    }

    public function getBodyContent()
    {
      $output = "<h1>".$this->getTitle()."</h1>\n";

      $output .= "<form name=editor action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
      $output .= "<input type=submit name=action value=\"save\"><p />\n";

      $all = Page::GetAll();
      natcasesort($all);
      foreach ($all as $item)
      {
        $output .= Document::GetSelectList("page:".$item->getID(), Document::GetAll(), array(), $item->getDocument());
        $output .= " " . $item->getPageTitle();
        $output .= "<br />\n";
      }
      $output .= "</ul></p>\n";
      return $output;
    }
  }
  
  $page = new EditorAllWebPage();
  echo $page->getWebPage();
?>