<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Partnerseiten</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;bertragung der Partnerseiten.<br>
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
      //do the partner transition stuff here
      require_once 'includes/partners.php';
      // --- genres go first
      if (partnerTransition($old_link, $new_link, OldFSRoot, NewFSRoot))
      {
        echo "Partnerseiten wurden erfolgreich &uuml;bertragen!<br>\n";
        //We can proceed to the next step now, add link to go on.
        //echo '<p><a href="stepPlaceholder.php"><strong>N&auml;chster Schritt: Platzhalter</strong></a></p>';
      }
      else
      {
        echo "<span class=\"error\">Partnerseiten konnten nicht &uuml;bertragen werden!</span><br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>
