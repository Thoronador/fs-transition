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

require_once 'connect.inc.php';
require_once 'permissions.inc.php'; //required 'constants' for permission transition


/* function userTransition()
   transfers users from the old Frogsystem to the new Frogsystem by copying the
   data from the old user table to the new user table.
   User avatars will be copied, too.

   table structures (old and new):

   fs_user                                   fs2_user
     user_id       MEDIUMINT(8), auto_inc     user_id        MEDIUMINT(8), auto_inc
     user_name     CHAR(100)                  user_name      CHAR(100)
     user_password CHAR(32)                   user_password  CHAR(32)
     user_mail     CHAR(100)                  user_salt      VARCHAR(10)
     is_admin      TINYINT(4)                 user_mail      CHAR(100)
     reg_date      INT(11)                    user_is_staff  TINYINT(1)
     show_mail     TINYINT(4)                 user_group     MEDIUMINT(8)
                                              user_is_admin  TINYINT(1)
     PRIMARY INDEX (user_id)                  user_reg_date  INT(11)
                                              user_show_mail TINYINT(4)
                                              user_homepage  VARCHAR(100)
                                              user_icq       VARCHAR(50)
                                              user_aim       VARCHAR(50)
                                              user_wlm       VARCHAR(50)
                                              user_yim       VARCHAR(50)
                                              user_skype     VARCHAR(50)

                                              PRIMARY INDEX (user_id)

   The fields user_id, user_name, user_password, user_mail, user_reg_date and
   user_show_mail get their values from the corresponding fields of the old
   table. user_salt will be set to an empty string, because this way we can
   preserve the passwords from the old system. user_is_staff, user_group and
   user_is_admin will always be set to zero, so no user is in the staff, member
   of a group or admin. Staff members will get the user_is_staff value changed
   to one in the permissionsTable() function, if they have at least one
   permission for the admin control panel. The fields user_homepage, user_icq,
   user_aim, user_wlm, user_yim and user_skype will be set to empty strings,
   because these fields are not present in the old Frogsystem.
   The auto-increment value of the new table will be adjusted to match the one
   of the old table.
   During the transition process ALL previously existing users within the new
   user table will be deleted!

   The user avatars will be copied from the old to the new system, too. More in
   detail, all files with .gif extension (lower case only) from the avatar
   directory will be copied, because it's save to assume, that all GIF images
   there are user avatars. (FS1 always has .gif as avatar file extension, even
   if the file actually was a JPEG or PNG image.)

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
function userTransition($old_link, $new_link, $old_basedir, $new_basedir)
{
  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's user table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'user`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle user '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p> '.mysql_num_rows($result)." Nutzer im alten FS gefunden.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."user'", $old_link);
  if ($query_res===false)
  {
    echo '<p class="error">Die Statusabfrage f&uuml;r die alte user-Tabelle '
        .'schlug fehl.<br>Folgender Fehler trat dabei auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p class="error">Das Ergebnis der Statusabfrage der Tabelle user '
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
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'user` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen user-Tabelle'
        .' konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    //escape strings
    $row['user_name'] = mysql_real_escape_string($row['user_name'], $new_link);
    $row['user_mail'] = mysql_real_escape_string($row['user_mail'], $new_link);
    //check whether escaping failed
    if ((false === $row['user_name']) || (false === $row['user_mail']))
    {
      echo '<p class="error">Ein Userwert konnte nicht mittels mysql_real_escape_string()'
          .'maskiert werden.<br>Betroffene User-ID:<br>'
          .htmlentities($row['user_id'])."</p>\n";
      return false;
    } //if escaping failed
    $row['user_id'] = intval($row['user_id']);
    //run query
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'user` '
                  .'(user_id, user_name, user_password, user_salt, user_mail, '
                  .'user_is_staff, user_group, user_is_admin, user_reg_date, '
                  .'user_show_mail, user_homepage, user_icq, user_aim, '
                  .'user_wlm, user_yim, user_skype) '
                  ."VALUES ('".$row['user_id']."', '".$row['user_name']."', '"
                  .$row['user_password']."', '', '".$row['user_mail']."', 0, " //0=no staff
                  ."0, 0, '".$row['reg_date']."', '".$row['show_mail']."', '',"
                  ."'', '', '', '', '')", $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Ein Wert konnte nicht in die neue user-Tabelle '
          .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'user` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Der Auto-increment-Wert der neuen Tabelle user '
        .'konnte nicht aktualisert werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //now copy the user images
  // ---- check old directory
  if (!is_dir($old_basedir.'images/avatare/'))
  {
    echo '<p class="error">'.htmlentities($old_basedir.'images/avatare/')
        .' ist kein Verzeichnis oder existiert nicht!</p>';
    return false;
  }
  // ---- check new directory
  if (!is_dir($new_basedir.'media/user-images/'))
  {
    echo '<p class="error">'.htmlentities($new_basedir.'media/user-images/')
        .' ist kein Verzeichnis oder existiert nicht!</p>';
    return false;
  }
  // ---- now open the directory to get the filenames
  $handle = opendir($old_basedir.'images/avatare/');
  if ($handle===false)
  {
    echo '<p class="error">Das Verzeichnis '.htmlentities($old_basedir.'images/avatare/')
         .' konnte nicht mit opendir() ge&ouml;ffnet werden!</p>';
    return false;
  }
  //read the filenames
  $avatars_copied = 0;
  while (false !== ($file = readdir($handle)))
  {
    //now check if it is a .gif file
    $ext = substr($file, -4);
    if ($ext !== false)
    {
      /*In Frogsystem version 1, all avatars are saved with .gif extension, even
        if they actually are JPEG or PNG files. (The browser will handle it.)*/
      if ($ext == '.gif')
      {
        //copy the file, finally
        if (!copy($old_basedir.'images/avatare/'.$file, $new_basedir.'media/user-images/'.$file))
        {
          echo '<p class="error">Avatar konnte nicht von '.$old_basedir
              .'images/avatare/'.$file."kopiert werden!</p>\n";
          //close the directory handle, because we return the line afterwards
          closedir($handle);
          return false;
        }
        $avatars_copied = $avatars_copied+1;
      }//if extension is .gif
    }//if substr() succeeded
  }//while
  closedir($handle);
  echo '<p>'.$avatars_copied.' Avatarbilder wurden kopiert.</p>'."\n";
  echo '<span>Fertig.</span>'."\n";
  return true;
}//function userTransition


