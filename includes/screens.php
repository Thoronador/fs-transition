<?php

require_once 'connect.inc.php';

function screenTransition($old_link, $new_link, $old_basedir, $new_basedir)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's screen table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."screen", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old screen table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from screen table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."screen'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old screen table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old screen table.<br>';
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
  //delete possible content that is in new screen table
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."screen WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new screen table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  $screenarray = array();
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO '.NewDBTablePrefix.'screen '
                  .'(screen_id, cat_id, screen_name) '
                  ."VALUES ('".$row['screen_id']."', '".$row['cat_id']."', '"
                  .$row['screen_name']."')", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into screen table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    //add current screen id to array
    $screenarray[] = (int) $row['screen_id'];
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query("ALTER TABLE ".NewDBTablePrefix."screen AUTO_INCREMENT=".$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on new screen table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //try to copy screenshot images and thumbnails
  echo '<span>Processing screenshot images and thumbnails...</span>';
  foreach ($screenarray as $value)
  {
    if (!is_int($value))
    {
      echo '<p>Found a non-integer value in screenarray! Aborting.'."</p>\n";
      return false;
    }
    //check for existing file (large image)
    if (!file_exists($old_basedir.'images/screenshots/'.$value.'.jpg'))
    {
      echo '<p>Could not find image for screen_id '.$value."!</p>\n";
      return false;
    }
    //copy large screenshot image
    if (!copy($old_basedir.'images/screenshots/'.$value.'.jpg', $new_basedir.'images/screenshots/'.$value.'.jpg'))
    {
      echo '<p>Could not copy image for artikel_id '.$value."!</p>\n";
      return false;
    }
    //check for existing file (small image)
    if (!file_exists($old_basedir.'images/screenshots/'.$value.'_s.jpg'))
    {
      echo '<p>Could not find thumbnail image for screen_id '.$value."!</p>\n";
      return false;
    }
    //copy small thumbnail image
    if (!copy($old_basedir.'images/screenshots/'.$value.'_s.jpg', $new_basedir.'images/screenshots/'.$value.'_s.jpg'))
    {
      echo '<p>Could not copy thumbnail image for screen_id '.$value."!</p>\n";
      return false;
    }
  }//foreach
  echo '<span>Success!</span><br>';

  return true;
}//function screenTransition


function screen_catTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's screen_cat table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."screen_cat", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old screen category table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from screen category table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."screen_cat'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old screen_cat table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old screen_cat table.<br>';
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
  //delete possible content that is in new screen_cat table
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."screen_cat WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new screen category table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('INSERT INTO '.NewDBTablePrefix.'screen_cat '
                  .'(cat_id, cat_name, cat_type, cat_visibility, cat_date, randompic) '
                  ."VALUES ('".$row['cat_id']."', '".$row['cat_name']."', 0, 1, '"
                  .$row['cat_date']."', 0)", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into screen_cat table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query('ALTER TABLE '.NewDBTablePrefix.'screen_cat AUTO_INCREMENT='.$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on new screen category table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  return true;
}//function screen_catTransition


function screen_configTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's screen_config table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."screen_config", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old screen config table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $entries = mysql_num_rows($result);
  if ($entries<1)
  {
    echo '<p>There was no entry in the screen configuration table. Aborting!'."</p>\n";
    return false;
  }
  if ($entries==1)
  {
    echo '<p>Got one entry from screen configuration table.</p>'."\n";
  }
  else if ($entries>1)
  {
    echo '<p>Got '.$entries.' entries from screen configuration table, but only'
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
  $query_res = mysql_query('SELECT COUNT(id) AS count FROM '.NewDBTablePrefix.'screen_config', $new_link);
  if (!$query_res)
  {
    echo '<p>Could not execute SELECT query on new screen_config table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  if (!($row = mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch query result from new screen_config table.</p>';
    return false;
  }//if
  //check for number of entries
  if ($row['count']<1)
  {
    echo '<p>There is no configuration in the new screen_config table!</p>';
    return false;
  }
  //update configuration in new DB's table
  echo '<span>Processing screenshot configuration...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query('UPDATE '.NewDBTablePrefix.'screen_config '
                  ."SET screen_x='".$row['screen_x']."' , screen_y='".$row['screen_y']
                  ."' , screen_thumb_x='".$row['thumb_x']."' , screen_thumb_y='"
                  .$row['thumb_y']."', screen_order='id', screen_sort='asc'",
                  $new_link);
    if (!$query_res)
    {
      echo '<p>Could not update values in new screen configuration table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    $affected = mysql_affected_rows($new_link);
    if ($affected<0)
    {
      //This should never happen, -1 only occurs on query failure.
      echo '<p>Update of screen configuration table failed, no rows were updated!</p>';
      return false;
    }
    if ($affected==0)
    {
      //This should usually not happen, but this can be a possible result for a
      // new table that already has the same settings like the old table, so no
      // row was actually affected/changed. Can also happen, if there is no row.
      // However, we know from previous COUNT() query, that there is at least
      // one data row in the table, so everything is alright here.
      echo '<p>Screen configurations of old and new table are already the same.</p>';
    }
  }//if
  else
  {
    echo '<p>Could not fetch old screenshot configuration!</p>';
    return false;
  }//else branch
  echo '<span>Done.</span>'."\n";
  return true;
}//function screen_configTransition

?>