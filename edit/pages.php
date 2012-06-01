<?php

  include_once dirname(__FILE__)."/../phplibraries/html/editor_html_class.php";
  include_once dirname(__FILE__)."/../phplibraries/globals_oo.php";

  class EditorPageWebPage extends EditorWebPage
  {
    const DISPLAY_NAME = 'Source Page';
    
    public function getJavascriptFunctionRestrict()
    {
      $type = $this::DISPLAY_NAME;
      $item = $this->getItem();

      $output = "function restrict_change(choice)\n{\n";
      $output .= "  restrict_change_document(choice);\n";
      $output .= "  restrict_change_page();\n";
      $output .= "}\n";
      $output .= "function restrict_change_page()\n{\n";

      $output .= "  var restrict = 'all';\n";
      $output .= "  for(var i=0; i < document.editor.restrict; i++)\n    {\n";
      $output .= "    if(document.editor.restrict[i].checked)\n      {\n";
      $output .= "      restrict = document.editor.restrict[i].value;\n";
      $output .= "    }\n";
      $output .= "  }\n";
      $output .= "  switch(restrict)\n  {\n";
      $output .= "    case 'all':\n";
      $output .= "      restrict_change_page_all(document.editor.document_open.value);\n";
      $output .= "      break;\n";
      $output .= "    case 'reviewed':\n";
      $output .= "      restrict_change_page_reviewed(document.editor.document_open.value);\n";
      $output .= "      break;\n";
      $output .= "    case 'unreviewed':\n";
      $output .= "      restrict_change_page_unreviewed(document.editor.document_open.value);\n";
      $output .= "      break;\n";
      $output .= "  }\n}\n";

      $pages = array();
      foreach (Document::GetAll() as $document)
      {
        $pages[$document->getID()] = $document->getPages();
      }
      $output .= javascript_select_change_function('restrict_change_page_all', 'document.editor.open', $pages, $item);
  
      $pages = array();
      foreach (Document::GetReviewed(Document::GetAll()) as $document)
      {
        $pages[$document->getID()] = Page::GetReviewed($document->getPages());
      }
      $output .= javascript_select_change_function('restrict_change_page_reviewed', 'document.editor.open', $pages, $item);

      $pages = array();
      foreach (Document::GetUnreviewed(Document::GetAll()) as $document)
      {
        $pages[$document->getID()] = Page::GetUnreviewed($document->getPages());
      }
      $output .= javascript_select_change_function('restrict_change_page_unreviewed', 'document.editor.open', $pages, $item);
            
      $output .= javascript_select_change_function('restrict_change_document', 'document.editor.document_open',
        array('all'=> Document::SortList(Document::GetAll()),
              'reviewed'=> Document::SortList(Document::GetReviewed()),
              'unreviewed'=> Document::SortList(Document::GetUnreviewed())), $item);
      return $output;
    }

    public function getItemCustomContent()
    {
      $page = $this->getItem();
    
      //document
      $output = "<a href=\"" . $page->getDocument()->getEditURL() . "\">part of</a> ";
      $output .= Document::GetSelectList("document", Document::GetAll(), array(), $page->getDocument());
      $output .= " <input type=submit name=action value=\"change\">\n";     
      $output .= " <button type=submit name=action value=\"newdocument\">new</button><p />\n";     
    
      //title
      $output .= "document title <input name=document_title size=70 value=\"" . htmlentities($page->getDocumentTitle(), ENT_QUOTES) . "\"><br />\n";
      $output .= "page title <input name=page_title size=70 value=\"" . htmlentities($page->getPageTitle(), ENT_QUOTES) . "\">\n";
      $output .= "<input type=submit name=action value=\"save\"></h2>\n";    
      $output .= "<p />\n";

      $output .= "<table border=0 width=\"100%\">\n";
    
      //transcription
      $edit = "<textarea name=\"text\" rows=8 style=\"width:100%;\">\n";
      $edit .= htmlentities($page->getTranscription(), ENT_QUOTES);
      $edit .= "</textarea>\n";
      $preview = $page->FormatForHTML($page->getTranscription());
      $output .= $this->getEditorTableCells("transcription", $edit, $preview);

      //citation
      $edit = "<textarea name=\"page_src\" rows=6 style=\"width:100%;\">\n";
      $edit .= htmlentities($page->getPageCitation(), ENT_QUOTES);
      $edit .= "</textarea>\n";
      $edit .= "document citation<br />\n";
      $edit .= "<textarea name=\"document_src\" rows=2 style=\"width:100%;\">\n";
      $edit .= htmlentities($page->getDocumentCitation(), ENT_QUOTES);
      $edit .= "</textarea>\n";
      $preview = "<i>".$page->getCitation()."</i>";
      $output .= $this->getEditorTableCells("page citation", $edit, $preview);

      //page note
      $edit = "<textarea name=\"page_note\" rows=5 style=\"width:100%;\">\n";
      $edit .= htmlentities($page->getPageNote(), ENT_QUOTES);
      $edit .= "</textarea>\n";
      $preview = $page->FormatForHTML($page->getPageNote());
      $output .= $this->getEditorTableCells("page note", $edit, $preview);
    
      //document note
      $edit = "<textarea name=\"document_note\" rows=2 style=\"width:100%;\">\n";
      $edit .= htmlentities($page->getDocumentNote(), ENT_QUOTES);
      $edit .= "</textarea>\n";
      $preview = $page->FormatForHTML($page->getDocumentNote());
      $output .= $this->getEditorTableCells("document note", $edit, $preview);

      $output .= "</table>\n";

      $output .= $this->editList("Cited in these Events:", "events", "Event", $page->getEvents());

      $output .= $page->getImage();
      return $output;
    }
    
    public function processActionSave($page)
    {
      $page->updateTranscription($_REQUEST['text']);
      $page->updatePageTitle($_REQUEST['page_title']);
      $page->updateDocumentTitle($_REQUEST['document_title']);
      $page->updatePageCitation($_REQUEST['page_src']);
      $page->updateDocumentCitation($_REQUEST['document_src']);
      $page->updatePageNote($_REQUEST['page_note']);
      $page->updateDocumentNote($_REQUEST['document_note']);
      $page->saveUpdates();
    }

    public function processAction()
    {
      if ($_REQUEST['action'] == "change")
      {
        $page = Page::Get($_REQUEST['save']);
        $page->updateDocument(Document::Get($_REQUEST['document']));
        $page->saveUpdates();
      }
      elseif ($_REQUEST['action'] == "newdocument")
      {
        $page = Page::Get($_REQUEST['save']);
        $document = Document::Make();
        $page->updateDocument($document);
        $page->saveUpdates();
        header("Location: ".$document->getEditURL(), true, 303);
        exit();
      }
      else
      {
        list($action, $type, $id) = preg_split("/:/" , $_REQUEST['action']) + array('', '', '');
        switch ($action) 
        {
          case "delete":
            $page = Page::Get($_REQUEST['save']);
            switch ($type)
            {
              case "events":
                $page->deleteEvents(Event::Get($id));
                break;
            }
            break;            
          case "add":
            $page = Page::Get($_REQUEST['save']);
            switch ($type)
            {
              case "events":
                $page->addEvents(Event::Get($_REQUEST['addevents']));
                break;
            }
            break;            
          case "new":
            $page = Page::Get($_REQUEST['save']);
            switch ($type)
            {
              case "events":
                $event = Event::Make();
                $page->addEvents($event);
                header("Location: ".$event->getEditURL(), true, 303);
                exit();
                break;
            }
            break;            
        }
      }
      return $page;
    }
  }
  
  $page = new EditorPageWebPage('Page');
  echo $page->getWebPage();
?>