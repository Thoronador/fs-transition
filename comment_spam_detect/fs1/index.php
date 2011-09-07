<?php
session_start();
include("config.inc.php");
include("functions.php");
include("adminfunctions.php");
include("phrases.inc.php");

///////////////////////////
///////// Cookie //////////
///////////////////////////

if ($_POST[stayonline]==1)
{
    admin_set_cookie($_POST[username], $_POST[userpassword]);
}

if ($HTTP_COOKIE_VARS["login"])
{
    $userpassword = substr($HTTP_COOKIE_VARS["login"], 0, 32);
    $username = substr($HTTP_COOKIE_VARS["login"], 32, strlen($HTTP_COOKIE_VARS["login"]));
    admin_login($username, $userpassword, true);
}
else
{
    $session_url = "&amp;sid=" . session_id();
}

if ($_POST[login]==1)
{
    admin_login($_POST[username], $_POST[userpassword], false);
}

///////////////////////////
// Unterseite festlegen ///
///////////////////////////

if ($_GET[go])
{
    $go = $_GET[go];
}
if ($_POST[go])
{
    $go = $_POST[go];
}

switch ($go)
{
    case 'allconfig':
        createpage('KONFIGURATION', $_SESSION[perm_allconfig], 'admin_allconfig.php');
        break;
    case 'allanouncement':
        createpage('ANKÜNDIGUNG', $_SESSION[perm_allanouncement], 'admin_allanouncement.php');
        break;
    case 'allphpinfo':
        createpage('PHP INFO', $_SESSION[perm_allphpinfo], 'admin_allphpinfo.php');
        break;
    case 'newsadd':
        createpage('NEWS HINZUFÜGEN', $_SESSION[perm_newsadd], 'admin_newsadd.php');
        break;
    case 'newsedit':
        createpage('NEWS ARCHIV', $_SESSION[perm_newsedit], 'admin_newsedit.php');
        break;
    case 'commentedit':
        createpage('KOMMENTAR EDITIEREN', $_SESSION[perm_newsedit], 'admin_commentedit.php');
        break;
    case 'commentlist':
        createpage('KOMMENTARE AUFLISTEN', $_SESSION[perm_newsedit], 'admin_commentlist.php');
        break;
    case 'newscat':
        createpage('NEWS KATEGORIEN', $_SESSION[perm_newscat], 'admin_newscat.php');
        break;
    case 'newsnewcat':
        createpage('KATEGORIE HINZUFÜGEN', $_SESSION[perm_newsnewcat], 'admin_newsnewcat.php');
        break;
    case 'newsconfig':
        createpage('NEWS KONFIGURATION', $_SESSION[perm_newsconfig], 'admin_newsconfig.php');
        break;
    case 'dladd':
        createpage('DOWNLOAD HINZUFÜGEN', $_SESSION[perm_dladd], 'admin_dladd.php');
        break;
    case 'dledit':
        createpage('DOWNLOAD BEARBEITEN', $_SESSION[perm_dledit], 'admin_dledit.php');
        break;
    case 'dlcat':
        createpage('DOWNLOAD KATEGORIEN', $_SESSION[perm_dlcat], 'admin_dlcat.php');
        break;
    case 'dlnewcat':
        createpage('KATEGORIE HINZUFÜGEN', $_SESSION[perm_dlnewcat], 'admin_dlnewcat.php');
        break;
    case 'polladd':
        createpage('UMFRAGE HINZUFÜGEN', $_SESSION[perm_polladd], 'admin_polladd.php');
        break;
    case 'polledit':
        createpage('UMFRAGEN ARCHIV', $_SESSION[perm_polledit], 'admin_polledit.php');
        break;
    case 'potmadd':
        createpage('POTM HINZUFÜGEN', $_SESSION[perm_potmadd], 'admin_potmadd.php');
        break;
    case 'potmedit':
        createpage('POTM ÜBERSICHT', $_SESSION[perm_potmedit], 'admin_potmedit.php');
        break;
    case 'screenadd':
        createpage('SCREENSHOT HINZUFÜGEN', $_SESSION[perm_screenadd], 'admin_screenadd.php');
        break;
    case 'screenedit':
        createpage('SCREENSHOT ÜBERSICHT', $_SESSION[perm_screenedit], 'admin_screenedit.php');
        break;
    case 'screencat':
        createpage('SCREENSHOT KATEGORIEN', $_SESSION[perm_screencat], 'admin_screencat.php');
        break;
    case 'screennewcat':
        createpage('KATEGORIE HINZUFÜGEN', $_SESSION[perm_screennewcat], 'admin_screennewcat.php');
        break;
    case 'screenconfig':
        createpage('SCREENSHOT KONFIGURATION', $_SESSION[perm_screenconfig], 'admin_screenconfig.php');
        break;
    case 'shopadd':
        createpage('ARTIKEL HINZUFÜGEN', $_SESSION[perm_shopadd], 'admin_shopadd.php');
        break;
    case 'shopedit':
        createpage('ARTIKEL ÜBERSICHT', $_SESSION[perm_shopedit], 'admin_shopedit.php');
        break;
    case 'map':
        createpage('COMMUNITY MAP BEARBEITEN', $_SESSION[perm_map], 'admin_map.php');
        break;
    case 'statview':
        createpage('STATISTIK ANZEIGEN', $_SESSION[perm_statview], 'admin_statview.php');
        break;
    case 'statedit':
        createpage('STATISTIK BEARBEITEN', $_SESSION[perm_statedit], 'admin_statedit.php');
        break;
    case 'statref':
        createpage('REFERRER ANZEIGEN', $_SESSION[perm_statref], 'admin_statref.php');
        break;
    case 'statspace':
        createpage('SPEICHERPLATZ STATISTIK', $_SESSION[perm_statspace], 'admin_statspace.php');
        break;
    case 'useradd':
        createpage('USER HINZUFÜGEN', $_SESSION[perm_useradd], 'admin_useradd.php');
        break;
    case 'useredit':
        createpage('USER BEARBEITEN', $_SESSION[perm_useredit], 'admin_useredit.php');
        break;
    case 'userrights':
        createpage('USER RECHTE', $_SESSION[perm_userrights], 'admin_userrights.php');
        break;
    case 'artikeladd':
        createpage('ARTIKEL SCHREIBEN', $_SESSION[perm_artikeladd], 'admin_artikeladd.php');
        break;
    case 'artikeledit':
        createpage('ARTIKEL BEARBEITEN', $_SESSION[perm_artikeledit], 'admin_artikeledit.php');
        break;
    case 'cimg':
        createpage('CONTENT IMAGES', $_SESSION[perm_artikeladd], 'admin_cimg.php');
        break;
    case 'cimgdel':
        createpage('CONTENT IMAGES ÜBERSICHT', $_SESSION[perm_artikeladd], 'admin_cimgdel.php');
        break;
    case 'artikeltemplate':
        createpage('ARTIKEL TEMPLATE BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_artikel.php');
        break;
    case 'polltemplate':
        createpage('UMFRAGEN TEMPLATE BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_poll.php');
        break;
    case 'potmtemplate':
        createpage('POTM TEMPLATE BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_potm.php');
        break;
    case 'shoptemplate':
        createpage('SHOP TEMPLATE BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_shop.php');
        break;
    case 'newstemplate':
        createpage('NEWS TEMPLATE BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_news.php');
        break;
    case 'alltemplate':
        createpage('ALLGEMEINE TEMPLATES BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_all.php');
        break;
    case 'dltemplate':
        createpage('DOWNLOAD TEMPLATE BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_dl.php');
        break;
    case 'screenshottemplate':
        createpage('SCREENSHOT TEMPLATE BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_screenshot.php');
        break;
    case 'usertemplate':
        createpage('USER TEMPLATE BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_user.php');
        break;
    case 'csstemplate':
        createpage('CSS DATEI BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_css.php');
        break;
    case 'emailtemplate':
        createpage('E-MAILS BEARBEITEN', $_SESSION[perm_templateedit], 'admin_template_email.php');
        break;
    case 'profil':
        createpage('Profil', 1, 'admin_profil.php');
        break;
    case 'logout':
        createpage('LOGOUT', 1, 'admin_logout.php');
        setcookie ("login", "", time() - 3600, "/");
        $_SESSION=array();
        break;
    default:
        createpage('LOGIN', 1, 'admin_login.php');
        break;
}

