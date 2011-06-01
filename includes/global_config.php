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

function global_configTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's global_config table
  $result = mysql_query('SELECT * FROM '.OldDBTablePrefix.'global_config', $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old global_config table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $entries = mysql_num_rows($result);
  if ($entries<1)
  {
    echo '<p>There was no entry in the global configuration table. Aborting!'."</p>\n";
    return false;
  }
  if ($entries==1)
  {
    echo '<p>Got one entry from global configuration table.</p>'."\n";
  }
  else if ($entries>1)
  {
    echo '<p>Got '.$entries.' entries from global configuration table, but only'
        .' the first one will be used.</p>'."\n";
  }
  
  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //check that there is one entry in the new configuration table
  $query_res = mysql_query('SELECT COUNT(id) AS count FROM '.NewDBTablePrefix.'global_config', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not execute SELECT query on new global_config table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  if (!($row = mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch query result from new global_config table.</p>';
    return false;
  }//if
  //check for number of entries
  if ($row['count']<1)
  {
    echo '<p>There is no configuration in the new global_config table!</p>';
    return false;
  }
  
  //update configuration in new DB's table
  echo '<span>Processing global configuration...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    //now execute the update query on the new configuration table
    $query_res = mysql_query('UPDATE '.NewDBTablePrefix.'global_config '
                  ."SET virtualhost='".$row['virtualhost']."', admin_mail='"
                  .$row['admin_mail']."'", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not update values in new global configuration table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    $affected = mysql_affected_rows($new_link);
    if ($affected<0)
    {
      //This should never happen, -1 only occurs on query failure.
      echo '<p>Update of global configuration table failed, no rows were updated!</p>';
      return false;
    }
    if ($affected==0)
    {
      //This should usually not happen, but this can be a possible result for a
      // new table that already has the same settings like the old table, so no
      // row was actually affected/changed. Can also happen, if there is no row.
      // However, we know from previous COUNT() query, that there is at least
      // one data row in the table, so everything is alright here.
      echo '<p>Global configurations of old and new table are already the same.</p>';
    }
  }//if
  else
  {
    echo '<p>Could not fetch old global configuration!</p>';
    return false;
  }//else branch
  echo '<span>Done.</span>'."\n";
  return true;
}//function global_configTransition

?>
