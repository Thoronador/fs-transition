<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - nk&uuml;ndigungen und Artikel</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;bertragung der Ank&uuml;ndigungen und Artikel. Dies kann einige Momente dauern.<br>
<?php
  include_once 'includes/config_constants.inc.php';
  include_once 'includes/connect.inc.php';

  echo 'Connections coming...<br>';
  //set up connection to old DB
  $old_link = connectOldDB();
  if (!$old_link)
  {
    echo '<p>Could not establish connection to FS1 database.<br>'
         .mysql_errno().': '.mysql_error()."</p>\n";
  }
  else
  {
    echo 'Link to old DB established!<br>';
    //set up connection to new DB
    $new_link = connectNewDB();
    if (!$new_link)
    {
      echo '<p>Could not establish connection to FS2 database.<br>'
           .mysql_errno().': '.mysql_error()."</p>\n";
    }
    else
    {
      echo 'Link to new DB established!<br>';
      //do the announcement transition stuff here
      require_once 'includes/anouncement.php';
      echo "Trying to copy announcement data. This will take some time...<br>\n";
      if (anouncementTransition($old_link, $new_link))
      {
        echo "Announcement data was copied successfully!<br>\n";
        // ---- articles
        require_once 'includes/artikel.php';
        if (artikelTransition($old_link, $new_link))
        {
          echo "Articles were copied successfully!<br>\n";
        }
        else
        {
          echo "Articles could not be copied to new FS!<br>\n";
        }
      }
      else
      {
        echo "Announcement data could not be copied to new FS!<br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>