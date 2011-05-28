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

require_once 'config_constants.inc.php';

function connectOldDB()
{
  return mysql_connect('localhost', OldDBUser, OldDBPassword);
}//function

function connectNewDB()
{
  return mysql_connect('localhost', NewDBUser, NewDBPassword);
}//function


function selectOldDB($link)
{
  return mysql_select_db(OldDBName, $link);
}//function

function selectNewDB($link)
{
  return mysql_select_db(NewDBName, $link);
}//function

?>