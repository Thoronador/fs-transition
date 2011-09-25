<?php
/*
    This file is part of the Frogsystem User Administration List.
    Copyright (C) 2011  Thoronador

    The Frogsystem User Administration List is free software: you can redistribute it
    and/or modify it under the terms of the GNU General Public License as
    published by the Free Software Foundation, either version 3 of the License,
    or (at your option) any later version.

    The Frogsystem User Administration List is distributed in the hope that it will be
    useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

  if (!isset($_GET['start']) || $_GET['start']<0)
  {
    $_GET['start'] = 0;
  }
  $_GET['start'] = (int) $_GET['start'];
  settype($_GET['start'], "integer");
  //Anzahl der Nutzer auslesen
  $query = mysql_query('SELECT COUNT(user_id) AS uc FROM fs_user', $db);
  $uc = mysql_fetch_assoc($query);
  $uc = (int) $uc['uc'];
  if ($_GET['start']>=$uc)
  {
    $_GET['start'] = $uc - ($uc % 30);
  }

  //Sortierreihenfolge festgelegt?
  if (!isset($_GET['order']))
  {
    $_GET['order'] = 1;
  }
  settype($_GET['order'], 'integer');
  //erlaubte Werte prüfen
  if ($_GET['order']!==1)
  {
    $_GET['order'] = 0;
  }//if

  //Sortierkriterium festgelegt?
  if (!isset($_GET['sort']))
  {
    $_GET['sort'] = 'reg_date';
  }
  settype($_GET['sort'], 'string');

  //switch checking for valid parameter values
  switch ($_GET['sort'])
  {
    case 'reg_date':
         if ($_GET['order']==1)
         {
           $order = 'reg_date DESC';
         }
         else
         {
           $order = 'reg_date ASC';
         }
         break;
    case 'mail':
         if ($_GET['order']==1)
         {
           $order = 'user_mail DESC';
         }
         else
         {
           $order = 'user_mail ASC';
         }
         break;
    case 'domain':
         if ($_GET['order']==1)
         {
           $order = 'mail_dom DESC';
         }
         else
         {
           $order = 'mail_dom ASC';
         }
         break;
    case 'name':
         if ($_GET['order']==1)
         {
           $order = 'user_name DESC';
         }
         else
         {
           $order = 'user_name ASC';
         }
         break;
    default:
         $_GET['sort'] = 'reg_date';
         if ($_GET['order']==1)
         {
           $order = 'reg_date DESC';
         }
         else
         {
           $order = 'reg_date ASC';
         }
         break;
  }//switch

  //Nutzer auslesen
  $query = mysql_query('SELECT user_id, user_name, user_mail, is_admin, reg_date, '
                      .'SUBSTRING(user_mail FROM LOCATE(\'@\', user_mail)) AS mail_dom '
                      .'FROM fs_user ORDER BY '.$order.' LIMIT '.$_GET['start'].', 30', $db);
  $rows = mysql_num_rows($query);
  //Bereich (zahlenmäßig)
  $bereich = '<font class="small">'.($_GET['start']+1).' ... '.($_GET[start] + $rows).'</font>';
  //Ist dies nicht die erste Seite?
  if ($_GET['start']>0)
  {
    $prev_start = $_GET['start']-30;
    if ($prev_start<0)
    {
      $prev_start = 0;
    }
    $prev_page = '<a href="'.$PHP_SELF.'?go=userlist&start='.$prev_start.'&sort='.$_GET['sort']
                .'&order='.$_GET['order'].'&PHPSESSID='.session_id().'"><- zurück</a>';
  }//if nicht erste Seite
  //Ist dies nicht die letzte Seite?
  if ($_GET['start']+30<$uc)
  {
    $next_page = '<a href="'.$PHP_SELF.'?go=userlist&start='.($_GET['start']+30)
                .'&sort='.$_GET['sort'].'&order='.$_GET['order']
                .'&PHPSESSID='.session_id().'">weiter -></a>';
  }//if nicht die letzte Seite

  $inverse_order = ($_GET['order']+1) % 2;
?>
                    <p>
                        <table border="0" cellpadding="2" cellspacing="0" width="600">
                            <tr>
                                <td align="center" class="config" colspan="5">
                                    Nutzerliste
                                </td>
                            </tr>
                            <tr>
                                <td class="config" width="30%">
<?php
  echo '<a href="'.$PHP_SELF.'?go=userlist&start='.$_GET['start'].'&sort=name&order='.$inverse_order.'">Name</a>';
?>
                                </td>
                                <td class="config" width="30%">
<?php
  echo '<a href="'.$PHP_SELF.'?go=userlist&start='.$_GET['start'].'&sort=mail&order='.$inverse_order.'">Mail</a> / '
      .'<a href="'.$PHP_SELF.'?go=userlist&start='.$_GET['start'].'&sort=domain&order='.$inverse_order.'">Domain</a>';
?>
                                </td>
                                <td class="config" width="10%">
                                    Admin
                                </td>
                                <td class="config" width="20%">
<?php
  echo '<a href="'.$PHP_SELF.'?go=userlist&start='.$_GET['start'].'&sort=reg_date&order='.$inverse_order.'">Reg.datum</a>';
?>
                                </td>
                                <td class="config" width="10%">
                                    bearbeiten
                                </td>
                            </tr>
<?php
  require_once 'eval_spam.inc.php';

  while ($user_arr = mysql_fetch_assoc($query))
  {
    $user_arr['reg_date'] = date('d.m.Y' , $user_arr['reg_date'])
                           ." um ".date('H:i' , $user_arr['reg_date']);
    settype($user_arr['user_id'], 'integer');
    //Artikelzahl bestimmen
    $sub_query = mysql_query('SELECT COUNT(artikel_url) AS ac FROM fs_artikel '
                            .'WHERE artikel_user=\''.$user_arr['user_id'].'\'' , $db);
    $sub_res = mysql_fetch_assoc($sub_query);
    $user_arr['artikel'] = (int) $sub_res['ac'];
    //Downloadzahl bestimmen
    $sub_query = mysql_query('SELECT COUNT(dl_id) AS dc FROM fs_dl '
                            .'WHERE user_id=\''.$user_arr['user_id'].'\'' , $db);
    $sub_res = mysql_fetch_assoc($sub_query);
    $user_arr['downloads'] = (int) $sub_res['dc'];
    //Newsanzahl bestimmen
    $sub_query = mysql_query('SELECT COUNT(news_id) AS nc FROM fs_news '
                            .'WHERE user_id=\''.$user_arr['user_id'].'\'' , $db);
    $sub_res = mysql_fetch_assoc($sub_query);
    $user_arr['news'] = (int) $sub_res['nc'];
    //Kommentaranzahl bestimmen
    $sub_query = mysql_query('SELECT COUNT(comment_id) AS cc FROM fs_news_comments '
                            .'WHERE comment_poster_id=\''.$user_arr['user_id'].'\'' , $db);
    $sub_res = mysql_fetch_assoc($sub_query);
    $user_arr['comments'] = (int) $sub_res['cc'];
    //auf Avatar prüfen
    $user_arr['avatar'] = file_exists('../images/avatare/'.$user_arr['user_id'].'.gif');
    //list the stuff
    echo'<tr>
           <td class="configthin">
               <a href="../?go=profil&userid='.$user_arr['user_id'].'" target="_blank">'
               .killhtml($user_arr['user_name']).'</a>
           </td>
           <td class="configthin">
               '.killhtml($user_arr['user_mail']).'
           </td>
           <td class="configthin">
               '.$user_arr['is_admin'].'
           </td>
           <td class="configthin">
               '.$user_arr['reg_date'].'
           </td>
           <td class="configthin">';
    if ($user_arr['is_admin']==0 && $user_arr['news']==0
        && $user_arr['artikel']==0 && $user_arr['downloads']==0
        && $user_arr['comments']==0 && !$user_arr['avatar'])
    {
      echo '             <form action="'.$PHP_SELF.'" method="post">
               <input type="hidden" value="useredit" name="go">
               <input type="hidden" value="'.session_id().'" name="PHPSESSID">
               <input type="hidden" name="euuserid" value="'.$user_arr['user_id'].'">
               <input class="button" type="submit" value="Editieren">
             </form>';
    }//if
    else
    {
      if ($user_arr['is_admin']!=0)
      {
        echo 'Admin';
      }
      else
      {
        echo 'FS-relevant';
      }
    }//else (outer)
    echo '           </td>
         </tr>
         <tr>
           <td class="configthin">
             <font size="1"><u>';
    if (($user_arr['artikel']+$user_arr['downloads']+$user_arr['news']+$user_arr['comments']>0) || $user_arr['avatar'])
    {
      echo '<b>Aktivit&auml;t:</b>';
    }
    else
    {
      echo 'Aktivit&auml;t:';
    }
    echo '</u></font>
           </td>
           <td class="configthin" colspan="4">
             <font size="1">';
    //Artikelanzahl zeigen
    if ($user_arr['artikel']>0)
    {
      echo '<b>Artikel: '.$user_arr['artikel'].'</b>&#09;';
    }
    else
    {
      echo 'Artikel: '.$user_arr['artikel'].'&#09;';
    }
    //Downloadanzahl zeigen
    if ($user_arr['downloads']>0)
    {
      echo '<b>Downloads: '.$user_arr['downloads'].'</b>&#09;';
    }
    else
    {
      echo 'Downloads: '.$user_arr['downloads'].'&#09;';
    }
    //Newsanzahl zeigen
    if ($user_arr['news']>0)
    {
      echo '<b>News: '.$user_arr['news'].'</b>&#09;';
    }
    else
    {
      echo 'News: '.$user_arr['news'].'&#09;';
    }
    //Kommentaranzahl zeigen
    if ($user_arr['comments']>0)
    {
      echo '<b>Kommentare: '.$user_arr['comments'].'</b>&#09;';
    }
    else
    {
      echo 'Kommentare: '.$user_arr['comments'].'&#09;';
    }
    //Avatarexistenz
    if ($user_arr['avatar']>0)
    {
      echo '<b>Avatar: ja</b>&#09;';
    }
    else
    {
      echo 'Avatar:  nein&#09;';
    }

echo '</font>
           </td>
         </tr>
         <tr>
           <td colspan="5"><hr width="95%" style="color: #cccccc; background-color: #cccccc;"></td>
         </tr>';
  }//while
?>
                            <tr>
                                <td colspan="5">
                                    &nbsp;
                                </td>
                            </tr>
                        </table>
                      <table border="0" cellpadding="2" cellspacing="0" width="600">
                          <tr>
                              <td width="33%" style="text-align:left;" class="configthin">
<?php
  if (isset($prev_page))
  {
    echo $prev_page;
  }
?>
                              </td>
                              <td width="33%" style="text-align:center;">
<?php
  if (isset($bereich))
  {
    echo $bereich;
  }
?>
                              </td>
                              <td width="33%" style="text-align:right;" class="configthin">
<?php
  if (isset($next_page))
  {
    echo $next_page;
  }
?>
                              </td>
                          </tr>
                      </table>
                    </p>