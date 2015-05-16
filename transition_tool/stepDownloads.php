<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Downloads</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;bertragung der Downloads. Dies kann einige Momente dauern.<br>
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
      //do the download transition stuff here
      require_once 'includes/downloads.php';
      if (dl_catTransition($old_link, $new_link))
      {
        echo "Downloadkategorien wurden erfolgreich kopiert!<br>\n";
        // ---- downloads themselves
        if (dlTransition($old_link, $new_link))
        {
          echo "Downloads wurden erfolgreich kopiert!<br>\n";
          // ---- copy mirror downloads
          if (dl_mirrorsTransition($old_link, $new_link))
          {
            echo "Downloadmirrors wurden erfolgreich kopiert!<br>\n";
            // ---- download comments (PNW only)
            if (dl_commentsTransition($old_link, $new_link))
            {
              //We can proceed to the next step now, add link to go on.
              echo '<p><a href="stepVisitStats.php"><strong>N&auml;chster Schritt: Benutzerstatistik</strong></a></p>';
            }
            else
            {
              echo "Downloadkommentare konnten nicht ins neue FS &uuml;bernommen werden!<br>\n";
            }
          }
          else
          {
            echo "Downloadmirrors konnten nicht ins neue FS &uuml;bernommen werden!<br>\n";
          }
        }
        else
        {
          echo "Downloads konnten nicht ins neue FS &uuml;bernommen werden!<br>\n";
        }
      }
      else
      {
        echo "Downloadkategorien konnten nicht ins neue FS &uuml;bernommen werden!<br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>
