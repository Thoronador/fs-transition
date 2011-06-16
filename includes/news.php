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


/* function news_catTransition()
   transfers news categories from the old Frogsystem to the new Frogsystem
   by copying the data from the old news_cat table to the new news_cat table.
   The category images are copied, too.

   table structures (old and new):

   fs_news_cat                           fs2_news_cat
     cat_id   SMALLINT(6), auto_inc        cat_id          SMALLINT(6), auto_inc
     cat_name CHAR(100)                    cat_name        VARCHAR(100)
                                           cat_description TEXT
     PRIMARY INDEX (cat_id)                cat_date        INT(11)
                                           cat_user        MEDIUMINT(8)

                                           PRIMARY INDEX (cat_id)

   The cat_id and cat_name fields in the new table get their values from the
   fields with the same name in the old table. cat_description will be an empty
   string for every category, cat_date will be set to the current Unix timestamp
   and cat_user will be set to one (1) for every category, because that usually
   is the super administrator in FS2.
   The auto-increment value of the new table will be adjusted to match the one
   of the old table.
   During the transition process ALL previously existing news categories within
   the new news_cat table will be deleted!

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
function news_catTransition($old_link, $new_link, $old_basedir, $new_basedir)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news_cat table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'news_cat`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle news_cat '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $cat_num = mysql_num_rows($result);
  if ($cat_num!=1)
  {
    echo '<p>'.$cat_num." Newskategorien im alten FS gefunden.</p>\n";
  }
  else
  {
    echo '<p>Eine Newskategorie im alten FS gefunden.</p>'."\n";
  }
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."news_cat'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte news_cat-Tabelle'
        .' schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle news_cat '
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
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'news_cat` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen news_cat-Tabelle'
        .' konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'news_cat` '
                  .'(cat_id, cat_name, cat_description, cat_date, cat_user) '
                  ."VALUES ('".$row['cat_id']."', '".$row['cat_name']."', '', "
                  .'UNIX_TIMESTAMP(), 1)', $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue news_cat-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'news_cat` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen news_cat-Tabelle '
        .'konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //now copy the news category images
  // ---- check old directory
  if (!is_dir($old_basedir.'images/newscat/'))
  {
    echo '<p class="error">'.htmlentities($old_basedir.'images/newscat/')
        .' ist kein Verzeichnis oder existiert nicht!</p>';
    return false;
  }
  // ---- check new directory
  if (!is_dir($new_basedir.'images/cat/'))
  {
    echo '<p class="error">'.htmlentities($new_basedir.'images/cat/')
        .' ist kein Verzeichnis oder existiert nicht!</p>';
    return false;
  }
  // ---- now open the directory to get the filenames
  $handle = opendir($old_basedir.'images/newscat/');
  if ($handle===false)
  {
    echo '<p class="error">Das Verzeichnis '.htmlentities($old_basedir.'images/newscat/')
         .' konnte nicht mit opendir() ge&ouml;ffnet werden!</p>';
    return false;
  }
  //read the filenames
  $files_copied = 0;
  while (false !== ($file = readdir($handle)))
  {
    //check for file, we do not want to copy directories
    if (is_file($old_basedir.'images/newscat/'.$file))
    {
      //copy the file
      if (!copy($old_basedir.'images/newscat/'.$file, $new_basedir.'images/cat/news_'.$file))
      {
        echo '<p class="error">Newskategoriebild von '.$old_basedir.'images/newscat/'.$file
            ." konnte nicht kopiert werden!</p>\n";
        //close the directory handle, because we return the line afterwards
        closedir($handle);
        return false;
      }
      $files_copied = $files_copied+1;
    }//if
  }//while
  closedir($handle);
  echo '<p>'.$files_copied.' Newskategoriebilder wurden kopiert.</p>'."\n";
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function news_catTransition


/* function newsTransition()
   transfers news from the old Frogsystem to the new Frogsystem by copying the
   data from the old news table to the new news table.

   table structures (old and new):

   fs_news                                 fs2_news
     news_id    MEDIUMINT(8), auto_inc       news_id               MEDIUMINT(8), auto_inc
     cat_id     SMALLINT(6)                  cat_id                SMALLINT(6)
     user_id    MEDIUMINT(8)                 user_id               MEDIUMINT(8)
     news_date  INT(11)                      news_date             INT(11)
     news_title VARCHAR(100)                 news_title            VARCHAR(255)
     news_text  TEXT                         news_text             TEXT
                                             news_active           TINYINT(1)
     PRIMARY INDEX (news_id)                 news_comments_allowed TINYINT(1)
                                             news_search_update    INT(11)

                                             PRIMARY INDEX (news_id)

   The new news table will get its values from the corresponding fields of the
   old news table, the following fields are copied directly from the old table
   to the new one: news_id, cat_id, user_id, news_date, news_title, news_text.
   The news_active and news_comments_allowed fields will be set to one always,
   so all news are shown and can have comments. The field news_search_update
   will be set to zero. (Not sure if this is the original intention of FS.)
   The auto-increment value of the new news table will be set to the value of
   the old news table.
   ALL previously existing news within the new news table will be deleted during
   the transition process!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function newsTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'news`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte News-Tabelle '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>'.mysql_num_rows($result)." Newsmeldung(en) im alten FS gefunden.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."news'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte news-Tabelle '
        .'schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der alten News-Tabelle'
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
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'news` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen News-'
        .'Tabelle konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat'
        .' beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'news` '
                  .'(news_id, cat_id, user_id, news_date, news_title, news_text, '
                  .' news_active, news_comments_allowed, news_search_update) '
                  ."VALUES ('".$row['news_id']."', '".$row['cat_id']."', '"
                  .$row['user_id']."', '".$row['news_date']."', '"
                  .$row['news_title']."', '".$row['news_text']."', 1, 1, 0)", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue News-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'news` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen News-Tabelle '
        .'konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function newsTransition


/* function news_linksTransition()
   transfers the news links from the old Frogsystem to the new Frogsystem by
   copying the data from the old news_links table to the new news_links table.

   table structures (old and new):

   fs_news_links                           fs2_news_links
     news_id     MEDIUMINT(8)                news_id     MEDIUMINT(8)
     link_id     MEDIUMINT(8), auto_inc      link_id     MEDIUMINT(8), auto_inc
     link_name   VARCHAR(100)                link_name   VARCHAR(100)
     link_url    VARCHAR(255)                link_url    VARCHAR(255)
     link_target TINYINT(4)                  link_target TINYINT(4)

     PRIMARY INDEX (link_id)                 PRIMARY INDEX (link_id)

   The table structure is exactly the same, so the transition is quite simple
   and obvious.
   The auto-increment value of the new news_links table will be set to the value
   of the old news_links table.
   ALL previously existing news links within the new news_links table will be
   deleted during the transition process!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function news_linksTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news_links table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'news_links`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte news_links-'
        .'Tabelle konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat'
        .' beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>'.mysql_num_rows($result)." Eintr&auml;ge in der news_links-Tabelle gefunden.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."news_links'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte news_links-'
        .'Tabelle schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle news_links'
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
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'news_links` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen news_links-'
        .'Tabelle konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat'
        .' beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'news_links` '
                  .'(news_id, link_id, link_name, link_url, link_target) '
                  ."VALUES ('".$row['news_id']."', '".$row['link_id']."', '"
                  .$row['link_name']."', '".$row['link_url']."', '"
                  .$row['link_target']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue news_links-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'news_links` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen news_links-Tabelle'
        .' konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function news_linksTransition


/* function news_commentsTransition()
   transfers the news comments from the old Frogsystem to the new Frogsystem by
   copying the data from the old news_commentss table to the new news_commentss
   table.

   table structures (old and new):

   fs_news_comments                              fs2_news_comments
     comment_id        MEDIUMINT(8), auto_inc      comment_id        MEDIUMINT(8), auto_inc
     news_id           MEDIUMINT(8)                news_id           MEDIUMINT(8)
     comment_poster    VARCHAR(32)                 comment_poster    VARCHAR(32)
     comment_poster_id MEDIUMINT(8)                comment_poster_id MEDIUMINT(8)
     comment_date      INT(11)                     comment_poster_ip VARCHAR(16)
     comment_title     VARCHAR(100)                comment_date      INT(11)
     comment_text      TEXT                        comment_title     VARCHAR(100)
                                                   comment_text      TEXT
     PRIMARY INDEX (comment_id)
                                                   PRIMARY INDEX (comment_id)

   The table structures are almost the same, so the transition is quite simple
   and obvious for the fields of the same name. The comment_poster_ip field will
   be set to '127.0.0.1' for lack of a better value.
   The auto-increment value of the new news_comments table will be set to the
   value of the old news_comments table.
   ALL previously existing news comments within the new news_comments table will
   be deleted during the transition process!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function news_commentsTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news_comments table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'news_comments', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle '
        .'news_comments konnte nicht ausgef&uuml;hrt werden!<br>Folgender '
        .'Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>'.mysql_num_rows($result)." Eintr&auml;ge in der Tabelle news_comments gefunden.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."news_comments'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte news_comments-'
        .'Tabelle schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle news_comments'
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
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'news_comments` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Newskommentare konnten nicht '
        .'gel&ouml;scht werden.<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'news_comments` '
                  .'(comment_id, news_id, comment_poster, comment_poster_id, '
                  .'comment_poster_ip, comment_date, comment_title, comment_text) '
                  ."VALUES ('".$row['comment_id']."', '".$row['news_id']."', '"
                  .$row['comment_poster']."', '".$row['comment_poster_id']
                  ."', '127.0.0.1', '".$row['comment_date']."', '"
                  .$row['comment_title']."', '".$row['comment_text']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue news_comments-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'news_comments` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen news_comments-'
        .'Tabelle konnte nicht aktualisert werden.<br>Folgender Fehler trat '
        .'beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function news_commentsTransition


/* converts the news configuration's setting value for HTML and FS code to the
   appropriate value for the new Frogsystem

   parameters:
       old_val - value within the old configuration, has to be an integer value
                 in [1;3]

   return value:
       setting value within the new configuration
*/
function codeSettingTransition($old_val)
{
  /* HTML-/FS-Code settings:

     code setting    | FS1 (old) | FS2 (new)
     ----------------+-----------+-----------
     off             |     1     |     1
     news only       |     2     |     2
     comments only   |    N/A    |     3
     news + comments |     3     |     4
  */
  if ($old_val==3) return 4;
  return $old_val;
}//function codeSettingTransition


