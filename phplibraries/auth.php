<?php

  include_once dirname(__FILE__)."/auth_parameters.php";
  /*
   these should have an array of authorized users
   $authorized_users = array(
     'username' => 'password');
  */
  
  function isAuthorized()
  {
    $authorized_users = $GLOBALS['authorized_users'];
    return (
      $_SERVER['PHP_AUTH_USER'] != '' &&
      $_SERVER['PHP_AUTH_PW'] != '' &&
      $authorized_users[$_SERVER['PHP_AUTH_USER']] === $_SERVER['PHP_AUTH_PW']);
  }

?>