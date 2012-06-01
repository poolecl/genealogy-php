<?php
  include_once dirname(__FILE__)."/phplibraries/genealogy/gsql-includes.php";

  $person = Person::SearchName("Richard", "Pierce");
  if (is_null($person))
  {
    header("Location: ".Person::DISPLAY_URL, true, 303);
  }
  else
  {
    header("Location: ".$person->getDisplayURL(), true, 303);
  }
?>