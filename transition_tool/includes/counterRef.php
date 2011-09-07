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

require_once 'connect.inc.php'; //required for selectOldDB() and selectNewDB()

/* function counter_refTransition()
   transfers reference counter data from the old Frogsystem to the new Frog-
   system by copying the data from the old counter_ref table to the new
   counter_ref table.

   table structures (old and new):

   fs_counter_ref                   fs2_counter_ref
     ref_url   CHAR(255), INDEX       ref_url   VARCHAR(255), INDEX
     ref_count INT(11)                ref_count INT(11)
     ref_date  INT(11)                ref_first INT(11)
                                      ref_last  INT(11)

   The ref_url and ref_count fields in the new table will be filled with the
   data from the fields of the same name in the old table, with the change that
   ref_url will be escaped.
   The field ref_first gets the value from the old ref_date field, because the
   old Frogsystem only kept track of the first occurence. The ref_last field
   will be set to zero(0), because we don't have any data for it. That causes
   all fields to have the 1st of January 1970 as date. This way I try to
   indicate that there is no data. I could have decided to set it to the same
   value as ref_first instead, but that would give the wrong impression of
   actually having some real data for that field although it can't be provided.
   Before the insertion of the new values the table index will be deactivated
   to speed up the insertion. As soon as the transition is finished, the index
   will be reactivated and rebuilt. (It is faster that way than updating the
   index after every insert operation.)
   During the transition process ALL previously existing data within the new
   counter_ref table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function counter_refTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's counter_stat table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'counter_ref`', $old_link);
  if ($result===false)
  {
     echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle counter_ref '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $ref_entries = mysql_num_rows($result);
  if ($ref_entries!=1)
  {
    echo '<p>'.$ref_entries." Eintr&auml;ge in der Tabelle counter_ref gefunden.</p>\n";
  }
  else
  {
    echo '<p>Einen Eintrag in der Tabelle counter_ref gefunden.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'counter_ref` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen Tabelle'
        .'counter_ref konnten nicht gel&ouml;scht werden.<br>Folgender Fehler '
        .'trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //disable keys temporatily to speed up inserts
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'counter_ref` DISABLE KEYS', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die Aktualisierung des Indexes in der neuen '
        .'counter_ref-Tabelle konnte nicht vorr&uuml;bergehend deaktiviert '
        .'werden.<br>Folgender Fehler trat bei dem Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  $has_to_do = true;
  while ($has_to_do)
  {
    $query_string = 'INSERT INTO `'.NewDBTablePrefix.'counter_ref` '
                   .'(ref_url, ref_count, ref_first, ref_last) VALUES ';
    $row_count = 0;
    while (($row = mysql_fetch_assoc($result)) && ($row_count<25))
    {
      $row_count = $row_count + 1;
      $query_string .= "('".mysql_real_escape_string($row['ref_url'], $new_link)
                      ."', '".$row['ref_count']."', '".$row['ref_date']."', 0),";
    }//while
    $has_to_do = ($row!==false);
    if ($row_count>0)
    {
      //cut of the ',' character at the end to prevent SQL syntax error
      $query_string = substr($query_string, 0, -1);
      //execute query to add new rows
      $query_res = mysql_query($query_string, $new_link);
      if (!$query_res)
      {
        echo '<p class="error">Ein Wert konnte nicht in die neue counter_ref-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
        echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
        //try to reactivate the index we've disabled earlier
        $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'counter_ref` ENABLE KEYS', $new_link);
        if (!$query_res)
        {
          echo '<p class="error">Die Aktualisierung des Indexes in der neuen '
              .'counter_ref-Tabelle konnte nicht wieder aktiviert werden.<br>'
              .'Folgender Fehler trat auf:<br>';
          echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
        }//if
        return false;
      }//if
    }//if
  }//while (outer)

  //re-enable keys
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'counter_ref` ENABLE KEYS', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die Aktualisierung des Indexes in der neuen '
        .'counter_ref-Tabelle konnte nicht wieder aktiviert werden.<br>'
        .'Folgender Fehler trat bei dem Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  echo '<span>Fertig.</span>'."\n";
  return true;
}//function counter_refTransition

?>