<?php
  include_once dirname(__FILE__)."/auth.php";
  include_once dirname(__FILE__)."/genealogy/gsql-includes.php";
  
  function usort_people_by_birth($a, $b)
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
  
  function javascript_select_change_function($name, $object, $choices, $default)
  {
    $output = "function ".$name."(choice)\n{\n";
    $output .= "  switch(choice)\n  {\n";
    foreach ($choices as $id => $items)
    {
      $output .= "    case '".$id."':\n";
      $output .= "      ".$object.".options.length=0;\n";
      $i = 0;
      foreach ($items as $item)
      {
        $output .= "      ".$object.".options[".$i++."]=new Option(\"".htmlentities($item->getTitle(), ENT_QUOTES)."\", \"".htmlentities($item->getID(), ENT_QUOTES)."\", false, false);\n";
        if (isset($default) && in_array($default, $items))
        {
          $output .= "      ".$object.".value='".htmlentities($default->getID(), ENT_QUOTES)."';\n";
        }
      }
      $output .= "      break;\n";
    }
    $output .= "  }\n";
    $output .= "}\n";
    return $output;
  }
?>