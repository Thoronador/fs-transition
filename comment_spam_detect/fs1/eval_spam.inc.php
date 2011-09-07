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

  /* evaluates a comment and returns its "spam level", i.e. an integer value
     that indicates the likeliness of a comment being spam. Main criterion is
     the number of links included in the comment's text.
     A return value of zero means that the comment does not seem to be spam, a
     value of three or higher indicates that the comment is most likely to be
     a spam comment. Values in between indicate that the comment might be spam.

     parameters:
         title        - the title of the comment
         poster_id    - the ID of the user who posted the comment; must be zero
                        for an unregistered user
         poster_name  - the name of the user who posted the comment
         comment_text - the comment's complete text
  */
  function spamEvaluation($title, $poster_id, $poster_name, $comment_text)
  {
    //test for url tags in comment text
    // ---- raise level for every opening url tag
    $comment_text = strtolower($comment_text);
    $spam_level = substr_count($comment_text, '[url=')
                 + substr_count($comment_text, '[url]')
                 + substr_count($comment_text, '<a href=');
    // ---- if no spam was found so far, check for plain URLs
    if ($spam_level==0)
    {
      $spam_level = substr_count($comment_text, 'http://');
    }
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
      $res = substr($title, 0, 3);
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

  /* turns the given spam level into a human-readable text with approoriate
     colour and returns that as HTML code snippet

     parameters:
         level - the spam level, an integer value (ideally the one returned by
                 the spamEvaluation() function)
  */
  function spamLevelToText($level)
  {
    if ($level<=0) return '<font color="#00cc0">unwahrscheinlich</font>';
    if ($level==1) return '<font color="#cccc00">gering</font>';
    if ($level==2) return '<font color="#ff8000">mittel</font>';
    //3 or higher
    return '<font color="#ff0000"><b>hoch</b></font>';
  }//function
?>