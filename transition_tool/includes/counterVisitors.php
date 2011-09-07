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


/* function counter_statTransition()
   transfers counter statistics from the old Frogsystem to the new Frogsystem
   by copying the data from the old counter_stat table to the new counter_stat
   table.

   table structures (old and new):

   fs_counter_stat             fs2_counter_stat
     s_year   INT(4)             s_year   INT(4)
     s_month  INT(2)             s_month  INT(2)
     s_day    INT(2)             s_day    INT(2)
     s_visits INT(11)            s_visits INT(11)
     s_hits   INT(11)            s_hits   INT(11)

     INDEX (s_year,              PRIMARY INDEX (s_year,
            s_month, s_day)              s_month, s_day)

   Since the table structure is nearly the same, transition is straightforward,
   no need to explain. However, the index in the old table is not unique, but
   the one in the new table is, so we will lose table rows, if there are days
   with more than one entry.
   During the transition process ALL previously existing data within the new
   counter_stat table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function counter_statTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's counter_stat table
  // ---- GROUP BY is neccessary to supress multiple occurences of the same day
  //      within the data, because the index in the old table is not unique
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'counter_stat` '
           .'GROUP BY s_year, s_month, s_day' , $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle '
        .'counter_stat konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler'
        .' trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $stat_entries = mysql_num_rows($result);
  if ($stat_entries!=1)
  {
    echo '<p>'.$stat_entries." Eintr&auml;ge in der Tabelle counter_stat gefunden.</p>\n";
  }
  else
  {
    echo '<p>Einen Eintrag in der Tabelle counter_stat gefunden.</p>'."\n";
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
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'counter_stat` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen Tabelle'
        .'counter_stat konnten nicht gel&ouml;scht werden.<br>Folgender Fehler '
        .'trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'counter_stat` '
                  .'(s_year, s_month, s_day, s_visits, s_hits) '
                  ."VALUES ('".$row['s_year']."', '".$row['s_month']."', '"
                  .$row['s_day']."', '".$row['s_visits']."', '".$row['s_hits']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue counter_stat-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function counter_statTransition


/* function counterTransition()
   transfers gerneral counter data from the old Frogsystem to the new Frogsystem
   by copying the data from the old counter table to the new counter table.

   table structures (old and new):

   fs_counter                  fs2_counter
     visits   INT(11)            id       TINYINT(1), PRIMARY INDEX
     hits     INT(11)            visits   INT(11)
     user     MEDIUMINT(8)       hits     INT(11)
     artikel  SMALLINT(6)        user     MEDIUMINT(8)
     news     SMALLINT(6)        artikel  SMALLINT(6)
     comments MEDIUMINT(8)       news     SMALLINT(6)
                                 comments MEDIUMINT(8)

   Since the table structure is almost the same, transition is straightforward.
   The new table should already have one row of own data, and this function
   updates this row with the corresponding data from the old table. The id field
   remains unchanged.
   If there is not at least one row in the new table, the function will return
   false, indicating failure. However, this should not happen with a proper
   installation of Frogsystem 2.

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function counterTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's counter table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'counter`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle counter '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $entries = mysql_num_rows($result);
  if ($entries<1)
  {
    echo '<p class="error">Die alte counter-Tabelle hat keine Eintr&auml;ge. '
        .'Der Vorgang wird abgebrochen!'."</p>\n";
    return false;
  }
  if ($entries==1)
  {
    echo '<p>Einen Eintrag in der Tabelle counter gefunden.</p>'."\n";
  }
  else if ($entries>1)
  {
    echo '<p class="hint">'.$entries.' Eintr&auml;ge in der Tabelle counter '
        .'gefunden, aber nur der erste davon wird ber&uuml;cksichtigt.</p>'
        ."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //check that there is one entry in the counter table
  $query_res = mysql_query('SELECT COUNT(id) AS count FROM `'.NewDBTablePrefix.'counter`');
  if (!$query_res)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die neue Tabelle counter '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  if (!($row = mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Abfrageergebnis der neuen counter-Tabelle konnte nicht '
        .'ermittelt werden.</p>';
    return false;
  }//if
  //check for number of entries
  if ($row['count']<1)
  {
    echo '<p class="error">Die neue counter-Tabelle enth&auml;t keine Eintr&auml;ge!</p>';
    return false;
  }

  //update stats in new counter table
  echo '<span>Verarbeitung...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    //execute the update query on the new configuration table
    $query_res = mysql_query('UPDATE `'.NewDBTablePrefix.'counter` '
                  ."SET visits='".$row['visits']."', hits='".$row['hits']
                  ."', user='".$row['user']."', artikel='".$row['artikel']
                  ."', news='".$row['news']."', comments='".$row['comments']
                  ."'", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Die Werte in der neuen counter-Tabelle konnten '
          .'nicht aktualisiert werden.<br>Folgender Fehler trat beim '
          .'Versuch auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    $affected = mysql_affected_rows($new_link);
    if ($affected<0)
    {
      //This should never happen, -1 only occurs on query failure.
      echo '<p class="error">Aktualisierung der Tabelle counter ist '
          .'fehlgeschlagen, kein Datensatz konnte aktualisiert werden!</p>';
      return false;
    }
    if ($affected==0)
    {
      //This should usually not happen, but this can be a possible result for a
      // new table that already has the same settings like the old table, so no
      // row was actually affected/changed. Can also happen, if there is no row.
      // However, we know from previous COUNT() query, that there is at least
      // one data row in the table, so everything is alright here.
      echo '<p>Die counter-Tabellen des alten und neuen FS haben schon den '
          .'gleichen Inhalt.</p>';
    }
  }//if
  else
  {
    echo '<p class="error">Die Werte aus der alten counter-Tabelle konnten nicht'
        .' ermittelt werden!</p>';
    return false;
  }//else branch
  echo '<span>Fertig.</span>'."\n";
  return true;
}//funcion counterTransition

?>