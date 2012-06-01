<?php
/**
 * genealogy_class.php
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
  abstract class GenealogyAbstract
  {
    const DISPLAY_URL = "";
    const EDIT_URL = "";

    const TABLE_NAME = "";
    const TABLE_KEY = "";
    const TABLE_CACHE_FIELDS = "";

    private $cache_fields_generic = array('Reviewed', 'Followup', 'FollowupDetail');
    protected $cache_fields = array();
    
    private $key = '';
    private $data = array();
    private $updates = array();
    

    private static $instance_master_list = array();

    // insure only one instance of each object
    public static function Get($id)
    {
      $class = get_called_class();
      if (!isset(self::$instance_master_list[$class]))
      {
        self::$instance_master_list[$class] = array();
      }
      if (is_null($id) || $id== '')
      {
        return NULL;
      }
      if (!isset(self::$instance_master_list[$class][$id]))
      {
        $new = $class::GetNew($id);
        if (is_null($new))
        {
          return NULL;
        }
        if (is_null($new->getID()))
        {
          return NULL;
        }
        self::$instance_master_list[$class][$id] = $new;
      }
      return self::$instance_master_list[$class][$id];
    }

    // insure only one instance of each object
    public static function Delete($id)
    {
      $class = get_called_class();
      if (!isset(self::$instance_master_list[$class]))
      {
        self::$instance_master_list[$class] = array();
      }
      // remove cached copy of the item
      if (isset(self::$instance_master_list[$class][$id]))
      {
        unset(self::$instance_master_list[$class][$id]);
      }
      // do a mysql query to remove the item from the table
      $query = "DELETE FROM ".$class::TABLE_NAME." WHERE ".$class::TABLE_KEY."=".$id;
      $class::GetQuery($query);
    }

    // turn an array of id's into an array of objects
    public static function GetArray($id_array)
    {
      $class = get_called_class();
      $instance_array = array();
      foreach($id_array as $id)
      {
        $new = $class::Get($id);
        if (!is_null($new))
        {
          array_push($instance_array, $new);
        }
      }
      return $instance_array;
    }
    
    public static function GetAll()
    {
      $class = get_called_class();
      $query = "SELECT ".$class::TABLE_KEY." FROM ".$class::TABLE_NAME;
      return $class::GetQueryClassList($query, $class, $class::TABLE_KEY);
    }

    public static function GetReviewed()
    {
      $class = get_called_class();
      $query = "SELECT ".$class::TABLE_KEY." FROM ".$class::TABLE_NAME." WHERE Reviewed";
      return $class::GetQueryClassList($query, $class, $class::TABLE_KEY);
    }

    public static function GetUnreviewed()
    {
      $class = get_called_class();
      $query = "SELECT ".$class::TABLE_KEY." FROM ".$class::TABLE_NAME." WHERE NOT Reviewed";
      return $class::GetQueryClassList($query, $class, $class::TABLE_KEY);
    }

    public static function GetFollowupList()
    {
      $class = get_called_class();
      $query = "SELECT ".$class::TABLE_KEY." FROM ".$class::TABLE_NAME." WHERE (Followup IS NOT NULL && (Followup != '')) || (FollowupDetail IS NOT NULL && (FollowupDetail != ''))";
      return $class::GetQueryClassList($query, $class, $class::TABLE_KEY);
    }

    public static function GetFollowupCatagories()
    {
      $class = get_called_class();
      $query = "SELECT Followup FROM ".$class::TABLE_NAME." WHERE (Followup IS NOT NULL && (Followup != ''))";
      return $class::GetQueryValueList($query, 'Followup');
    }

    protected static function GetQuery($query)
    {
      GenealogyDatabase::Open();
      $result = array();
//error_log('GetQuery: '.$query);
      $query_result = mysql_query($query);
      if (is_bool($query_result))
      {
        return array();
      }
      else
      {
        return mysql_fetch_array($query_result);
      }
    }

    protected static function GetQueryClassList($query, $class, $field)
    {
      GenealogyDatabase::Open();
      $result = array();
//error_log('GetQueryClassList: '.$query);
      $query_result = mysql_query($query);
      while($row = mysql_fetch_array($query_result))
      {
        $result[] = $class::Get($row[$field]);
      }
      return $result;
    }

    protected static function GetQueryValueList($query, $field)
    {
      GenealogyDatabase::Open();
      $result = array();
//error_log('GetQueryValueList: '.$query);
      $query_result = mysql_query($query);
      while($row = mysql_fetch_array($query_result))
      {
//error_log('GetQueryValueList,item '.$row[$field]);

        if (isset($row[$field]) && !is_null($row[$field]) && $row[$field] != '')
        {
//error_log('GetQueryValueList,item saved');
          $result[] = $row[$field];
        }
      }
      return array_unique($result);
    }
        
    protected static function GetQueryCount($query)
    {
      GenealogyDatabase::Open();
//error_log('GetQueryCount: '.$query);
      $query_result = mysql_query($query);
      return mysql_num_rows($query_result);
    }

    public static function SortList($list)
    {
      $class = get_called_class();
      usort($list, $class."::Compare");
      return $list;
    }

    protected static function Compare($a, $b)
    {
      if ($a === $b)
      {
        return 0;
      }
      if (is_null($a))
      {
        return -1;
      }
      if (is_null($b))
      {
        return 1;
      }
      $aclass = get_class($a);
      $bclass = get_class($b);
      if ($aclass == $bclass)
      {
        return $aclass::ClassCompare($a, $b);
      }
      return strnatcasecmp($a->getTitle(), $b->getTitle());
    }

    protected static function ClassCompare($a, $b)
    {
      return strnatcasecmp($a->getTitle(), $b->getTitle());
    }
    
    public static function GetSelectList($name, $list, $ignored, $selected = NULL)
    {
      $class = get_called_class();
      $result = "<select name=\"$name\">\n";
      $result .= "<option value=\"NULL\"";
      if (is_null($selected))
      {
        $result .= " selected";
      }
      $result .= ">---NONE---</option>\n";
      foreach ($class::SortList(array_diff($list, $ignored)) as $item)
      {
        $result .= "<option value=\"" . $item->getID() . "\"";
        if ($selected == $item)
        {
          $result .= " selected";
        }
        $result .= ">";
        if (get_class($item) == 'Event')
        {
          $result .= $item->getDateString()." - ".$item->getTitle();
        }
        else
        {
          $result .= $item->getTitle();
        }
        $result .= "</option>\n";
      }
      $result .= "</select>";
      return $result;
    }

    // child object should call it's own "new"
    abstract protected static function GetNew($id);
        
    public static function Make()
    {
      $class = get_called_class();
      $query = "INSERT INTO ".$class::TABLE_NAME." () VALUES()";
      $class::GetQuery($query);
      return $class::Get(mysql_insert_id());
    }

    protected function __construct($file)
    {
      $this->key = $file;
      $this->cache_fields = array_merge($this->cache_fields_generic, $this->cache_fields);
      $fields = '';
      foreach ($this->cache_fields as $field)
      {
        if ($fields == '')
        {
          $fields = $field;
        }
        else
        {
          $fields .= ", ".$field;
        }
      }
      $query = "SELECT $fields FROM ".$this::TABLE_NAME." WHERE ".$this::TABLE_KEY."=".$this->getID();
      $result = $this->GetQuery($query);
      if ($result)
      {
        $this->data = $result;
      }
      else
      {
        $this->key = NULL;
      }
    }

    public function __toString()
    {
      return $this->getTitle();
    }

    protected function getField($fieldname)
    {
      if (!array_key_exists($fieldname, $this->data))
      {
//error_log('uncached '.get_called_class().' '.$this->getID().' '.$fieldname);
        $this->data[$fieldname] = $this->getFieldDirect($fieldname);
      }
      return $this->data[$fieldname];
    }
    protected function getFieldDirect($fieldname)
    {
      $query = "SELECT ".$fieldname." FROM ".$this::TABLE_NAME." WHERE ".$this::TABLE_KEY."=".$this->getID();
      $result = $this->GetQuery($query);
      return $result[$fieldname];
    }

    protected function updateField($fieldname, $value, $default = NULL)
    {
      if (is_null($default))
      {
        $this->updates[$fieldname] = GenealogyDatabase::SetStringBlankIsNull($fieldname, $value);
      }
      else
      {
        $this->updates[$fieldname] = GenealogyDatabase::SetStringBlankIsWhat($fieldname, $value, $default);
      }
      $this->data[$fieldname] = $value;
    }

    protected function updateFieldBoolean($fieldname, $boolean)
    {
      $this->updates[$fieldname] = GenealogyDatabase::SetStringBoolean($fieldname, $boolean);
      $this->data[$fieldname] = $boolean;
    }
    
    public function saveUpdates()
    {
      if(count($this->updates) > 0)
      {
        $setstring = '';
        foreach ($this->updates as $set)
        {
          if ($setstring == '')
          {
            $setstring = $set;
          }
          else
          {
            $setstring .= ", ".$set;
          }
        }
        $this->updates = array();
        $query = "UPDATE ".$this::TABLE_NAME." SET ".$setstring." WHERE ".$this::TABLE_KEY."=".$this->getID();
//error_log("save update: ".$query);
        $this->GetQuery($query);
       }
    }

    public function getID()
    {
      return $this->key;
    }

    public function isReviewed()
    {
      return $this->getField('Reviewed');
    }
    public function enableReviewed()
    {
      $this->updateField('Reviewed', true);
    }
    public function disableReviewed()
    {
      $this->updateField('Reviewed', false);
    }
        
    public function getFollowup()
    {
      return $this->getField('Followup');
    }
    
    public function updateFollowup($followup)
    {
      $this->updateField('Followup', $followup);
    }
    
    public function getFollowupDetail()
    {
      return $this->getField('FollowupDetail');
    }
    
    public function updateFollowupDetail($followup)
    {
      $this->updateField('FollowupDetail', $followup);
    }

    public function getNote()
    {
      return $this->getField('Note');
    }

    public function updateNote($note)
    {
      $this->updateField('Note', $note);
    }
 
    public function getDisplayURL()
    {
      return $this::DISPLAY_URL . $this->getID();
    }
    public function getDisplayLink()
    {
      return "<a href=\"" . $this->getDisplayURL() . "\">" . $this->getTitle() . "</a>";
    }
    
    public function getEditURL()
    {
      return $this::EDIT_URL . $this->getID();
    }
    public function getEditLink()
    {
      return "<a href=\"" . $this->getEditURL() . "\">" . $this->getTitle() . "</a>";
    }
   
    abstract public function getTitle();
 
    public static function FormatForHTML($text)
    {
      if (preg_match("/(<|>)/", $text))
      {
        // html formatted
        return $text;
      }
      else
      {
        if (preg_match("/\|/", $text))
        {
          // table
          $text =  preg_replace("/^(.*)\$/m", '<tr><td>\1</td></tr>', $text);
          $text =  preg_replace("/\|/m", '</td><td>', $text);
          return "<table border=1>$text</table>";
        }
        else
        {
          // plain text
          return preg_replace("/^(.*)\$/m", '\1<br />', $text);
        }
      }
    }
  }
?>