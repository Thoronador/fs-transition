<?php
  //constants for translating permissions of FS1 to FS2
  define('FS2perm_newsadd', 'news_add'); //News hinzufügen
  define('FS2perm_newsedit', 'news_edit'); //News bearbeiten
  define('FS2perm_newscat', 'news_cat'); //News Kategorien
  //define('FS2perm_newsnewcat', '???'); //News Kategorien hinzufügen
  define('FS2perm_newsconfig', 'news_config'); //Newskonfiguration
  define('FS2perm_dladd', 'dl_add'); //Downloads hinzufügen
  define('FS2perm_dledit', 'dl_edit'); //Downloads bearbeiten
  define('FS2perm_dlcat', 'dl_cat'); //Downloadkategorien
  define('FS2perm_dlnewcat', 'dl_newcat'); //Downloadkategorien hinzufügen
  define('FS2perm_polladd',  'poll_add'); //Umfragen hinzufügen
  define('FS2perm_polledit',  'poll_edit'); //Umfragenarchiv
  //define('FS2perm_potmadd',  '???'); //POTM-Bild hinzufügen
  //define('FS2perm_potmedit',  '???'); //POTM-Übersicht
  define('FS2perm_screenadd', 'screen_add'); //Screenshots hinzufügen
  define('FS2perm_screenedit', 'screen_edit'); //Screenshots bearbeiten
  define('FS2perm_screencat', 'gallery_cat'); //Screenshotkategorien
  define('FS2perm_screennewcat', 'gallery_newcat'); //Screenshotkategorien hinzufügen
  define('FS2perm_screenconfig', 'gallery_config'); //Screenshotkonfiguration
  define('FS2perm_shopadd', 'shop_add'); //Shopartikel hinzufügen
  define('FS2perm_shopedit', 'shop_edit'); //Shopübersicht
  define('FS2perm_statedit', 'stat_edit'); //Statistik bearbeiten
  define('FS2perm_useradd', 'user_add'); //Benutzer hinzufügen
  define('FS2perm_useredit', 'user_edit'); //Benutzer bearbeiten
  define('FS2perm_userrights', 'user_rights'); //Benutzerrechte
  //define('FS2perm_map', '???'); //Community Map
  define('FS2perm_statview', 'stat_view'); //Statistik anzeigen
  define('FS2perm_statref', 'stat_ref'); //Referrerstatistik
  define('FS2perm_artikeladd', 'articles_add'); //Artikel hinzufügen
  define('FS2perm_artikeledit', 'articles_edit'); //Artikel bearbeiten
  define('FS2perm_templateedit', array('tpl_articles',
                                       'tpl_dl',
                                       'tpl_news',
                                       'tpl_poll',
                                       'tpl_press',
                                       'tpl_screens',
                                       'tpl_shop',
                                       'tpl_user')); //Templates (alle)
  define('FS2perm_allphpinfo', 'gen_phpinfo'); //PHP-Info anzeigen(?)
  define('FS2perm_allconfig', 'gen_config'); //allgemeine Konfiguration
  define('FS2perm_allanouncement', 'gen_announcement'); //Ankündigung
  define('FS2perm_statspace', 'stat_space'); //Speicherplatz Statistik
?>