<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
  <title>&Uuml;bertragung der Daten zwischen FS1 und FS2 - Einleitung</title>
  <meta http-equiv="content-type" content="text/html;charset=UTF-8">
  <link rel="stylesheet" type="text/css" href="css/t.css">
</head>
<body>
<div style="margin:10%;"><h1>&Uuml;bertragung der Daten zwischen FS1 und FS2</h1><br>

Dieses und die folgenden PHP-Scripte dienen dazu, die Inhalte von einer
bestehenden Frogsystem&nbsp;1-Installation auf eine frische
Frogsystem&nbsp;2-Installation zu &uuml;bertragen. Letzteres muss also auch
schon installiert sein.<br>
<br>
Damit die Scripte wie vorgesehen arbeiten, m&uuml;ssen zuvor in der Datei
<tt>includes/config_constants.inc.php</tt> einige Anpassungen vorgenommen
werden. In dieser Datei m&uuml;ssen die Zugangsdaten f&uuml;r den MySQL-Server,
die jeweiligen Datenbanknamen sowie die bei der Installation des Frogsystem
angegebenen Tabellenpr&auml;fixe angegeben werden. Ist dies geschehen, kann mit
dem n&auml;chsten Schritt begonnen werden.<br>
<br>
Je nach Menge der zu &uuml;bertragenden Daten und Auslastung des Servers kann es
passieren, dass die Scripte erst nach mehreren Sekunden geladen werden. Dies ist
normal und kein Fehler, in dieser Zeit werden die Daten in die Datenbank der
neuen Frogsystem-Installation kopiert und f&uuml;r das neue System angepasst.<br>
<br>
<p><a href="stepCheck.php"><strong>N&auml;chster Schritt: &Uuml;berpr&uuml;fung der Konfigurationseinstellungen</strong></a></p>
</div>
</body>
</html>