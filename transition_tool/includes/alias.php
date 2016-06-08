<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2011, 2016  Thoronador

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

require_once 'connect.inc.php'; //required for selectNewDB()

/* creates some aliases that help to keep URLs from the old Frogsystem intact
   under the new Frogsystem

   parameters:
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/

function createAliasForOldURLs($new_link)
{
  //select new DB
  if (!selectNewDB($new_link))
  {
    echo '<p class="error">Die Datenbank des FS2 konnte nicht ausgew&auml;hlt '
        .'werden!<br>Folgender Fehler trat beim Versuch auf:<br>';
    echo mysql_errno($new_link).': '.htmlentities(mysql_error($new_link))."</p>\n";
    return false;
  }
  //"list" of aliases
  $aliases = array('newsarchiv'  => 'news_search',
                   'partner'     => 'affiliates',
                   'pollarchiv'  => 'polls',
                   'profil'      => 'user',
                   'screenshots' => 'gallery');
  $total = count($aliases);//gets the number of aliases
  $created = 0;
  //check for presence of aliases
  foreach($aliases as $from => $to)
  {
    $query_res = mysql_query('SELECT * FROM `'.NewDBTablePrefix."aliases` WHERE alias_go='"
                             .$from."' AND alias_forward_to='".$to."' AND alias_active=1",
                             $new_link);
    if (!$query_res)
    {
      echo '<p class="error">Die bestehenden, aktiven Aliase konnten nicht abgefragt werden.<br>';
      echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
      return false;
    }//if
    //check if there was such a line
    if (mysql_num_rows($query_res)<1)
    {
      //there's no such alias, add it
      $query_res = mysql_query('INSERT INTO `'.NewDBTablePrefix.'aliases` '
                              .'(alias_go, alias_forward_to, alias_active) '
                              ."VALUES ('".$from."', '".$to."', '1')", $new_link);
      if (!$query_res)
      {
        echo '<p class="error">Alias konnte nicht erzeugt werden.<br>';
        echo mysql_errno($new_link).': '.mysql_error($new_link)."</p>\n";
        return false;
      }//if
      $created = $created +1;
      echo 'Aliasweiterleitung von ?go='.$from.' nach ?go='.$to.' wurde erstellt.<br>';
    }//if num_rows<1
  }//foreach
  //show statistics
  echo $created.' von '.$total.' m&ouml;glichen Aliasweiterleitungen wurde';
  if ($created!=1)
  {
    echo 'n';
  }
  echo ' erstellt, '.($total-$created).' war(en) schon vorhanden.<br>'."\n";
  return true;
}//function createAliasForOldURLs

?>
