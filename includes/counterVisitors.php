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

function counter_statTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's counter_stat table
  // ---- GROUP BY is neccessary to supress multiple occurences of the same day
  //      within the data, because the index in the old table is not unique
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'counter_stat` '
           .'GROUP BY s_year, s_month, s_day' , $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old counter_stat table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from counter_stat table.</p>\n";

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'counter_stat` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new counter_stat table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'counter_stat` '
                  .'(s_year, s_month, s_day, s_visits, s_hits) '
                  ."VALUES ('".$row['s_year']."', '".$row['s_month']."', '"
                  .$row['s_day']."', '".$row['s_visits']."', '".$row['s_hits']."')", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new counter_stat table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  return true;
}//function counter_statTransition


function counterTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's counter table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'counter`', $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old counter table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $entries = mysql_num_rows($result);
  if ($entries<1)
  {
    echo '<p>There was no entry in the old counter table. Aborting!'."</p>\n";
    return false;
  }
  if ($entries==1)
  {
    echo '<p>Got one entry from counter table.</p>'."\n";
  }
  else if ($entries>1)
  {
    echo '<p>Got '.$entries.' entries from counter table, but only the first '
        .'one will be used.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //check that there is one entry in the counter table
  $query_res = mysql_query('SELECT COUNT(id) AS count FROM `'.NewDBTablePrefix.'counter`');
  if (!$query_res)
  {
    echo '<p>Could not execute SELECT query on new counter table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  if (!($row = mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch query result from new counter table.</p>';
    return false;
  }//if
  //check for number of entries
  if ($row['count']<1)
  {
    echo '<p>There are no values in the new counter table!</p>';
    return false;
  }

  //update stats in new counter table
  echo '<span>Processing general statistics...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    //execute the update query on the new configuration table
    $query_res = mysql_query('UPDATE `'.NewDBTablePrefix.'counter` '
                  ."SET visits='".$row['visits']."', hits='".$row['hits']
                  ."', user='".$row['user']."', artikel='".$row['artikel']
                  ."', news='".$row['news']."', comments='".$row['comments']
                  ."'", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not update values in new counter table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    $affected = mysql_affected_rows($new_link);
    if ($affected<0)
    {
      //This should never happen, -1 only occurs on query failure.
      echo '<p>Update of counter table failed, no rows were updated!</p>';
      return false;
    }
    if ($affected==0)
    {
      //This should usually not happen, but this can be a possible result for a
      // new table that already has the same settings like the old table, so no
      // row was actually affected/changed. Can also happen, if there is no row.
      // However, we know from previous COUNT() query, that there is at least
      // one data row in the table, so everything is alright here.
      echo '<p>Counter tables of old and new table are already the same.</p>';
    }
  }//if
  else
  {
    echo '<p>Could not fetch old counter values!</p>';
    return false;
  }//else branch
  echo '<span>Done.</span>'."\n";
  return true;
}//funcion counterTransition

?>