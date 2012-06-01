<?php

  include_once dirname(__FILE__)."/../phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/../phplibraries/genealogy/gsql-includes.php";

  class EditorBulkCiteWebPage extends WebPageAbstract
  {        
    private $document;
    
    private $note;
    
    public function __construct()
    {
      if (isset($_REQUEST['document']))
      {
        $this->document = Document::Get($_REQUEST['document']);        
      }
      if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'save')
      {
        $this->note = "save <p /><ul>\n";
        foreach($_REQUEST as $request => $data)
        {
          if(preg_match('/^doctitle:(?P<id>.*)$/', $request, $matches))
          {
            $document = Document::Get($matches['id']);
            $document->updateTitle($data);
            $document->saveUpdates();
            $this->note .= "<li>";
            $this->note .= $document->getID() . "-D: title: " . $data;
            $this->note .= "</li>\n";
          }
          if(preg_match('/^doccite:(?P<id>.*)$/', $request, $matches))
          {
            $document = Document::Get($matches['id']);
            $document->updateCitation($data);
            $document->saveUpdates();
            $this->note .= "<li>";
            $this->note .= $document->getID() . "-D: cite: " . $data;
            $this->note .= "</li>\n";
          }

          if(preg_match('/^pagetitle:(?P<id>.*)$/', $request, $matches))
          {
            $page = Page::Get($matches['id']);
            $page->updatePageTitle($data);
            $page->saveUpdates();
            $this->note .= "<li>";
            $this->note .= $page->getID() . "-P: title: " . $data;
            $this->note .= "</li>\n";
          }
          if(preg_match('/^pagecite:(?P<id>.*)$/', $request, $matches))
          {
            $page = Page::Get($matches['id']);
            $page->updatePageCitation($data);
            $page->saveUpdates();
            $this->note .= "<li>";
            $this->note .= $page->getID() . "-P: cite: " . $data;
            $this->note .= "</li>\n";
          }

        $this->note .= "</ul>\n";
        }
      }
    }
  
    public function getJavascriptFunctions()
    {
      $output = "function changeit(element, value)\n{\n";
      $output .= "  e=document.getElementsByName(element);\n";
      $output .= "  e[0].value = value;\n";
      $output .= "}\n";
      return $output;
    }

    public function getTitle()
    {
      return "Genealogy Editor: Bulk Document Citation";
    }

    public function getBodyContent()
    {
      $output = "<h1>".$this->getTitle()."</h1>\n";

//      $output .= $this->note;

      $output .= "<form name=editor action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
      
      $output .= Document::GetSelectList("document", Document::GetAll(), array(), $this->document);
      $output .= "\n<input type=submit name=action value=\"open\">\n";
      $output .= "<input type=submit name=action value=\"save\"><p />\n";

      if (isset($this->document))
      {
        $output .= "<h3>Document</h3>\n";

        $output .= "<input name=\"doctitle:".$this->document->getID()."\" size=70 value=\"" . htmlentities($this->document->getTitle(), ENT_QUOTES) . "\">\n";
        $output .= "<a href=\"".$this->document->getEditURL()."\">e</a><br />\n";
        $output .= "<textarea name=\"doccite:".$this->document->getID()."\" rows=6 style=\"width:50%;\">\n";
        $output .= htmlentities($this->document->getCitation(), ENT_QUOTES);
        $output .= "</textarea>\n";
        $output .= "<h3>Pages</h3>\n";
      
        foreach ($this->document->getPages() as $page)
        {
          $output .= "<p><input name=\"pagetitle:".$page->getID()."\" size=70 value=\"" . htmlentities($page->getPageTitle(), ENT_QUOTES) . "\">\n";
          $output .= "<a href=\"".$page->getEditURL()."\">e</a>";
          $output .= $this->getReplacementButton($page);
          $output .= "<br />\n";
          $output .= "<textarea name=\"pagecite:".$page->getID()."\" rows=6 style=\"width:50%;\">\n";
          $output .= htmlentities($page->getPageCitation(), ENT_QUOTES);
          $output .= "</textarea></p>\n";
        }
      }
      return $output;    
    }
    
    private function getReplacementButton($item)
    {
      $output = '';
      list($news, $gb, $year, $mon, $day, $pagen) = split("-", $item->getPageTitle(), 6) + Array('', '', '', '', '', '');
      if ($news == 'newspaper')
      {
        $months = array(
          'jan' => 'January',
          'feb' => 'Febuary',
          'mar' => 'March',
          'apr' => 'April',
          'may' => 'May',
          'jun' => 'June',
          'jul' => 'July',
          'aug' => 'August',
          'sep' => 'September',
          'oct' => 'October',
          'nov' => 'November',
          'dec' => 'December');
        $output .= "<button type=\"button\" onclick=\"";
        $output .="changeit('pagetitle:".$item->getID()."', '".$months[$mon]." $day, $year page ".rtrim(ltrim($pagen,'page'),'.jpg')."');";
        $output .="changeit('pagecite:".$item->getID()."', '$day ".ucwords($mon)." $year page ".rtrim(ltrim($pagen,'page'),'.jpg')."');";
        $output .="\">change</button>";
      }
      return $output;
    }
  }
  
  $page = new EditorBulkCiteWebPage();
  echo $page->getWebPage();
?>