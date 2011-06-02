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

function counter_refTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's counter_stat table
  $result = mysql_query('SELECT * FROM '.OldDBTablePrefix.'counter_ref ', $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old counter_ref table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from counter_ref table.</p>\n";

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete possible content that is in new DB
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."counter_ref WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new counter_ref table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //disable keys temporatily to speed up inserts
  $query_res = mysql_query('ALTER TABLE '.NewDBTablePrefix.'counter_ref DISABLE KEYS', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not disable keys in new counter_ref table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  $has_to_do = true;
  while ($has_to_do)
  {
    $query_string = 'INSERT INTO '.NewDBTablePrefix.'counter_ref '
                   .'(ref_url, ref_count, ref_first, ref_last) VALUES ';
    $row_count = 0;
    while (($row = mysql_fetch_assoc($result)) && ($row_count<25))
    {
      $row_count = $row_count + 1;
      $query_string .= "('".mysql_real_escape_string($row['ref_url'], $new_link)
                      ."', '".$row['ref_count']."', '".$row['ref_date']."', 0),";
    }//while
    $has_to_do = ($row!==false);
    if ($row_count>0)
    {
      //cut of the ',' character at the end to prevent SQL syntax error
      $query_string = substr($query_string, 0, -1);
      //execute query to add new rows
      $query_res = mysql_query($query_string, $new_link);
      if (!$query_res)
      {
        echo '<p>Could not insert values into new counter_ref table.<br>';
        echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
        return false;
      }//if
    }//if
  }//while (outer)
  
  //re-enable keys
  $query_res = mysql_query('ALTER TABLE '.NewDBTablePrefix.'counter_ref ENABLE KEYS', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not re-enable keys in new counter_ref table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  
  echo '<span>Done.</span>'."\n";
  return true;
}//function counter_refTransition

?>