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

/* transfers announcement data from the old Frogsystem to the new Frogsystem by
   copying the data from the old anouncement [sic!] table to the new announcement
   table.

   table structures (old and new):

   fs_anouncement       fs2_announcement
     text     TEXT        id                    SMALLINT(4)
                          announcement_text     TEXT
                          show_announcement     TINYINT(1)
                          activate_announcement TINYINT(1)
                          ann_html              TINYINT(1)
                          ann_fscode            TINYINT(1)
                          ann_para              TINYINT(1)

   The field announcement_text in the new table will get its value from the text
   field of the old table (naturally), the other fields are filled with some
   pre-defined values (in this case: 1 for all tinyint/smallint fields, because
   we want the announcement to be shown, active, and allow HTML code, FS code
   and paragraph processing).
   During that process ALL existing announcements within the new announcement
   table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/

function anouncementTransition($old_link, $new_link) //yes, it's spelled the wrong way
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's anouncement table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'anouncement`', $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old anouncement table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $announcement_entries = mysql_num_rows($result);
  if ($announcement_entries!=1)
  {
    echo '<p>'.$announcement_entries." Eintr&auml;ge in der Tabelle anouncement gefunden.</p>\n";
  }
  else
  {
    echo '<p>Einen Eintrag in der Tabelle anouncement gefunden.</p>'."\n";
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
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'announcement` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen announcement-'
        .'Tabelle konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat'
        .' dabei auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;ft...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'announcement` '
                  .'(id, announcement_text, show_announcement, '
                  .'activate_announcement, ann_html, ann_fscode, ann_para) '
                  ."VALUES (1, '".$row['text']."', 1, 1, 1, 1, 1)", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Es konnten keine Werte in die neue announcement-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//if
  else
  {
    //no announcement in old table, so create at least standard data row
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'announcement` '
                  .'(id, announcement_text, show_announcement, '
                  .'activate_announcement, ann_html, ann_fscode, ann_para) '
                  ."VALUES (1, '', 2, 0, 1, 1, 1)", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Es konnte kein Wert in die neue announcement-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function anouncementTransition

?>