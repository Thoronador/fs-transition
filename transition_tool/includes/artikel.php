<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2011, 2016  Thoronador

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


/* function artikelTransition()
   transfers articles from the old Frogsystem to the new Frogsystem by copying
   the data from the old artikel table to the new article table.

   table structures (old and new):

   fs_artikel                       fs2_article
     artikel_url    VARCHAR(100)      article_id            MEDIUMINT(8), auto_increment
     artikel_title  VARCHAR(100)      article_url           VARCHAR(100)
     artikel_date   INT(11)           article_title         VARCHAR(255)
     artikel_user   VARCHAR(100)      article_date          INT(11)
     artikel_text   MEDIUMTEXT        article_user          MEDIUMINT(8)
     artikel_index  TINYINT(4)        article_text          TEXT
     artikel_fscode TINYINT(4)        article_html          TINYINT(1)
                                      article_fscode        TINYINT(1)
                                      article_para          TINYINT(1)
                                      article_cat_id        MEDIUMINT(8)
                                      article_search_update INT(11)

   The new article table will get its values from the old artikel table where
   applicable. The article_id field will not be set explicitly (due to its
   absence in the old table), but will be set implicitly during the auto-
   increment process. However, the auto-increment value of the table will be
   reset before the article data is inserted. The following fields will be
   copied directly from the old table into the new table without any changes:

   artikel_url    -> article_url
   artikel_title  -> article_title
   artikel_date   -> article_date
   artikel_user   -> article_user
   artikel_text   -> article_text
   artikel_index  -> article_search_update (probably not the best solution)
   article_fscode -> article_fscode, article_para

   These values should already be properly escaped where neccessary, so they
   will not be escaped again.
   artikel_user is a string type (varchar) in the old table and will be copied
   into the new table's article_user value, which is an integer type (mediumint).
   This should work as expected, because artikel_user always seems to be the
   string containing the integer of the author's id.
   The field article_html will be set to 1 in every case, allowing HTML code in
   every article, because the old Frogsystem always allowed it implicitly.
   The field article_cat_id will be set to 1 in every case, too, because that
   is the ID of the only predefined article category in the new FS.

   During that process ALL previously existing articles within the new article
   table, except the one about FS code (?go=fscode) will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/

function artikelTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's user table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'artikel`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle artikel '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $artikel_entries = mysql_num_rows($result);
  if ($artikel_entries!=1)
  {
    echo '<p>'.$artikel_entries." Eintr&auml;ge in der Tabelle artikel gefunden.</p>\n";
  }
  else
  {
    echo '<p>Einen Eintrag in der Tabelle artikel gefunden.</p>'."\n";
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
  /* We delete all articles except the one which is about the FS code, because
     it is linked in the menu and is helpful anyway.
  */
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix."articles` WHERE article_url<>'fscode'", $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen articles-'
        .'Tabelle konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat'
        .' beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //now update the ID of the fscode article
  $query_res = mysql_query('UPDATE `'.NewDBTablePrefix.'articles` SET article_id=1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die ID des bestehenden Artikels in der neuen articles-'
        .'Tabelle konnte nicht aktualisiert werden.<br>Folgender Fehler trat'
        .' beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //set the auto_increment value to 2 (because we don't have more than one article here)
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'articles` AUTO_INCREMENT=2', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Tabelle articles '
        .'konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    //escape strings
    $row['artikel_url'] = mysql_real_escape_string($row['artikel_url'], $new_link);
    $row['artikel_title'] = mysql_real_escape_string($row['artikel_title'], $new_link);
    $row['artikel_text'] = mysql_real_escape_string($row['artikel_text'], $new_link);
    //check whether escaping failed
    if ((false === $row['artikel_url']) || (false === $row['artikel_url'])
        || (false === $row['artikel_url']))
    {
      echo '<p class="error">Ein Artikelwert konnte nicht mittels mysql_real_escape_string()'
          .'maskiert werden.<br>Betroffene Artikel-URL:<br>'
          .htmlentities($row['artikel_url'])."</p>\n";
      return false;
    } //if escaping failed
    //run the query
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'articles` '
                  .'(article_url, article_title, article_date, article_user, '
                  .'article_text, article_html, article_fscode, article_para, '
                  .'article_cat_id, article_search_update) '
                  ."VALUES ('".$row['artikel_url']."', '".$row['artikel_title']
                  ."', '".$row['artikel_date']."', '".intval($row['artikel_user'])
                  ."', '".$row['artikel_text']."', 1, '".$row['artikel_fscode']
                  ."', '".$row['artikel_fscode']."', 1, '".$row['artikel_index']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue articles-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function artikelTransition

?>
