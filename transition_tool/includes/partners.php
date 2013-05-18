<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2013  Thoronador

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

/* function partnerTransition()
   transfers affiliates from the old Frogsystem to the new Frogsystem by copying
   the data from the old partner table to the new partner table.
   Partner images/icons will be copied, too.

   table structures (old and new):

   fs_partner (old)
     partner_id           SMALLINT(3), auto_inc
     partner_bild88x31    VARCHAR(10)
     partner_bild120x50   VARCHAR(10)
     partner_name         VARCHAR(150)
     partner_link         VARCHAR(250)
     partner_beschreibung TEXT
     partner_permanent    TINYINT(1) unsigned

   PRIMARY KEY (partner_id)


   fs2_partner (new)
     partner_id           SMALLINT(3) unsigned, auto_inc
     partner_name         VARCHAR(150)
     partner_link         VARCHAR(250)
     partner_beschreibung TEXT
     partner_permanent    TINYINT(1) unsigned

   PRIMARY KEY (partner_id)

   The fields partner_id, partner_name, partner_link, partner_beschreibung and
   partner_permanent get their values from the corresponding fields of the old
   table. partner_bild88x31 and partner_bild120x50 of the old table will be
   used to copy the related images from the old Frogsystem.
   The auto-increment value of the new table will be adjusted to match the one
   of the old table.
   During the transition process ALL previously existing parnters within the new
   partnerer table will be deleted!

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
function partnerTransition($old_link, $new_link, $old_basedir, $new_basedir)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's partner table
  $result = mysql_query('SELECT * FROM `fsplus_partner`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle partner '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p> '.mysql_num_rows($result)." Partner im alten FS gefunden.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE 'fsplus_partner'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte partner-Tabelle '
        .'schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle partner '
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
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'partner` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen partner-Tabelle'
        .' konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $row['partner_name'] = mysql_real_escape_string($row['partner_name'], $new_link);
    $row['partner_link'] = mysql_real_escape_string($row['partner_link'], $new_link);
    $row['partner_beschreibung'] = mysql_real_escape_string($row['partner_beschreibung'], $new_link);

    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'partner` '
                  .'(partner_id, partner_name, partner_link,
                     partner_beschreibung, partner_permanent) '
                   ."VALUES ('".$row['partner_id']."', '".$row['partner_name']."', '"
                   .$row['partner_link']."', '".$row['partner_beschreibung']."', '"
                   .$row['partner_permanent']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue partner-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    
    //copy files
    $file_old = $old_basedir.'images/partner/'.$row['partner_id'].'_120x50.'.$row['partner_bild120x50'];
    $file_new = $new_basedir.'images/partner/'.$row['partner_id'].'_big.'.$row['partner_bild120x50'];
    if (!copy($file_old, $file_new))
    {
      echo '<p class="error">Partnerbild (gro&szlig;) konnte nicht von '.$file_old
          .' nach '.$file_new." kopiert werden!</p>\n";
      return false;
    }
    $file_old = $old_basedir.'images/partner/'.$row['partner_id'].'_88x31.'.$row['partner_bild88x31'];
    $file_new = $new_basedir.'images/partner/'.$row['partner_id'].'_small.'.$row['partner_bild88x31'];
    if (!copy($file_old, $file_new))
    {
      echo '<p class="error">Partnerbild (klein) konnte nicht von '.$file_old
          .' nach '.$file_new." kopiert werden!</p>\n";
      return false;
    }
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'partner` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Tabelle partner '
        .'konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  echo '<span>Fertig.</span>'."\n";
  return true;
}//function partnerTransition

?>
