<html>
  <head>
    <link href="people.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript">
      function populateDiv(div,person)
      {
        var xmlhttp;
        if (window.XMLHttpRequest)
        {// code for IE7+, Firefox, Chrome, Opera, Safari
          xmlhttp=new XMLHttpRequest();
        }
        else
        {// code for IE6, IE5
          xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange=function()
        {
          if (xmlhttp.readyState==4 && xmlhttp.status==200)
          {
            document.getElementById(div).innerHTML=xmlhttp.responseText;
          }
        }
        xmlhttp.open("GET","peoplediv.php?person="+person+"&div="+div,true);
        xmlhttp.send();
      }
      function populateDocument(person)
      {
        populateDiv("personName",person);
        populateDiv("mapLink",person);
        populateDiv("personGender",person);
        populateDiv("personStats",person);
        populateDiv("personTimeline",person);
        populateDiv("personFamilytree",person);
      }
      function populate()
      {
<?php
  include_once dirname(__FILE__)."/phplibraries/html/display_html_class.php";
  include_once dirname(__FILE__)."/phplibraries/html/family_table_class.php";
  include_once dirname(__FILE__)."/phplibraries/genealogy/gsql-includes.php";
  include_once dirname(__FILE__)."/phplibraries/auth.php";
  
  $file = $_SERVER['QUERY_STRING'];
  $person = NULL;
  if ($file != '')
  {
    $person = Person::Get($file);
  }
  if (is_null($person))
  {
    $person = Person::Get(48);
  }
  echo "        populateDocument('".$person->getID()."');\n";
  echo "      }\n";
  echo "    </script>\n";
  echo "    <title>\n";
  echo "      Genealogy Person Viewer/AJAX\n";
  echo "      - ".$person->getName()."\n";

?>
    </title>
  </head>
  <body onload="populate()">
  <div id="personName" class="personName">
  </div>
  <div id="mapLink" class="mapLink">
  </div>
  <div id="personGender" class="personGender">
  </div>
  <div id="personStats" class="personStats">
  </div>
  <div class="personTimelineFamilytreeBody">
    <div id="personTimeline" class="personTimeline">
    </div>
    <div id="personFamilytree" class="personFamilytree">
    </div>
  </div>
  </body>
</html>