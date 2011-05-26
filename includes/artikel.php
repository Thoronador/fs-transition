<?php

require_once 'connect.inc.php';

function artikelTransition($old_link, $new_link) //yes, it's spelled the wrong way
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's user table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."artikel", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old artikel table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $artikel_entries = mysql_num_rows($result);
  if ($artikel_entries!=1)
  {
    echo '<p>Got '.$artikel_entries." entries from artikel table.</p>\n";
  }
  else
  {
    echo '<p>Got one entry from artikel table.</p>'."\n";
  }

  //go on with new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //delete possible content that is in new DB
  /* We delete all articles except the one which is about the FS code, because
     it is linked in the menu and is helpful anyway.
  */
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."articles WHERE article_url<>'fscode'", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new articles table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //now update the ID of the fscode article
  $query_res = mysql_query('UPDATE '.NewDBTablePrefix."articles SET article_id=1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not update article ID in new articles table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //set the auto_increment value to 2 (because we don't have more than one article here)
  $query_res = mysql_query('ALTER TABLE '.NewDBTablePrefix."articles AUTO_INCREMENT=2", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set auto-increment value on new articles table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if

  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query("INSERT INTO ".NewDBTablePrefix."articles "
                  .'(article_url, article_title, article_date, article_user, '
                  .'article_text, article_html, article_fscode, article_para, '
                  .'article_cat_id, article_search_update) '
                  ."VALUES ('".$row['artikel_url']."', '".$row['artikel_title']
                  ."', '".$row['artikel_date']."', '".$row['artikel_user']
                  ."', '".$row['artikel_text']."', 1, '".$row['artikel_fscode']
                  ."', '".$row['artikel_fscode']."', 1, '".$row['artikel_index']."')", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into new articles table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  return true;
}//function artikelTransition

?>