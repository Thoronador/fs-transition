<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2016  Thoronador

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

require_once 'includes/config_constants.inc.php';


/* checks whether the configuration setting for the old FS root directory points
   to a directory or not

   return value:
       Returns true, if the directory setting seems to be correct.
       The function returns a string containing an error message in HTML code,
       if the directory setting seems to be incorrect.
*/
function checkOldFSRoot()
{
  //check, if it is a directory
  if (!is_dir(OldFSRoot))
  {
    $real = realpath(OldFSRoot);
    $err = '<p>Das Wurzelverzeichnis des alten FS ist offenbar nicht korrekt!'
          .'<br>Der Pfad &quot;'.htmlentities(OldFSRoot)."&quot; ";
    if ($real !== FALSE)
    {
      $err .= '(kanonischer Pfad &quot;'.htmlentities($real).'&quot;) ';
    }
    $err .= "bezeichnet kein Verzeichnis.</p>\n";
    return $err;
  }
  //check, if the admin/ directory exists
  if (!is_dir(OldFSRoot."admin/"))
  {
    return '<p>Das Adminverzeichnis des alten FS existiert nicht!'
          .'<br>Der Pfad &quot;'.htmlentities(OldFSRoot."admin/")."&quot; bezeichnet kein Verzeichnis.</p>\n";
  }
  //all OK so far
  return true;
}


/* checks whether the configuration setting for the new FS root directory points
   to a directory or not

   return value:
       Returns true, if the directory setting seems to be correct.
       The function returns a string containing an error message in HTML code,
       if the directory setting seems to be incorrect.
*/
function checkNewFSRoot()
{
  //check, if it is a directory
  if (!is_dir(NewFSRoot))
  {
    $real = realpath(NewFSRoot);
    $err = '<p>Das Wurzelverzeichnis des neuen FS ist offenbar nicht korrekt!'
          .'<br>Der Pfad &quot;'.htmlentities(NewFSRoot)."&quot; ";
    if ($real !== FALSE)
    {
      $err .= '(kanonischer Pfad &quot;'.htmlentities($real).'&quot;) ';
    }
    $err .= "bezeichnet kein Verzeichnis.</p>\n";
    return $err;
  }
  //check, if the admin/ directory exists
  if (!is_dir(NewFSRoot."admin/"))
  {
    return '<p>Das Adminverzeichnis des neuen FS existiert nicht!'
          .'<br>Der Pfad &quot;'.htmlentities(NewFSRoot."admin/")."&quot; bezeichnet kein Verzeichnis.</p>\n";
  }
  //all OK so far
  return true;
}

?>
