Kommentarliste und Spamerkennung für FrogSystem 1
=================================================

Diese Dateien fügen dem Admin-CP des FrogSystem 1 eine Auflistungsmöglichkeit
für Newskommentare hinzu. Um diese angezeigt zu bekommen, muss man die Berech-
tigung haben, Newsmeldungen zu editieren. Neben der reinen Auflistung werden
die Kommentare auch auf möglichen Spam untersucht und das Ergebnis wird neben
dem Kommentar in der Liste angezeigt. Hauptkriterium ist dabei das Vorhanden-
sein von Links in den Kommentaren. Es kann jedoch mitunter auch passieren, dass
normale Kommentare als Spam eingestuft werden oder Spamkommentare nicht als
solche erkannt werden. Daher ist die angezeigte Einstufung nur als Hilfe und
Orientierungswert zu verstehen. Ein PHP-Skript wird es niemals schaffen, stets
völlig eindeutig und richtig bestimmen können, ob ein Kommentar Spam ist oder
nicht.


Installation
------------

1. Die beigefügten Dateien admin_commentlist.php und eval_spam.inc.php in den
   Unterordner admin des FrogSystem kopieren.
2. Dieser Schritt unterscheidet sich abhängig davon, ob das FrogSystem modifi-
   ziert wurde oder nicht. In beiden Fällen ist es ratsam, vor der Ausführung
   eine Kopie der zu ändernden Datei index.php im admin-Unterordner des FS
   anzulegen, um diese bei der Deinstallation der Kommentarliste wieder zur
   Hand zu haben.
   a) Bei unmodifiziertem FrogSystem:
      Die Datei index.php ebenfalls in den Unterordner admin des FrogSystem
      kopieren. Diese Datei ersetzt damit die schon vorhandene index.php.
   b) Bei modifiziertem FrogSystem:
      In der Datei index.php im Unterordner admin des FrogSystem sind die in
      der mitgelieferten Datei index.php durch Kommentare gekennzeichnten
      Änderungen sinngemäß an der entsprechenden Stelle der modifizierten Datei
      index.php vorzunehmen. In der mitgelieferten Datei betrifft das zwei
      Anpassungen, welche in den Zeilen 66-74 und den Zeilen 339-353 zu finden
      sind.

   Die Kommentarliste steht dann beim nächsten Besuch des Admin-CP zu Verfügung.


Deinstallation
--------------

Die Deinstallation erfolgt analog zur Installation durch Umkehr der dort
beschriebenen Schritte.

1. Ersetzen der Datei index.php im admin-Unterordner des FrogSystem durch die
   im zweiten Schritt der Installation angelegten Kopie. Im Falle eines unmodi-
   fizierten FrogSystems kann auch die beigefügte Datei index.php.orig in
   index.php umbenannt und zur Ersetzung der index.php mit Kommentarliste
   benutzt werden.
2. Die Dateien admin_commentlist.php und eval_spam.inc.php im admin-Unterordner
   des FrogSystem entfernen.


Kompatibilität und Upgrades
---------------------------

Die Dateien wurden für ein unmodifiziertes FrogSystem 1 entwickelt und sind
daher NICHT mit späteren Versionen, im besonderen FrogSystem 2 aller Arten,
kompatibel. Bei Upgrades des FrogSystem (1) oder Modifikationen sollten die
Dateien admin_commentlist.php und eval_spam.inc.php jedoch in der Regel weiter
unverändert funktionieren; es muss nur die index.php im admin-Unterordner ange-
passt werden, indem man die Änderungen aus Schritt 2b des Installations-
abschnittes durchführt.