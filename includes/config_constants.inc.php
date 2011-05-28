<?php
/*
    This file is part of the Frogsystem Transition Tool. 
    Copyright (C) 2011  Thoronador

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