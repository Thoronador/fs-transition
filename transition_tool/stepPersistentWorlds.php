<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Persistente Welten</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;bertragung der persistenten Welten.<br>
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
      //do the persistent world transition stuff here
      require_once 'includes/persistent_worlds.php';
      // --- genres go first
      if (persistent_genreTransition($old_link, $new_link))
      {
        echo "PW-Genres wurden erfolgreich &uuml;bertragen!<br>\n";
        // --- migrate PW settings
        if (persistent_settingTransition($old_link, $new_link))
        {
          echo "PW-Settings wurden erfolgreich &uuml;bertragen!<br>\n";
          // --- migrate PW entries
          if (persistentTransition($old_link, $new_link))
          {
            echo "Persistente Welten wurden erfolgreich &uuml;bertragen!<br>\n";
            // --- migrate PW interviews
            if (persisinterviewTransition($old_link, $new_link))
            {
              echo "Interviews zu Persistenten Welten wurden erfolgreich &uuml;bertragen!<br>\n";
              // --- migrate PW comments
              if (persistent_commentsTransition($old_link, $new_link))
              {
                echo "Kommentare zu Persistenten Welten wurden erfolgreich &uuml;bertragen!<br>\n";
                //We can proceed to the next step now, add link to go on.
                //echo '<p><a href="stepPlaceholder.php"><strong>N&auml;chster Schritt: Platzhalter</strong></a></p>';
              }
              else
              {
                echo "<span class=\"error\">Kommentare zu Persistenten Welten konnten nicht &uuml;bertragen werden!</span><br>\n";
              }
            }
            else
            {
              echo "<span class=\"error\">Interviews zu Persistenten Welten konnten nicht &uuml;bertragen werden!</span><br>\n";
            }
          }
          else
          {
            echo "<span class=\"error\">Persistente Welten konnten nicht &uuml;bertragen werden!</span><br>\n";
          }
        }
        else
        {
          echo "<span class=\"error\">PW-Settings konnten nicht &uuml;bertragen werden!</span><br>\n";
        }
      }
      else
      {
        echo "<span class=\"error\">PW-Genres konnten nicht &uuml;bertragen werden!</span><br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>
