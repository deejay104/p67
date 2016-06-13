<?
/*
    SoceIt v2.0
    Copyright (C) 2005 Matthieu Isorez

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

    ($Author: miniroot $)
    ($Date: 2016-04-22 22:08:32 +0200 (ven., 22 avr. 2016) $)
    ($Revision: 460 $)
*/

// Class Manifestation
class manip_class{
	# Constructor
	function __construct($id=0,$sql){
		$this->sql=$sql;
		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Load user informations
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM p67_manips WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->titre=$res["titre"];
		$this->comment=$res["comment"];
		$this->dte_manip=$res["dte_manip"];
		$this->uid_creat=$res["uid_creat"];
		$this->dte_creat=$res["dte_creat"];
		$this->uid_maj=$res["uid_maj"];
		$this->dte_maj=$res["dte_maj"];
	}

	function Valid($k,$v) 
	{
	}


	function Save()
	{ global $uid;
		$sql=$this->sql;

		if ($this->dte_manip=="")
		  { return "La date est obligatoire"; }


		// Vérifie la date/heure de la manip
		$t=split(" ",$this->dte_manip);
		if (!eregi("^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$",$t[0]))
		  { return "La date de début n'a pas un format correct (jj/mm/aaaa).<br />"; }
		if (!eregi("^[0-9]{1,2}(:[0-9]{1,2}(:[0-9]{1,2})?)?$",$t[1]))
		  { return "L'heure de début n'a pas un format correct (hh:mm:ss).<br />"; }

		$hdeb=ereg_replace('^([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$','\\1', $t[1]);
		$mdeb=ereg_replace('^([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$','\\2', $t[1]);
		$dte=date2sql($t[0])." $hdeb".(($mdeb!="") ? ":$mdeb" : ":00");
		$this->dte_deb=$dte;

		if ($this->id==0)
		  {
			$query="INSERT INTO p67_manips SET uid_creat=$uid, dte_creat=NOW()";
			$this->id=$sql->Insert($query);

			$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
			$query.="VALUES (NULL , 'manifestations', 'p67_manips', '".$this->id."', '$uid', NOW(), 'ADD', 'Create meeting')";
			$sql->Insert($query);
		  }

		// Met à jour les infos
		$query ="UPDATE p67_manips SET ";
		$query.="titre='$this->titre',";
		$query.="comment='$this->comment',";
		$query.="dte_manip='$this->dte_manip',";
		$query.="uid_maj=$uid, dte_maj=NOW() ";
		$query.="WHERE id=$this->id";
		$sql->Update($query);

		$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'manifestations', 'p67_manips', '".$this->id."', '$uid', NOW(), 'MOD', 'Modify meeting')";
		$sql->Insert($query);

		return "";
	}

	function Delete()
	{ global $uid;
		$sql=$this->sql;
		$query="UPDATE p67_manips SET actif='non', uid_maj=$uid, dte_maj=NOW() WHERE id='$this->id'";
		$sql->Update($query);

		$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'manifestations', 'p67_manips', '".$this->id."', '$uid', NOW(), 'DEL', 'Delete meeting')";
		$sql->Insert($query);
	}
}



function GetActiveManips($sql,$ress,$jour="")
{
	$query="SELECT id FROM p67_manips WHERE actif='oui' AND dte_manip='$jour'";
	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }

	if (($jour!="") && (count($res)>0))
	  { return 1; }
	else if ($jour!="")
	  { return 0; }
	else
	  { return $res; }
}

function GetManifestation($sql,$start,$end)
{
	$query="SELECT id FROM p67_manips WHERE actif='oui' AND dte_manip>='$start' AND dte_manip<='$end'";
	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
			$sql->GetRow($i);
			$res[$i]=$sql->data["id"];
	  }

	return $res;
}

?>