/* function permissionsTransition()
   transfers user permissions from the old Frogsystem to the new Frogsystem
   by copying the adjsuted data from the old permissions table to the new
   user_permissions table.

   table structures (old and new):

   fs_permissions                          fs2_user_permissions
     user_id             MEDIUMINT(8)        perm_id        VARCHAR(255)
     perm_newsadd        TINYINT(4)          x_id           MEDIUMINT(8)
     perm_newsedit       TINYINT(4)          perm_for_group TINYINT(1)
     perm_newscat        TINYINT(4)
     perm_newsnewcat     TINYINT(4)          PRIMARY INDEX (perm_id, x_id,
     perm_newsconfig     TINYINT(4)                         perm_for_group)
     perm_dladd          TINYINT(4)
     perm_dledit         TINYINT(4)
     perm_dlcat          TINYINT(4)
     perm_dlnewcat       TINYINT(4)
     perm_polladd        TINYINT(4)
     perm_polledit       TINYINT(4)
     perm_potmadd        TINYINT(4)
     perm_potmedit       TINYINT(4)
     perm_screenadd      TINYINT(4)
     perm_screenedit     TINYINT(4)
     perm_screencat      TINYINT(4)
     perm_screennewcat   TINYINT(4)
     perm_screenconfig   TINYINT(4)
     perm_shopadd        TINYINT(4)
     perm_shopedit       TINYINT(4)
     perm_statedit       TINYINT(4)
     perm_useradd        TINYINT(4)
     perm_useredit       TINYINT(4)
     perm_userrights     TINYINT(4)
     perm_map            TINYINT(4)
     perm_statview       TINYINT(4)
     perm_statref        TINYINT(4)
     perm_artikeladd     TINYINT(4)
     perm_artikeledit    TINYINT(4)
     perm_templateedit   TINYINT(4)
     perm_allphpinfo     TINYINT(4)
     perm_allconfig      TINYINT(4)
     perm_allanouncement TINYINT(4)
     perm_statspace      TINYINT(4)

   Since the new table's structure is rather different, requiring one data row
   for every single permission given to a user (as opposed to one larger row for
   all permissions of a user within the old FS), this function will check all
   permissions and add a new data row for every permission granted to a user.
   (In the case of perm_templateedit the user will get several new permissions
   within the new table, one for each possible template permission of the new
   Frogsystem.) This function will also set the user table's user_is_staff field
   to one for every user that has at least one permission. That's why this
   function has to be called after the userTransition() function, because other-
   wise you wouldn't have any user data in the new user table.
   During the transition process ALL previously existing permissions within the
   new user_permissions table will be deleted!

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function permissionsTransition($old_link, $new_link)
{
  global $FS2_permissions; //get access to the FS2_permissions array defined in
                           // permissions.inc.php

  if (!selectOldDB($old_link))
  {
    echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's user table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'permissions`', $old_link);
  if ($result===false)
  {
    echo '<p class="error">Eine SQL-Abfrage f&uuml;r die alte Tabelle permissions '
        .'konnte nicht ausgef&uuml;hrt werden!<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $permission_entries = mysql_num_rows($result);
  if ($permission_entries!=1)
  {
    echo '<p>'.$permission_entries." Eintr&auml;ge in der Tabelle permissions gefunden.</p>\n";
  }
  else
  {
    echo '<p>Einen Eintrag in der Tabelle permissions gefunden.</p>'."\n";
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
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'user_permissions` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p class="error">Die existierenden Werte in der neuen user_permissions-Tabelle'
        .' konnten nicht gel&ouml;scht werden.<br>Folgender Fehler trat beim '
        .'Versuch auf:<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Verarbeitung l&auml;uft...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_string = 'INSERT INTO `'.NewDBTablePrefix.'user_permissions` '
                   .'(perm_id, x_id, perm_for_group) VALUE ';
    $need_query = false;

    //now go through all the permissions
    foreach($row as $key => $value)
    {
      if (($key!='user_id') && ($value!=0) && (array_key_exists($key, $FS2_permissions)))
      {
        if (!is_array($FS2_permissions[$key]))
        {
          $query_string = $query_string . "('".$FS2_permissions[$key]."', '".$row['user_id']."', 0),";
        }
        else
        {
          //loop through the permission strings
          foreach($FS2_permissions[$key] as $perm_val)
          {
            $query_string = $query_string . "('".$perm_val."', '".$row['user_id']."', 0),";
          }//foreach (inner)
        }//else branch
        $need_query = true;
      }//if valid key
    }//foreach

    //check, if we need to execute queries
    if ($need_query)
    {
      //cut of the ',' character at the end to prevent SQL syntax error
      $query_string = substr($query_string, 0, -1);
      $query_res = mysql_query($query_string, $new_link);
      if (!$query_res)
      {
        echo '<p class="error">Ein Wert konnte nicht in die neue user_permissions-Tabelle '
            .'eingef&uuml;gt werden.<br>Folgender Fehler trat auf:<br>';
        echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
        return false;
      }//if
      //now make the user member of the site's staff
      $query_res = mysql_query('UPDATE `'.NewDBTablePrefix.'user` '
                    ."SET user_is_staff='1' WHERE user_id='".$row['user_id']
                    ."' LIMIT 1", $new_link);
      if (!$query_res)
      {
        echo '<p class="error">Der Staffstatus in der neuen user-Tabelle konnte'
            .'nicht aktualisiert werden.<br>Folgender Fehler trat auf:<br>';
        echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
        return false;
      }//if
    }//if query needed
    else
    {
      echo '<p class="hint">Hinweis: Keine Berechtigungsaktualisierung '
          .'notwendig f&uuml;r userid '.$row['user_id'].'.</p>';
    }
  }//while
  echo '<span>Fertig.</span>';
  return true;
}//function permissionsTransition

?>
