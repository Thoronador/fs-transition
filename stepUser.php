<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Benutzerprofile und -berechtigungen</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;bertragung der Nutzer. Dies kann einige Momente dauern.<br>
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
      //do the user transition stuff here
      require_once 'includes/user.php';
      echo "Trying to copy user data. This will take some time...<br>\n";
      if (userTransition($old_link, $new_link, OldFSRoot, NewFSRoot))
      {
        echo "User data was copied successfully!<br>\n";
        // ---- user permissions
        if (permissionsTransition($old_link, $new_link))
        {
          echo "User permissions were copied successfully!<br>\n";
        }
        else
        {
          echo "User permissions could not be copied to new FS!<br>\n";
        }
      }
      else
      {
        echo "User data could not be copied to new FS!<br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>