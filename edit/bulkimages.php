<?php

  include_once dirname(__FILE__)."/../phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/../phplibraries/genealogy/gsql-includes.php";

  class EditorImagesWebPage extends WebPageAbstract
  {
    private $new_only = true;

    public function __construct()
    {
      if (isset($_REQUEST['action']))
      {
        switch ($_REQUEST['action'])
        {
          case 'newonly':
            $this->new_only = true;
            break;
          case 'all':
            $this->new_only = false;
            break;
          default:
            list($action, $filename) = preg_split("/:/" , $_REQUEST['action']) + array('', '');
            switch ($action)
            {
              case 'new':
                $page = Page::Make();
                $page->updatePageTitle($filename);
                $page->updateImageFile($filename);
                $page->saveUpdates();
                header("Location: ".$page->getEditURL(), true, 303);
                exit();
                break;
              case 'set':
                $page = Page::Get($_REQUEST['page:'.preg_replace('/[^a-zA-Z0-9_\-]/','_',$filename)]);
                $page->updateImageFile($filename);
                $page->saveUpdates();
                header("Location: ".$page->getEditURL(), true, 303);
                exit();
                break;
            }
        }
      }
    }
 
    public function getTitle()
    {
      return "Genealogy Editor: Bulk Image Page Selector";
    }

    public function getBodyContent()
    {
      $output = "<h1>".$this->getTitle()."</h1>\n";

      $output .= "<form name=editor action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
      if ($this->new_only)
      {
        $output .= "<button type=submit name=action value=\"all\">all images</button><p />\n";
      }
      else
      {
        $output .= "<button type=submit name=action value=\"newonly\">new images only</button><p />\n";
      }

      $dir = opendir(Page::IMAGE_DIRECTORY);
      $filenames = array();
      while($filename = readdir($dir))
      {
        if (preg_match("/^.*\.jpg$/", $filename))
        {
          $filenames[] = $filename;
        }
      }
      closedir($dir);
      natcasesort($filenames);
      
      $output .= "<table border=1>\n";
      foreach ($filenames as $filename)
      {
        $page = Page::SearchImage($filename);
        if (is_null($page))
        {
          $output .= "<tr><td><a href=\"".Page::URL_DIRECTORY.$filename ."\">".$filename."</a></td>";
          $output .= "<td>";
          $output .= "<button type=submit name=action value=\"new:".$filename."\">new</button>\n";
          $output .= "</td></tr><tr><td colspan=2>";
          $output .= Page::GetSelectList("page:".$filename, Page::GetNoImageFile(), array(), NULL);
          $output .= "<button type=submit name=action value=\"set:".$filename."\">set</button>\n";    
          $output .= "</td></tr>\n";
        }
        elseif (!$this->new_only)
        {
          $output .= "<tr><td><a href=\"".$page->getImageFileURL()."\">".$filename."</a></td><td>".$page->getEditLink()."</td></tr>\n";
        }
      }
      return $output;
    }
  }
  
  $page = new EditorImagesWebPage();
  echo $page->getWebPage();
?>