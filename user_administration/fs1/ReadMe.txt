Nutzerliste für FrogSystem 1
============================

Diese Dateien fügen dem Admin-CP des FrogSystem 1 eine Auflistungsmöglichkeit
für registriere Benutzer hinzu. Um diese angezeigt zu bekommen, muss man die
Berechtigung haben, Benutzer zu editieren. Neben der reinen Auflistung wird
auch die Anzahl der vom jeweiligen Nutzer erstellten Artikel, Downloads, News
und Newskommentare angezeigt.


Installation
------------

1. Die beigefügte Datei admin_userlist.php in den Unterordner admin des
   FrogSystem kopieren.
2. Vor Ausführung dieses Schrittes ist es ratsam, eine Kopie der zu ändernden
   Datei index.php im admin-Unterordner des FS anzulegen, um diese bei der De-
   installation der Nutzerliste wieder zur Hand zu haben.

      In der Datei index.php im Unterordner admin des FrogSystem sind die in
      der mitgelieferten Datei index.php durch Kommentare gekennzeichnten
      Änderungen sinngemäß an der entsprechenden Stelle der Datei index.php
      vorzunehmen. In der mitgelieferten Datei betrifft das zwei Anpassungen,
      welche in den Zeilen 141-149 und den Zeilen 998-1012 zu finden sind.

   Die Nutzerliste steht dann beim nächsten Besuch des Admin-CP zu Verfügung.


Deinstallation
--------------

Die Deinstallation erfolgt analog zur Installation durch Umkehr der dort
beschriebenen Schritte.

1. Ersetzen der Datei index.php im admin-Unterordner des FrogSystem durch die
   im zweiten Schritt der Installation angelegten Kopie bzw. Entfernen der dort
   vorgenommenen Einfügungen.
2. Die Datei admin_userlist.php im admin-Unterordner des FrogSystem entfernen.


Kompatibilität und Upgrades
---------------------------

Die Dateien wurden für ein unmodifiziertes FrogSystem 1 entwickelt und sind
daher NICHT mit späteren Versionen, im besonderen FrogSystem 2 aller Arten,
kompatibel. Bei Modifikationen des FrogSystem (1) sollte die Datei
admin_userlist.php jedoch in der Regel weiter unverändert funktionieren; es
muss nur die index.php im admin-Unterordner angepasst werden, indem man die
Änderungen aus Schritt 2 des Installationsabschnittes durchführt.