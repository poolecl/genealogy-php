<?php

  include_once dirname(__FILE__)."/database_parameters.php";
  /*
   these constants should be in your database_parameters.php file
   const SQL_USERNAME = "username";
   const SQL_PASSWORD = "password";
   const SQL_DATABASE = "database name";
   const SQL_SERVER = 'database server';
  */

  class GenealogyDatabase
  {
    private static $database_opened = false;

    public static function Open()
    {
      if (!self::$database_opened)
      {
        mysql_connect(SQL_SERVER,SQL_USERNAME,SQL_PASSWORD);
        mysql_select_db(SQL_DATABASE) or die( "Unable to select database");
        
        self::$database_opened = true;
      }
    }
    
    public static function SetStringBoolean($key, $value)
    {
      if ($value)
      {
        return $key."=TRUE";
      }
      else
      {
        return $key."=FALSE";
      }
    }

    public static function SetStringBlankIsNull($key, $value)
    {
      if ($value == '')
      {
        return $key."=NULL";
      }
      else
      {
        return $key."='".mysql_real_escape_string($value)."'";
      }
    }
  
    public static function SetStringBlankIsWhat($key, $value, $what)
    {
      if ($value == '')
      {
        return $key."='".mysql_real_escape_string($what)."'";
      }
      else
      {
        return $key."='".mysql_real_escape_string($value)."'";
      }
    }
  
    public static function Close()
    {
      if (self::$database_opened)
      {
        mysql_close();
        
        self::$database_opened = false;
      }
    }
  }
?>