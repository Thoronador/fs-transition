<?php
/*
    This file is part of the Frogsystem Spam Detector.
    Copyright (C) 2011  Thoronador

    The Frogsystem Spam Detector is free software: you can redistribute it
    and/or modify it under the terms of the GNU General Public License as
    published by the Free Software Foundation, either version 3 of the License,
    or (at your option) any later version.

    The Frogsystem Spam Detector is distributed in the hope that it will be
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
  //Anzahl der Kommentare auslesen
  $query = mysql_query('SELECT COUNT(comment_id) AS cc FROM fs_news_comments', $db);
  $cc = mysql_fetch_assoc($query);
  $cc = (int) $cc['cc'];
  if ($_GET['start']>=$cc)
  {
    $_GET['start'] = $cc - ($cc % 30);
  }

  //Kommentare auslesen
  $query = mysql_query('SELECT comment_id, comment_title, comment_date, comment_poster, comment_poster_id, comment_text, '
                      .'fs_news_comments.news_id AS news_id, fs_news.news_id, news_title '
                      .'FROM fs_news_comments, fs_news WHERE fs_news_comments.news_id=fs_news.news_id '
                      .'ORDER BY comment_date DESC LIMIT '.$_GET['start'].', 30', $db);
  $rows = mysql_num_rows($query);
  //Bereich (zahlenm‰ﬂig)
  $bereich = '<font class="small">'.($_GET['start']+1).' ... '.($_GET[start] + $rows).'</font>';
  //Ist dies nicht die erste Seite?
  if ($_GET['start']>0)
  {
    $prev_start = $_GET['start']-30;
    if ($prev_start<0)
    {
      $prev_start = 0;
    }
    $prev_page = '<a href="'.$PHP_SELF.'?go=commentlist&start='.$prev_start.'&PHPSESSID='.session_id().'"><- zur¸ck</a>';
  }//if nicht erste Seite
  //Ist dies nicht die letzte Seite?
  if ($_GET['start']+30<$cc)
  {
    $next_page = '<a href="'.$PHP_SELF.'?go=commentlist&start='.($_GET['start']+30).'&PHPSESSID='.session_id().'">weiter -></a>';
  }//if nicht die letzte Seite

?>
                    <p>
                        <table border="0" cellpadding="2" cellspacing="0" width="600">
                            <tr>
                                <td align="center" class="config" colspan="5">
                                    Kommentare
                                </td>
                            </tr>
                            <tr>
                                <td class="config" width="30%">
                                    Titel
                                </td>
                                <td class="config" width="30%">
                                    Poster
                                </td>
                                <td class="config" width="20%">
                                    Datum
                                </td>
                                <td class="config" width="10%">
                                    Spamwahrscheinlichkeit
                                </td>
                                <td class="config" width="10%">
                                    bearbeiten
                                </td>
                            </tr>
<?php
  require_once 'eval_spam.inc.php';

  while ($comment_arr = mysql_fetch_assoc($query))
  {
    $dbcommentposterid = $comment_arr['comment_poster_id'];
    if ($comment_arr['comment_poster_id'] != 0)
    {
      $userindex = mysql_query('SELECT user_name FROM fs_user WHERE user_id = \''.$comment_arr['comment_poster_id'].'\'', $db);
      $comment_arr['comment_poster'] = mysql_result($userindex, 0, 'user_name');
    }
    $comment_arr['comment_date'] = date('d.m.Y' , $comment_arr['comment_date'])
                                      ." um ".date('H:i' , $comment_arr['comment_date']);
    echo'<tr>
           <td class="configthin">
               '.$comment_arr['comment_title'].'
           </td>
           <td class="configthin">
               '.$comment_arr['comment_poster'].'
           </td>
           <td class="configthin">
               '.$comment_arr['comment_date'].'
           </td>
           <td class="configthin">
               '.spamLevelToText(spamEvaluation($comment_arr['comment_title'],
                 $comment_arr['comment_poster_id'], $comment_arr['comment_poster'], $comment_arr['comment_text'])).'
           </td>
           <td class="configthin">
             <form action="'.$PHP_SELF.'" method="post">
               <input type="hidden" value="commentedit" name="go">
               <input type="hidden" value="'.session_id().'" name="PHPSESSID">
               <input type="hidden" name="commentid" value="'.$comment_arr['comment_id'].'">
               <input class="button" type="submit" value="Editieren">
             </form>
           </td>
         </tr>
         <tr>
           <td style="text-align:center;" colspan="5"><font size="1">Zugeh&ouml;rige Newsmeldung: <a href="../?go=comments&id='.$comment_arr['news_id'].'">&quot;'
                                              .htmlentities($comment_arr['news_title'], ENT_QUOTES).'&quot;</a></font>
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