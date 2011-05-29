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

function dl_catTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's dl_cat table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."dl_cat", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old dl_cat table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from dl_cat table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."dl_cat'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old dl_cat table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old dl_cat table.<br>';
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
  $query_res = mysql_query('DELETE FROM '.NewDBTablePrefix.'dl_cat WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new dl_cat table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO '.NewDBTablePrefix.'dl_cat '
                  .'(cat_id, subcat_id, cat_name) '
                  ."VALUES ('".$row['cat_id']."', '".$row['subcat_id']."', '"
                  .$row['cat_name']."')", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new dl_cat table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE '.NewDBTablePrefix.'dl_cat AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on dl_cat table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  return true;
}//function dl_catTransition


function dlTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's dl table
  $result = mysql_query('SELECT * FROM '.OldDBTablePrefix.'dl', $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old dl table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from dl table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."dl'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old dl table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old dl table.<br>';
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
  // ---- delete the stuff in fs2_dl
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."dl WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new dl table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  // ---- delete the stuff in fs2_dl_files
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."dl_files WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new dl_files table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  // ---- set auto-inc. value in new dl_files table to 1, in case it was higher
  $query_res = mysql_query('ALTER TABLE '.NewDBTablePrefix.'dl_files AUTO_INCREMENT=1', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set auto-increment value on dl_files table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  
  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    //the download itself
    $query_res = mysql_query('INSERT INTO '.NewDBTablePrefix.'dl '
                  .'(dl_id, cat_id, user_id, dl_date, dl_name, dl_text, '
                  .'dl_autor, dl_autor_url, dl_open, dl_search_update) '
                  ."VALUES ('".$row['dl_id']."', '".$row['cat_id']."', '"
                  .$row['user_id']."', '".$row['dl_date']."', '".$row['dl_name']
                  ."', '".$row['dl_text']."', '".$row['dl_autor']."', '"
                  .$row['dl_autor_url']."', '".$row['dl_open']
                  ."', UNIX_TIMESTAMP())", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new dl table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    //the file data
    $query_res = mysql_query('INSERT INTO '.NewDBTablePrefix.'dl_files '
                  .'(dl_id, file_count, file_name, file_url, file_size, file_is_mirror) '
                  ."VALUES ('".$row['dl_id']."', '".$row['dl_loads']."', '"
                  .$row['dl_name']."', '".$row['dl_url']."', '".$row['dl_size']
                  ."', 0)", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into dl_files table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE '.NewDBTablePrefix.'dl AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on new dl table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  return true;
}//function dlTransition


function dl_mirrorsTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's dl table
  $result = mysql_query('SELECT '.OldDBTablePrefix.'dl_mirrors.dl_id AS dl_id, mirror_count,'
           .' mirror_name, mirror_url, '.OldDBTablePrefix.'dl.dl_id, dl_size '
           .'FROM '.OldDBTablePrefix.'dl_mirrors, '.OldDBTablePrefix.'dl '
           .'WHERE '.OldDBTablePrefix.'dl.dl_id = '.OldDBTablePrefix.'dl_mirrors.dl_id',
           $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old dl_mirrors table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from dl_mirrors table.</p>\n";
  
  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //put stuff into new DB's table
  while ($row = mysql_fetch_assoc($result))
  {
    //the download itself
    $query_res = mysql_query('INSERT INTO '.NewDBTablePrefix.'dl_files '
                  .'(dl_id, file_count, file_name, file_url, file_size, file_is_mirror) '
                  ."VALUES ('".$row['dl_id']."', '".$row['mirror_count']."', '"
                  .$row['mirror_name']."', '".$row['mirror_url']."', '".$row['dl_size']
                  ."', 1)", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new dl_files table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  return true;
}//function dl_mirrorsTransition

?>