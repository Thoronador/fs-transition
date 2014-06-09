<?php
/*
    This file is part of the Frogsystem Transition Tool.
    Copyright (C) 2014  Thoronador

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

require_once 'connect.inc.php'; //required for selectOldDB() and selectNewDB()


/* function prereinterviewsTransition()
   transfers pre-/re-/interview data from the old Frogsystem to the new
   Frogsystem by copying the data from the old prereinterviews table to the
   appropriate new tables.

   table structure (old):

   fs_prereinterviews
     prereinterviews_id      SMALLINT(6), auto_inc
     prereinterviews_titel   VARCHAR(150)
     prereinterviews_url     VARCHAR(255)
     prereinterviews_datum   INT(10), UNSIGNED
     prereinterviews_text    TEXT
     prereinterviews_lang    TINYINT(1), UNSIGNED
     prereinterviews_spiel   TINYINT(1), UNSIGNED
     prereinterviews_cat     TINYINT(1), UNSIGNED
     prereinterviews_wertung VARCHAR(100)

     PRIMARY INDEX (prereinterviews_id)


   table structure (new):

   fs2_press
     press_id    SMALLINT(6), auto_inc
     press_titel VARCHAR(150)
     press_url   VARCHAR(255)
     press_date  INT(12)
     press_intro TEXT
     press_text  TEXT
     press_note  TEXT
     press_lang  INT(11)
     press_game  TINYINT(2)
     press_cat   TINYINT(2)

     PRIMARY INDEX (press_id)


   fs2_press_admin
     id    MEDIUMINT(8), auto_inc
     type  TINYINT(1)
     title VARCHAR(100)

     PRIMARY INDEX(id)

   fs2_press_config
     structure does not matter for transition


   Remarks: todo

   parameters:
       old_link - the MySQL link identifier (resource type) for the connection
                  to the old database
       new_link - the MySQL link identifier (resource type) for the connection
                  to the new database

   return value:
       true in case of success; false if failure
*/
function prereinterviewsTransition($old_link, $new_link)
{
  return false; //not implemented yet
}
