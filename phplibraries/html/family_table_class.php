<?php
  include_once dirname(__FILE__)."/../genealogy/gsql-includes.php";

  class FamilyTable
  {
    private $person;
    
    public function __construct($person)
    {
      $this->person = $person;
    }
    
    private function getColumnCount()
    {
      return 3;
    }
    
    private function getTableRowSectionHeading($title)
    {
      return "<tr><td colspan=\"".$this->getColumnCount()."\"><i>".$title.": </i></td></tr>";
    }
    
    public function getTable()
    {
      $person = $this->person;
      $output = "<table class=vitals>\n";
      $output .= "<th><td>birth</td><td>death</td></th>\n";
      $output .= $this->getTableRowSectionHeading("parents");

      $families = $person->getFamiliesAsChild();
      if (count($families) == 0)
      {
        $output .= $this->getTableRowPerson(NULL);
        $output .= $this->getTableRowPerson(NULL);
      }
      foreach ($families as $family)
      {
        $output .= $this->getTableRowPerson($family->getFather());
        $output .= $this->getTableRowPerson($family->getMother());
      }
  
      foreach ($person->getFamiliesAsParent() as $family)
      {
        $output .= $this->getTableRowSectionHeading("spouse");
        $spouse = ($person == $family->getFather()) ? $family->getMother() : $family->getFather();
        $output .= $this->getTableRowPerson($spouse);
        $children = $family->getChildren();
        if (count($children) > 0)
        {
          $output .= $this->getTableRowSectionHeading("children");
          usort($children, get_called_class()."::usort_people_by_birth");
          foreach ($children as $child)
          {
            $output .= $this->getTableRowPerson($child);
          }
        }
      }
      $output .= "</table>\n";
      return $output;
    }

    private function getTableRowPerson($person)
    {
      $name = 'Unknown';
      $birth = '';
      $death = '';
      if (!is_null($person))
      {
        $name = $person->getDisplayLink();
        $event = $person->getBirthEvent();
        if (is_null($event))
        {
          $birth = $person->getBirthDateString();
        }
        else
        {
          $birth = "<a href=\"".$event->getDisplayURL()."\">".$event->getDateString()."</a>";
        }
        $event = $person->getDeathEvent();
        if (is_null($event))
        {
          $death = $person->getDeathDateString();
        }
        else
        {
          $death = "<a href=\"".$event->getDisplayURL()."\">".$event->getDateString()."</a>";
        }
      }
      $output = "<tr><td>$name</a></td>";
      $output .= "<td>$birth</td>";
      $output .= "<td>$death</td>";
      $output .= "</tr>\n";
      return $output;
    }
    
    public function usort_people_by_birth($a, $b)
    {
      $aevent = $a->getBirthEvent();
      $bevent = $b->getBirthEvent();
      $adate = (is_null($aevent)) ? 0 : $aevent->getDateValue();
      $bdate = (is_null($bevent)) ? 0 : $bevent->getDateValue();
      if ($adate == $bdate)
      {
        return 0;
      }
      return ($adate < $bdate) ? -1 : 1;
    }
  }
?>