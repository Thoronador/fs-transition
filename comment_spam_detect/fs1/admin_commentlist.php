<?php
  //Kommentare auslesen
  $query = mysql_query('SELECT comment_id, comment_title, comment_date, comment_poster, comment_poster_id, comment_text '
                      .'FROM fs_news_comments '
                      //WHERE news_id = $_POST[newsid]
                      .'ORDER BY comment_date DESC LIMIT 30', $db);
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
                                <!--      <input class="button" type="submit" value="Editieren"> -->
                                </td>
                            </tr>
                        </table>