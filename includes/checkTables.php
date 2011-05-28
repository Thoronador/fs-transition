<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2011  Thoronador

    The Frogsystem Transition Tool is free software: you can redistribute it
    and/or modify it under the terms of the GNU General Public License as
    published by the Free Software Foundation, either version 3 of the License,
    or (at your option) any later version.

    The Frogsystem Transition Tool is distributed in the hope that it will be
    useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once 'includes/config_constants.inc.php';

/* checks for the presence of the tables of the old FS and returns true in case
   of success or a string containing an error message (in HTML) in case of
   failure.

   parameters:
       link - the MySQL connection
*/

function checkOldTables($link)
{
  $result = mysql_query('SHOW TABLES FROM '.OldDBName." LIKE '".OldDBTablePrefix."%'", $link);
  if (!$result)
  {
    return '<p>Eine SQL-Abfrage konnte nicht ausgef&uuml;hrt werden!'
          .'<br>Folgender Fehler trat beim Versuch auf:<br>'.mysql_errno()
          .': '.htmlentities(mysql_error())."</p>\n";
  }
  $tables_found = array();
  while ($row = mysql_fetch_row($result))
  {
    $tables_found[] = $row[0];
  }//while
  //list of required tables (not complete yet)
  $tables_required = array(
      OldDBTablePrefix.'anouncement', OldDBTablePrefix.'artikel',
      OldDBTablePrefix.'dl', OldDBTablePrefix.'dl_cat',
      OldDBTablePrefix.'dl_mirrors', OldDBTablePrefix.'global_config',
      OldDBTablePrefix.'news', OldDBTablePrefix.'news_cat',
      OldDBTablePrefix.'news_comments', OldDBTablePrefix.'news_config',
      OldDBTablePrefix.'news_links', OldDBTablePrefix.'permissions',
      OldDBTablePrefix.'poll', OldDBTablePrefix.'poll_answers',
      OldDBTablePrefix.'screen', OldDBTablePrefix.'screen_cat',
      OldDBTablePrefix.'screen_config', OldDBTablePrefix.'shop',
      OldDBTablePrefix.'user');
  //check for each table
  foreach($table_required as $value)
  {
    if (!in_array($value, $tables_found))
    {
      return '<p>Eine der Tabellen des alten FS konnte nicht gefunden werden!'
          .'<br>Der Tabellenname lautet &quot;'.htmlentities($value)."&quot;.</p>\n";
    }
  }//foreach
  return true;
}//function checkOldTables


/* checks for the presence of the tables of the new FS and returns true in case
   of success or a string containing an error message (in HTML) in case of
   failure.

   parameters:
       link - the MySQL connection
*/

function checkNewTables($link)
{
  $result = mysql_query('SHOW TABLES FROM '.NewDBName." LIKE '".NewDBTablePrefix."%'", $link);
  if (!$result)
  {
    return '<p>Eine SQL-Abfrage konnte nicht ausgef&uuml;hrt werden!'
          .'<br>Folgender Fehler trat beim Versuch auf:<br>'.mysql_errno()
          .': '.htmlentities(mysql_error())."</p>\n";
  }
  $tables_found = array();
  while ($row = mysql_fetch_row($result))
  {
    $tables_found[] = $row[0];
  }//while
  //list of required tables (not complete yet)
  $tables_required = array(
      NewDBTablePrefix.'announcement', NewDBTablePrefix.'articles',
      NewDBTablePrefix.'articles_cat', NewDBTablePrefix.'articles_config',
      NewDBTablePrefix.'dl', NewDBTablePrefix.'dl_cat',
      NewDBTablePrefix.'dl_config', NewDBTablePrefix.'dl_files',
      NewDBTablePrefix.'news', NewDBTablePrefix.'news_cat',
      NewDBTablePrefix.'news_comments', NewDBTablePrefix.'news_config',
      NewDBTablePrefix.'news_links', NewDBTablePrefix.'poll',
      NewDBTablePrefix.'poll_answers', NewDBTablePrefix.'poll_config',
      NewDBTablePrefix.'poll_voters', NewDBTablePrefix.'screen',
      NewDBTablePrefix.'screen_cat', NewDBTablePrefix.'screen_config',
      NewDBTablePrefix.'shop', NewDBTablePrefix.'user',
      NewDBTablePrefix.'user_permissions',);
  //check for each table
  foreach($table_required as $value)
  {
    if (!in_array($value, $tables_found))
    {
      return '<p>Eine der Tabellen des neuen FS konnte nicht gefunden werden!'
          .'<br>Der Tabellenname lautet &quot;'.htmlentities($value)."&quot;.</p>\n";
    }
  }//foreach
  return true;
}//function checkNewTables

?>