<?php

require_once 'connect.inc.php';

function pollTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all stuff from old DB's poll table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."poll", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old poll table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from poll table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."poll'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old poll table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old poll table.<br>';
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
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."poll WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new poll table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query("INSERT INTO ".NewDBTablePrefix."poll "
                  .'(poll_id, poll_quest, poll_start, poll_end, poll_type, poll_participants) '
                  ."VALUES ('".$row['poll_id']."', '".$row['poll_quest']."', '"
                  .$row['poll_start']."', '".$row['poll_end']."', '"
                  .$row['poll_type']."', 1)", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into poll table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto increment value
  $query_res = mysql_query("ALTER TABLE ".NewDBTablePrefix."poll AUTO_INCREMENT=".$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on poll table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //now try to get the participant count right
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database again.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  $result = mysql_query("SELECT poll_id, SUM(answer_count) AS participants FROM ".OldDBTablePrefix."poll_answers GROUP BY poll_id", $old_link);
  if ($result==false)
  {
    echo '<p>Could not execute query for participants calculation on old poll_answers table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //switch to new DB
  if (!selectNewDB($new_link))
  {
    echo '<p>Could not select new database.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }
  //fetch new row from previous result
  while ($row = mysql_fetch_assoc($result))
  {
    //set participant count
    $query_res = mysql_query("UPDATE ".NewDBTablePrefix."poll SET poll_participants='"
                  .$row['participants']."' WHERE poll_id='".$row['poll_id']."'");
    if (!$query_res)
    {
      echo '<p>Could not set update participant count in new poll table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  return true;
}//function pollTransition


function poll_answersTransition($old_link, $new_link)
{
  if (!selectOldDB($old_link))
  {
    echo '<p>Could not select old database.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  //get all anwer stuff from old DB's poll table
  $result = mysql_query("SELECT * FROM ".OldDBTablePrefix."poll_answers WHERE 1", $old_link);
  if ($result===false)
  {
    echo '<p>Could not execute query on old poll_answers table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  echo '<p>Got '.mysql_num_rows($result)." entries from poll_answers table.</p>\n";
  //get current auto-increment value
  $query_res = mysql_query("SHOW TABLE STATUS LIKE '".OldDBTablePrefix."poll_answers'", $old_link);
  if ($query_res===false)
  {
    echo '<p>Could not execute status query on old poll_answers table.<br>';
    echo mysql_errno($old_link).': '.mysql_error($old_link)."</p>\n";
    return false;
  }
  if (!($row=mysql_fetch_assoc($query_res)))
  {
    echo '<p>Could not fetch row from status query of old poll_answers table.<br>';
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
  $query_res = mysql_query("DELETE FROM ".NewDBTablePrefix."poll_answers WHERE 1", $new_link);
  if (!$query_res)
  {
    echo '<p>Could not delete existing values in new poll_answers table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  //put stuff into new DB's table
  echo '<span>Processing...</span>';
  while ($row = mysql_fetch_assoc($result))
  {
    $query_res = mysql_query("INSERT INTO ".NewDBTablePrefix."poll_answers "
                  .'(poll_id, answer_id, answer, answer_count) '
                  ."VALUES ('".$row['poll_id']."', '".$row['answer_id']."', '"
                  .$row['answer']."', '".$row['answer_count']."')", $new_link);
    if (!$query_res)
    {
      echo '<p>Could not insert values into poll_answers table.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
  }//while
  echo '<span>Done.</span>'."\n";
  //set auto-increment value
  $query_res = mysql_query("ALTER TABLE ".NewDBTablePrefix."poll_answers AUTO_INCREMENT=".$auto_inc_value, $new_link);
  if (!$query_res)
  {
    echo '<p>Could not set new auto-increment value on poll_answers table.<br>';
    echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
    return false;
  }//if
  return true;
}//function poll_answersTransition

?>