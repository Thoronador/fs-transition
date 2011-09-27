Kommentarliste und Spamerkennung f�r FrogSystem 2
=================================================

Diese Dateien f�gen dem Admin-CP des FrogSystem 2 eine Auflistungsm�glichkeit
f�r Newskommentare hinzu. Neben der reinen Auflistung werden die Kommentare
auch auf m�glichen Spam untersucht und das Ergebnis wird neben dem Kommentar in
der Liste angezeigt. Hauptkriterium ist dabei das Vorhandensein von Links in
den Kommentaren. Es kann jedoch mitunter auch passieren, dass normale
Kommentare als Spam eingestuft werden oder Spamkommentare nicht als solche er-
kannt werden. Daher ist die angezeigte Einstufung nur als Hilfe und Orientie-
rungswert zu verstehen. Ein PHP-Skript wird es niemals schaffen, stets v�llig
eindeutig und richtig bestimmen k�nnen, ob ein Kommentar Spam ist oder nicht.


Installation
------------

1. Die beigef�gten Dateien admin_news_comments_list.php und eval_spam.inc.php
   in den Unterordner admin des FrogSystem 2 kopieren.
2. Damit die Kommentarliste im Men� erscheint, muss noch ein Eintrag in der
   Datenbank des FS2 vorgenommen werden, welchen man durch folgende SQL-
   Abfrage erzeugt:

      INSERT INTO `fs2_admin_cp` (`page_id`, `group_id`, `page_title`,
        `page_link`, `page_file`, `page_pos`, `page_int_sub_perm`)
        VALUES ('news_comments_list', 5, 'Kommentare', 'Kommentare',
        'admin_news_comments_list.php', 4, 0);

   Die Kommentarliste steht dann beim n�chsten Besuch des Admin-CP zu Verf�gung.


Deinstallation
--------------

Die Deinstallation erfolgt analog zur Installation durch Umkehr der dort
beschriebenen Schritte.

1. Entfernen des Eintrages mit der page_id 'news_comments_list' aus der Tabelle
   fs2_admin_cp

      DELETE FROM `fs2_admin_cp` WHERE `page_id`='news_comments_list' LIMIT 1

2. Die Dateien admin_news_comments_list.php und eval_spam.inc.php im admin-
   Unterordner des FrogSystem 2 entfernen.


Kompatibilit�t und Upgrades
---------------------------

Die Dateien wurden f�r ein unmodifiziertes FrogSystem 2 der Version alix5c
entwickelt und wurden NICHT mit anderen Versionen getestet. Bei Upgrades des
FrogSystem 2 oder Modifikationen sollten die Dateien admin_news_comments_list.php
und eval_spam.inc.php jedoch in der Regel weiter unver�ndert funktionieren,
sofern beim Update keine �nderungen der Datenbankstruktur durchgef�hrt wurden.