///////////////////////////
// HTML Header ausgeben ///
///////////////////////////

echo'
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Frog System - '.$pagetitle.'</title>
    <link rel="stylesheet" type="text/css" href="admin.css">
    <script src="functions.js" type="text/javascript"></script>
</head>
<body>

    <div id="menushadow">
        <div id="menu">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="100%" height="100" valign="top">
                        <img border="0" src="img/frogsystem.gif" width="140" height="78">
                    </td>
                </tr>

';

///////////////////////////
///// Allgemein Menü //////
///////////////////////////

if (
       ($_SESSION[perm_allconfig] == 1) ||
       ($_SESSION[perm_allanouncement] == 1) ||
       ($_SESSION[perm_allphpinfo] == 1)
   )
{
     echo'
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Allgemein
                    </td>
                </tr>
     ';
}
if ($_SESSION[perm_allconfig] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=allconfig'.$session_url.'">
                            Konfiguration
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_allanouncement] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=allanouncement'.$session_url.'">
                            Ankündigung
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_allphpinfo] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=allphpinfo'.$session_url.'">
                            PHP Info
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
//////// News Menü ////////
///////////////////////////

if (
       ($_SESSION[perm_newsadd] == 1) ||
       ($_SESSION[perm_newsedit] == 1) ||
       ($_SESSION[perm_newscat] == 1) ||
       ($_SESSION[perm_newsnewcat] == 1) ||
       ($_SESSION[perm_newsconfig] == 1)
   )
{
     echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        News
                    </td>
                </tr>
     ';
}
if ($_SESSION[perm_newsadd] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=newsadd'.$session_url.'">
                            schreiben
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_newsedit] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=newsedit'.$session_url.'">
                            Archiv / editieren
                        </a>
                    </td>
                </tr>
    ';
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=commentlist'.$session_url.'">
                            Kommentare auflisten
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_newscat] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=newscat'.$session_url.'">
                            Kategorien
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_newsnewcat] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=newsnewcat'.$session_url.'">
                            Neue Kategorie
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_newsconfig] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=newsconfig'.$session_url.'">
                            Konfiguration
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
////// Artikel Menü ///////
///////////////////////////

