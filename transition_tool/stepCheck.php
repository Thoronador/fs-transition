<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - &Uuml;berpr&uuml;fung der Konfigurationseinstellungen</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div>Beginne &Uuml;berpr&uuml;fung.<br>
<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2011, 2016  Thoronador

    The Frogsystem Transition Tool is free software: you can redistribute it
    and/or modify it under the terms of the GNU General Public License as
    published by the Free Software Foundation, either version 3 of the License,
    or (at your option) any later version.

    The Frogsystem Transition Tool is distributed in the hope that it will be
    useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

  require_once 'includes/config_constants.inc.php';
  require_once 'includes/connect.inc.php';

  echo '&Uuml;berpr&uuml;fe Verbindungen zum MySQL-Server...<br>';
  //set up connection to old DB
  $old_link = connectOldDB();
  if (!$old_link)
  {
    echo '<p class="error">Die Verbindung zum MySQL-Server mit den Daten des FS1 konnte nicht'
        .' hergestellt werden!<br>Folgender Fehler trat beim Verbindungsversuch'
        .' auf:<br>'.mysql_errno().': '.htmlentities(mysql_error())."</p>\n";
  }
  else
  {
    echo 'Verbindungsversuch zum MySQL-Server des alten FS erfolgreich!<br>';
    //set up connection to new DB
    $new_link = connectNewDB();
    if (!$new_link)
    {
      echo '<p class="error">Die Verbindung zum MySQL-Server mit den Daten des FS2 konnte '
          .'nicht hergestellt werden!<br>Folgender Fehler trat beim '
          .'Verbindungsversuch auf:<br>'.mysql_errno().': '
          .htmlentities(mysql_error())."</p>\n";
    }
    else
    {
      echo 'Verbindungsversuch zum MySQL-Server des neuen FS erfolgreich!<br>';
      //now check the existence of the databases
      echo 'Pr&uuml;fe Existenz der Datenbanken...<br>';
      // ---- old DB first
      $old_db = selectOldDB($old_link);
      if (!$old_db)
      {
        echo '<p class="error">Die Datenbank des FS1 konnte nicht ausgew&auml;hlt werden!<br>'
            .'Folgender Fehler trat beim Versuch auf:<br>'.mysql_errno($old_link).': '
            .htmlentities(mysql_error($old_link))."</p>\n";
      }
      else
      {
        echo 'Datenbank des alten FS vorhanden!<br>';
        // ---- new DB is next
        $new_db = selectNewDB($new_link);
        if (!$new_db)
        {
          echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt werden!'
              .'<br>Folgender Fehler trat beim Versuch auf:<br>'.mysql_errno($new_link)
              .': '.htmlentities(mysql_error($new_link))."</p>\n";
        }
        else
        {
          echo 'Datenbank des neuen FS vorhanden!<br>';
          //check tables
          require_once 'includes/checkTables.php';
          $ret = checkOldTables($old_link);
          if ($ret!==true)
          {
            echo '<p class="error">Die Tabellen des FS1 konnten nicht gefunden werden!'
                .'<br>Folgender Fehler trat beim Versuch auf:<br>'."</p>\n".$ret;
          }
          else
          {
            echo 'Tabellen des alten FS vorhanden!<br>';
            //old tables are present, check new ones
            $ret = checkNewTables($new_link);
            if ($ret!==true)
            {
              echo '<p>Die Tabellen des FS2 konnten nicht gefunden werden!'
                  .'<br>Folgender Fehler trat beim Versuch auf:<br>'."</p>\n"
                  .$ret;
            }
            else
            {
              echo 'Tabellen des neuen FS vorhanden!<br>';
              //check paths
              require_once 'includes/checkRoots.php';
              $ret = checkOldFSRoot();
              if ($ret !== true)
              {
                echo '<p class="error">Das Wurzelverzeichnis des FS1 konnte nicht gefunden werden!'
                .'<br>Folgender Fehler trat auf:<br>'."</p>\n".$ret;
              }
              else
              {
                echo 'Wurzelverzeichnis des alten FS ist vorhanden!<br>';
                echo 'Kanonischer Pfad: '.htmlentities(realpath(OldFSRoot)) ."<br>\n";
                $ret = checkNewFSRoot();
                if ($ret !== true)
                {
                  echo '<p class="error">Das Wurzelverzeichnis des FS2 konnte nicht gefunden werden!'
                  .'<br>Folgender Fehler trat auf:<br>'."</p>\n".$ret;
                }
                else
                {
                  echo 'Wurzelverzeichnis des neuen FS ist vorhanden!<br>';
                  echo 'Kanonischer Pfad: '.htmlentities(realpath(NewFSRoot)) ."<br>\n";
                  //all checks passed, go on
                  echo '<br><br><font color="#008000">Konfiguration ist in Ordnung.</font><br><br>';
                  echo '<a href="stepPollShop.php"><strong>N&auml;chster Schritt: Umfragen und Shop</strong></a>';
                }//else
              }//else
            }//else
          }//else
        }//else
      }//else
    }//else
  }//else
?>
</div>
</body>
</html>
