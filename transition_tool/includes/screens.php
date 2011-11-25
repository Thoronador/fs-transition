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


/* function screenTransition()
   transfers screenshots from the old Frogsystem to the new Frogsystem by
   copying the data from the old screen table to the new screen table. The images
   (thumbnails and full-sized) will be copied, too.

   table structures (old and new):

   fs_screen                               fs2_screen
     screen_id   MEDIUMINT(8), auto_inc      screen_id MEDIUMINT(8), auto_inc
     cat_id      SMALLINT(6), UNSIGNED       cat_id    SMALLINT(6), UNSIGNED
     screen_name CHAR(100)                   screen_name VARCHAR(255)

     PRIMARY INDEX (screen_id)               PRIMARY INDEX (screen_id)
                                             INDEX(cat_id)

   The new screen table will get its values from the fields of the same name in
   the old screen table, the structure is nearly the same. The auto-increment
   value of the new table will be adjusted to match the one of the old table.
   During that process ALL previously existing entries within the new screen
   table will be deleted!
   The function will also copy the images, both the small thumbnail version and
   the larger version. If one of the images cannot be copied, the function
   fails, too.

   parameters:
       old_link    - the MySQL link identifier (resource type) for the
                     connection to the old database
       new_link    - the MySQL link identifier (resource type) for the
                     connection to the new database
       old_basedir - root directory of the old Frogsystem installation
       new_basedir - root directory of the new Frogsystem installation

   return value:
       true in case of success; false if failure
*/
function screenTransition($old_link, $new_link, $old_basedir, $new_basedir)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's screen table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'screen`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle screen '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $screen_entries = mysql_num_rows($result);
  if ($screen_entries!=1)
  {
    echo '<p>'.$screen_entries." Eintr&auml;ge in der Screenshottabelle gefunden.</p>\n";
  }
  else
  {
    echo '<p>Einen Eintrag in der Screenshottabelle gefunden.</p>'."\n";
  }
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."screen'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte screen-Tabelle '
        .'schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle screen '
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
  //delete possible content that is in new screen table
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'screen` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen screen-Tabelle'
        .' konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  $screenarray = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'screen` '
                  .'(screen_id, cat_id, screen_name) '
                  ."VALUES ('".$row['screen_id']."', '".$row['cat_id']."', '"
                  .$row['screen_name']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue screen-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    //add current screen id to array
    $screenarray[] = (int) $row['screen_id'];
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'screen` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Tabelle screen '
        .'konnte nicht aktualisiert werden.<br>Folgender Fehler trat beim'
        .' Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  echo '<span>Fertig.</span><br>'."\n";

  //try to copy screenshot images and thumbnails
  echo '<span>Kopiere Screenshots und Thumbnails...</span>';
  foreach ($screenarray as $value)
  {
    if (!is_int($value))
    {
      echo '<p class="error">Ung&uuml;ltiger Wert in screenarray gefunden! Abbruch.'."</p>\n";
      return false;
    }
    //check for existing file (large image)
    if (!file_exists($old_basedir.'images/screenshots/'.$value.'.jpg'))
    {
      echo '<p class="error">Das Bild f&uuml;r screen_id '.$value.' wurde nicht'
          ." gefunden!</p>\n";
      return false;
    }
    //copy large screenshot image
    if (!copy($old_basedir.'images/screenshots/'.$value.'.jpg', $new_basedir.'images/screenshots/'.$value.'.jpg'))
    {
      echo '<p class="error">Das Bild f&uuml;r screen_id '.$value.' konnte nicht'
          ." kopiert werden!</p>\n";
      return false;
    }
    //check for existing file (small image)
    if (!file_exists($old_basedir.'images/screenshots/'.$value.'_s.jpg'))
    {
      echo '<p class="error">Das Thumbnail-Bild f&uuml;r screen_id '.$value
          ." wurde nicht gefunden!</p>\n";
      return false;
    }
    //copy small thumbnail image
    if (!copy($old_basedir.'images/screenshots/'.$value.'_s.jpg', $new_basedir.'images/screenshots/'.$value.'_s.jpg'))
    {
      echo '<p class="error">Das Thumbnail-Bild f&uuml;r screen_id '.$value
          ." konnte nicht kopiert werden!</p>\n";
      return false;
    }
  }//foreach
  echo '<span>Erfolg!</span><br>';
  return true;
}//function screenTransition


