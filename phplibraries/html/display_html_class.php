<?php

  include_once dirname(__FILE__)."/html_class.php";

  abstract class DisplayWebPage extends WebPageAbstract
  {
    private $file;
    private $item;
    private $class;
    
    const DEFAULT_TITLE = '';
        
    public function __construct($class)
    {
      $this->class = $class;
      $this->file = $_SERVER['QUERY_STRING'];
      if ($this->file != '')
      {
        $this->item = $class::Get($this->file);
      }
    }
    
    protected function getItem()
    {
      return $this->item;
    }

    public function getTitle()
    {
      if (isset($this->item))
      {
        return $this->item->getTitle();
      }
      else
      {
        return $this::DEFAULT_TITLE;
      }
    }

    public function getBodyContent()
    {
      if (isset($this->item))
      {
        return $this->getBodyContentItem();
      }
      else
      {
        return $this->getBodyContentAll();
      }
    }
    
    public abstract function getBodyContentItem();
    
    public function getBodyContentAll()
    {
      $class = $this->class;
      $output = "<h1>" . $this->getTitle() . "</h1>\n";

      $output .= "<p>\n";
      $output .= $this->displayList('', $class, $class::GetAll());
      $output .= "</p>\n";
      return $output;
    }
    
    public static function displayList($heading, $class, $items)
    {
      $output = "$heading<ul>";
      $items = $class::SortList($items);
      foreach ($items as $item)
      {
        $output .= "<li>\n";
        $output .= $item->getDisplayLink();
        $output .= "</li>\n";
      }
      $output .= "</ul>\n";
      return $output;
    }    
  }
?>