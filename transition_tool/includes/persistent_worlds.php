<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2012, 2013  Thoronador

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
require_once 'persistent_functions.php';

/* function persistent_genreTransition()
   transfers persisten world genres from the old Frogsystem to the new Frogsystem.

   table structures (old and new):

   fsplus_persistent_genre                 fs2_persistent_genre
     genre_id   SMALLINT(6), auto_inc        genre_id   SMALLINT(6), auto_inc
     genre_name CHAR(100)                    genre_name VARCHAR(100)
     genre_date INT(11)                      genre_date INT(11)

     PRIMARY INDEX (genre_id)                PRIMARY INDEX (genre_id)

   Table structures are the same, so every field in the new table gets its value
   from the field with the same name in the old table, except genre_id - the id
   is not used in the old system anyway.

   The auto-increment value of the new table will be set to the number of genres
   plus one.
   During the transition process ALL previously existing genres within the new
   genre table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function persistent_genreTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's genre table
  $result = mysql_query('SELECT * FROM `fsplus_persistent_genre` ORDER BY genre_date ASC', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle persistent_genre'
        .' konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $genre_num = mysql_num_rows($result);
  if ($genre_num!=1)
  {
    echo '<p>'.$genre_num." Genres im alten FS gefunden.</p>\n";
  }
  else
  {
    echo '<p>Ein Genre im alten FS gefunden.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete table in new DB, if present
  $query_res = mysql_query('DROP TABLE IF EXISTS `'.NewDBTablePrefix.'persistent_genre`', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierende persistent_genre-Tabelle in der '
        .'neuen DB konnte nicht gel&ouml;scht werden.<br>Folgender Fehler trat '
        .'beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //create table in new DB
  $query_res = mysql_query('CREATE TABLE `'.NewDBTablePrefix.'persistent_genre` (
  `genre_id` smallint(6) NOT NULL auto_increment,
  `genre_name` varchar(100) NOT NULL,
  `genre_date` int(11) NOT NULL default \'0\',
  PRIMARY KEY  (`genre_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die neue persistent_genre-Tabelle konnte nicht '
        .'angelegt werden.<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'persistent_genre` '
                  .'(genre_name, genre_date) '
                  ."VALUES ('".mysql_real_escape_string($row['genre_name'], $new_link)."', '"
                  .$row['genre_date']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue persistent_genre-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  return true;
}


/* function persistent_settingTransition()
   transfers persisten world settings from the old Frogsystem to the new Frogsystem.

   table structures (old and new):

   fsplus_persistent_setting               fs2_persistent_setting
     setting_id   SMALLINT(6), auto_inc      setting_id   SMALLINT(6), auto_inc
     setting_name CHAR(100)                  setting_name VARCHAR(100)
     setting_date INT(11)                    setting_date INT(11)

     PRIMARY KEY (setting_id)                PRIMARY KEY (setting_id)

   Table structures are the same, so every field in the new table gets its value
   from the field with the same name in the old table, except setting_id - the
   id is not used in the old system anyway.

   The auto-increment value of the new table will be set to the number of
   settings plus one.
   During the transition process ALL previously existing settings within the new
   setting table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function persistent_settingTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's setting table
  $result = mysql_query('SELECT * FROM `fsplus_persistent_setting` ORDER BY setting_date ASC', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle persistent_setting'
        .' konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $setting_num = mysql_num_rows($result);
  if ($setting_num!=1)
  {
    echo '<p>'.$setting_num." Settings im alten FS gefunden.</p>\n";
  }
  else
  {
    echo '<p>Ein Setting im alten FS gefunden.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete table in new DB, if present
  $query_res = mysql_query('DROP TABLE IF EXISTS `'.NewDBTablePrefix.'persistent_setting`', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierende persistent_setting-Tabelle in der '
        .'neuen DB konnte nicht gel&ouml;scht werden.<br>Folgender Fehler trat '
        .'beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //create table in new DB
  $query_res = mysql_query('CREATE TABLE `'.NewDBTablePrefix.'persistent_setting` (
  `setting_id` smallint(6) NOT NULL auto_increment,
  `setting_name` varchar(100) NOT NULL,
  `setting_date` int(11) NOT NULL default \'0\',
  PRIMARY KEY  (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die neue persistent_setting-Tabelle konnte nicht '
        .'angelegt werden.<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'persistent_setting` '
                  .'(setting_name, setting_date) '
                  ."VALUES ('".mysql_real_escape_string($row['setting_name'], $new_link)."', '"
                  .$row['setting_date']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue persistent_setting-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  return true;
}

/* function persistentTransition()
   transfers persistent world entries from the old Frogsystem to the new Frogsystem.

   table structures (old and new):

   fsplus_persistent (old)
    `persistent_id`        smallint(6) unsigned, auto_inc
    `persistent_name`      varchar(150)
    `persistent_url`       varchar(255)
    `persistent_text`      text
    `persistent_spiel`     tinyint(1) unsigned
    `persistent_setting`   varchar(200)
    `persistent_genre`     varchar(200)
    `persistent_termine`   varchar(200)
    `persistent_dlsize`    varchar(200)
    `persistent_dlsvu`     varchar(200)
    `persistent_dlhdu`     varchar(200)
    `persistent_dlcep`     varchar(200)
    `persistent_dlmotb`    varchar(200)
    `persistent_anmeldung` varchar(200)
    `persistent_handycap`  text
    `persistent_dm`        varchar(200)
    `persistent_maxzahl`   varchar(200)
    `persistent_maxlevel`  varchar(200)
    `persistent_expcap`    varchar(200)
    `persistent_fights`    varchar(200)
    `persistent_traps`     varchar(200)
    `persistent_items`     varchar(200)
    `persistent_pvp`       varchar(200)
    `persistent_datum`     int(11) unsigned
    `persistent_interview` varchar(200)
    `persistent_posterid` mediumint(8) unsigned
    `persistent_link` varchar(100)

    PRIMARY KEY (persistent_id)


   fs2_persistent (new)
    `persistent_id`         smallint(6) unsigned, auto_inc
    `persistent_name`       varchar(150)
    `persistent_url`        varchar(255)
    `persistent_text`       text
    `persistent_spiel`      tinyint(1) unsigned
    `persistent_setting_id` smallint(6)
    `persistent_genre_id`   smallint(6)
    `persistent_termine`    tinyint
    `persistent_dlsize`     int
    `persistent_dlsvu`      tinyint
    `persistent_dlhdu`      tinyint
    `persistent_dlcep`      tinyint
    `persistent_dlmotb`     tinyint
    `persistent_dlsoz`      tinyint
    `persistent_anmeldung`  tinyint
    `persistent_handycap`   text
    `persistent_dm`         int
    `persistent_maxzahl`    varchar(200)
    `persistent_maxlevel`   varchar(200)
    `persistent_expcap`     tinyint
    `persistent_fights`     tinyint
    `persistent_traps`      tinyint
    `persistent_items`      tinyint
    `persistent_pvp`        tinyint
    `persistent_datum`      int(11) unsigned
    `persistent_interview`  varchar(200)
    `persistent_posterid`   mediumint(8) unsigned
    `persistent_link`       varchar(100)

    PRIMARY KEY (persistent_id)


   Table structures are basically the same, except for some fields that have
   been changed to integer types. So most fields in the new table get their
   values from the field with the same name in the old table.

   The auto-increment value of the new table will be set to the number of
   persistent world entries plus one.
   During the transition process ALL previously existing entries within the new
   persistent table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function persistentTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's persistent world table
  $result = mysql_query('SELECT * FROM `fsplus_persistent` ORDER BY persistent_id ASC', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle persistent'
        .' konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $persistent_num = mysql_num_rows($result);
  if ($persistent_num!=1)
  {
    echo '<p>'.$persistent_num." persistente Welten im alten FS gefunden.</p>\n";
  }
  else
  {
    echo '<p>Eine persistente Welt im alten FS gefunden.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete entries from table in new DB, if any are present
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'persistent`', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Eintr&auml;ge der persistent_setting-Tabelle in der '
        .'neuen DB konnte nicht gel&ouml;scht werden.<br>Folgender Fehler trat '
        .'beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    //find setting ID
    $sub_query = mysql_query('SELECT * FROM `'.NewDBTablePrefix.'persistent_setting`
                              WHERE setting_name=\''.mysql_real_escape_string($row['persistent_setting'], $new_link)."' LIMIT 1", $new_link);
    if (!$sub_query)
    {
      echo '<p class="error">Die Setting-ID von &quot;'.$row['persistent_setting']
          .'&quot; konnte nicht ermittelt werden.<br>Folgender Fehler trat '
          .'beim Versuch auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }
    if (mysql_num_rows($sub_query)<=0)
    {
      echo '<p class="error">Das Setting &quot;'.$row['persistent_genre']
          .'&quot; existiert nicht in der neuen Tabelle.'."</p>\n";
      return false;
    }
    $sub_row = mysql_fetch_assoc($sub_query);
    $row['persistent_setting_id'] = $sub_row['setting_id'];

    //find genre ID
    $sub_query = mysql_query('SELECT * FROM `'.NewDBTablePrefix.'persistent_genre`
                              WHERE genre_name=\''.mysql_real_escape_string($row['persistent_genre'], $new_link)."' LIMIT 1", $new_link);
    if (!$sub_query)
    {
      echo '<p class="error">Die Genre-ID von &quot;'.$row['persistent_genre']
          .'&quot; konnte nicht ermittelt werden.<br>Folgender Fehler trat '
          .'beim Versuch auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }
    if (mysql_num_rows($sub_query)<=0)
    {
      echo '<p class="error">Das Genre &quot;'.$row['persistent_genre']
          .'&quot; existiert nicht in der neuen Tabelle.'."</p>\n";
      return false;
    }
    $sub_row = mysql_fetch_assoc($sub_query);
    $row['persistent_genre_id'] = $sub_row['genre_id'];

    $row['persistent_name'] = mysql_real_escape_string($row['persistent_name'], $new_link);
    $row['persistent_url']  = mysql_real_escape_string($row['persistent_url'], $new_link);
    $row['persistent_text'] = mysql_real_escape_string($row['persistent_text'], $new_link);
    $row['persistent_termine'] = getPersistentUptimeAsInt($row['persistent_termine']);
    $row['persistent_dlsize'] = getPersistentDLSizeAsInt($row['persistent_dlsize']);
    $row['persistent_dlsvu'] = getPersistentSvUAsInt($row['persistent_dlsvu']);
    $row['persistent_dlhdu'] = getPersistentHdUAsInt($row['persistent_dlhdu']);
    $row['persistent_dlcep'] = getPersistentCEPAsInt($row['persistent_dlcep']);
    $row['persistent_dlmotb'] = getPersistentMotBAsInt($row['persistent_dlmotb']);
    $row['persistent_anmeldung'] = getPersistentRegAsInt($row['persistent_anmeldung']);
    $row['persistent_handycap'] = mysql_real_escape_string($row['persistent_handycap'], $new_link);
    $row['persistent_dm'] = getPersistentDMAsInt($row['persistent_dm']);
    $row['persistent_maxzahl'] = mysql_real_escape_string($row['persistent_maxzahl'], $new_link);
    $row['persistent_maxlevel'] = mysql_real_escape_string($row['persistent_maxlevel'], $new_link);
    $row['persistent_expcap'] = getPersistentEXPCapAsInt($row['persistent_expcap']);
    $row['persistent_fights'] = getPersistentDifficultyAsInt($row['persistent_fights']);
    $row['persistent_traps'] = getPersistentDifficultyAsInt($row['persistent_traps']);
    $row['persistent_items'] = getPersistentFrequencyAsInt($row['persistent_items']);
    $row['persistent_pvp'] = getPersistentPvPAsInt($row['persistent_pvp']);
    $row['persistent_interview'] = mysql_real_escape_string($row['persistent_interview'], $new_link);
    $row['persistent_link'] = mysql_real_escape_string($row['persistent_link'], $new_link);

    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'persistent` '
                  .'(persistent_id, persistent_name,
                     persistent_url, persistent_text,
                     persistent_spiel, persistent_setting_id,
                     persistent_genre_id, persistent_termine,
                     persistent_dlsize,
                     persistent_dlsvu, persistent_dlhdu,
                     persistent_dlcep,
                     persistent_dlmotb, persistent_dlsoz,
                     persistent_anmeldung, persistent_handycap,
                     persistent_dm, persistent_maxzahl,
                     persistent_maxlevel, persistent_expcap,
                     persistent_fights, persistent_traps,
                     persistent_items, persistent_pvp,
                     persistent_datum, persistent_interview,
                     persistent_posterid, persistent_link)
                     VALUES (\''.$row['persistent_id']."', '" .$row['persistent_name']."', '"
                     .$row['persistent_url']."', '".$row['persistent_text']."', '"
                     .$row['persistent_spiel']."', '".$row['persistent_setting_id']."', '"
                     .$row['persistent_genre_id']."', '".$row['persistent_termine']."', '"
                     .$row['persistent_dlsize']."', '"
                     .$row['persistent_dlsvu']."', '".$row['persistent_dlhdu']."', '"
                     .$row['persistent_dlcep']."', '"
                     .$row['persistent_dlmotb']."', '0', '"
                     .$row['persistent_anmeldung']."', '".$row['persistent_handycap']."', '"
                     .$row['persistent_dm']."', '".$row['persistent_maxzahl']."', '"
                     .$row['persistent_maxlevel']."', '".$row['persistent_expcap']."', '"
                     .$row['persistent_fights']."', '".$row['persistent_traps']."', '"
                     .$row['persistent_items']."', '".$row['persistent_pvp']."', '"
                     .$row['persistent_datum']."', '".$row['persistent_interview']."', '"
                     .$row['persistent_posterid']."', '".$row['persistent_link']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue persistent-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  return true;
}

/* function persisinterviewTransition()
   transfers persistent world interviews from the old Frogsystem to the new Frogsystem.

   table structures (old and new):

   fsplus_persisinterview (old)
    `persisinterview_id`        smallint(6) unsigned, auto_inc
    `persisinterview_spiel`     smallint(1) unsigned
    `persisinterview_name`      varchar(150)
    `persisinterview_url`       varchar(255)
    `persisinterview_antwort01` text
    `persisinterview_antwort02` text
    `persisinterview_antwort03` text
    `persisinterview_antwort04` text
    `persisinterview_antwort05` text
    `persisinterview_antwort06` text
    `persisinterview_antwort07` text
    `persisinterview_antwort08` text
    `persisinterview_antwort09` text
    `persisinterview_antwort10` text
    `persisinterview_antwort11` text
    `persisinterview_antwort12` text
    `persisinterview_antwort13` text
    `persisinterview_datum`     int(11) unsigned
    `persisinterview_posterid`  mediumint(8) unsigned
    `persisinterview_link`      varchar(100)

   PRIMARY KEY (`persisinterview_id`)


  `fs2_persisinterview` (new)
    `persisinterview_id`        smallint(6) unsigned, auto_inc
    `persisinterview_spiel`     smallint(1) unsigned
    `persisinterview_name`      varchar(150)
    `persisinterview_url`       varchar(255)
    `persisinterview_antwort01` text
    `persisinterview_antwort02` text
    `persisinterview_antwort03` text
    `persisinterview_antwort04` text
    `persisinterview_antwort05` text
    `persisinterview_antwort06` text
    `persisinterview_antwort07` text
    `persisinterview_antwort08` text
    `persisinterview_antwort09` text
    `persisinterview_antwort10` text
    `persisinterview_antwort11` text
    `persisinterview_antwort12` text
    `persisinterview_antwort13` text
    `persisinterview_datum`     int(11) unsigned
    `persisinterview_posterid`  mediumint(8) unsigned
    `persisinterview_link`      varchar(100)

  PRIMARY KEY  (`persisinterview_id`)


   Table structures are the same, so all fields in the new table get their
   values from the field with the same name in the old table.

   The auto-increment value of the new table will be set to the number of
   persistent world entries plus one.
   During the transition process ALL previously existing entries within the new
   persisinterview table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function persisinterviewTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's interview table
  $result = mysql_query('SELECT * FROM `fsplus_persisinterview` ORDER BY persisinterview_id ASC', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle persisinterview'
        .' konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $persistent_num = mysql_num_rows($result);
  if ($persistent_num!=1)
  {
    echo '<p>'.$persistent_num." Interviews zu persistenten Welten im alten FS gefunden.</p>\n";
  }
  else
  {
    echo '<p>Ein Interview zu persistenten Welten im alten FS gefunden.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete table in new DB, if present
  $query_res = mysql_query('DROP TABLE IF EXISTS `'.NewDBTablePrefix.'persisinterview`', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierende persisinterview-Tabelle in der '
        .'neuen DB konnte nicht gel&ouml;scht werden.<br>Folgender Fehler trat '
        .'beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //create table in new DB
  $query_res = mysql_query('CREATE TABLE `'.NewDBTablePrefix."persisinterview` (
  `persisinterview_id` smallint(6) unsigned NOT NULL auto_increment,
  `persisinterview_spiel` smallint(1) unsigned NOT NULL default '0',
  `persisinterview_name` varchar(150) NOT NULL default '',
  `persisinterview_url` varchar(255) NOT NULL default '',
  `persisinterview_antwort01` text NOT NULL,
  `persisinterview_antwort02` text NOT NULL,
  `persisinterview_antwort03` text NOT NULL,
  `persisinterview_antwort04` text NOT NULL,
  `persisinterview_antwort05` text NOT NULL,
  `persisinterview_antwort06` text NOT NULL,
  `persisinterview_antwort07` text NOT NULL,
  `persisinterview_antwort08` text NOT NULL,
  `persisinterview_antwort09` text NOT NULL,
  `persisinterview_antwort10` text NOT NULL,
  `persisinterview_antwort11` text NOT NULL,
  `persisinterview_antwort12` text NOT NULL,
  `persisinterview_antwort13` text NOT NULL,
  `persisinterview_datum` int(11) unsigned NOT NULL default '0',
  `persisinterview_posterid` mediumint(8) unsigned NOT NULL default '0',
  `persisinterview_link` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`persisinterview_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1", $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die neue persisinterview-Tabelle konnte nicht '
        .'angelegt werden.<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $row['persisinterview_name'] = mysql_real_escape_string($row['persisinterview_name'], $new_link);
    $row['persisinterview_url'] = mysql_real_escape_string($row['persisinterview_url'], $new_link);
    for ($i=1; $i<10; $i=$i+1)
    {
      $row['persisinterview_antwort0'.$i] = mysql_real_escape_string($row['persisinterview_antwort0'.$i], $new_link);
    }
    for ($i=10; $i<=13; $i=$i+1)
    {
      $row['persisinterview_antwort'.$i] = mysql_real_escape_string($row['persisinterview_antwort'.$i], $new_link);
    }
    $row['persisinterview_link'] = mysql_real_escape_string($row['persisinterview_link'], $new_link);

    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'persisinterview` '
                  .'(persisinterview_id, persisinterview_spiel,
                     persisinterview_name, persisinterview_url,
                     persisinterview_antwort01, persisinterview_antwort02,
                     persisinterview_antwort03, persisinterview_antwort04,
                     persisinterview_antwort05, persisinterview_antwort06,
                     persisinterview_antwort07, persisinterview_antwort08,
                     persisinterview_antwort09, persisinterview_antwort10,
                     persisinterview_antwort11, persisinterview_antwort12,
                     persisinterview_antwort13,
                     persisinterview_datum, persisinterview_posterid,
                     persisinterview_link) '
                   ."VALUES ('".$row['persisinterview_id']."', '".$row['persisinterview_spiel']."', '"
                   .$row['persisinterview_name']."', '".$row['persisinterview_url']."', '"
                   .$row['persisinterview_antwort01']."', '".$row['persisinterview_antwort02']."', '"
                   .$row['persisinterview_antwort03']."', '".$row['persisinterview_antwort04']."', '"
                   .$row['persisinterview_antwort05']."', '".$row['persisinterview_antwort06']."', '"
                   .$row['persisinterview_antwort07']."', '".$row['persisinterview_antwort08']."', '"
                   .$row['persisinterview_antwort09']."', '".$row['persisinterview_antwort10']."', '"
                   .$row['persisinterview_antwort11']."', '".$row['persisinterview_antwort12']."', '"
                   .$row['persisinterview_antwort13']."', '"
                   .$row['persisinterview_datum']."', '".$row['persisinterview_posterid']."', '"
                   .$row['persisinterview_link']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue persisinterview-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  return true;
}

/* function persistent_commentsTransition()
   transfers persistent world comments from the old Frogsystem to the new Frogsystem.

   table structures (old and new):

   fsplus_persistent_comments (old)
    `persistent_comment_id` mediumint(8), auto_inc
    `persistent_link`       varchar(150)
    `comment_poster`        varchar(32)
    `comment_poster_id`     mediumint(8)
    `comment_date`          int(11)
    `comment_title`         varchar(100)
    `comment_text`          text

   PRIMARY KEY (persistent_comment_id)


   fs2_persistent_comments (news)
    `persistent_comment_id` mediumint(8), auto_inc
    `persistent_id` smallint(6) unsigned
    `comment_poster` varchar(32)
    `comment_poster_id` mediumint(8)
    `comment_date` int(11)
    `comment_title` varchar(100)
    `comment_text` text

   PRIMARY KEY  (persistent_comment_id)


   Table structures are nearly the same, so all fields in the new table get their
   values from the field with the same name in the old table.
   The field persistent_id will be set to the ID of the related persistent world
   in the table fs2_persistent.

   The auto-increment value of the new table will be set to the number of
   persistent world entries plus one.
   During the transition process ALL previously existing entries within the new
   persisinterview table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function persistent_commentsTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's interview table
  $result = mysql_query('SELECT * FROM `fsplus_persistent_comments` ORDER BY persistent_comment_id ASC', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle persistent_comments'
        .' konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $persistent_num = mysql_num_rows($result);
  if ($persistent_num!=1)
  {
    echo '<p>'.$persistent_num." Kommentare zu persistenten Welten im alten FS gefunden.</p>\n";
  }
  else
  {
    echo '<p>Ein Kommentar zu persistenten Welten im alten FS gefunden.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete table in new DB, if present
  $query_res = mysql_query('DROP TABLE IF EXISTS `'.NewDBTablePrefix.'persistent_comments`', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierende persistent_comments-Tabelle in der '
        .'neuen DB konnte nicht gel&ouml;scht werden.<br>Folgender Fehler trat '
        .'beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //create table in new DB
  $query_res = mysql_query('CREATE TABLE `'.NewDBTablePrefix."persistent_comments` (
  `persistent_comment_id` mediumint(8) NOT NULL auto_increment,
  `persistent_id` smallint(6) unsigned NOT NULL,
  `comment_poster` varchar(32) default NULL,
  `comment_poster_id` mediumint(8) default NULL,
  `comment_date` int(11) default NULL,
  `comment_title` varchar(100) default NULL,
  `comment_text` text,
  PRIMARY KEY  (`persistent_comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1", $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die neue persistent_comments-Tabelle konnte nicht '
        .'angelegt werden.<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $row['persistent_link'] = mysql_real_escape_string($row['persistent_link'], $new_link);
    $row['comment_poster'] = mysql_real_escape_string($row['comment_poster'], $new_link);
    $row['comment_title'] = mysql_real_escape_string($row['comment_title'], $new_link);
    $row['comment_text'] = mysql_real_escape_string($row['comment_text'], $new_link);

    //get persistent_id
    $id_query = mysql_query('SELECT persistent_id FROM `'.NewDBTablePrefix."persistent`
                             WHERE persistent_link ='".$row['persistent_link']."' LIMIT 1", $new_link);
    if ($id_query===false)
    {
      echo '<p class="error">Die persistent_id zu einem PW-Kommentar konnte nicht '
          .'ermittelt werden.<br>Folgender Fehler trat beim Versuch auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }
    $id = mysql_fetch_assoc($id_query);
    if ($id===false)
    {
      echo '<p class="error">Keine persistent_id zum PW-Kommentar #'
           .$row['persistent_comment_id']." gefunden!</p>\n";
      return false;
    }
    $row['persistent_id'] = $id['persistent_id'];

    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'persistent_comments` '
                  .'(persistent_comment_id, persistent_id,
                     comment_poster, comment_poster_id,
                     comment_date, comment_title,
                     comment_text) '
                   ."VALUES ('".$row['persistent_comment_id']."', '".$row['persistent_id']."', '"
                   .$row['comment_poster']."', '".$row['comment_poster_id']."', '"
                   .$row['comment_date']."', '".$row['comment_title']."', '"
                   .$row['comment_text']."')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue persistent_comments-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  return true;
}

?>
