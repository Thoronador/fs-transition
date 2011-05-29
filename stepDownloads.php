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
    echo '<p>Could not establish connection to FS1 database.<br>'
         .mysql_errno().': '.mysql_error()."</p>\n";
  }
  else
  {
    //set up connection to new DB
    $new_link = connectNewDB();
    if (!$new_link)
    {
      echo '<p>Could not establish connection to FS2 database.<br>'
           .mysql_errno().': '.mysql_error()."</p>\n";
    }
    else
    {
      //do the download transition stuff here
      require_once 'includes/downloads.php';
      echo "Trying to copy download data. This will take some time...<br>\n";
      if (dl_catTransition($old_link, $new_link))
      {
        echo "Download categories were copied successfully!<br>\n";
        // ---- downloads themselves
        if (dlTransition($old_link, $new_link))
        {
          echo "Downloads were copied successfully!<br>\n";
          // ---- copy mirror downloads
          if (dl_mirrorsTransition($old_link, $new_link))
          {
            echo "Download mirrors were copied successfully!<br>\n";
            //We can proceed to the next step now, add link to go on.
            echo '<p><a href="stepVisitStats.php"><strong>N&auml;chster Schritt: Benutzerstatistik</strong></a></p>';
          }
          else
          {
            echo "Download mirrors could not be copied to new FS!<br>\n";
          }
        }
        else
        {
          echo "Downloads could not be copied to new FS!<br>\n";
        }
      }
      else
      {
        echo "Download categories could not be copied to new FS!<br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>