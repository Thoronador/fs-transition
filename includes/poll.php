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

require_once 'connect.inc.php';

/* function pollTransition()
   transfers polls from the old Frogsystem to the new Frogsystem.

   table structures (old and new):

   fs_poll                                 fs2_poll
     poll_id    MEDIUMINT(8), auto_inc       poll_id           MEDIUMINT(8), auto_inc
     poll_quest CHAR(255)                    poll_quest        VARCHAR(255)
     poll_start INT(11)                      poll_start        INT(11)
     poll_end   INT(11)                      poll_end          INT(11)
     poll_type  TINYINT(4)                   poll_type         TINYINT(4)
                                             poll_participants MEDIUMINT(8)
     PRIMARY INDEX (poll_id)
                                             PRIMARY INDEX (poll_id)
   fs_poll_answers
     poll_id      MEDIUMINT(8)
     answer_id    MEDIUMINT(8), auto_inc
     answer       CHAR(255)
     answer_count MEDIUMINT(8)

     PRIMARY INDEX (answer_id)

   Table structures are almost the same, so every field in the new table gets
   its value from the field with the same name in the old table, except
   poll_participants. That field's value will be calculated as the sum of the
   old poll_answers table's answer_count fields belonging to the same poll.

   The auto-increment value of the new table will be adjusted to match the one
   of the old table.
   During the transition process ALL previously existing polls within the new
   poll table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function pollTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's poll table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'poll`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle poll '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $poll_num = mysql_num_rows($result);
  if ($poll_num!=1)
  {
    echo '<p>'.$poll_num." Umfragen im alten FS gefunden.</p>\n";
  }
  else
  {
    echo '<p>Eine Umfrage im alten FS gefunden.</p>'."\n";
  }
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."poll'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte poll-Tabelle '
        .'schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle poll '
        .'konnte nicht ermittelt werden.<br>Folgender Fehler trat auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $auto_inc_value = $row['Auto_increment'];

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'poll` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen poll-Tabelle '
        .'konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;ft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'poll` '
                  .'(poll_id, poll_quest, poll_start, poll_end, poll_type, poll_participants) '
                  ."VALUES ('".$row['poll_id']."', '".$row['poll_quest']."', '"
                  .$row['poll_start']."', '".$row['poll_end']."', '"
                  .$row['poll_type']."', 1)", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue poll-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'poll` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Tabelle poll '
        .'konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //now try to get the participant count right
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht erneut '
        .'ausgew&auml;hlt werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $result = mysql_query('SELECT poll_id, SUM(answer_count) AS participants FROM `'
                        .OldDBTablePrefix.'poll_answers` GROUP BY poll_id', $old_link);
  if ($result==false)
  {
    echo '<p class="error">Die SQL-Abfrage f&uuml;r die alte poll_answers-Tabelle'
        .' zur Berechnung der Umfrageteilnehmer konnte nicht ausgef&uuml;hrt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //switch to new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //fetch new row from previous result
  while ($row = mysql_fetch_assoc($result))
  {
    //set participant count
    $query_res = mysql_query('UPDATE `'.NewDBTablePrefix."poll` SET poll_participants='"
                  .$row['participants']."' WHERE poll_id='".$row['poll_id']."'");
    if (!$query_res)
    {
      echo '<p class="error">Die Teilnehmeranzahl einer Umfrage in der neuen '
          .'poll-Tabelle konnte nicht in aktualisiert werden.<br>Folgender '
          .'Fehler trat beim Versuch auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function pollTransition


/* function poll_answersTransition()
   transfers poll answers from the old Frogsystem to the new Frogsystem.

   table structures (old and new):

   fs_poll_answers                         fs2_poll_answers
     poll_id      MEDIUMINT(8)               poll_id      MEDIUMINT(8)
     answer_id    MEDIUMINT(8), auto_inc     answer_id    MEDIUMINT(8), auto:inc
     answer       CHAR(255)                  answer       VARCHAR(255)
     answer_count MEDIUMINT(8)               answer_count MEDIUMINT(8)

     PRIMARY INDEX (answer_id)               PRIMARY INDEX (answer_id)

   Table structures are almost the same, so every field in the new table gets
   its value from the field with the same name in the old table. The auto-
   increment value of the new table will be adjusted to match the one of the old
   table.
   During the transition process ALL previously existing poll answers within the
   new poll_answers table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function poll_answersTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all anwer stuff from old DB's poll table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'poll_answers` WHERE 1', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle poll_answers'
        .' konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $answer_num = mysql_num_rows($result);
  if ($answer_num!=1)
  {
    echo '<p>'.$answer_num." Umfrageantworten im alten FS gefunden.</p>\n";
  }
  else
  {
    echo '<p>Eine Umfrageantwort im alten FS gefunden. Welch eine sinnlose Art '
        .'von Umfrage ist das denn?</p>'."\n";
  }
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."poll_answers'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte poll_answers-'
        .'Tabelle schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle poll_answers'
        . 'konnte nicht ermittelt werden.<br>Folgender Fehler trat auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $auto_inc_value = $row['Auto_increment'];

   //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'poll_answers` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen poll_answers-'
        .'Tabelle konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat'
        .' beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;ft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'poll_answers` '
                  .'(poll_id, answer_id, answer, answer_count) '
                  ."VALUES ('".$row['poll_id']."', '".$row['answer_id']."', '"
                  .$row['answer']."', '".$row['answer_count']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue poll_answers-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto-increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'poll_answers` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Tabelle poll_answers'
        .'konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function poll_answersTransition

?>