/* function news_configTransition()
   transfers the news configuration settings from the old Frogsystem to the new
   Frogsystem by updating the data of the new news_config table with values from
   the old news_config table.

   table structures (old and new):

   fs_news_config                  fs2_news_config
     num_news  INT(11)               id                   TINYINT(1)
     num_head  INT(11)               num_news             INT(11)
     html_code TINYINT(4)            num_head             INT(11)
     fs_code   TINYINT(4)            html_code            TINYINT(4)
                                     fs_code              TINYINT(4)
                                     para_handling        TINYINT(4)
                                     cat_pic_x            SMALLINT(4)
                                     cat_pic_y            SMALLINT(4)
                                     cat_pic_size         SMALLINT(4)
                                     com_rights           TINYINT(1)
                                     com_antispam         TINYINT(1)
                                     com_sort             VARCHAR(4)
                                     news_headline_length SMALLINT(3)
                                     news_headline_ext    VARCHAR(30)
                                     acp_per_page         SMALLINT(3)
                                     acp_view             TINYINT(1)

                                     PRIMARY INDEX (id)

   The new table should already have one row of own data, and this function
   updates this row with the corresponding data from the old table. All fields
   except num_news, num_head, html_code, fs_code and com_sort remain unchanged.
   num_news and num_head are set to the values of the same name from the old
   table; html_code and fs_code are set to adjusted values of html_code and
   fs_code of the old table. com_sort will be set to 'ASC' to get the usual
   sorting style of the old Frogsystem.
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
function news_configTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news_config table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'news_config`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle '
        .'news_config konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler '
        .'trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $entries = mysql_num_rows($result);
  if ($entries<1)
  {
    echo '<p class="error">Die alte news_config-Tabelle hat keine '
        .'Eintr&auml;ge. Der Vorgang wird abgebrochen!'."</p>\n";
    return false;
  }
  if ($entries==1)
  {
    echo '<p>Einen Eintrag in der alten Newskonfigurationstabelle gefunden.</p>'."\n";
  }
  else if ($entries>1)
  {
    echo '<p class="hint">'.$entries.' Eintr&auml;ge in der Tabelle news_config'
        .' gefunden, aber nur der erste davon wird ber&uuml;cksichtigt.</p>'."\n";
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
  $query_res = mysql_query('SELECT COUNT(id) AS count FROM `'.NewDBTablePrefix.'news_config`', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die neue Tabelle news_config '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  if (!($row = mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Abfrageergebnis der neuen news_config-Tabelle'
        .' konnte nicht ermittelt werden.</p>';
    return false;
  }//if
  //check for number of entries
  if ($row['count']<1)
  {
    echo '<p class="error">Die neue news_config-Tabelle enth&auml;t keine Konfiguration!</p>';
    return false;
  }

  //update configuration in new DB's table
  echo '<span>Aktualisiere Newskonfiguration...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    //check, if fs_code and html_code are valid
    if (($row['fs_code']!=1) && ($row['fs_code']!=2) && ($row['fs_code']!=3))
    {
      echo '<p class="error">Ung&uuml;ltigen Wert f&uuml;r fs_code in der alten'
          .' Konfigurationstabelle gefunden!<br>Der Wert war &quot;'
          .htmlentities($row['fs_code']).'&quot;, aber nur ganzzahlige Werte'
          .'von 1 bis 3 sind zul&auml;ssig.</p>'."\n";
      return false;
    }
    if (($row['html_code']!=1) && ($row['html_code']!=2) && ($row['html_code']!=3))
    {
      echo '<p class="error">Ung&uuml;ltigen Wert f&uuml;r html_code in der '
          .'alten Konfigurationstabelle gefunden!<br>Der Wert war &quot;'
          .htmlentities($row['html_code']).'&quot;, aber nur ganzzahlige Werte'
          .'von 1 bis 3 sind zul&auml;ssig.</p>'."\n";
      return false;
    }
    //adjust codes
    $row['html_code'] = codeSettingTransition($row['html_code']);
    $row['fs_code'] = codeSettingTransition($row['fs_code']);
    //now execute the update query on the new configuration table
    $query_res = mysql_query('UPDATE `'.NewDBTablePrefix.'news_config` '
                  ."SET num_news='".$row['num_news']."', num_head='".$row['num_head']
                  ."', html_code='".$row['html_code']."', fs_code='"
                  .$row['fs_code']."', com_sort='ASC'", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Die Werte in der neuen news_config-Tabelle '
          .'konnten nicht aktualisiert werden.<br>Folgender Fehler trat beim '
          .'Versuch auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    $affected = mysql_affected_rows($new_link);
    if ($affected<0)
    {
      //This should never happen, -1 only occurs on query failure.
      echo '<p class="error">Aktualisierung der Tabelle news_config ist '
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
      echo '<p>Newskonfigurationen der alten und neuen Tabelle stimmen '
          .' bereits &uuml;berein.</p>';
    }
  }//if
  else
  {
    echo '<p class="error">Die Werte aus der alten news_config-Tabelle '
        .'konnten nicht ermittelt werden!</p>';
    return false;
  }//else branch
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function news_configTransition

?>