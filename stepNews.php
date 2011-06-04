<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Newsmeldungen</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;bertragung der News. Dies kann einige Momente dauern.<br>
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
      //do the news transition stuff here
      require_once 'includes/news.php';
      // ---- news categories first
      //echo "Trying to copy news category data. This will take some time...<br>\n";
      if (news_catTransition($old_link, $new_link, OldFSRoot, NewFSRoot))
      {
        echo "Newskategorien wurden erfolgreich &uuml;bertragen!<br>\n";
        // ---- news themselves
        if (newsTransition($old_link, $new_link))
        {
          echo "Newsmeldungen wurden erfolgreich &uuml;bertragen!<br>\n";
          // ---- news links are next
          if (news_linksTransition($old_link, $new_link))
          {
            echo "Newslinks wurden erfolgreich &uuml;bertragen!!<br>\n";
            // ---- news comments are next
            if (news_commentsTransition($old_link, $new_link))
            {
              echo "Newskommentare wurden erfolgreich &uuml;bertragen!<br>\n";
              // ---- the news configuration
              if (news_configTransition($old_link, $new_link))
              {
                echo "Newskonfiguration wurden aktualisiert!<br>\n";
                //We can proceed to the next step now, add link to go on.
                echo '<p><a href="stepDownloads.php"><strong>N&aul;chster Schritt: Downloads</strong></a></p>';
              }//if
              else
              {
                echo "<span class=\"error\">Newskonfiguration konnte nicht ins neue FS &uuml;bertragen werden!</span><br>\n";
              }
            }//if
            else
            {
              echo "<span class=\"error\">Newskommentare konnten nicht ins neue FS &uuml;bertragen werden!</span><br>\n";
            }//else
          }//if
          else
          {
            echo "<span class=\"error\">Newslinks konnten nicht ins neue FS &uuml;bertragen werden!</span><br>\n";
          }//else
        }//if
        else
        {
          echo "<span class=\"error\">News konnten nicht ins neue FS &uuml;bertragen werden!</span><br>\n";
        }//else
      }//if
      else
      {
        echo "<span class=\"error\">Newskategorien konnten nicht ins neue FS &uuml;bertragen werden!</span><br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>