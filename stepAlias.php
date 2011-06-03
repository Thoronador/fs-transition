<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Aliasweiterleitungen</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Die Aliasweiterleitungen werden genutzt, um bestimmte Sachen, welche im
alten System eine andere URL hatten als im neuen System, auch weiterhin
&uuml;ber die alten Links zug&auml;nglich zu halten.<br>
<?php
  include_once 'includes/config_constants.inc.php';
  include_once 'includes/connect.inc.php';

  //set up connection to new DB
  $new_link = connectNewDB();
  if (!$new_link)
  {
    echo '<p class="error">Die Verbindung zur Datenbank des FS2 konnte nicht hergestellt werden.<br>'
         .mysql_errno().': '.mysql_error()."</p>\n";
  }
  else
  {
    //do the alias creation stuff here
    require_once 'includes/alias.php';
    echo "Versuche, Aliasweiterleitungen zu erstellen...<br>\n";
    if (createAliasForOldURLs($new_link))
    {
      echo "Erfolg!<br>\n";
    }
    else
    {
       echo "<span class=\"error\">Weiterleitungen konnten nicht erstellt werden!</span><br>\n";
    }
  }//else
?>
</div>
</body>
</html>