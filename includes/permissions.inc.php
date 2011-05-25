<?php
  /*"constants" for translating permissions of FS1 to FS2
    Unfortunately, constants created via define() only allow scalar types and
    no arrays, so I did this as a variable array instead. That way we can handle
    permission transition a bit easier.
  */
  $FS2_permissions = array(
      'perm_newsadd' => 'news_add', //News hinzufügen
      'perm_newsedit' => 'news_edit', //News bearbeiten
      'perm_newscat' => 'news_cat', //News Kategorien
      //'perm_newsnewcat' => '???', //News Kategorien hinzufügen
      'perm_newsconfig' => 'news_config', //Newskonfiguration
      'perm_dladd' => 'dl_add', //Downloads hinzufügen
      'perm_dledit' => 'dl_edit', //Downloads bearbeiten
      'perm_dlcat' => 'dl_cat', //Downloadkategorien
      'perm_dlnewcat' => 'dl_newcat', //Downloadkategorien hinzufügen
      'perm_polladd' => 'poll_add', //Umfragen hinzufügen
      'perm_polledit' => 'poll_edit', //Umfragenarchiv
      //'perm_potmadd' => '???', //POTM-Bild hinzufügen
      //'perm_potmedit' => '???', //POTM-Übersicht
      'perm_screenadd' => 'screen_add', //Screenshots hinzufügen
      'perm_screenedit' => 'screen_edit', //Screenshots bearbeiten
      'perm_screencat' => 'gallery_cat', //Screenshotkategorien
      'perm_screennewcat' => 'gallery_newcat', //Screenshotkategorien hinzufügen
      'perm_screenconfig' => 'gallery_config', //Screenshotkonfiguration
      'perm_shopadd' => 'shop_add', //Shopartikel hinzufügen
      'perm_shopedit' => 'shop_edit', //Shopübersicht
      'perm_statedit' => 'stat_edit', //Statistik bearbeiten
      'perm_useradd' => 'user_add', //Benutzer hinzufügen
      'perm_useredit' => 'user_edit', //Benutzer bearbeiten
      'perm_userrights' => 'user_rights', //Benutzerrechte
      //'perm_map' => '???', //Community Map
      'perm_statview' => 'stat_view', //Statistik anzeigen
      'perm_statref' => 'stat_ref', //Referrerstatistik
      'perm_artikeladd' => 'articles_add', //Artikel hinzufügen
      'perm_artikeledit' => 'articles_edit', //Artikel bearbeiten
      'perm_templateedit' => array('tpl_affiliates', //Template für Partnerseiten
                                   'tpl_articles',   //Artikeltemplates
                                   'tpl_dl',         //Download(template)s
                                   'tpl_editor',     //Editor
                                   'tpl_fscodes',    //FS-Code
                                   'tpl_general',    //Allgemein
                                   'tpl_news',       //News
                                   'tpl_player',     //Flash-Player
                                   'tpl_poll',       //Umfragen
                                   'tpl_press',      //Presseberichte
                                   'tpl_previewimg', //Vorschaubild
                                   'tpl_screens',    //Screenshots
                                   'tpl_search',     //Suche
                                   'tpl_shop',       //Shop
                                   'tpl_user',       //Benutzer
                                   'tpl_wp'          //Wallpaper
                                   ), //Templates (alle)
      'perm_allphpinfo' => 'gen_phpinfo', //PHP-Info anzeigen(?)
      'perm_allconfig' => 'gen_config', //allgemeine Konfiguration
      'perm_allanouncement' => 'gen_announcement', //Ankündigung
      'perm_statspace' => 'stat_space', //Speicherplatz Statistik
  ); //end of array()
?>