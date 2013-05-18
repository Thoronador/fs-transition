<?php
/*
    Auxiliary functions for transition of persistent world entries
    Copyright (C) 2013  Thoronador

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function getPersistentEXPCapAsInt($exp)
{
  if ($exp==='nein') return 0;
  if ($exp==='ja') return 1;
  if ($exp==='speziell') return 2;
  if ($exp==='k. A.') return -1;
  throw new Exception('Unknown exp. cap string value "'.$exp.'"!');
}

function getPersistentUptimeAsInt($uptime)
{
  if ($uptime==='ständig') return 1;
  if ($uptime==='regelmäßig') return 2;
  if ($uptime==='unregelmäßig') return 3;
  if ($uptime==='k.A.' || $uptime==='k. A.') return -1;
  throw new Exception('Unknown Uptime string value "'.$uptime.'"!');
}

function getPersistentDLSizeAsInt($dlsize)
{
  if ($dlsize==='k. A.' || $dlsize==='') return -1;
  if ($dlsize==='0 bis 25 MB') return 25;
  if ($dlsize==='26 bis 50 MB') return 50;
  if ($dlsize==='51 bis 100 MB') return 100;
  if ($dlsize==='mehr als 100 MB') return 101;
  if ($dlsize==='101 bis 250 MB') return 250;
  if ($dlsize==='251 bis 500 MB') return 500;
  if ($dlsize==='mehr als 500 MB') return 501;
  throw new Exception('Unknown DL size string value "'.$dlsize.'"!');
}//func

function getPersistentSvUAsInt($svu)
{
  if ($svu==='Schatten von Undernzit') return 1;
  if ($svu=='') return 0;
  throw new Exception('Unknown SvU string value "'.$svu.'"!');
}//func

function getPersistentHdUAsInt($hdu)
{
  if ($hdu==='Horden des Unterreichs') return 1;
  if ($hdu=='') return 0;
  throw new Exception('Unknown HdU string value "'.$hdu.'"!');
}//func

function getPersistentCEPAsInt($cep)
{
  if ($cep==='Community Expansion Pack') return 1;
  if ($cep=='') return 0;
  throw new Exception('Unknown CEP string value "'.$cep.'"!');
}//func

function getPersistentMotBAsInt($motb)
{
  if ($motb==='Mask of the Betrayer') return 1;
  if ($motb=='') return 0;
  throw new Exception('Unknown MotB string value "'.$motb.'"!');
}//func

function getPersistentRegAsInt($reg)
{
  if ($reg==='von Anfang an') return 0;
  if ($reg==='Level 1') return 1;
  if ($reg==='Level 2') return 2;
  if ($reg==='Level 3') return 3;
  if ($reg==='Level 4') return 4;
  if ($reg==='Level 5') return 5;
  if ($reg==='&gt; Level 5') return 6;
  if ($reg==='speziell') return 100;
  if ($reg==='nie') return 127;
  if ($reg==='k. A.') return -1;
  throw new Exception('Unknown reg. string value "'.$reg.'"!');
}

function getPersistentDMAsInt($dm)
{
  if (intval($dm)>0 && intval($dm)<=10)
  {
    return intval($dm);
  }//if
  if ($dm==='&gt; 10' || $dm==='> 10') return 11;
  if ($dm==='k. A.') return -1;
  throw new Exception('Unknown DM string value "'.$dm.'"!');
}//func

function getPersistentDifficultyAsInt($diff)
{
  if ($diff==='keine') return 0;
  if ($diff==='leicht') return 1;
  if ($diff==='mittel') return 2;
  if ($diff==='schwer') return 3;
  if ($diff==='uneinheitlich') return 4;
  if ($diff==='k. A.' || $diff==='k.A.') return -1;
  throw new Exception('Unknown difficulty string value "'.$diff.'"!');
}

function getPersistentFrequencyAsInt($diff)
{
  if ($diff==='keine') return 0;
  if ($diff==='selten') return 1;
  if ($diff==='normal') return 2;
  if ($diff==='oft') return 3;
  if ($diff==='uneinheitlich') return 4;
  if ($diff==='k. A.' || $diff==='k.A.') return -1;
  throw new Exception('Unknown frequency string value "'.$diff.'"!');
}

function getPersistentPvPAsInt($pvp)
{
  if ($pvp==='ja') return 1;
  if ($pvp==='nach Absprache') return 2;
  if ($pvp==='nein') return 3;
  if ($pvp==='speziell') return 4;
  if ($pvp==='k. A.') return -1;
  throw new Exception('Unknown PvP string value "'.$pvp.'"!');
}

?>
