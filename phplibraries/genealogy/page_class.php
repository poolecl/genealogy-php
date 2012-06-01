<?php
/**
 * page_class.php
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
  class Page extends GenealogyAbstract
  {
    const IMAGE_DIRECTORY = "/var/www/pierce/production/sources/";
    const URL_DIRECTORY = "/sources/";

    const DISPLAY_URL = "/page.php?";
    const EDIT_URL = "/edit/pages.php?action=open&open=";
    
    const TABLE_NAME = "Pages";
    const TABLE_KEY = "PageId";

    protected $cache_fields = array(
      'Title',
      'DocumentId',
      'ImageFile');
    private $data = array();

    protected static function GetNew($id)
    {
      return new Page($id);
    }
    
    public static function GetNoImageFile()
    {
      $class = get_called_class();
      $query = "SELECT ".$class::TABLE_KEY." FROM ".$class::TABLE_NAME." WHERE ImageFile IS NULL OR ImageFile=''";
      return $class::GetQueryClassList($query, $class, $class::TABLE_KEY);
    }

    public static function GetUntranscribed()
    {
      $class = get_called_class();
      $query = "SELECT ".$class::TABLE_KEY." FROM ".$class::TABLE_NAME." WHERE Transcription IS NULL OR Transcription=''";
      return $class::GetQueryClassList($query, $class, $class::TABLE_KEY);
    }
    
    public static function SearchImage($filename)
    {
      GenealogyDatabase::Open();
      $query = "SELECT PageId FROM Pages WHERE ";
      $query .= GenealogyDatabase::SetStringBlankIsWhat('ImageFile', $filename, 'gfuijbgfds');
      $found = Page::GetQueryClassList($query, 'Page', 'PageId');
      if (count($found) == 0)
      {
        return NULL;
      }
      else
      {
        return array_shift($found);
      }
    }

    public function getDocument()
    {
      $document = Document::Get($this->getField('DocumentId'));
      if (is_null($document))
      {
        $document = Document::Get(Document::UNSORTED);
      }
      return $document;
    }
    public function updateDocument($document)
    {
      if (is_null($document))
      {
        $this->updateField('DocumentId', Document::UNSORTED);
      }
      else
      {
        $this->updateField('DocumentId', $document->getID());
      }
    }
    
    public function saveUpdates()
    {
      parent::saveUpdates();
      $document = $this->getDocument();
      if (!is_null($document))
      {
        $document->saveUpdates();
      }
    }

    public function getTitle()
    {
      return $this->getDocumentTitle() . " " . $this->getPageTitle();
    }
    public function getPageTitle()
    {
      return $this->getField('Title');
    }
    public function updatePageTitle($title)
    {
      $this->updateField('Title', $title);
    }
    public function getDocumentTitle()
    {
      $document = $this->getDocument();
      if (is_null($document))
      {
        return '';
      }
      else
      {
        return $document->getTitle();
      }
    }
    public function updateDocumentTitle($title)
    {
      $document = $this->getDocument();
      if (!is_null($document))
      {
        $document->updateTitle($title);
      }
    }

    public function getTranscription()
    {
      return $this->getField('Transcription');
    }
    public function updateTranscription($text)
    {
      $this->updateField('Transcription', $text);
    }

    public function getCitation()
    {
      return $this->FormatForHTML($this->getPageCitation()) . "\n<br />\n" . $this->FormatForHTML($this->getDocumentCitation());
    }
    public function getPageCitation()
    {
      return $this->getField('Citation');
    }
    public function updatePageCitation($cite)
    {
      $this->updateField('Citation', $cite);
    }
    public function getDocumentCitation()
    {
      $document = $this->getDocument();
      if (is_null($document))
      {
        return '';
      }
      else
      {
        return $document->getCitation();
      }
    }
    public function updateDocumentCitation($title)
    {
      $document = $this->getDocument();
      if (!is_null($document))
      {
        $document->updateCitation($title);
      }
    }

    public function getPageNote()
    {
      return $this->getField('Note');
    }
    public function updatePageNote($note)
    {
      $this->updateField('Note', $note);
    }
    public function getDocumentNote()
    {
      $document = $this->getDocument();
      if (is_null($document))
      {
        return '';
      }
      else
      {
        return $document->getNote();
      }
    }
    public function updateDocumentNote($title)
    {
      $document = $this->getDocument();
      if (!is_null($document))
      {
        $document->updateNote($title);
      }
    }

    public function getEvents()
    {
      if (!isset($this->data['Events']))
      {
        $query = "SELECT EventId FROM EventsPages WHERE PageId=".$this->getID();
        $this->data['Events'] = $this->GetQueryClassList($query, 'Event', 'EventId');
      }
      return $this->data['Events'];
    }
    public function addEvents($event)
    {
      if (isset($this->data['Events'])) $this->data['Events'][] = $event;
      $query = "INSERT INTO EventsPages (EventId, PageId) VALUES (".$event->getID().", ".$this->getID().")";
      $this->GetQuery($query);
    }
    public function deleteEvents($event)
    {
      if (isset($this->data['Events'])) array_diff($this->data['Events'], array($event));
      $query = "DELETE FROM EventsPages WHERE EventId=".$event->getID()." AND PageId=".$this->getID();
      $this->GetQuery($query);
    }

    public function getImage()
    {
      if ($this->getImageFile() != '')
      {
        return "<a href=\"".$this->getImageFileURL()."\"><img src=\"".$this->getImageFileURL()."\" style=\"max-width:100%;\"></a><br />\n";
      }
      return '';
    }
    
    public function getImageFileWithPath()
    {
      return $this::IMAGE_DIRECTORY . $this->getImageFile();
    }
    public function getImageFileURL()
    {
      return $this::URL_DIRECTORY . $this->getImageFile();
    }

    public function getImageFile()
    {
      return $this->getField('ImageFile');
    }
    public function updateImageFile($filename)
    {
      $this->updateField('ImageFile', $filename);
    }
  }
?>