<?php
  function spamEvaluation($title, $poster_id, $poster_name, $comment_text)
  {
    //test for url tags in comment text
    // ---- raise level for every opening url tag
    $comment_text = strtolower($comment_text);
    $spam_level = substr_count($comment_text, '[url=')
                 + substr_count($comment_text, '[url]');
    //test for poster being not a registered user
    if ($poster_id!=0 && strlen($poster_name)>=3)
    {
      //check for name
      $res = strtolower(substr($poster_name, 0, 3));
      $no_vowel = true;
      for($i=0; $i<3 && $no_vowel; $i=$i+1)
      {
        if ($res[$i]=='a' || $res[$i]=='e' || $res[$i]=='i' || $res[$i]=='o' || $res[$i]=='u')
        {
          $no_vowel = false;
        }
      }//for
      if ($no_vowel)
      {
        $spam_level = $spam_level +1;
      }
    }//if
    //test for strange title
    if (strlen($title)>=3)
    {
      $res = substr($poster_name, 0, 3);
      $no_vowel = true;
      $case_profile = 0;
      for($i=0; $i<3; $i=$i+1)
      {
        if (strtoupper($res[$i])==$res[$i])
        {
          $case_profile = $case_profile + (1 << (2-$i));
        }
        else
        {
          $res[$i] = strtolower($res[$i]);
        }
        if ($res[$i]=='a' || $res[$i]=='e' || $res[$i]=='i' || $res[$i]=='o' || $res[$i]=='u')
        {
          $no_vowel = false;
        }
      }//for
      if ($no_vowel || ($case_profile!=0 && $case_profile<4))
      {
        $spam_level = $spam_level +1;
      }
    }//if title
    return $spam_level;
  }//function
  
  function spamLevelToText($level)
  {
    if ($level<=0) return '<font color="#00cc0">unwahrscheinlich</font>';
    if ($level==1) return '<font color="#cccc00">gering</font>';
    if ($level==2) return '<font color="#ff8000">mittel</font>';
    //3 or higher
    return '<font color="#ff0000"><b>hoch</b></font>';
  }//function
?>