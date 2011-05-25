<?php
  /* Die folgenden Zeilen definieren einige globale Konstanten, welche sp�ter
     im Laufe der Daten�bertragung zwischen den FrogSystem-Versionen benutzt
     werden. Ggf. sind diese noch anzupassen, damit der Umwandlungsprozess mit
     anderen Datenbanken/ Seiten funktioniert.
  */

  //Name des MySQL-Nutzers f�r die Daten des alten FS (d.h. FS1)
  define('OldDBUser', 'user');
  //Passwort des MySQL-Nutzers f�r die Daten des alten FS (d.h. FS1)
  define('OldDBPassword', 'password');
  //Name der MySQL-Datenbank, welche die Daten des alten FS (d.h. FS1) enth�lt
  define('OldDBName', 'db_name');
  //Prefix f�r Tabellennamen in der alten Version des FS (d.h. FS1)
  define('OldDBTablePrefix', 'fs_');
  /*relativer Pfad des Wurzelverzeichnises des alten FS (also jener Ordner, der
    die Unterordner admin, data, images, inc, usw. enth�lt) zum Ort der Datei
    startTransition.php. Der Schr�gstrich am Ende MUSS enthalten sein. */
  define('OldFSRoot', '../../www/');
  
  //Name des MySQL-Nutzers f�r die Daten des neuen FS (d.h. FS2)
  define('NewDBUser', 'user');
  //Passwort des MySQL-Nutzers f�r die Daten des neuen FS (d.h. FS2)
  define('NewDBPassword', 'password');
  //Name der MySQL-Datenbank, welche die Daten des neuen FS (d.h. FS2) enth�lt
  define('NewDBName', 'db_name2');
  //Prefix f�r Tabellennamen in der neuen Version des FS (d.h. FS2)
  define('NewDBTablePrefix', 'fs2_');
  /*relativer Pfad des Wurzelverzeichnises des neuen FS (also jener Ordner, der
    die Unterordner admin, applets, data, images, usw. enth�lt) zum Ort der
    Datei startTransition.php. Der Schr�gstrich am Ende MUSS enthalten sein. */
  define('NewFSRoot', '../../www2/');
?>