<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Ank&uuml;ndigungen und Artikel</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;bertragung der Ank&uuml;ndigungen und Artikel. Dies kann einige Momente dauern.<br>
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
      //do the announcement transition stuff here
      require_once 'includes/anouncement.php';
      if (anouncementTransition($old_link, $new_link))
      {
        echo "Ank&uuml;ndigungsdaten wurden erfolgreich kopiert!<br>\n";
        // ---- articles
        require_once 'includes/artikel.php';
        if (artikelTransition($old_link, $new_link))
        {
          echo "Artikel wurden erfolgreich kopiert!<br>\n";
          //We can proceed to the next step now, add link to go on.
          echo '<p><a href="stepNews.php"><strong>N&auml;chster Schritt: Newsmeldungen</strong></a></p>';
        }
        else
        {
          echo "<span class=\"error\">Die Artikel konnten nicht ins neue FS kopiert werden!</span><br>\n";
        }
      }
      else
      {
        echo "<span class=\"error\">Ank&uuml;ndigungsdaten konnten nicht ins neue FS kopiert werden!</span><br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>