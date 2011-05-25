<?php
  /* Die folgenden Zeilen definieren einige globale Konstanten, welche später
     im Laufe der Datenübertragung zwischen den FrogSystem-Versionen benutzt
     werden. Ggf. sind diese noch anzupassen, damit der Umwandlungsprozess mit
     anderen Datenbanken/ Seiten funktioniert.
  */

  //Name des MySQL-Nutzers für die Daten des alten FS (d.h. FS1)
  define('OldDBUser', 'user');
  //Passwort des MySQL-Nutzers für die Daten des alten FS (d.h. FS1)
  define('OldDBPassword', 'password');
  //Name der MySQL-Datenbank, welche die Daten des alten FS (d.h. FS1) enthält
  define('OldDBName', 'db_name');
  //Prefix für Tabellennamen in der alten Version des FS (d.h. FS1)
  define('OldDBTablePrefix', 'fs_');
  /*relativer Pfad des Wurzelverzeichnises des alten FS (also jener Ordner, der
    die Unterordner admin, data, images, inc, usw. enthält) zum Ort der Datei
    startTransition.php. Der Schrägstrich am Ende MUSS enthalten sein. */
  define('OldFSRoot', '../../www/');
  
  //Name des MySQL-Nutzers für die Daten des neuen FS (d.h. FS2)
  define('NewDBUser', 'user');
  //Passwort des MySQL-Nutzers für die Daten des neuen FS (d.h. FS2)
  define('NewDBPassword', 'password');
  //Name der MySQL-Datenbank, welche die Daten des neuen FS (d.h. FS2) enthält
  define('NewDBName', 'db_name2');
  //Prefix für Tabellennamen in der neuen Version des FS (d.h. FS2)
  define('NewDBTablePrefix', 'fs2_');
  /*relativer Pfad des Wurzelverzeichnises des neuen FS (also jener Ordner, der
    die Unterordner admin, applets, data, images, usw. enthält) zum Ort der
    Datei startTransition.php. Der Schrägstrich am Ende MUSS enthalten sein. */
  define('NewFSRoot', '../../www2/');
?>