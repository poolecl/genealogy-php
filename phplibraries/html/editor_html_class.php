<?php

  include_once dirname(__FILE__)."/html_class.php";
  include_once dirname(__FILE__)."/../globals_oo.php";
  

  abstract class EditorWebPage extends WebPageAbstract
  {
    private $item;
    private $class;
    
    const DISPLAY_NAME = '';
            
    public function __construct($class)
    {
      $this->class = $class;
      $this->item = $this->processActionPrivate();
    }
    
    public function getItem()
    {
      return $this->item;
    }
        
    public function getTitle()
    {
      $output = "Genealogy ".$this::DISPLAY_NAME." Editor";
      if (isset($this->item))
      {
        $output .= ": " . $this->item->getTitle();
      }
      return $output;
    }

    public function getJavascriptFunctions()
    {
      $output = $this->getJavascriptFunctionFollowup();
      $output .= $this->getJavascriptFunctionRestrict();
      return $output;
    }
    
    public function getJavascriptFunctionFollowup()
    {
      if (!isset($this->item))
      {
        $output = "function followupchange()\n{\n";
        $output .= "  if (document.editor.followup.value == 'other')\n  {\n";
        $output .= "    document.editor.followupother.style.visibility = 'visible';\n  }\n";
        $output .= "  else\n  {\n";
        $output .= "    document.editor.followupother.style.visibility = 'hidden';\n  }\n}\n";
      }
      return $output;
    }

    public function getJavascriptFunctionRestrict()
    {
      if (!isset($this->item))
      {
        $class = $this->class;
        $output = javascript_select_change_function('restrict_change', 'document.editor.open',
          array('all'=> $class::SortList($class::GetAll()),
                'reviewed'=> $class::SortList($class::GetReviewed()),
                'unreviewed'=> $class::SortList($class::GetUnreviewed())), $this->getItem());
      }
      return $output;
    }

    public function getBodyContent()
    {
      $type = $this::DISPLAY_NAME;
      $item = $this->item;
      $class = $this->class;
      
      if (!isset($_REQUEST['restrict']))
      {
        $_REQUEST['restrict'] = 'all';
      }

      $output = "<h1>Genealogy ".$type." Editor</h1>\n";
      $output .= "<form name=editor action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
      $output .= "<p /> \n";

      if (isset($this->item))
      {
        $output .= $this->getItemGenericContent();
        $output .= $this->getItemCustomContent();
      }
      else
      {
        $output .= $this->getBodyOpenDeleteNew();
      }
      return $output;
    }
    
    public function getBodyOpenDeleteNew()
    {
      $type = $this::DISPLAY_NAME;
      $item = $this->item;
      $class = $this->class;

      $output = "<input type=radio name=restrict value=all";
      if (!isset($_REQUEST['restrict']) || $_REQUEST['restrict'] == 'all')
      {
        $output .= " checked";
      }    
      $output .= " onchange=\"restrict_change('all')\"> all\n";  

      $output .= "<input type=radio name=restrict value=reviewed";
      if ($_REQUEST['restrict'] == 'reviewed')
      {
        $output .= " checked";
      }
      $output .= " onchange=\"restrict_change('reviewed')\"> reviewed\n";
  
      $output .= "<input type=radio name=restrict value=unreviewed";
      if ($_REQUEST['restrict'] == 'unreviewed')
      {
        $output .= " checked";
      }
      $output .= " onchange=\"restrict_change('unreviewed')\"> unreviewed\n"; 
 
      if ($class == 'Page')
      {
        $document = Document::Get(Document::UNSORTED);
        if (isset($item))
        {
          $document = $item->getDocument();
        }
        switch ($_REQUEST['restrict'])
        {
          case 'reviewed':
            $output .= $class::GetSelectList("document_open\" onchange=\"restrict_change_page()",
              Document::GetReviewed(), array(), $document);
            $output .= $class::GetSelectList("open", Page::GetReviewed($document->getPages()), array(), $item);
            break;
          case 'unreviewed':
            $output .= $class::GetSelectList("document_open\" onchange=\"restrict_change_page()",
              Document::GetUnreviewed(), array(), $document);
            $output .= $class::GetSelectList("open", Page::GetUnreviewed($document->getPages()), array(), $item);
            break;
          default:
            $output .= $class::GetSelectList("document_open\" onchange=\"restrict_change_page()",
              Document::GetAll(), array(), $document);
            $output .= $class::GetSelectList("open", $document->getPages(), array(), $item);
            break;
        }
      }
      else
      {
        switch ($_REQUEST['restrict'])
        {
          case 'reviewed':
            $output .= $class::GetSelectList("open", $class::GetReviewed(), array(), $item);
            break;
          case 'unreviewed':
            $output .= $class::GetSelectList("open", $class::GetUnreviewed(), array(), $item);
            break;
          default:
            $output .= $class::GetSelectList("open", $class::GetAll(), array(), $item);
            break;
        }
      }
      $output .= "<input type=submit name=action value=\"open\">\n";
      $output .= "<input type=submit name=action value=\"delete\">\n";
      $output .= "<input type=submit name=action value=\"new\"></p>\n";
      return $output;
    }
    
    public function getItemGenericContent()
    {
      $item = $this->item;
      $class = get_class($item);
  
      // view mode link
      $output = "<h3><a href=\"" . $item->getDisplayURL() . "\">View " . $item->getTitle() . "</a></h3>\n";
    
      // reviewed checkbox
      $output .= "reviewed <input name=\"reviewed\" type=\"checkbox\" value=\"enabled\"";
      if ($item->isReviewed())
      {
        $output .= " checked";
      }
      $output .= " /><br />\n";

      // followup
      $output .= "<a href=/pierce/edit/followup.php>Followup needed</a>: \n";
      $output .= "<select name=\"followup\" onchange=\"followupchange();\">\n";
      $output .= "<option value=\"other\"";
      if ($item->getFollowup() == "")
      {
        $output .= " selected";
      }
      $output .= ">Other</option>\n";
     
      $followup_list = array_unique(array_merge(Person::GetFollowupCatagories(), Family::getFollowupCatagories(), Event::getFollowupCatagories(), Document::getFollowupCatagories(), Page::getFollowupCatagories()));
      natcasesort($followup_list);
      foreach ($followup_list as $followup_item)
      {
        $output .= "<option value=\"" . $followup_item . "\"";
        if ($item->getFollowup() == $followup_item)
        {
          $output .= " selected";
        }
        $output .= ">" . $followup_item . "</option>\n";
      }
      $output .= "</select>\n";
      $output .= "<input name=\"followupother\" size=50";
      if ($item->getFollowup() != "")
      {
        $output .= " style=\"visibility:hidden\" ";
      }
      $output .= "><br />\n";
      $output .= "Followup details: \n";
      $output .= "<input name=\"followupdetail\" size=70 value=\"" . htmlentities($item->getFollowupDetail(), ENT_QUOTES) . "\"><br />\n";

      // send item key with form submissions
      $output .= "<input type=hidden name=save value=\"" . $item->getID() . "\"><p />\n";
      return $output;
    }
  
    public abstract function getItemCustomContent();
    
    public function getEditorTableCells($name, $edit, $preview)
    {
      $output = "<tr><td colspan=2>".$name."</td></tr>\n";
      $output .= "<tr><td width=\"50%\" valign=top>\n";
      $output .= $edit;
      $output .= "</td><td width=\"50%\" valign=top>\n";
      $output .= $preview;
      $output .= "</td></tr>\n";
      return $output;
    }

    public function displayEditList($heading, $class, $items)
    {
      $output = "$heading<ul>";
      $items = $class::SortList($items);
      foreach ($items as $item)
      {
        $output .= "<li>\n";
        $output .= $item->getEditLink();
        $output .= "</li>\n";
      }
      $output .= "</ul>\n";
      return $output;
    }

    function editList($heading, $type, $class, $items)
    {
      $items = $class::SortList($items);
      $output = "$heading<ul>";
      foreach ($items as $item)
      {
        $output .= "<li>\n";
        $output .= $item->getEditLink();
        $output .= "<button type=submit name=action value=\"delete:$type:" . $item->getID() . "\">delete</button>\n";    
        $output .= "</li>\n";
      }
     
      $output .= $class::GetSelectList("add" . $type, $class::GetAll(), $items, NULL);
      $output .= "<button type=submit name=action value=\"add:$type\">add</button>\n";    
      $output .= "<button type=submit name=action value=\"new:$type\">new</button><br />\n";    
      $output .= "</ul>";
      return $output;
    }

    private function processActionPrivate()
    {
      $item = NULL;
      if (isset($_REQUEST['action']))
      {
        $class = $this->class;
        switch ($_REQUEST['action'])
        {
         case "open":
           $item = $class::Get($_REQUEST['open']);
            break;
          case "delete":
            $class::Delete($_REQUEST['open']);          
            break;
          case "new":
            $item = $class::Make();
            break;
          case "save":
            $item = $class::Get($_REQUEST['save']);
            // reviewed
            if (isset($_REQUEST['reviewed']) && $_REQUEST['reviewed'] == 'enabled')
            {
              $item->enableReviewed();
            }
            else
            {
              $item->disableReviewed();
            }    
            // followup
            if (isset($_REQUEST['followup']) && $_REQUEST['followup'] == 'other')
            {
              $item->updateFollowup($_REQUEST['followupother']);
            }
            else
            {
              $item->updateFollowup($_REQUEST['followup']);
            }
            $item->updateFollowupDetail($_REQUEST['followupdetail']);
            $this->processActionSave($item);
            break;
          default:
            $item = $this->processAction();
            break;
        }
      }
      return $item;
    }

    public abstract function processActionSave($item);
    public abstract function processAction();

  }
?>