<?php

require_once 'connect.inc.php';

function news_catTransition($old_link, $new_link, $old_basedir, $new_basedir)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news_cat table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."news_cat", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old news_cat table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from news_cat table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."news_cat'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old news_cat table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old news_cat table.<br>';
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
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."news_cat WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new news_cat table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query("INSERT INTO ".NewDBTablePrefix."news_cat "
                  .'(cat_id, cat_name, cat_description, cat_date, cat_user) '
                  ."VALUES ('".$row['cat_id']."', '".$row['cat_name']."', '', "
                  .'UNIX_TIMESTAMP(), 1)', $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new news_cat table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query("ALTER TABLE ".NewDBTablePrefix."news_cat AUTO_INCREMENT=".$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on news_cat table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //now copy the news category images
  // ---- check old directory
  if (!is_dir($old_basedir.'images/newscat/'))
  {
    echo '<p>'.htmlentities($old_basedir.'images/newscat/').' is not a '
        .'directory or does not exist!</p>';
    return false;
  }
  // ---- check new directory
  if (!is_dir($new_basedir.'images/cat/'))
  {
    echo '<p>'.htmlentities($new_basedir.'images/cat/').' is not a '
        .'directory or does not exist!</p>';
    return false;
  }
  // ---- now open the directory to get the filenames
  $handle = opendir($old_basedir.'images/newscat/');
  if ($handle===false)
  {
    echo '<p>Unable to open '.htmlentities($old_basedir.'images/newscat/')
         .' with opendir() function!</p>';
    return false;
  }
  //read the filenames
  $files_copied = 0;
  while (false !== ($file = readdir($handle)))
  {
    //check for file, we do not want to copy directories
    if (is_file($old_basedir.'images/newscat/'.$file))
    {
      //copy the file
      if (!copy($old_basedir.'images/newscat/'.$file, $new_basedir.'images/cat/news_'.$file))
      {
        echo '<p>Could not copy user image from '.$old_basedir.'images/newscat/'.$file."!</p>\n";
        //close the directory handle, because we return the line afterwards
        closedir($handle);
        return false;
      }
      $files_copied = $files_copied+1;
    }//if
  }//while
  closedir($handle);
  echo '<p>'.$files_copied.' news category images were copied.</p>'."\n";

  return true;
}//function news_catTransition


function newsTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."news", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old news table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from news table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."news'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old news table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old news table.<br>';
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
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."news WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new news table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query("INSERT INTO ".NewDBTablePrefix."news "
                  .'(news_id, cat_id, user_id, news_date, news_title, news_text, '
                  .' news_active, news_search_update) '
                  ."VALUES ('".$row['news_id']."', '".$row['cat_id']."', '"
                  .$row['user_id']."', '".$row['news_date']."', '"
                  .$row['news_title']."', '".$row['news_text']."', 1, 0)", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new news table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query("ALTER TABLE ".NewDBTablePrefix."news AUTO_INCREMENT=".$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on news table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  return true;
}//function newsTransition


function news_linksTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."news_links", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old news_links table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from news_links table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."news_links'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old news_links table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old news_links table.<br>';
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
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."news_links WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new news_links table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query("INSERT INTO ".NewDBTablePrefix."news_links "
                  .'(news_id, link_id, link_name, link_url, link_target) '
                  ."VALUES ('".$row['news_id']."', '".$row['link_id']."', '"
                  .$row['link_name']."', '".$row['link_url']."', '"
                  .$row['link_target']."')", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new news_links table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query("ALTER TABLE ".NewDBTablePrefix."news_links AUTO_INCREMENT=".$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on news_links table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  return true;
}//function news_linksTransition


