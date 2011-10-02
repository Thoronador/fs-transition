Kommentarliste und Spamerkennung für FrogSystem 2
=================================================

Diese Dateien fügen dem Admin-CP des FrogSystem 2 eine Auflistungsmöglichkeit
für Newskommentare hinzu. Neben der reinen Auflistung werden die Kommentare
auch auf möglichen Spam untersucht und das Ergebnis wird neben dem Kommentar in
der Liste angezeigt. Dafür wird ein statistischer Spamfilter genutzt
(bayesscher Spamfilter, hier implementiert mit Hilfe von b8, siehe dazu auch
<http://nasauber.de/opensource/b8/>). Dieser Filter "lernt" anhand schon vor-
handener Kommentare, was Spam ist und was nicht. Dazu füttert man den Filter
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

     ALTER TABLE `fs2_news_comments` ADD `comment_classification` TINYINT NOT NULL DEFAULT '0';

3. Die Dateien des Spamfilters ins Verzeichnis b8 unterhalb in der Serverkonfi-
   guration festgelegten "Document Root"-Verzeichnisses installieren. Meist ist
   dies auch das Verzeichnis, in dem sich das Frogsystem befindet (d.h. in der
   Regel heißt dieses Verzeichnis www/).
4. Die beigefügten Dateien admin_news_comments_list.php und eval_spam.inc.php
   in den Unterordner admin des FrogSystem 2 kopieren.
5. Damit die Kommentarliste im Menü erscheint, muss noch ein Eintrag in der
   Datenbank des FS2 vorgenommen werden, welchen man durch folgende SQL-
   Abfrage erzeugt:

      INSERT INTO `fs2_admin_cp` (`page_id`, `group_id`, `page_title`,
        `page_link`, `page_file`, `page_pos`, `page_int_sub_perm`)
        VALUES ('news_comments_list', 5, 'Kommentare', 'Kommentare',
        'admin_news_comments_list.php', 4, 0);

   Die Kommentarliste steht dann beim nächsten Besuch des Admin-CP zu Verfügung.


Deinstallation
--------------

Die Deinstallation erfolgt analog zur Installation durch Umkehr der dort
beschriebenen Schritte.

1. Entfernen des Eintrages mit der page_id 'news_comments_list' aus der Tabelle
   fs2_admin_cp

      DELETE FROM `fs2_admin_cp` WHERE `page_id`='news_comments_list' LIMIT 1

2. Die Dateien admin_news_comments_list.php und eval_spam.inc.php im admin-
   Unterordner des FrogSystem 2 entfernen.
3. Die b8-Dateien entfernen.
4. Entfernen der hinzugefügten Spalte in der Kommentartabelle:

      ALTER TABLE `fs2_news_comments` DROP COLUMN `comment_classification`

5. Entferen der Tabelle mit der Wortliste für den Spamfilter:

      DROP TABLE b8_wordlist

   Danach ist der Spamfilter wieder komplett entfernt.


Kompatibilität und Upgrades
---------------------------

Die Dateien wurden für ein unmodifiziertes FrogSystem 2 der Version alix5c
entwickelt und wurden NICHT mit anderen Versionen getestet. Bei Upgrades des
FrogSystem 2 oder Modifikationen sollten die Dateien admin_news_comments_list.php
und eval_spam.inc.php jedoch in der Regel weiter unverändert funktionieren,
sofern beim Update keine Änderungen der Datenbankstruktur durchgeführt wurden.