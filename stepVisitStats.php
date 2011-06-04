<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Benutzerstatisik</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;bertragung der Benutzerstatistik. Dies kann einige Momente dauern.<br>
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
      //do the counter transition stuff here
      require_once 'includes/counterVisitors.php';
      //echo "Trying to copy the daily visitor data. This will take some time...<br>\n";
      if (counter_statTransition($old_link, $new_link))
      {
        echo "Besucherstatistik wurde erfolgreich &uuml;bertragen!<br>\n";
        // ---- general counter
        if (counterTransition($old_link, $new_link))
        {
          echo "Allgemeine Statistik wurde erfolgreich &uuml;bertragen!<br>\n";
          //We can proceed to the next step now, add link to go on.
          echo '<p><a href="stepRefStats.php"><strong>N&auml;chster Schritt: Refererstatistik</strong></a><br>'
              .'<strong>Achtung!</strong> Dieser Schritt kann sehr lange dauern. Ggf. ist es notwendig, '
              .'diesen Schritt zu &uuml;berspringen und beim nachfolgenden Schritt fortzufahren.<br><br></p>';
          echo '<p><a href="stepConfig.php"><strong>&Uuml;bern&auml;chster Schritt: globale Konfiguration</strong></a></p>';
        }
        else
        {
          echo "Allgemeine Statistik konnte nicht &uuml;bertragen werden!<br>\n";
        }
      }
      else
      {
        echo "Besucherstatistik konnte nicht &uuml;bertragen werden!<br>\n";
      }
    }//else
  }//else
?>
</div>
</body>
</html>