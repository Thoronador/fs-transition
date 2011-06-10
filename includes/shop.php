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

function shopTransition($old_link, $new_link, $old_basedir, $new_basedir)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's shop table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'shop`', $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old shop table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from shop table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."shop'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old shop table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old shop table.<br>';
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
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'shop` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new shop table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  $artikelarray = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'shop` '
                  .'(artikel_id, artikel_name, artikel_url, artikel_text, artikel_preis, artikel_hot) '
                  ."VALUES ('".$row['artikel_id']."', '".$row['artikel_name']."', '"
                  .$row['artikel_url']."', '".$row['artikel_text']."', '"
                  .$row['artikel_preis']."', '".$row['artikel_hot']."')", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new shop table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    //add current article id to array
    $artikelarray[] = (int) $row['artikel_id'];
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE `'.NewDBTablePrefix.'shop` AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on shop table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //try to copy images for shop
  echo '<span>Processing shop images...</span>';
  foreach ($artikelarray as $value)
  {
    if (!is_int($value))
    {
      echo '<p>Found a non-integer value in artikelarray! Aborting.'."</p>\n";
      return false;
    }
    //check for existing file (large image)
    if (!file_exists($old_basedir.'images/shop/'.$value.'.jpg'))
    {
      echo '<p>Could not find image for artikel_id '.$value."!</p>\n";
      return false;
    }
    //copy large artikel image
    if (!copy($old_basedir.'images/shop/'.$value.'.jpg', $new_basedir.'images/shop/'.$value.'.jpg'))
    {
      echo '<p>Could not copy image for artikel_id '.$value."!</p>\n";
      return false;
    }
    //check for existing file (small image)
    if (!file_exists($old_basedir.'images/shop/'.$value.'_s.jpg'))
    {
      echo '<p>Could not find small image for artikel_id '.$value."!</p>\n";
      return false;
    }
    //copy small artikel image
    if (!copy($old_basedir.'images/shop/'.$value.'_s.jpg', $new_basedir.'images/shop/'.$value.'_s.jpg'))
    {
      echo '<p>Could not copy small image for artikel_id '.$value."!</p>\n";
      return false;
    }
  }//foreach
  echo '<span>Success!</span><br>';

  return true;
}//function shopTransition

?>