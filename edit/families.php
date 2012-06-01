<?php

  include_once dirname(__FILE__)."/../phplibraries/html/editor_html_class.php";

  class EditorFamilyWebPage extends EditorWebPage
  {
    const DISPLAY_NAME = 'Family';
    
    public function getItemCustomContent()
    {
      $family = $this->getItem();
      $output = "<input type=submit name=action value=\"save\"></h2>\n";    
  
      $output .= "<p />\n";

      $output .= "<table border=0 width=\"100%\">\n";

      //father
      $person = $family->getFather();
      $edit = Event::GetSelectList('father', Person::GetAllGender(Person::GENDER_MALE), array(), $person);
      $edit .= "<button type=submit name=action value=\"new:father\">new</button><br />\n";    
      $preview = '';
      if (!is_null($person))
      {
        $preview = $person->getEditLink();
      }
      $output .= $this->getEditorTableCells("father", $edit, $preview);

      //mother
      $person = $family->getMother();
      $edit = Event::GetSelectList('mother', Person::GetAllGender(Person::GENDER_FEMALE), array(), $person);
      $edit .= "<button type=submit name=action value=\"new:mother\">new</button><br />\n";    
      $preview = '';
      if (!is_null($person))
      {
        $preview = $person->getEditLink();
      }
      $output .= $this->getEditorTableCells("mother", $edit, $preview);

      //marriage
      $event = $family->getMarriageEvent();
      $edit = Event::GetSelectList('marriage', Event::GetAll(), array(), $event);
      $edit .= "<button type=submit name=action value=\"new:marriage\">new</button><br />\n";    
      $preview = '';
      if (!is_null($event))
      {
        $preview = $event->getEditLink();
      }
      $output .= $this->getEditorTableCells("marriage", $edit, $preview);

      //divorce
      $event = $family->getDivorceEvent();
      $edit = Event::GetSelectList('divorce', Event::GetAll(), array(), $event);
      $edit .= "<button type=submit name=action value=\"new:divorce\">new</button><br />\n";    
      $preview = '';
      if (!is_null($event))
      {
        $preview = $event->getEditLink();
      }
      $output .= $this->getEditorTableCells("divorce", $edit, $preview);

      //note
      $output .= "<tr><td colspan=2>note</td></tr>\n";
      $output .= "<tr><td width=\"50%\" valign=top>\n";
      $output .= "<textarea name=\"note\" rows=5 style=\"width:100%;\">\n";
      $output .= htmlentities($family->getNote(), ENT_QUOTES);
      $output .= "</textarea>\n";
      $output .= "</td>";
      $output .= "<td width=\"50%\" valign=top>\n";
      $output .= $family->FormatForHTML($family->getNote());
      $output .= "</td></tr>\n";
    
      $output .= "<tr><td valign=top>\n";
    
      $output .= $this->editList("Parent Events:", "parentevent", "Event", $family->getParentsEvents());

      $output .= "</td><td valign=top>\n";

      $output .= $this->editList("Children:", "child", "Person", $family->getChildren());
      $output .= $this->editList("Children Events:", "childevent", "Event", $family->getChildrenEvents());

      $output .= "</td></tr>\n";
      $output .= "</table>\n";
      return $output;
    }
    
    public function processActionSave($family)
    {
      $family->updateFather(Person::Get($_REQUEST['father']));
      $family->updateMother(Person::Get($_REQUEST['mother']));
      $family->
      iageEvent(Event::Get($_REQUEST['marriage']));
      $family->updateDivorceEvent(Event::Get($_REQUEST['divorce']));
      $family->updateNote($_REQUEST['note']);
      $family->saveUpdates();
    }

    public function processAction()
    {
      $family = NULL;
      list($action, $type, $id) = preg_split("/:/" , $_REQUEST['action']) + array('', '', '');
      switch ($action) 
      {
        case "delete":
          $family = Family::Get($_REQUEST['save']);
          switch ($type)
          {
            case "child":
              $family->deleteChildren(Person::Get($id));
              break;
            case "parentevent":
              $family->deleteParentsEvents(Event::Get($id));
              break;
            case "childevent":
              $family->deleteChildrenEvents(Event::Get($id));
              break;
          }
          break;            
        case "add":
          $family = Family::Get($_REQUEST['save']);
          switch ($type)
          {
            case "child":
              $family->addChildren(Person::Get($_REQUEST['addchild']));
              break;
            case "parentevent":
              $family->addParentsEvents(Event::Get($_REQUEST['addparentevent']));
              break;
            case "childevent":
              $family->addChildrenEvents(Event::Get($_REQUEST['addchildevent']));
              break;
          }
          break;            
        case "new":
          $family = Family::Get($_REQUEST['save']);
          switch ($type)
          {
            case "child":
              $person = Person::Make();
              $family->addChildren($person);
              header("Location: ".$person->getEditURL(), true, 303);
              exit();
              break;
            case "parentevent":
              $event = Event::Make();
              $family->addParentsEvents($event);
              header("Location: ".$event->getEditURL(), true, 303);
              exit();
              break;
            case "childevent":
              $event = Event::Make();
              $family->addChildrenEvents($event);
              header("Location: ".$event->getEditURL(), true, 303);
              exit();
              break;
            case "father":
              $person = Person::Make();
              $family->updateFather($person);
              $family->saveUpdates();
              header("Location: ".$person->getEditURL(), true, 303);
              exit();
              break;
            case "mother":
              $person = Person::Make();
              $family->updateMother($person);
              $family->saveUpdates();
              header("Location: ".$person->getEditURL(), true, 303);
              exit();
              break;
            case "marriage":
              $event = Event::Make();
              $family->updateMarriageEvent($event);
              $family->saveUpdates();
              header("Location: ".$event->getEditURL(), true, 303);
              exit();
              break;
            case "divorce":
              $event = Event::Make();
              $family->updateDivorceEvent($event);
              $family->saveUpdates();
              header("Location: ".$event->getEditURL(), true, 303);
              exit();
              break;
          }
          break;            
      }
      return $family;
    }
  }
  
  $page = new EditorFamilyWebPage('Family');
  echo $page->getWebPage();
?>