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

/* function shopTransition()
   transfers shop articles from the old Frogsystem to the new Frogsystem by
   copying the data from the old shop table to the new shop table. The images
   (thumbnails and full-sized) of the articles will be copied, too.

   table structures (old and new):

   fs_shop                                        fs2_shop
     artikel_id    SMALLINT(6), UNSIGNED, auto      artikel_id    MEDIUMINT(8), auto
     artikel_name  VARCHAR(100)                     artikel_name  VARCHAR(100)
     artikel_url   VARCHAR(255)                     artikel_url   VARCHAR(255)
     artikel_text  TEXT                             artikel_text  TEXT
     artikel_preis VARCHAR(10)                      artikel_preis VARCHAR(10)
     artikel_hot   TINYINT(4)                       artikel_hot   TINYINT(4)

     PRIMARY INDEX (artikel_id)                     PRIMARY INDEX (artikel_id)

   The new shop table will get its values from the old shop table, the structure
   is nearly the same. The auto-increment value of the new table will be
   adjusted to match the one of the old table.
   During that process ALL previously existing items within the new shop table
   will be deleted!

   The function will also copy the images of the articles, both the small
   thumbnail version and the larger version. If one of the images cannot be
   copied, the function fails, too.

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
function shopTransition($old_link, $new_link, $old_basedir, $new_basedir)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's shop table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'shop`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Shop-Tabelle '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $shop_entries = mysql_num_rows($result);
  if ($shop_entries!=1)
  {
    echo '<p>'.$shop_entries." Artikel im Shop gefunden.</p>\n";
  }
  else
  {
    echo '<p>Einen Eintrag in der Tabelle shop gefunden.</p>'."\n";
  }
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."shop'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte Shop-Tabelle '
        .'schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle shop '
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
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'shop` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen Shop-Tabelle'
        .' konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  $artikelarray = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'shop` '
                  .'(artikel_id, artikel_name, artikel_url, artikel_text, artikel_preis, artikel_hot) '
                  ."VALUES ('".$row['artikel_id']."', '".$row['artikel_name']."', '"
                  .$row['artikel_url']."', '".$row['artikel_text']."', '"
                  .$row['artikel_preis']."', '".$row['artikel_hot']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue Shop-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    //add current article id to array
    $artikelarray[] = (int) $row['artikel_id'];
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'shop` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Shop-Tabelle konnte nicht '
        .'aktualisert werden.<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  echo '<span>Fertig.</span>'."\n";

  //try to copy images for shop
  echo '<span>Verarbeite Bilder...</span>';
  foreach ($artikelarray as $value)
  {
    if (!is_int($value))
    {
      echo '<p class="error">Ung&uuml;ltiger Wert in artikelarray gefunden!'
          .' Abbruch.'."</p>\n";
      return false;
    }
    //check for existing file (large image)
    if (!file_exists($old_basedir.'images/shop/'.$value.'.jpg'))
    {
      echo '<p class="error">Das Bild f&uuml;r artikel_id '.$value.' konnte nicht'
          ." gefunden werden!</p>\n";
      return false;
    }
    //copy large artikel image
    if (!copy($old_basedir.'images/shop/'.$value.'.jpg', $new_basedir.'images/shop/'.$value.'.jpg'))
    {
      echo '<p class="error">Das Bild f&uuml;r artikel_id '.$value.' konnte '
          ."nicht kopiert werden!</p>\n";
      return false;
    }
    //check for existing file (small image)
    if (!file_exists($old_basedir.'images/shop/'.$value.'_s.jpg'))
    {
      echo '<p class="error">Das kleine Bild f&uuml;r artikel_id '.$value
          ." konnte nicht gefunden werden!</p>\n";
      return false;
    }
    //copy small artikel image
    if (!copy($old_basedir.'images/shop/'.$value.'_s.jpg', $new_basedir.'images/shop/'.$value.'_s.jpg'))
    {
      echo '<p class="error">Das kleine Bild f&uuml;r artikel_id '.$value
          ." konnte nicht kopiert werden!</p>\n";
      return false;
    }
  }//foreach
  echo '<span>Erfolg!</span><br>';
  return true;
}//function shopTransition

?>