<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2014, 2015  Thoronador

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


/* function preparePressAdminNewPNW()
   prepares the table press_admin (e.g. fs2_press_admin) by setting up the
   games, "genres" (pre-/re-/interview) and the languages in the table.
   This functions has to be called before prereinterviewsTransition().
   Warning: Existing content of the table will be deleted during the process!
*/
function preparePressAdminNewPNW($new_link)
{
  if (!selectOldDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //clean out the existing stuff
  $result = mysql_query('TRUNCATE `'.NewDBTablePrefix.'press_admin`', $new_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die neue Pressebericht-Tabelle '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //add the wanted stuff
  $query = 'INSERT INTO `'.NewDBTablePrefix.'press_admin` (`id`, `type`, `title`) '
         . 'VALUES '
         ."(1, 3, 'Deutsch'),"
         ."(2, 3, 'Englisch'),"
         ."(3, 2, 'Preview'),"
         ."(4, 1, 'Neverwinter Nights')," //old game id: 1
         ."(5, 2, 'Review'),"
         ."(6, 2, 'Interview')"
         ."(7, 1, 'Schatten von Undernzit')," //old game id: 2
         ."(8, 1, 'Horden des Unterreichs')," //old game id: 3
         ."(9, 1, 'Neverwinter Nights 2')," //old game id: 4
         ."(10, 1, 'Mask of the Betrayer')," //old game id: 5
         ."(11, 1, 'Mysteries of Westgate')," //old game id: 6
         ."(12, 1, 'Storm of Zehir')"; //old game id: 7
  $result = mysql_query($query, $new_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die neue Pressebericht-Tabelle '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //all games entered into table, success :-)
  return true;
}


/* function prereinterviewsTransition()
   transfers pre-/re-/interview data from the old Frogsystem to the new
   Frogsystem by copying the data from the old prereinterviews table to the
   appropriate new tables.
   WARNING: Existing content in the new tables will get deleted!

   table structure (old):

   fs_prereinterviews
     prereinterviews_id      SMALLINT(6), auto_inc
     prereinterviews_titel   VARCHAR(150)
     prereinterviews_url     VARCHAR(255)
     prereinterviews_datum   INT(10), UNSIGNED
     prereinterviews_text    TEXT
     prereinterviews_lang    TINYINT(1), UNSIGNED
     prereinterviews_spiel   TINYINT(1), UNSIGNED
     prereinterviews_cat     TINYINT(1), UNSIGNED
     prereinterviews_wertung VARCHAR(100)

     PRIMARY INDEX (prereinterviews_id)


   table structure (new):

   fs2_press
     press_id    SMALLINT(6), auto_inc
     press_title VARCHAR(150)
     press_url   VARCHAR(255)
     press_date  INT(12)
     press_intro TEXT
     press_text  TEXT
     press_note  TEXT
     press_lang  INT(11)
     press_game  TINYINT(2)
     press_cat   TINYINT(2)

     PRIMARY INDEX (press_id)


   fs2_press_admin
     id    MEDIUMINT(8), auto_inc
     type  TINYINT(1)
     title VARCHAR(100)

     PRIMARY INDEX(id)

   fs2_press_config
     structure does not matter for transition


   Remarks: todo

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function prereinterviewsTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's pre-/re-/interview table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'prereinterviews`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Pre-/Re-/Interview-Tabelle '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $prereinterview_entries = mysql_num_rows($result);
  if ($prereinterview_entries!=1)
  {
    echo '<p>'.$prereinterview_entries." Pre-/Re-/Interviews in der alten DB gefunden.</p>\n";
  }
  else
  {
    echo '<p>Einen Eintrag zu Pre-/Re-/Interviews in der alten DB gefunden.</p>'."\n";
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
  $query_res = mysql_query('TRUNCATE `'.NewDBTablePrefix.'press`', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen Presseartikel-Tabelle'
        .' konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if


  //array for matching old game IDs to new game IDs
  $game_ids = array(
                1 => 4,  //NWN
                2 => 7,  //SvU
                3 => 8,  //HdU
                4 => 9,  //NWN 2
                5 => 10, //MotB
                6 => 11, //Mysteries of Westgate
                7 => 12  //SoZ
              );
  //array for matching old lang ID to new lang ID
  $lang_ids = array(
                1 => 1, //German
                2 => 2 //English
              );
  //array for matching old category ID to new category ID
  $cat_ids = array(
               1 => 3, //Preview
               2 => 5, //Review
               3 => 6  //Interview
             );


  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
     $row['prereinterviews_lang'] = intval($row['prereinterviews_lang']);
     $row['prereinterviews_spiel'] = intval($row['prereinterviews_spiel']);
     $row['prereinterviews_cat'] = intval($row['prereinterviews_cat']);

     if (!isset($game_ids[$row['prereinterviews_spiel']]))
     {
       echo '<p class="error">Ein game-id-Wert konnte nicht in aufgel&ouml;st werden: '
           .'Spiel-ID '.$row['prereinterviews_spiel'].' in Zeile mit ID '.$row['prereinterviews_id']."</p>\n";
       return false;
     }
     if (!isset($lang_ids[$row['prereinterviews_lang']]))
     {
       echo '<p class="error">Ein lang-id-Wert konnte nicht in aufgel&ouml;st werden: '
           .'Sprach-ID '.$row['prereinterviews_lang'].' in Zeile mit ID '.$row['prereinterviews_id']."</p>\n";
       return false;
     }
     if (!isset($cat_ids[$row['prereinterviews_cat']]))
     {
       echo '<p class="error">Ein cat-id-Wert konnte nicht in aufgel&ouml;st werden: '
           .'Kategorie-ID '.$row['prereinterviews_cat'].' in Zeile mit ID '.$row['prereinterviews_id']."</p>\n";
       return false;
     }

     $new_cat_id = $cat_ids[$row['prereinterviews_cat']];
     $new_game_id = $game_ids[$row['prereinterviews_spiel']];
     $new_lang_id = $lang_ids[$row['prereinterviews_lang']];

     $subquery = 'INSERT INTO `'.NewDBTablePrefix.'press` '
                .'(press_title, press_url, press_date, press_intro, press_text, press_note, press_lang, press_game, press_cat) '
                ."VALUES ('".mysql_real_escape_string($row['prereinterviews_titel'], $new_link)."', '"
                .mysql_real_escape_string($row['prereinterviews_url'], $new_link)."', '"
                .intval($row['prereinterviews_datum'])."', '', '" //yes, empty intro text
                .mysql_real_escape_string($row['prereinterviews_text'], $new_link)."', '"
                .mysql_real_escape_string($row['prereinterviews_wertung'], $new_link)."', '"
                .$new_lang_id."', '".$new_game_id."', '".$new_cat_id."');";
    $query_res = mysql_query($subquery, $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue Presseartikel-'
          .'Tabelle eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Fertig.</span>'."\n";
  return true;
}
