<?php
/**
 * family_class.php
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
  class Family extends GenealogyAbstract
  {
    const DISPLAY_URL = "/families.php?";
    const EDIT_URL = "/edit/families.php?action=open&open=";

    const TABLE_NAME = 'Families';
    const TABLE_KEY = 'FamilyId';

    protected $cache_fields = array(
      'FatherPersonId',
      'MotherPersonId',
      'MarriageEventId',
      'DivorceEventId');
    private $data = array();

    protected static function GetNew($id)
    {
      return new Family($id);
    }

    public function getTitle()
    {
      return $this->getFatherName()." & ".$this->getMotherName();
    }

    public function getFather()
    {
      return Person::Get($this->getField('FatherPersonId'));
    }
    public function getFatherName()
    {
      $father = $this->getFather();
      if (is_null($father))
      {
        return 'Unknown';
      }
      else
      {
        return $father->getName();
      }
    }
    public function updateFather($person)
    {
      if (is_null($person))
      {
        $this->updateField('FatherPersonId', NULL);
      }
      else
      {
        $this->updateField('FatherPersonId', $person->getID());
      }
    }

    public function getMother()
    {
      return Person::Get($this->getField('MotherPersonId'));
    }
    public function getMotherName()
    {
      $mother = $this->getMother();
      if (is_null($mother))
      {
        return 'Unknown';
      }
      else
      {
        return $mother->getName();
      }
    }
    public function updateMother($person)
    {
      if (is_null($person))
      {
        $this->updateField('MotherPersonId', NULL);
      }
      else
      {
        $this->updateField('MotherPersonId', $person->getID());
      }
    }

    public function getMarriageEvent()
    {
      return Event::Get($this->getField('MarriageEventId'));
    }
    public function updateMarriageEvent($event)
    {
      if (is_null($event))
      {
        $this->updateField('MarriageEventId', NULL);
      }
      else
      {
        $this->updateField('MarriageEventId', $event->getID());
      }
    }

    public function getDivorceEvent()
    {
      return Event::Get($this->getField('DivorceEventId'));
    }
    public function updateDivorceEvent($event)
    {
      if (is_null($event))
      {
        $this->updateField('DivorceEventId', NULL);
      }
      else
      {
        $this->updateField('DivorceEventId', $event->getID());
      }
    }

    public function getParentsEvents()
    {
      if (!isset($this->data['ParentsEvents']))
      {
        $query = "SELECT EventId FROM FamiliesEventsParents WHERE FamilyId=".$this->getID();      
        $this->data['ParentsEvents'] = $this->GetQueryClassList($query, 'Event', 'EventId');
      }
      return $this->data['ParentsEvents'];
    }
    public function addParentsEvents($event)
    {
      if (isset($this->data['ParentsEvents'])) $this->data['ParentsEvents'][] = $event;
      $query = "INSERT INTO FamiliesEventsParents (FamilyId, EventId) VALUES (".$this->getID().", ".$event->getID().")";
      $this->GetQuery($query);
    }
    public function deleteParentsEvents($event)
    {
      if (isset($this->data['ParentsEvents'])) array_diff($this->data['ParentsEvents'], array($event));
      $query = "DELETE FROM FamiliesEventsParents WHERE FamilyId=".$this->getID()." AND EventId=".$event->getID();
      $this->GetQuery($query);
    }
    public function getChildren()
    {
      if (!isset($this->data['Children']))
      {
        $query = "SELECT PersonId FROM FamiliesPeopleChildren WHERE FamilyId=".$this->getID();
        $this->data['Children'] = $this->GetQueryClassList($query, 'Person', 'PersonId');
      }
      return $this->data['Children'];
    }
    public function containsChild($person)
    {
      return (in_array($person, $this->getChildren()));
    }
    public function addChildren($person)
    {
      if (isset($this->data['Children'])) $this->data['Children'][] = $person;
      $query = "INSERT INTO FamiliesPeopleChildren (FamilyId, PersonId) VALUES (".$this->getID().", ".$person->getID().")";
      $this->GetQuery($query);
    }
    public function deleteChildren($person)
    {
      if (isset($this->data['Children'])) array_diff($this->data['Children'], array($person));
      $query = "DELETE FROM FamiliesPeopleChildren WHERE FamilyId=".$this->getID()." AND PersonId=".$person->getID();
      $this->GetQuery($query);
    }
    public function getChildrenEvents()
    {
      if (!isset($this->data['ChildrenEvents']))
      {
        $query = "SELECT EventId FROM FamiliesEventsChildren WHERE FamilyId=".$this->getID();      
        $this->data['ChildrenEvents'] = $this->GetQueryClassList($query, 'Event', 'EventId');
      }
      return $this->data['ChildrenEvents'];
    }
    public function addChildrenEvents($event)
    {
      if (isset($this->data['ChildrenEvents'])) $this->data['ChildrenEvents'][] = $event;
      $query = "INSERT INTO FamiliesEventsChildren (FamilyId, EventId) VALUES (".$this->getID().", ".$event->getID().")";
      $this->GetQuery($query);
    }
    public function deleteChildrenEvents($event)
    {
      if (isset($this->data['ChildrenEvents'])) array_diff($this->data['ChildrenEvents'], array($event));
      $query = "DELETE FROM FamiliesEventsChildren WHERE FamilyId=".$this->getID()." AND EventId=".$event->getID();
      $this->GetQuery($query);
    }
    
    public function containsEvent($event)
    {
      return in_array($event, $this->getParentsEvents(), true) ||
        in_array($event, $this->getChildrenEvents(), true) ||
        $event == $this->getMarriageEvent() ||
        $event == $this->getDivorceEvent();
    }
    
    protected static function ClassCompare($a, $b)
    {
      $afather = $a->getFather();
      $bfather = $b->getFather();
      $result = Person::Compare($afather, $bfather);
      if ($result == 0)
      {
        $result = Event::Compare($a->getMarriageEvent(), $b->getMarriageEvent());
      }
      return $result;
    }

  }
?>