function news_commentsTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."news_comments", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old news_comments table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from news_comments table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."news_comments'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old news_comments table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old news_comments table.<br>';
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
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."news_comments WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new news_comments table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query("INSERT INTO ".NewDBTablePrefix."news_comments "
                  .'(comment_id, news_id, comment_poster, comment_poster_id, '
                  .'comment_poster_ip, comment_date, comment_title, comment_text) '
                  ."VALUES ('".$row['comment_id']."', '".$row['news_id']."', '"
                  .$row['comment_poster']."', '".$row['comment_poster_id']
                  ."', '127.0.0.1', '".$row['comment_date']."', '"
                  .$row['comment_title']."', '".$row['comment_text']."')", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new news_comments table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query("ALTER TABLE ".NewDBTablePrefix."news_comments AUTO_INCREMENT=".$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on news_comments table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  return true;
}//function news_commentsTransition

function codeSettingTransition($old_val)
{
  /* HTML-/FS-Code settings:
  
     code setting    | FS1 (old) | FS2 (new)
     ----------------+-----------+-----------
     off             |     1     |     1
     news only       |     2     |     2
     comments only   |    N/A    |     3
     news + comments |     3     |     4
  */
  if ($old_val==3) return 4;
  return $old_val;
}//function codeSettingTransition

function news_configTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's news table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."news_config", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old news_config table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $entries = mysql_num_rows($result);
  if ($entries<1)
  {
    echo '<p>There was no entry in the news configuration table. Aborting!'."</p>\n";
    return false;
  }
  if ($entries==1)
  {
    echo '<p>Got one entry from news configuration table.</p>'."\n";
  }
  else if ($entries>1)
  {
    echo '<p>Got '.$entries.' entries from news configuration table, but only'
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
  $query_res = mysql_query('SELECT COUNT(id) AS count FROM '.NewDBTablePrefix.'news_config');
  if (!$query_res)
  {
    echo '<p>Could not execute SELECT query on new news_config table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  if (!($row = mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch query result from new news_config table.</p>';
    return false;
  }//if
  //check for number of entries
  if ($row['count']<1)
  {
    echo '<p>There is no configuration in the new news_config table!</p>';
    return false;
  }

  //update configuration in new DB's table
  echo '<span>Processing news configuration...</span>';
  if ($row = mysql_fetch_assoc($result))
  {
    //check, if fs_code and html_code are valid
    if (($row['fs_code']!=1) && ($row['fs_code']!=2) && ($row['fs_code']!=3))
    {
      echo '<p>Got invalid fs_code value from old configuration table.<br>';
      echo 'Value was &quot;'.htmlentities($row['fs_code']).'&quot;, but only '
          .'integer values from 1 to 3 are allowed here.</p>'."\n";
      return false;
    }
    if (($row['html_code']!=1) && ($row['html_code']!=2) && ($row['html_code']!=3))
    {
      echo '<p>Got invalid fs_code value from old configuration table.<br>';
      echo 'Value was &quot;'.htmlentities($row['html_code']).'&quot;, but only '
          .'integer values from 1 to 3 are allowed here.</p>'."\n";
      return false;
    }
    //adjust codes
    $row['html_code'] = codeSettingTransition($row['html_code']);
    $row['fs_code'] = codeSettingTransition($row['fs_code']);
    //now execute the update query on the new configuration table
    $query_res = mysql_query('UPDATE '.NewDBTablePrefix.'news_config '
                  ."SET num_news='".$row['num_news']."', num_head='".$row['num_head']
                  ."', html_code='".$row['html_code']."', fs_code='"
                  .$row['fs_code']."', com_sort='ASC'", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not update values in new news configuration table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    $affected = mysql_affected_rows($new_link);
    if ($affected<0)
    {
      //This should never happen, -1 only occurs on query failure.
      echo '<p>Update of news configuration table failed, no rows were updated!</p>';
      return false;
    }
    if ($affected==0)
    {
      //This should usually not happen, but this can be a possible result for a
      // new table that already has the same settings like the old table, so no
      // row was actually affected/changed. Can also happen, if there is no row.
      // However, we know from previous COUNT() query, that there is at least
      // one data row in the table, so everything is alright here.
      echo '<p>News configurations of old and new table are already the same.</p>';
    }
  }//if
  else
  {
    echo '<p>Could not fetch old news configuration!</p>';
    return false;
  }//else branch
  echo '<span>Done.</span>'."\n";
  return true;
}//function news_configTransition

?>