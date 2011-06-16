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

require_once 'config_constants.inc.php'; //required for the DB names and MySQL-
                                         // passwords

/* tries to connect to the old database (i.e. database of FS1) and returns the
   MySQL link identifier (resource type) for the connection on success or false
   on failure
*/
function connectOldDB()
{
  return mysql_connect(OldDBServer, OldDBUser, OldDBPassword);
}//function

/* tries to connect to the new database (i.e. database of FS2) and returns the
   MySQL link identifier (resource type) for the connection on success or false
   on failure
*/
function connectNewDB()
{
  return mysql_connect(NewDBServer, NewDBUser, NewDBPassword);
}//function

/* tries to set the database of the old Frogsystem as the active database and
   returns true on success, or false on failure.

   parameters
       link - the MySQL link identifier (resource type) for the connection to
              the old database
*/
function selectOldDB($link)
{
  return mysql_select_db(OldDBName, $link);
}//function

/* tries to set the database of the new Frogsystem as the active database and
   returns true on success, or false on failure.

   parameters
       link - the MySQL link identifier (resource type) for the connection to
              the new database
*/
function selectNewDB($link)
{
  return mysql_select_db(NewDBName, $link);
}//function

?>