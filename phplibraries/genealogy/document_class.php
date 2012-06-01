<?php
/**
 * document_class.php
 * 
 * @author Christopher Poole <poolecl@yahoo.ca>
 * @version .01
 * @package genealogy
 */
  include_once dirname(__FILE__)."/gsql-includes.php";

  /**
   * @author Christopher Poole <poolecl@yahoo.ca>
   * @version .01
   * @package genealogy
   */
  class Document extends GenealogyAbstract
  {
    const DISPLAY_URL = "/sources.php?";
    const EDIT_URL = "/edit/documents.php?action=open&open=";

    const UNSORTED = 1;

    const TABLE_NAME = 'Documents';
    const TABLE_KEY = 'DocumentId';

    protected $cache_fields = array('Title');
    private $data = array();

    protected static function GetNew($id)
    {
      return new Document($id);
    }
    
    public function getTitle()
    {
      return $this->getField('Title');
    }
    public function updateTitle($title)
    {
      $this->updateField('Title', $title);
    }

    public function getCitation()
    {
      return $this->getField('Citation');
    }
    public function updateCitation($cite)
    {
      $this->updateField('Citation', $cite);
    }

    public function getPages()
    {
      if (!isset($this->data['Pages']))
      {
        $query = "SELECT PageId FROM Pages WHERE DocumentId=".$this->getID();
        if ($this->getID() == $this::UNSORTED)
        {
          $query .= " OR DocumentId IS NULL";
        }
        $this->data['Pages'] =  $this->GetQueryClassList($query, 'Page', 'PageId');
      }
      return $this->data['Pages'];
    }
  }
?>