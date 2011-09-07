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

  //Kommentare auslesen
  $query = mysql_query('SELECT comment_id, comment_title, comment_date, comment_poster, comment_poster_id, comment_text '
                      .'FROM fs_news_comments '
                      //WHERE news_id = $_POST[newsid]
                      .'ORDER BY comment_date DESC LIMIT 30', $db);
                      
  echo '                    <p>
                    <form action="'.$PHP_SELF.'" method="post">
                        <input type="hidden" value="commentedit" name="go">
                        <input type="hidden" value="'.session_id().'" name="PHPSESSID">';
?>
                        <table border="0" cellpadding="2" cellspacing="0" width="600">
                            <tr>
                                <td align="center" class="config" colspan="4">
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
               <input type="radio" name="commentid" value="'.$comment_arr['comment_id'].'">
           </td>
         </tr>';
  }//while
?>
                            <tr>
                                <td colspan="4">
                                    &nbsp;
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" align="center">
                                      <input class="button" type="submit" value="Editieren">
                                </td>
                            </tr>
                        </table>
                      </form>