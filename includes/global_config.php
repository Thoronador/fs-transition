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

/* function global_configTransition()
   transfers configuration settings from the old Frogsystem to the new Frogsystem
   by copying the data from the old global_config table to the new global_config
   table.

   table structures (old and new):

   fs_global_config             fs2_global_config
     virtualhost CHAR(255)        id                   TINYINT(1), PRIMARY INDEX
     admin_mail  CHAR(100)        version              VARCHAR(10)
                                  virtualhost          VARCHAR(255)
                                  admin_mail           VARCHAR(100)
                                  title                VARCHAR(255)
                                  dyn_title            TINYINT(1)
                                  dyn_title_ext        VARCHAR(255)
                                  description          TEXT
                                  keywords             TEXT
                                  publisher            VARCHAR(255)
                                  copyright            TEXT
                                  show_favicon         TINYINT(1)
                                  style_id             MEDIUMINT(8)
                                  style_tag            VARCHAR(30)
                                  allow_other_designs  TINYINT(1)
                                  date                 VARCHAR(255)
                                  time                 VARCHAR(255)
                                  datetime             VARCHAR(255)
                                  page                 TEXT
                                  page_next            TEXT
                                  page_prev            TEXT
                                  random_timed_deltime INT(12)
                                  feed                 VARCHAR(15)
                                  language_text        VARCHAR(5)
                                  home                 TINYINT(1)
                                  home_text            VARCHAR(100)
                                  auto_forward         INT(2)
                                  search_index_update  TINYINT(1)
                                  search:index_time    INT(11)
   
   The new table should already have one row of own data, and this function
   updates this row with the corresponding data from the old table. All fields
   except virtualhost and admin_mail remain unchanged. virtualhost and
   admin_mail are set to the values retrieved from the old table.
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
function global_configTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's global_config table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'global_config`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle global_config '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $entries = mysql_num_rows($result);
  if ($entries<1)
  {
    echo '<p class="error">Die alte global_config-Tabelle hat keine '
        .'Eintr&auml;ge. Der Vorgang wird abgebrochen!'."</p>\n";
    return false;
  }
  if ($entries==1)
  {
    echo '<p>Einen Eintrag in der alten Tabelle global_config gefunden.</p>'."\n";
  }
  else if ($entries>1)
  {
    echo '<p class="hint">'.$entries.' Eintr&auml;ge in der Tabelle '
        .'global_config gefunden, aber nur der erste davon wird '
        .'ber&uuml;cksichtigt.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //check that there is one entry in the new configuration table
  $query_res = mysql_query('SELECT COUNT(id) AS count FROM `'.NewDBTablePrefix.'global_config`', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die neue Tabelle global_config '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  if (!($row = mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Abfrageergebnis der neuen global_config-Tabelle'
        .' konnte nicht ermittelt werden.</p>';
    return false;
  }//if
  //check for number of entries
  if ($row['count']<1)
  {
    echo '<p class="error">Die neue global_config-Tabelle enth&auml;t keine Konfiguration!</p>';
    return false;
  }

  //update configuration in new DB's table
  echo '<span>Aktualisiere globale Konfiguration...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    //check for length of virtualhost
    if (strlen($row['virtualhost'])<10)
    {
      echo '<p class="error">Der Eintrag virtualhost in der alten '
          .'global_config-Tabelle scheint zu kurz zu sein, um eine komplette '
          .'URL beinhalten zu k&ouml;nnen!</p>';
      return false;
    }
    //check for trailing slash in virtualhost setting
    if (substr($row['virtualhost'], -1)!='/')
    {
      $row['virtualhost'] .= '/';
    }
    //now execute the update query on the new configuration table
    $query_res = mysql_query('UPDATE `'.NewDBTablePrefix.'global_config` '
                  ."SET virtualhost='".$row['virtualhost']."', admin_mail='"
                  .$row['admin_mail']."'", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Die Werte in der neuen global_config-Tabelle '
          .'konnten nicht aktualisiert werden.<br>Folgender Fehler trat beim '
          .'Versuch auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    $affected = mysql_affected_rows($new_link);
    if ($affected<0)
    {
      //This should never happen, -1 only occurs on query failure.
      echo '<p class="error">Aktualisierung der Tabelle global_config ist '
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
      echo '<p>Die globalen Konfigurationen der alten und neuen Tabelle stimmen'
          .' bereits &uuml;berein.</p>';
    }
  }//if
  else
  {
    echo '<p class="error">Die Werte aus der alten global_config-Tabelle '
        .'konnten nicht ermittelt werden!</p>';
    return false;
  }//else branch
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function global_configTransition

?>