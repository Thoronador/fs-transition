<?php

require_once 'config_constants.inc.php';

function connectOldDB()
{
  return mysql_connect('localhost', OldDBUser, OldDBPassword);
}//function

function connectNewDB()
{
  return mysql_connect('localhost', NewDBUser, NewDBPassword);
}//function


function selectOldDB($link)
{
  return mysql_select_db(OldDBName, $link);
}//function

function selectNewDB($link)
{
  return mysql_select_db(NewDBName, $link);
}//function

?>