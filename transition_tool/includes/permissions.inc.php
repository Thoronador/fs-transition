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

  /*"constants" for translating permissions of FS1 to FS2
    Unfortunately, constants created via define() only allow scalar types and
    no arrays, so I did this as a variable array instead. That way we can handle
    permission transition a bit easier.
  */
  $FS2_permissions = array(
      'perm_newsadd' => 'news_add', //News hinzufügen
      'perm_newsedit' => array('news_edit',    //News bearbeiten
                               'news_delete',  //News löschen (ist in FS1 in newsedit inbegriffen)
                               'news_comments' //Newskommentare bearbeiten (in FS1 in newsedit enthalten)
                              ),
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