if (
       ($_SESSION[perm_artikeladd] == 1) ||
       ($_SESSION[perm_artikeledit] == 1)
   )
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Artikel
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_artikeladd] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=artikeladd'.$session_url.'">
                            schreiben
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_artikeledit] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=artikeledit'.$session_url.'">
                            editieren
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_artikeladd] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=cimg'.$session_url.'">
                            Bild hochladen
                        </a>
                    </td>
                </tr>
    ';
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=cimgdel'.$session_url.'">
                            Bilder Übersicht
                        </a>
                    </td>
                </tr>
    ';
}
///////////////////////////
////// Download Menü //////
///////////////////////////

if (
       ($_SESSION[perm_dladd] == 1) ||
       ($_SESSION[perm_dledit] == 1) ||
       ($_SESSION[perm_dlcat] == 1) ||
       ($_SESSION[perm_dlnewcat] == 1)
   )
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Download
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_dladd] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=dladd'.$session_url.'">
                            hinzufügen
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_dledit] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=dledit'.$session_url.'">
                            bearbeiten
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_dlcat] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=dlcat'.$session_url.'">
                            Kategorien
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_dlnewcat] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=dlnewcat'.$session_url.'">
                            Neue Kategorie
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
////// Umfrage Menü ///////
///////////////////////////

if (
       ($_SESSION[perm_polladd] == 1) ||
       ($_SESSION[perm_polledit] == 1)
   )
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Umfrage
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_polladd] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=polladd'.$session_url.'">
                            hinzufügen
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_polledit] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=polledit'.$session_url.'">
                            Archiv / editieren
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
//////// POTM Menü ////////
///////////////////////////

if (
       ($_SESSION[perm_potmadd] == 1) ||
       ($_SESSION[perm_potmedit] == 1)
   )
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        POTM
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_potmadd] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=potmadd'.$session_url.'">
                            Bild hinzufügen
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_potmedit] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=potmedit'.$session_url.'">
                            Übersicht
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
///// Screenshot Menü /////
///////////////////////////

if (
       ($_SESSION[perm_screenadd] == 1) ||
       ($_SESSION[perm_screenedit] == 1) ||
       ($_SESSION[perm_screencat] == 1) ||
       ($_SESSION[perm_screennewcat] == 1) ||
       ($_SESSION[perm_screenconfig] == 1)
   )
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Screenshots
                    </td>
                </tr>
   ';
}
if ($_SESSION[perm_screenadd] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=screenadd'.$session_url.'">
                            Bild hinzufügen
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_screenedit] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=screenedit'.$session_url.'">
                            Übersicht
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_screencat] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=screencat'.$session_url.'">
                            Kategorien
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_screennewcat] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=screennewcat'.$session_url.'">
                            Neue Kategorie
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_screenconfig] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=screenconfig'.$session_url.'">
                            Konfiguration
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
//////// Shop Menü ////////
///////////////////////////

