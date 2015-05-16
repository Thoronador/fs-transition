<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2011, 2015  Thoronador

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


/* function dl_catTransition()
   transfers download categories from the old Frogsystem to the new Frogsystem
   by copying the data from the old dl_cat table to the new dl_cat table.

   table structures (old and new):

   fs_dl_cat                              fs2_dl_cat
     cat_id    MEDIUMINT(8), auto_inc       cat_id    MEDIUMINT(8), auto_inc
     subcat_id MEDIUMINT(8)                 subcat_id MEDIUMINT(8)
     cat_name  CHAR(100)                    cat_name  VARCHAR(100)

     PRIMARY INDEX (cat_id)                 PRIMARY INDEX (cat_id)

   Table structures are almost the same, so every field in the new table gets
   its value from the field with the same name in the old table. The auto-
   increment value of the new table will be adjusted to match the one of the
   old table.
   During the transition process ALL previously existing download categories
   within the new dl_cat table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function dl_catTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's dl_cat table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'dl_cat`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle dl_cat '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $dl_cat_entries = mysql_num_rows($result);
  if ($dl_cat_entries!=1)
  {
    echo '<p>'.$dl_cat_entries." Downloadkategorien im alten FS gefunden.</p>\n";
  }
  else
  {
    echo '<p>Eine Downloadkategorie im alten FS gefunden.</p>'."\n";
  }
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."dl_cat'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte dl_cat-Tabelle '
        .'schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle dl_cat '
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
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'dl_cat` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen dl_cat-Tabelle'
        .' konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'dl_cat` '
                  .'(cat_id, subcat_id, cat_name) '
                  ."VALUES ('".$row['cat_id']."', '".$row['subcat_id']."', '"
                  .$row['cat_name']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue dl_cat-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'dl_cat` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Tabelle dl_cat '
        .'konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function dl_catTransition


/* function dlTransition()
   transfers downloads from the old Frogsystem to the new Frogsystem by copying
   the data from the old dl table to the new dl table.

   table structures (old and new):

   fs_dl                                   fs2_dl
     dl_id        MEDIUMINT(8), auto_inc     dl_id            MEDIUMINT(8), auto_inc
     cat_id       MEDIUMINT(8)               cat_id           MEDIUMINT(8)
     user_id      MEDIUMINT(8)               user_id          MEDIUMINT(8)
     dl_date      INT(11)                    dl_date          INT(11)
     dl_name      VARCHAR(100)               dl_name          VARCHAR(100)
     dl_size      MEDIUMINT(8)               dl_text          TEXT
     dl_text      TEXT                       dl_autor         VARCHAR(100)
     dl_autor     VARCHAR(100)               dl_autor_url     VARCHAR(255)
     dl_autor_url VARCHAR(255)               dl_open          TINYINT(4)
     dl_url       VARCHAR(255)               dl_search_update INT(11)
     dl_open      TINYINT(4)
     dl_loads     MEDIUMINT(8)               PRIMARY INDEX (dl_id)

     PRIMARY INDEX (dl_id)                 fs2_dl_files
                                             dl_id          MEDIUMINT(8)
                                             file_id        MEDIUMINT(8), auto_inc
                                             file_count     MEDIUMINT(8)
                                             file_name      VARCHAR(100)
                                             file_url       VARCHAR(255)
                                             file_size      MEDIUMINT(8)
                                             file_is_mirror TINYINT(1)

                                             PRIMARY INDEX (file_id)
                                             INDEX (dl_id)

   The new dl table will get its values from the corresponding fields of the old
   dl table, the following fields are copied directly from the old table to the
   new one: dl_id, cat_id, user_id, dl_date, dl_name, dl_text, dl_autor,
   dl_autor_url, dl_open. The field dl_search_update will be set to the current
   Unix timestamp value. (Not sure if this is the original intention of FS.)

   The file data of the old table, i.e. fields dl_size, dl_loads and dl_url,
   will be stored in the new dl_files table. The fields of that table get their
   values as follows:

   dl_id          <- dl_id from the old dl table
   file_id        <- will be set implicitly be the auto-increment mechanism
   file_count     <- dl_loads from the old dl table
   file_name      <- dl_name from the old dl_table
   file_url       <- dl_url from the old dl table
   file_size      <- dl_size from the old dl table
   file_is_mirror <- always zero (0), because the files aren't mirrors

   The auto-increment value of dl_files table will be reset before this happens.
   The auto-increment value of the new dl table will be set to the value of the
   old dl table.

   ALL previously existing downloads within the new dl table and all files
   within the new dl_files table will be deleted during the transition process!
   That's why this function has to be called before dl_mirrorsTransition() to
   make sure the mirrors won't get deleted.

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function dlTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's dl table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'dl`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle dl '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $dl_num = mysql_num_rows($result);
  if ($dl_num!=1)
  {
    echo '<p>'.$dl_num." Downloads in der alten Downloadtabelle gefunden.</p>\n";
  }
  else
  {
    echo "<p>Einen Download in der alten Downloadtabelle gefunden.</p>\n";
  }
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."dl'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte dl-Tabelle '
        .'schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle dl '
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
  // ---- delete the stuff in fs2_dl
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'dl` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen Download-'
        .'Tabelle konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat'
        .' beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  // ---- delete the stuff in fs2_dl_files
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'dl_files` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen dl_files-'
        .'Tabelle konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat'
        .' beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  // ---- set auto-inc. value in new dl_files table to 1, in case it was higher
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'dl_files` AUTO_INCREMENT=1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Tabelle dl_files '
        .'konnte nicht zur&uuml;ckgesetzt werden.<br>Folgender Fehler trat beim'
        .' Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    //the download itself
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'dl` '
                  .'(dl_id, cat_id, user_id, dl_date, dl_name, dl_text, '
                  .'dl_autor, dl_autor_url, dl_open, dl_search_update) '
                  ."VALUES ('".$row['dl_id']."', '".$row['cat_id']."', '"
                  .$row['user_id']."', '".$row['dl_date']."', '".$row['dl_name']
                  ."', '".$row['dl_text']."', '".$row['dl_autor']."', '"
                  .$row['dl_autor_url']."', '".$row['dl_open']
                  ."', UNIX_TIMESTAMP())", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue Download-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    //the file data
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'dl_files` '
                  .'(dl_id, file_count, file_name, file_url, file_size, file_is_mirror) '
                  ."VALUES ('".$row['dl_id']."', '".$row['dl_loads']."', '"
                  .$row['dl_name']."', '".$row['dl_url']."', '".$row['dl_size']
                  ."', 0)", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue dl_files-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'dl` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Download-Tabelle '
        .'konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function dlTransition


/* function dl_mirrorsTransition()
   transfers download mirror data from the old Frogsystem to the new Frogsystem
   by copying the data from the old dl_mirrors table (and dl table) to the new
   dl_files table.

   table structures (old and new):

   fs_dl                                   fs2_dl_files
     dl_id        MEDIUMINT(8), auto_inc     dl_id          MEDIUMINT(8)
     cat_id       MEDIUMINT(8)               file_id        MEDIUMINT(8), auto_inc
     user_id      MEDIUMINT(8)               file_count     MEDIUMINT(8)
     dl_date      INT(11)                    file_name      VARCHAR(100)
     dl_name      VARCHAR(100)               file_url       VARCHAR(255)
     dl_size      MEDIUMINT(8)               file_size      MEDIUMINT(8)
     dl_text      TEXT                       file_is_mirror TINYINT(1)
     dl_autor     VARCHAR(100)
     dl_autor_url VARCHAR(255)               PRIMARY INDEX (file_id)
     dl_url       VARCHAR(255)               INDEX (dl_id)
     dl_open      TINYINT(4)
     dl_loads     MEDIUMINT(8)

     PRIMARY INDEX (dl_id)

   fs_dl_mirrors
     dl_id        MEDIUMINT(8)
     mirror_id    MEDIUMINT(8), auto_increment
     mirror_count MEDIUMINT(8)
     mirror_name  VARCHAR(100)
     mirror_url   VARCHAR(255)

   PRIMARY INDEX (mirror_id)

   The file data of the old dl_mirrors and dl table will be stored in the new
   dl_files table. The fields of that table get their values as follows:

   dl_id          <- dl_id from the old dl table
   file_id        <- will be set implicitly be the auto-increment mechanism
   file_count     <- mirror_count from the old dl_mirrors table
   file_name      <- mirror_name from the old dl_mirrors table
   file_url       <- mirror_url from the old dl_mirrors table
   file_size      <- dl_size from the old dl table
   file_is_mirror <- always one (1), because the files are mirrors

   This function has to be called after dlTransition() and NOT before it,
   because dlTransition() will delete all existing entries in dl_files, which
   would also delete the mirror data that will be copied into the new table by
   the dl_mirrorsTransition() function.

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function dl_mirrorsTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's dl_mirrors and dl table
  $result = mysql_query('SELECT `'.OldDBTablePrefix.'dl_mirrors`.dl_id AS dl_id, mirror_count,'
           .' mirror_name, mirror_url, `'.OldDBTablePrefix.'dl`.dl_id, dl_size '
           .'FROM `'.OldDBTablePrefix.'dl_mirrors`, `'.OldDBTablePrefix.'dl` '
           .'WHERE `'.OldDBTablePrefix.'dl`.dl_id = `'.OldDBTablePrefix.'dl_mirrors`.dl_id',
           $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alten Tabellen dl und '
        .'dl_mirrors konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler '
        .'trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $mirror_entries = mysql_num_rows($result);
  if ($mirror_entries!=1)
  {
    echo '<p>'.$mirror_entries." Eintr&auml;ge in der Tabelle dl_mirrors gefunden.</p>\n";
  }
  else
  {
    '<p>Einen Eintrag in der Tabelle dl_mirrors gefunden.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //put stuff into new DB's table
  while ($row = mysql_fetch_assoc($result))
  {
    //the mirror download
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'dl_files` '
                  .'(dl_id, file_count, file_name, file_url, file_size, file_is_mirror) '
                  ."VALUES ('".$row['dl_id']."', '".$row['mirror_count']."', '"
                  .$row['mirror_name']."', '".$row['mirror_url']."', '".$row['dl_size']
                  ."', 1)", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue dl_files-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function dl_mirrorsTransition


/* function dl_commentsTransition
   transfers the download comments from the old fsplus_dl_comments table (only
   in "FS plus", e.g. extended variant, or PNW) to the new generalized comments
   table (that should later be able to hold all kinds of comments, not just
   the download comments).

   table structures (old and new):

   fsplus_dl_comments
     dl_comment_id     MEDIUMINT(8), auto_inc
     dl_id             MEDIUMINT(8)
     comment_poster    VARCHAR(32)
     comment_poster_id MEDIUMINT(8)
     comment_date      INT(11)
     comment_title     VARCHAR(100)
     comment_text      TEXT

     PRIMARY INDEX(dl_comment_id)


   fs2_comments
     comment_id        MEDIUMINT(8), AUTO_INCREMENT
     content_id        MEDIUMINT(8)
     content_type      VARCHAR(32)
     comment_poster    VARCHAR(32)
     comment_poster_id MEDIUMINT(8)
     comment_poster_ip VARCHAR(16)
     comment_date      INT(11)
     comment_title     VARCHAR(100)
     comment_text      TEXT

     PRIMARY KEY (comment_id),
     FULLTEXT KEY comment_title_text (comment_text,comment_title)

   The data of the old dl_comments table will be stored in the new
   comments table. The fields of that table get their values as follows:

   comment_id        -> dl_comment_id of the old table or via auto increment
   content_id        -> dl_id of the old dl_comment table
   content_type      -> always 'dl'
   comment_poster    -> comment_poster of the old table
   comment_poster_id -> comment_poster_id of the old table
   comment_poster_ip -> no adequate value, so we set "0.0.0.0"
   comment_date      -> comment_date of the old table
   comment_title     -> comment_title of the old table
   comment_text      -> comment_text of the old table


   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database


   return value:
       true in case of success; false if failure
*/
function dl_commentsTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's dl_comments table
  $result = mysql_query('SELECT * FROM `fsplus_dl_comments`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle '
        .'dl_comments konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler'
        .' trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $dl_comment_entries = mysql_num_rows($result);
  if ($dl_comment_entries!=1)
  {
    echo '<p>'.$dl_comment_entries." Eintr&auml;ge in der Tabelle dl_comments gefunden.</p>\n";
  }
  else
  {
    '<p>Einen Eintrag in der Tabelle dl_comments gefunden.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //remove existing download comments from table, if any
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'comments` '
              ."WHERE content_type='dl' OR content_type='download'", $new_link);
  if ($query_res===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die neue Tabelle '
        .'comments konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler'
        .' trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //put old stuff into new DB's table
  while ($row = mysql_fetch_assoc($result))
  {
    //escape all text data
    $row['comment_poster'] = mysql_real_escape_string($row['comment_poster'], $new_link);
    $row['comment_title'] = mysql_real_escape_string($row['comment_title'], $new_link);
    $row['comment_text'] = mysql_real_escape_string($row['comment_text'], $new_link);
    //now put it into the new table
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'comments` '
                  .'(content_id, content_type, comment_poster, comment_poster_id, '
                  .'comment_poster_ip, comment_date, comment_title, comment_text) '
                  ."VALUES ('".$row['dl_id']."', 'dl', '".$row['comment_poster']
                  ."', '".intval($row['comment_poster_id'])."', '0.0.0.0', '"
                  .intval($row['comment_date'])."', '".$row['comment_title']
                  ."', '".$row['comment_text']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue comments-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Fertig.</span>'."\n";
  return true;
} //function dl_commentsTransition
?>
