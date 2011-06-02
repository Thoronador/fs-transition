<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Umfragen und Shop</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne Daten&uuml;bertragung. Dies kann einige Momente dauern.<br>
<?php
  include_once 'includes/config_constants.inc.php';
  include_once 'includes/connect.inc.php';

  //set up connection to old DB
  $old_link = connectOldDB();
  if (!$old_link)
  {
    echo '<p class="error">Could not establish connection to FS1 database.<br>'
         .mysql_errno().': '.mysql_error()."</p>\n";
  }
  else
  {
    //set up connection to new DB
    $new_link = connectNewDB();
    if (!$new_link)
    {
      echo '<p class="error">Could not establish connection to FS2 database.<br>'
           .mysql_errno().': '.mysql_error()."</p>\n";
    }
    else
    {
      //do the real transition stuff here
      // ---- polls go first
      require_once 'includes/poll.php';
      echo "Trying to copy poll data...<br>\n";
      if (pollTransition($old_link, $new_link))
      {
        echo "Polls were copied successfully!<br>\n";
        // ---- poll answers
        if (poll_answersTransition($old_link, $new_link))
        {
          echo "Poll answers were copied successfully!<br>\n";
          // ---- shop articles
          require_once 'includes/shop.php';
          if (shopTransition($old_link, $new_link, OldFSRoot, NewFSRoot))
          {
            echo "Shop data was copied successfully!<br>\n";
            //We can proceed to the next step now, add link to go on.
            echo '<a href="stepScreens.php"><strong>Next: screenshots</strong></a>';
          }
          else
          {
            echo "<span class=\"error\">Shop transition failed!</span><br>\n";
          }
        }
        else
        {
          echo "<span class=\"error\">Poll answers failed!</span><br>\n";
        }
      }
      else
      {
        echo "<span class=\"error\">Polls failed!</span><br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>