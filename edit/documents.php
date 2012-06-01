<?php

  include_once dirname(__FILE__)."/../phplibraries/html/editor_html_class.php";

  class EditorDocumentWebPage extends EditorWebPage
  {
    const DISPLAY_NAME = 'Document';
    
    public function getItemCustomContent()
    {
      $document = $this->getItem();
    
      //title
      $output = "title <input name=title size=70 value=\"" . htmlentities($document->getTitle(), ENT_QUOTES) . "\">\n";
      $output .= "<input type=submit name=action value=\"save\"></h2>\n";    
      $output .= "<p />\n";

      $output .= "<table border=0 width=\"100%\">\n";
    
      //citation
      $output .= "<tr><td colspan=2>citation</td></tr>\n";
      $output .= "<tr><td width=\"50%\" valign=top>\n";
      $output .= "<textarea name=\"src\" rows=6 style=\"width:100%;\">\n";
      $output .= htmlentities($document->getCitation(), ENT_QUOTES);
      $output .= "</textarea>\n";
      $output .= "</td>";
      $output .= "<td width=\"50%\" valign=top>\n";
      $output .= $document->FormatForHTML($document->getCitation());
      $output .= "</td></tr>\n";

      //note
      $output .= "<tr><td colspan=2>note</td></tr>\n";
      $output .= "<tr><td width=\"50%\" valign=top>\n";
      $output .= "<textarea name=\"note\" rows=5 style=\"width:100%;\">\n";
      $output .= htmlentities($document->getNote(), ENT_QUOTES);
      $output .= "</textarea>\n";
      $output .= "</td>";
      $output .= "<td width=\"50%\" valign=top>\n";
      $output .= $document->FormatForHTML($document->getNote());
      $output .= "</td></tr>\n";
    
      $output .= "</table>\n";

      $output .= $this->displayEditList('Pages:', 'Page', $document->getPages());
      $output .= "<ul><li><button type=submit name=action value=\"new:page\">new page</button></li></ul>\n";
      return $output;
    }
    public function processActionSave($document)
    {
      $document->updateTitle($_REQUEST['title']);
      $document->updateCitation($_REQUEST['src']);
      $document->updateNote($_REQUEST['note']);
      $document->saveUpdates();
    }

    public function processAction()
    {
      $document = NULL;
      list($action, $type, $id) = preg_split("/:/" , $_REQUEST['action']) + array('', '', '');
      switch ($action) 
      {
        case "new":
          $document = Document::Get($_REQUEST['save']);
          switch ($type)
          {
            case "page":
              $page = Page::Make();
              $page->updateDocument($document);
              $page->saveUpdates();
              header("Location: ".$page->getEditURL(), true, 303);
              exit();
              break;
          }
          break;            
      }
      return $document;
    }
  }
  
  $page = new EditorDocumentWebPage('Document');
  echo $page->getWebPage();
?>