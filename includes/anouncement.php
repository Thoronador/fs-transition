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

function anouncementTransition($old_link, $new_link) //yes, it's spelled the wrong way
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's user table
  $result = mysql_query('SELECT * FROM `'.OldDBTablePrefix.'anouncement`', $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old anounement table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $announcement_entries = mysql_num_rows($result);
  if ($announcement_entries!=1)
  {
    echo '<p>Got '.$announcement_entries." entries from anouncement table.</p>\n";
  }
  else
  {
    echo '<p>Got one entry from anouncement table.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete possible content that is in new DB
  $query_res = mysql_query('DELETE FROM `'.NewDBTablePrefix.'announcement` WHERE 1', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new announcement table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'announcement` '
                  .'(id, announcement_text, show_announcement, '
                  .'activate_announcement, ann_html, ann_fscode, ann_para) '
                  ."VALUES (1, '".$row['text']."', 1, 1, 1, 1, 1)", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert value into new announcement table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//if
  else
  {
    //no announcement in old table, so create at least standard data row
    $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'announcement` '
                  .'(id, announcement_text, show_announcement, '
                  .'activate_announcement, ann_html, ann_fscode, ann_para) '
                  ."VALUES (1, '', 2, 0, 1, 1, 1)", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert value into new announcement table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }
  echo '<span>Done.</span>'."\n";
  return true;
}//function anouncementTransition

?>