/* function screen_catTransition()
   transfers screenshot categories from the old Frogsystem to the new Frogsystem
   by copying the data from the old screen_cat table to the new screen_cat table.

   table structures (old and new):

   fs_screen_cat                            fs2_screen_cat
     cat_id   SMALLINT(6), UNSIGNED, auto     cat_id         SMALLINT(6), auto_inc
     cat_name CHAR(100)                       cat_name       VARCHAR(255)
     cat_date INT(11)                         cat_type       TINYINT(1)
                                              cat_visibility TINYINT(1)
     PRIMARY INDEX (cat_id)                   cat_date       INT(11)
                                              randompic      TINYINT(1)

                                              PRIMARY INDEX (cat_id)

   The cat_id, cat_name and cat_date fields in the new table will get their
   values from the fields of the same name in the old table. cat_type will be
   set to 0 (zero) always, cat_visibility will be 1 (one), because every
   category of the old system was visible. randompic will be zero (0).
   The auto-increment value of the new screen_cat table will be set to the value
   of the old screen_cat table.
   During that process ALL previously existing screenshot categories within the
   new screen_cat table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function screen_catTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's screen_cat table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'screen_cat`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle screen_cat '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $s_cats = mysql_num_rows($result);
  if ($s_cats!=1)
  {
    echo '<p>'.$s_cats." Screenshotkategorien gefunden.</p>\n";
  }
  else
  {
    echo '<p>Eine Screenshotkategorie gefunden.</p>'."\n";
  }
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."screen_cat'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte screen_cat-Tabelle'
        .' schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle screen_cat'
        .' konnte nicht ermittelt werden.<br>Folgender Fehler trat auf:<br>';
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
  //delete possible content that is in new screen_cat table
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'screen_cat` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen screen_cat-'
        .'Tabelle konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat'
        .' beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'screen_cat` '
                  .'(cat_id, cat_name, cat_type, cat_visibility, cat_date, randompic) '
                  ."VALUES ('".$row['cat_id']."', '".$row['cat_name']."', 1, 1, '"
                  .$row['cat_date']."', 0)", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue screen_cat-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'screen_cat` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen screen_cat-Tabelle'
        .' konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function screen_catTransition


/* function screen_configTransition()
   transfers screenshot configuration settings from the old Frogsystem to the
   new Frogsystem by updating the data of the new screen_config table with
   values from the old screen_config table.

   table structures (old and new):

   fs_screen_config          fs2_screen_config
     screen_x INT(11)          id TINYINT(1)
     screen_y INT(11)          screen_x INT(4)
     thumb_x  INT(11)          screen_y INT(4)
     thumb_y  INT(11)          screen_thumb_x INT(4)
                               screen_thumb_y INT(4)
                               screen_size INT(4)
                               screen_rows INT(2)
                               screen_cols INT(2)
                               screen_order VARCHAR(10)
                               screen_sort VARCHAR(4)
                               show_type TINYINT(1)
                               show_size_x SMALLINT(4)
                               show_size_y SMALLINT(4)
                               show_img_x INT(4)
                               show_img_y INT(4)
                               wp_x INT(4)
                               wp_y INT(4)
                               wp_thumb_x INT(4)
                               wp_thumb_y INT(4)
                               wp_order VARCHAR(10)
                               wp_size INT(4)
                               wp_rows INT(2)
                               wp_cols INT(2)
                               wp_sort VARCHAR(4)

                               PRIMARY INDEX (id)

   The new table should already have one row of own data, and this function
   updates this row with the corresponding data from the old table. All fields
   except screen_x, screen_y, screen_thumb_x, screen_thumb_y, screen_order and
   screen_sort remain unchanged. screen_x and screen_y are set to the values of
   the same name from the old table; screen_thumb_x and screen_thumb_y are set
   to the value of thumb_x and thumb_y of the old table. screen_order will be
   set to 'id' and screen_sort will be set to 'asc' to get the usual sorting
   style of the old Frogsystem.
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
function screen_configTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's screen_config table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'screen_config`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle screen_config '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $entries = mysql_num_rows($result);
  if ($entries<1)
  {
    echo '<p class="error">Die alte screen_config-Tabelle hat keine '
        .'Eintr&auml;ge. Der Vorgang wird abgebrochen!'."</p>\n";
    return false;
  }
  if ($entries==1)
  {
    echo '<p>Einen Eintrag in der alten Screenshotkonfigurationstabelle gefunden.</p>'."\n";
  }
  else if ($entries>1)
  {
    echo '<p class="hint">'.$entries.' Eintr&auml;ge in der Tabelle '
        .'screen_config gefunden, aber nur der erste davon wird '
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
  $query_res = mysql_query('SELECT COUNT(id) AS count FROM `'.NewDBTablePrefix.'screen_config`', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die neue Tabelle screen_config '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  if (!($row = mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Abfrageergebnis der neuen screen_config-Tabelle'
        .' konnte nicht ermittelt werden.</p>';
    return false;
  }//if
  //check for number of entries
  if ($row['count']<1)
  {
    echo '<p class="error">Die neue screen_config-Tabelle enth&auml;t keine Konfiguration!</p>';
    return false;
  }
  //update configuration in new DB's table
  echo '<span>Aktualisiere Screenshotkonfiguration...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('UPDATE `'.NewDBTablePrefix.'screen_config` '
                  ."SET screen_x='".$row['screen_x']."' , screen_y='".$row['screen_y']
                  ."' , screen_thumb_x='".$row['thumb_x']."' , screen_thumb_y='"
                  .$row['thumb_y']."', screen_order='id', screen_sort='asc'",
                  $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Die Werte in der neuen screen_config-Tabelle '
          .'konnten nicht aktualisiert werden.<br>Folgender Fehler trat beim '
          .'Versuch auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    $affected = mysql_affected_rows($new_link);
    if ($affected<0)
    {
      //This should never happen, -1 only occurs on query failure.
      echo '<p class="error">Aktualisierung der Tabelle screen_config ist '
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
      echo '<p>Screenshotkonfigurationen der alten und neuen Tabelle stimmen '
          .' bereits &uuml;berein.</p>';
    }
  }//if
  else
  {
    echo '<p class="error">Die Werte aus der alten screen_config-Tabelle '
        .'konnten nicht ermittelt werden!</p>';
    return false;
  }//else branch
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function screen_configTransition

?>