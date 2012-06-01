<?php
/**
 * person_class.php
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
  class Person extends GenealogyAbstract
  {
    const DISPLAY_URL = "/people.php?";
    const DISPLAY_MAP_URL = "/peoplemap.php?";
    const EDIT_URL = "/edit/people.php?action=open&open=";

    const TABLE_NAME = 'People';
    const TABLE_KEY = 'PersonId';

    const GENDER_MALE = "male";
    const GENDER_FEMALE = "female";
    const GENDER_UNKNOWN = "unknown";
    
    protected $cache_fields = array(
      'FirstName',
      'LastName',
      'Gender',
      'BirthEventId',
      'DeathEventId');
    private $data = array();

    protected static function GetNew($id)
    {
      return new Person($id);
    }
    
    public static function SearchName($first, $last)
    {
      GenealogyDatabase::Open();
      $query = "SELECT PersonId FROM People WHERE ";
      $query .= GenealogyDatabase::SetStringBlankIsWhat('FirstName', $first, 'gfuijbgfds');
      $query .= " AND ";
      $query .= GenealogyDatabase::SetStringBlankIsWhat('LastName', $last, 'gfuijbgfds');
      $found = Person::GetQueryClassList($query, 'Person', 'PersonId');
      if (count($found) == 0)
      {
        return NULL;
      }
      else
      {
        return array_shift($found);
      }
    }
    
    public static function GetAllGender($gender)
    {
      $class = get_called_class();
      $query = "SELECT ".$class::TABLE_KEY." FROM ".$class::TABLE_NAME." WHERE Gender='".$gender."'";
      return $class::GetQueryClassList($query, $class, $class::TABLE_KEY);
    }

    public static function GetReviewed()
    {
      $class = get_called_class();
      $query = "SELECT ".$class::TABLE_KEY." FROM ".$class::TABLE_NAME." WHERE Reviewed";
      return $class::GetQueryClassList($query, $class, $class::TABLE_KEY);
    }

    public function getTitle()
    {
      return $this->getName();
    }

    public function getName()
    {
      return $this->getFirstName() . " " . strtoupper($this->getLastName());
    }
    public function getFirstName()
    {
      return $this->getField('FirstName');
    }
    public function updateFirstName($givenname)
    {
      $this->updateField('FirstName', $givenname);
    }
    public function getLastName()
    {
      return $this->getField('LastName');
    }
    public function updateLastName($surname)
    {
      $this->updateField('LastName', $surname);
    }

    public function getGender()
    {
      return $this->getField('Gender');
    }
    public function updateGender($gender)
    {
      $this->updateField('Gender', $gender);
    }

    public function getBirthEvent()
    {
      return Event::Get($this->getField('BirthEventId'));
    }
    public function updateBirthEvent($event)
    {
      if (is_null($event))
      {
        $this->updateField('BirthEventId', NULL);
      }
      else
      {
        $this->updateField('BirthEventId', $event->getID());
      }
    }
    public function getBirthDateString()
    {
      $event = $this->getBirthEvent();
      if (is_null($event))
      {
        return Event::BuildDateString(Event::DATE_PREFIX_UNKNOWN, 1900, 6, 15);
      }
      else
      {
        return $event->getDateString();
      }
    }
    
    public function getDeathEvent()
    {
      return Event::Get($this->getField('DeathEventId'));
    }
    public function updateDeathEvent($event)
    {
      if (is_null($event))
      {
        $this->updateField('DeathEventId', NULL);
      }
      else
      {
        $this->updateField('DeathEventId', $event->getID());
      }
    }
    public function getDeathDateString()
    {
      $event = $this->getDeathEvent();
      if (is_null($event))
      {
        return Event::BuildDateString(Event::DATE_PREFIX_UNKNOWN, 1900, 'Jun', 15);
      }
      else
      {
        return $event->getDateString();
      }
    }

    public function getPersonalEvents()
    {
      if (!isset($this->data['PersonalEvents']))
      {
        $query = "SELECT EventId FROM PeopleEvents WHERE PersonId=".$this->getID();
        $this->data['PersonalEvents'] = $this->GetQueryClassList($query, 'Event', 'EventId');
      }
      return $this->data['PersonalEvents'];
    }
    public function addPersonalEvents($event)
    {
      if (isset($this->data['PersonalEvents'])) $this->data['PersonalEvents'][] = $event;
      $query = "INSERT INTO PeopleEvents (PersonId, EventId) VALUES (".$this->getID().", ".$event->getID().")";
      $this->GetQuery($query);
    }
    public function deletePersonalEvents($event)
    {
      if (isset($this->data['PersonalEvents'])) array_diff($this->data['PersonalEvents'], array($event));
      $query = "DELETE FROM PeopleEvents WHERE PersonId=".$this->getID()." AND EventId=".$event->getID();
      $this->GetQuery($query);
    }

    public function getFamiliesAsChild()
    {
      if (!isset($this->data['FamiliesAsChild']))
      {
        $query = "SELECT FamilyId FROM FamiliesPeopleChildren WHERE PersonId=".$this->getID();
        $this->data['FamiliesAsChild'] = $this->GetQueryClassList($query, 'Family', 'FamilyId');
      }
      return $this->data['FamiliesAsChild'];
    }

    private function getFamiliesAsChildEvents()
    {
      if (!isset($this->data['FamiliesAsChildEvents']))
      {
        $query = 'SELECT EventId FROM FamiliesEventsChildren INNER JOIN FamiliesPeopleChildren USING (FamilyId) WHERE PersonId='.$this->getID();
        $this->data['FamiliesAsChildEvents'] = $this->GetQueryClassList($query, 'Event', 'EventId');
      }
      return $this->data['FamiliesAsChildEvents'];
    }

    public function getFamiliesAsParent()
    {
      if (!isset($this->data['FamiliesAsParent']))
      {
        $query = "SELECT FamilyId FROM Families WHERE FatherPersonId=".$this->getID()." OR MotherPersonId=".$this->getID();
        $this->data['FamiliesAsParent'] = $this->GetQueryClassList($query, 'Family', 'FamilyId');
      }
      return $this->data['FamiliesAsParent'];
    }

    private function getFamiliesAsParentEvents()
    {
      if (!isset($this->data['FamiliesAsParentEvents']))
      {
        $query = 'SELECT EventId FROM FamiliesEventsParents INNER JOIN Families USING (FamilyId) WHERE FatherPersonId='.$this->getID().' OR MotherPersonId='.$this->getID();
        $this->data['FamiliesAsParentEvents'] = $this->GetQueryClassList($query, 'Event', 'EventId');
      }
      return $this->data['FamiliesAsParentEvents'];
    }

    public function containsEvent($event)
    {
      return $event == $this->getBirthEvent() ||
        in_array($event, $this->getPersonalEvents(), true) ||
        $event == $this->getDeathEvents();
    }
    public function getEvents()
    {
      $all_events = array();
      $event = $this->getBirthEvent();
      if (!is_null($event))
      {
        $all_events[] = $event;
      }
      $event = $this->getDeathEvent();
      if (!is_null($event))
      {
        $all_events[] = $event;
      }
      $all_events = array_merge($all_events, $this->getPersonalEvents());
      $all_events = array_merge($all_events, $this->getFamiliesAsChildEvents());
      $all_events = array_merge($all_events, $this->getFamiliesAsParentEvents());
      foreach ($this->getFamiliesAsParent() as $family)
      {
        // marriage and divorce events for us
        $event = $family->getMarriageEvent();
        if (!is_null($event))
        {
          $all_events[] = $event;
        }
        $event = $family->getDivorceEvent();
        if (!is_null($event))
        {
          $all_events[] = $event;
        }
        // add birth events for our children automatically
        foreach ($family->getChildren() as $child)
        {
          $event = $child->getBirthEvent();
          if (!is_null($event))
          {
            $all_events[] = $event;
          }
        }
        // add death events for our spoused automatically
        foreach (array($family->getFather(), $family->getMother()) as $person)
        {
          if (!is_null($person) && $person != $this)
          {
            $event = $person->getDeathEvent();
            if (!is_null($event))
            {
              $all_events[] = $event;
            }
          }
        }
      }
      return $all_events;
    }

    public function getDisplayMapURL()
    {
      return $this::DISPLAY_MAP_URL . $this->getID();
    }
    public function getDisplayMapLink()
    {
      return "<a href=\"" . $this->getDisplayMapURL() . "\">" . $this->getTitle() . "</a>";
    }

    protected static function ClassCompare($a, $b)
    {
      if (strtoupper($a->getLastName()) == strtoupper($b->getLastName()))
      {
        return strnatcasecmp($a->getFirstName(), $b->getFirstName());
      }
      return strnatcasecmp($a->getLastName(), $b->getLastName());
    }
  }
?>