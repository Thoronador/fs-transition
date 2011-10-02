Kommentarliste und Spamerkennung für FrogSystem 1
=================================================

Diese Dateien fügen dem Admin-CP des FrogSystem 1 eine Auflistungsmöglichkeit
für Newskommentare hinzu. Um diese angezeigt zu bekommen, muss man die Berech-
tigung haben, Newsmeldungen zu editieren. Neben der reinen Auflistung werden
die Kommentare auch auf möglichen Spam untersucht und das Ergebnis wird neben
dem Kommentar in der Liste angezeigt. Dafür wird ein statistischer Spamfilter
genutzt (bayesscher Spamfilter, hier implementiert mit Hilfe von b8, siehe dazu
auch <http://nasauber.de/opensource/b8/>). Dieser Filter "lernt" anhand schon
vorhandener Kommentare, was Spam ist und was nicht. Dazu füttert man den Filter
mit Kommentaren und teilt ihm mit, ob der Kommentar Spam ist. Dafür stehen in
der Liste entsprechende Einstellungsmöglichkeiten zur Verfügung. In Zukunft
werden dann Worte aus diesen Kommentaren bei Berechnung der Spamwahrschein-
lichkeit berücksichtigt.
Direkt nach der Installation wird jeder Kommentar zunächst mit 50% bewertet.
Das liegt daran, dass am Anfang die Wortliste noch leer ist. Um eine einiger-
maßen sinnvolle Bewertung zu erhalten, muss der Filter mit mindestens einem
Spamkommentar und einem spamfreien Kommentar gefüttert worden sein. Je mehr
Kommentare man den Filter "lernen" lässt, umso umfassender wird die Bewertung
ausfallen. Man sollte dabei jedoch tunlichst vermeiden, einen Spamkommentar als
spamfrei an den Filter zu schicken oder einen spamfreien Kommentar als Spam an
den Filter zu geben. Dies verschlechtert die Erkennungsrate.


Installation
------------

1. Zunächst muss in der MySQL-Datenbank eine neue Tabelle angelegt werden,
   worin der Spamfilter seine Wortliste ablegt. Dazu sind folgende SQL-Abfragen
   notwendig:

      CREATE TABLE `b8_wordlist` (
        `token` varchar(255) character set utf8 collate utf8_bin NOT NULL,
        `count` varchar(255) default NULL,
        PRIMARY KEY  (`token`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

      INSERT INTO `b8_wordlist` VALUES ('bayes*dbversion', '2');
      INSERT INTO `b8_wordlist` VALUES ('bayes*texts.ham', '0');
      INSERT INTO `b8_wordlist` VALUES ('bayes*texts.spam', '0');

   Die letzten drei Anweisungen erzeugen Tabelleneinträge, welche für die
   korrekte Funktionsweise des Filters notwendig sind.
2. Weiterhin muss der Tabelle mit den Newskommentaren ein weiteres Feld hinzu-
   gefügt werden, welches speichert, ob ein Kommentar schon als Spam klassifi-
   ziert wurde:

     ALTER TABLE `fs_news_comments` ADD `comment_classification` TINYINT NOT NULL DEFAULT '0';

3. Die Dateien des Spamfilters ins Verzeichnis b8 unterhalb in der Serverkonfi-
   guration festgelegten "Document Root"-Verzeichnisses installieren. Meist ist
   dies auch das Verzeichnis, in dem sich das Frogsystem befindet (d.h. in der
   Regel heißt dieses Verzeichnis www/).
4. Die beigefügten Dateien admin_commentlist.php und eval_spam.inc.php in den
   Unterordner admin des FrogSystem kopieren.
5. Vor Ausführung dieses Schrittes ist es ratsam, eine Kopie der zu ändernden
   Datei index.php im admin-Unterordner des FS anzulegen, um diese bei der De-
   installation der Nutzerliste wieder zur Hand zu haben.

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
   im fünften Schritt der Installation angelegten Kopie bzw. Entfernung der
   dort vorgenommenen Einfügungen.
2. Die Dateien admin_commentlist.php und eval_spam.inc.php im admin-Unterordner
   des FrogSystem entfernen.
3. Die b8-Dateien entfernen.
4. Entfernen der hinzugefügten Spalte in der Kommentartabelle:

      ALTER TABLE `fs_news_comments` DROP COLUMN `comment_classification`

5. Entferen der Tabelle mit der Wortliste für den Spamfilter:

      DROP TABLE b8_wordlist

   Danach ist der Spamfilter wieder komplett entfernt.


Kompatibilität und Upgrades
---------------------------

Die Dateien wurden für ein unmodifiziertes FrogSystem 1 entwickelt und sind
daher NICHT mit späteren Versionen, im besonderen FrogSystem 2 aller Arten,
kompatibel. Bei Upgrades des FrogSystem (1) oder Modifikationen sollten die
Dateien admin_commentlist.php und eval_spam.inc.php jedoch in der Regel weiter
unverändert funktionieren; es muss nur die index.php im admin-Unterordner ange-
passt werden, indem man die Änderungen aus Schritt 5 des Installations-
abschnittes durchführt.