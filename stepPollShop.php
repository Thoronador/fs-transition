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
    echo '<p class="error">Die Verbindung zur Datenbank des FS1 konnte nicht hergestellt werden.<br>'
         .mysql_errno().': '.mysql_error()."</p>\n";
  }
  else
  {
    //set up connection to new DB
    $new_link = connectNewDB();
    if (!$new_link)
    {
      echo '<p class="error">Die Verbindung zur Datenbank des FS2 konnte nicht hergestellt werden.<br>'
           .mysql_errno().': '.mysql_error()."</p>\n";
    }
    else
    {
      //do the real transition stuff here
      // ---- polls go first
      require_once 'includes/poll.php';
      if (pollTransition($old_link, $new_link))
      {
        echo "Umfragen wurden erfolgreich kopiert!<br>\n";
        // ---- poll answers
        if (poll_answersTransition($old_link, $new_link))
        {
          echo "Umfrageantworten erfolgreich kopiert!<br>\n";
          // ---- shop articles
          require_once 'includes/shop.php';
          if (shopTransition($old_link, $new_link, OldFSRoot, NewFSRoot))
          {
            echo "Shopdaten wurden erfolgreich kopiert!<br>\n";
            //We can proceed to the next step now, add link to go on.
            echo '<a href="stepScreens.php"><strong>N&auml;chster Schritt: Screenshots</strong></a>';
          }
          else
          {
            echo "<span class=\"error\">Shop konnte nicht kopiert werden!</span><br>\n";
          }
        }
        else
        {
          echo "<span class=\"error\">Umfrageantworten konnten nicht kopiert werden!</span><br>\n";
        }
      }
      else
      {
        echo "<span class=\"error\">Umfragen konnten nicht kopiert werden!</span><br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>