Kommentarliste und Spamerkennung f�r FrogSystem 1
=================================================

Diese Dateien f�gen dem Admin-CP des FrogSystem 1 eine Auflistungsm�glichkeit
f�r Newskommentare hinzu. Um diese angezeigt zu bekommen, muss man die Berech-
tigung haben, Newsmeldungen zu editieren. Neben der reinen Auflistung werden
die Kommentare auch auf m�glichen Spam untersucht und das Ergebnis wird neben
dem Kommentar in der Liste angezeigt. Hauptkriterium ist dabei das Vorhanden-
sein von Links in den Kommentaren. Es kann jedoch mitunter auch passieren, dass
normale Kommentare als Spam eingestuft werden oder Spamkommentare nicht als
solche erkannt werden. Daher ist die angezeigte Einstufung nur als Hilfe und
Orientierungswert zu verstehen. Ein PHP-Skript wird es niemals schaffen, stets
v�llig eindeutig und richtig bestimmen k�nnen, ob ein Kommentar Spam ist oder
nicht.


Installation
------------

1. Die beigef�gten Dateien admin_commentlist.php und eval_spam.inc.php in den
   Unterordner admin des FrogSystem kopieren.
2. Vor Ausf�hrung dieses Schrittes ist es ratsam, eine Kopie der zu �ndernden
   Datei index.php im admin-Unterordner des FS anzulegen, um diese bei der De-
   installation der Nutzerliste wieder zur Hand zu haben.

      In der Datei index.php im Unterordner admin des FrogSystem sind die in
      der mitgelieferten Datei index.php durch Kommentare gekennzeichnten
      �nderungen sinngem�� an der entsprechenden Stelle der modifizierten Datei
      index.php vorzunehmen. In der mitgelieferten Datei betrifft das zwei
      Anpassungen, welche in den Zeilen 66-74 und den Zeilen 339-353 zu finden
      sind.

   Die Kommentarliste steht dann beim n�chsten Besuch des Admin-CP zu Verf�gung.


Deinstallation
--------------

Die Deinstallation erfolgt analog zur Installation durch Umkehr der dort
beschriebenen Schritte.

1. Ersetzen der Datei index.php im admin-Unterordner des FrogSystem durch die
   im zweiten Schritt der Installation angelegten Kopie bzw. Entfernung der dort
   vorgenommenen Einf�gungen.
2. Die Dateien admin_commentlist.php und eval_spam.inc.php im admin-Unterordner
   des FrogSystem entfernen.


Kompatibilit�t und Upgrades
---------------------------

Die Dateien wurden f�r ein unmodifiziertes FrogSystem 1 entwickelt und sind
daher NICHT mit sp�teren Versionen, im besonderen FrogSystem 2 aller Arten,
kompatibel. Bei Upgrades des FrogSystem (1) oder Modifikationen sollten die
Dateien admin_commentlist.php und eval_spam.inc.php jedoch in der Regel weiter
unver�ndert funktionieren; es muss nur die index.php im admin-Unterordner ange-
passt werden, indem man die �nderungen aus Schritt 2 des Installations-
abschnittes durchf�hrt.