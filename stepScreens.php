<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Screenshots</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;bertragung der Screenshots. Dies kann einige Momente dauern.<br>
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
      //do the screen transition stuff here
      require_once 'includes/screens.php';
      echo "Trying to copy screenshot data. This will take some time...<br>\n";
      if (screenTransition($old_link, $new_link, OldFSRoot, NewFSRoot))
      {
        echo "Screenshots were copied successfully!<br>\n";
        // ---- screenshot categories
        if (screen_catTransition($old_link, $new_link))
        {
          echo "Screenshot categories were copied successfully!<br>\n";
          // ---- screenshot configuration
          if (screen_configTransition($old_link, $new_link))
          {
            echo "Screenshot configuration was copied successfully!<br>\n";
            //We can proceed to the next step now, add link to go on.
            echo '<a href="stepUser.php"><strong>Next: user data</strong></a>';
          }
          else
          {
            echo "Screenshot configuration failed!<br>\n";
          }
        }
        else
        {
          echo "Screenshot categories failed!<br>\n";
        }
      }
      else
      {
        echo "Screenshots failed!<br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>