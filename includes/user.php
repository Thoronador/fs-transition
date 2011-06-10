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
require_once 'permissions.inc.php';

function userTransition($old_link, $new_link, $old_basedir, $new_basedir)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's user table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'user`', $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old user table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from user table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."user'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old user table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old user table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $auto_inc_value = $row['Auto_increment'];

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'user` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new user table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
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
      echo '<p>Could not insert values into new user table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'user` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on user table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //now copy the user images
  // ---- check old directory
  if (!is_dir($old_basedir.'images/avatare/'))
  {
    echo '<p>'.htmlentities($old_basedir.'images/avatare/').' is not a '
        .'directory or does not exist!</p>';
    return false;
  }
  // ---- check new directory
  if (!is_dir($new_basedir.'media/user-images/'))
  {
    echo '<p>'.htmlentities($new_basedir.'media/user-images/').' is not a '
        .'directory or does not exist!</p>';
    return false;
  }
  // ---- now open the directory to get the filenames
  $handle = opendir($old_basedir.'images/avatare/');
  if ($handle===false)
  {
    echo '<p>Unable to open '.htmlentities($old_basedir.'images/avatare/')
         .' with opendir() function!</p>';
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
          echo '<p>Could not copy user image from '.$old_basedir.'images/avatare/'.$file."!</p>\n";
          //close the directory handle, because we return the line afterwards
          closedir($handle);
          return false;
        }
        $avatars_copied = $avatars_copied+1;
      }//if extension is .gif
    }//if substr() succeeded
  }//while
  closedir($handle);
  echo '<p>'.$avatars_copied.' user avatars were copied.</p>'."\n";

  return true;
}//function userTransition

function permissionsTransition($old_link, $new_link)
{
  global $FS2_permissions;

  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's user table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'permissions`', $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old permissions table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from permissions table.</p>\n";

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'user_permissions` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new user permission table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
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
        echo '<p>Could not insert values into new user permission table.<br>';
        echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
        return false;
      }//if
      //now make the user member of the site's staff
      $query_res = mysql_query('UPDATE `'.NewDBTablePrefix.'user` '
                    ."SET user_is_staff='1' WHERE user_id='".$row['user_id']
                    ."' LIMIT 1", $new_link);
      if (!$query_res)
      {
        echo '<p>Could not update user staff status in new user table.<br>';
        echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
        return false;
      }//if
    }//if query needed
    else
    {
      echo '<p>Hint: No permission update neccessary for userid '.$row['user_id'].'.</p>';
    }
  }//while

  return true;
}//function permissionsTransition

?>