if (
       ($_SESSION[perm_shopadd] == 1) ||
       ($_SESSION[perm_shopedit] == 1)
   )
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Shop
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_shopadd] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=shopadd'.$session_url.'">
                            Artikel hinzufügen
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_shopedit] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=shopedit'.$session_url.'">
                            Übersicht
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
/// Community Map Menü ////
///////////////////////////

if ($_SESSION[perm_map] == 1)
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Community Map
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=map&amp;landid=1'.$session_url.'">
                            Deutschland
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=map&amp;landid=2'.$session_url.'">
                            Schweiz
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=map&amp;landid=3'.$session_url.'">
                            Österreich
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
///// Statistik Menü //////
///////////////////////////

if (
       ($_SESSION[perm_statedit] == 1) ||
       ($_SESSION[perm_statview] == 1) ||
       ($_SESSION[perm_statspace] == 1) ||
       ($_SESSION[perm_statref] == 1)
   )
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Statistik
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_statview] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=statview'.$session_url.'">
                            anzeigen
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_statedit] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=statedit'.$session_url.'">
                            bearbeiten
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_statref] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=statref'.$session_url.'">
                            referrer
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_statspace] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=statspace'.$session_url.'">
                            Speicherplatz
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
////// Template Menü //////
///////////////////////////

if ($_SESSION[perm_templateedit] == 1)
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Template
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=csstemplate'.$session_url.'">
                            CSS
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=emailtemplate'.$session_url.'">
                            E-Mails
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=alltemplate'.$session_url.'">
                            Allgemein
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=newstemplate'.$session_url.'">
                            News
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=artikeltemplate'.$session_url.'">
                            Artikel
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=pre-re-interviewtemplate'.$session_url.'">
                            Pre-, Re-, Interview
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=polltemplate'.$session_url.'">
                            Umfragen
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=dltemplate'.$session_url.'">
                            Download
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=potmtemplate'.$session_url.'">
                            POTM
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=screenshottemplate'.$session_url.'">
                            Screenshot
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=shoptemplate'.$session_url.'">
                            Shop
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=usertemplate'.$session_url.'">
                            User
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
//////// User Menü ////////
///////////////////////////

if (
       ($_SESSION[perm_useradd] == 1) ||
       ($_SESSION[perm_useredit] == 1) ||
       ($_SESSION[perm_userrights] == 1)
   )
{
    echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Benutzer
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_useradd] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=useradd'.$session_url.'">
                            hinzufügen
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_useredit] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=useredit'.$session_url.'">
                            bearbeiten
                        </a>
                    </td>
                </tr>
    ';
}
if ($_SESSION[perm_userrights] == 1)
{
    echo'
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=userrights'.$session_url.'">
                            Rechte
                        </a>
                    </td>
                </tr>
    ';
}

///////////////////////////
///// Standard Menüs //////
///////////////////////////

echo'
                <tr>
                    <td width="100%" height="20"></td>
                </tr>
                <tr>
                    <td width="100%" class="menuhead">
                        <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                        Profil
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=profil'.$session_url.'">
                            bearbeiten
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%" class="menu">
                        <a class="menu" href="'.$PHP_SELF.'?go=logout'.$session_url.'">
                            Logout
                        </a>
                    </td>
                </tr>
                <tr>
                    <td width="100%">
                        &nbsp;
                    </td>
                </tr>
            </table>

        </div>
    </div>

    <div id="main">
        <div id="mainshadow">
            <div id="maincontent">
                <img border="0" src="img/pointer.gif" width="5" height="8" alt="">
                <font style="font-size:8pt;"><b>'.$pagetitle.'</b></font>
                <div align="center">
                    <p>
';


///////////////////////////
///// Inhalt Include //////
///////////////////////////

include($filetoinc);

///////////////////////////
///////// Footer //////////
///////////////////////////

echo'
                </div>
           </div>
       </div>
       <p>
   </div>
</body>
</html>
';

mysql_close($db);

?>