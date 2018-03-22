<?
// ---------------------------------------------------------------------------------------------
//   Page de suivi des comptes
//     ($Author: miniroot $)
//     ($Date: 2013-02-05 20:35:03 +0100 (mar., 05 févr. 2013) $)
//     ($Revision: 420 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2
    Copyright (C) 2012 Matthieu Isorez

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>

<?

	if (GetDroit("AccesConfigVar"))
	  {
	  	$affrub="config";
	  }
	else if (GetDroit("AccesConfigGroupes"))
	  {
	  	$affrub="groupes";
	  }
	else if (GetDroit("AccesConfigEcheances"))
	  {
	  	$affrub="echeances";
	  }
	else if (GetDroit("AccesConfigPostes"))
	{
		$affrub="postes";
	}
?>
