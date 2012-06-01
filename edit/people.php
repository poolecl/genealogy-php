<?php

  include_once dirname(__FILE__)."/../phplibraries/html/editor_html_class.php";
  include_once dirname(__FILE__)."/../phplibraries/globals_oo.php";

  class EditorPeopleWebPage extends EditorWebPage
  {
    const DISPLAY_NAME = 'Person';
    
    public function getItemCustomContent()
    {
      $person = $this->getItem();
      $output = "<input name=firstname size=20 value=\"" . htmlentities($person->getFirstName(), ENT_QUOTES) . "\">\n";
      $output .= "<input name=lastname size=20 value=\"" . htmlentities($person->getLastName(), ENT_QUOTES) . "\">\n";

      $output .= "<select name=gender>\n";
      $output .= "<option value=\"" . Person::GENDER_UNKNOWN . "\"";
      if ($person->getGender() == Person::GENDER_UNKNOWN)
      {
        $output .= " selected";
      }
      $output .= ">Gender Unknown</option>\n";
      $output .= "<option value=\"" . Person::GENDER_MALE . "\"";
      if ($person->getGender() == Person::GENDER_MALE)
      {
        $output .= " selected";
      }
      $output .= ">Male</option>\n";
      $output .= "<option value=\"" . Person::GENDER_FEMALE . "\"";
      if ($person->getGender() == Person::GENDER_FEMALE)
      {
        $output .= " selected";
      }
      $output .= ">Female</option>\n";
      $output .= "</select>\n";

      $output .= "<input type=submit name=action value=\"save\"></h2>\n";    
  
      $output .= "<p />\n";

      $output .= "<table border=0 width=\"100%\">\n";

      //birth
      $event = $person->getBirthEvent();
      $edit = Event::GetSelectList('birth', Event::GetAll(), array(), $event);
      $edit .= "<button type=submit name=action value=\"new:birth\">new</button><br />\n";    
      $preview = '';
      if (!is_null($event))
      {
        $preview = $event->getEditLink();
      }
      $output .= $this->getEditorTableCells("birth", $edit, $preview);

      //death
      $event = $person->getDeathEvent();
      $edit = Event::GetSelectList('death', Event::GetAll(), array(), $event);
      $edit .= "<button type=submit name=action value=\"new:death\">new</button><br />\n";    
      $preview = '';
      if (!is_null($event))
      {
        $preview = $event->getEditLink();
      }
      $output .= $this->getEditorTableCells("death", $edit, $preview);

      //note
      $edit = "<textarea name=\"note\" rows=5 style=\"width:100%;\">\n";
      $edit .= htmlentities($person->getNote(), ENT_QUOTES);
      $edit .= "</textarea>\n";      
      $preview = $person->FormatForHTML($person->getNote());
      $output .= $this->getEditorTableCells("note", $edit, $preview);

      $output .= "</table>\n";

      $output .= $this->editList("Personal Events:", "events" , "Event", $person->getPersonalEvents());

      $output .= $this->editList("Families as a parent:", "parent" , "Family", $person->getFamiliesAsParent());
      $output .= $this->editList("Families as a child:", "child" , "Family", $person->getFamiliesAsChild());
      return $output;
    }
    
    public function processActionSave($person)
    {
      $person->updateFirstName($_REQUEST['firstname']);
      $person->updateLastName($_REQUEST['lastname']);
      $person->updateGender($_REQUEST['gender']);
      $person->updateNote($_REQUEST['note']);
      $person->updateBirthEvent(Event::Get($_REQUEST['birth']));
      $person->updateDeathEvent(Event::Get($_REQUEST['death']));
      $person->saveUpdates();
    }

    public function processAction()
    {
      list($action, $type, $id) = preg_split("/:/" , $_REQUEST['action']) + array('', '', '');
      switch ($action) 
      {
        case "delete":
          $person = Person::Get($_REQUEST['save']);
          switch ($type)
          {
            case "events":
              $person->deletePersonalEvents(Event::Get($id));
              break;
            case "parent":
              $family = Family::Get($id);
              if ($family->getFather() == $person)
              {
                $family->updateFather(NULL);
                $family->saveUpdates();
              }
              if ($family->getMother() == $person)
              {
                $family->updateMother(NULL);
                $family->saveUpdates();
              }
              break;
            case "child":
              $family = Family::Get($id);
              $family->deleteChildren($person);
              break;              
          }
          break;             
        case "add":
          $person = Person::Get($_REQUEST['save']);
          switch ($type)
          {
            case "events":
              $person->addPersonalEvents(Event::Get($_REQUEST['addevents']));
              break;
            case "parent":
              $family = Family::Get($_REQUEST['addparent']);
              if ($person->getGender() == Person::GENDER_FEMALE)
              {
                $family->updateMother($person);
                $family->saveUpdates();
              }
              else
              {
                $family->updateFather($person);
                $family->saveUpdates();
              }
              break;
            case "child":
              Family::Get($_REQUEST['addchild'])->addChildren($person);
              break;              
          }
          break;            
        case "new":
          $person = Person::Get($_REQUEST['save']);
          switch ($type)
          {
            case "events":
              $event = Event::Make();
              $person->addPersonalEvents($event);
              header("Location: ".$event->getEditURL(), true, 303);
              exit();
              break;
            case "parent":
              $family = Family::Make();
              if ($person->getGender() == Person::GENDER_FEMALE)
              {
                $family->updateMother($person);
                $family->saveUpdates();
              }
              else
              {
                $family->updateFather($person);
                $family->saveUpdates();
              }
              header("Location: ".$family->getEditURL(), true, 303);
              exit();
              break;
             case "child":
              $family = Family::Make();
              $family->addChildren($person);
              header("Location: ".$family->getEditURL(), true, 303);
              exit();
              break;
            case "birth":
              $event = Event::Make();
              $person->updateBirthEvent($event);
              $person->saveUpdates();
              header("Location: ".$event->getEditURL(), true, 303);
              exit();
              break;
            case "death":
              $event = Event::Make();
              $person->updateDeathEvent($event);
              $person->saveUpdates();
              header("Location: ".$event->getEditURL(), true, 303);
              exit();
              break;
          }
          break;            
      }
      return $person;
    }
  }
  
  $page = new EditorPeopleWebPage('Person');
  echo $page->getWebPage();
?>