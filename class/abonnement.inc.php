<?
/*
    SoceIt v2.2 ($Revision: 460 $)
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

    ($Author: miniroot $)
    ($Date: 2016-04-22 22:08:32 +0200 (ven., 22 avr. 2016) $)
    ($Revision: 460 $)
*/

// Class Reservation

class abonnement_class{
	# Constructor
	function __construct($id="",$sql){
		global $MyOpt;

		$this->sql=$sql;
		$this->tbl=$MyOpt["tbl"];

		$this->id="";
		$this->abonum="10000A";
		$this->uid=0;
		$this->dte_deb="";
		$this->dte_fin="";
		$this->jour_num="0";
		$this->jour_sem="-";
		$this->actif="oui";
		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Load user informations
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM ".$this->tbl."_abonnement WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->abonum=$res["abonum"];
		$this->actif=$res["actif"];
		$this->uid=$res["uid"];
		$this->dte_deb=sql2date($res["dtedeb"]);
		$this->dte_fin=sql2date($res["dtefin"]);
		$this->jour_num=$res["jour_num"];
		$this->jour_sem=$res["jour_sem"];
		$this->uid_maj=$res["uid_maj"];
		$this->dte_maj=$res["dte_maj"];
	}

	function Valid($k,$v) 
	{
	}


	function Save()
	{ global $uid;
		$sql=$this->sql;
		if (($this->dte_deb=="") || ($this->dte_fin==""))
		  { return "La date est obligatoire"; }

		// Vérifie la date/heure de début
		// if (!preg_match("/^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$/",$this->dte_deb))
		  // { return "La date de début n'a pas un format correct (jj/mm/aaaa).<br />"; }

		// Vérifie la date/heure de fin
		// if (!preg_match("/^[0-9]{1,2}/[0-9]{1,2}/[0-9]{2,4}$/",$this->dte_fin))
		  // { return "La date de fin n'a pas un format correct (jj/mm/aaaa).<br />"; }

/*
		if (date_diff_txt($this->dte_deb,$this->dte_fin)<0) 
		  {
			$dte=$this->dte_fin;
			$this->dte_fin=$this->dte_deb;
			$this->dte_deb=$dte;
		  }
*/

		if ($this->id=="")
		  {
			$this->abonum=$this->NewRevision();
		  }
		else
		  {
			$query ="UPDATE ".$this->tbl."_abonnement SET ";
			$query.="actif='non', ";
			$query.="uid_maj=$uid, dte_maj='".now()."' ";
			$query.="WHERE abonum LIKE '".substr($this->abonum,0,6)."%'";
			$sql->Update($query);
			$this->abonum=$this->NewRevision();
		  }

		$query="INSERT INTO ".$this->tbl."_abonnement SET abonum='".$this->abonum."', uid='".$this->uid."', dtedeb='".date2sql($this->dte_deb)."', dtefin='".date2sql($this->dte_fin)."', jour_num='".$this->jour_num."', jour_sem='".$this->jour_sem."', actif='oui', uid_maj=$uid, dte_maj='".now()."'";
		$this->id=$sql->Insert($query);

		
		foreach($this->lignes as $i=>$ligne)
		  { 
			if ($ligne["mouvid"]>0)
			  {
				$query="INSERT INTO ".$this->tbl."_abo_ligne SET abonum='".$this->abonum."', uid='".$this->uid."', mouvid='".$ligne["mouvid"]."', montant='".$ligne["montant"]."'";
				$sql->Insert($query);			
			  }
		  }

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'abonnement', '".$this->tbl."_abonnement', '".$this->id."', '$uid', '".now()."', 'ADD', 'Create subscription')";
		$sql->Insert($query);

		return $this->id;
	}

	function Copy()
	  {
		$this->id="";
		$this->Save();
		return $this->id;
	  }

	function Desactive()
	  {  global $gl_uid;
		$sql=$this->sql;
		$query ="UPDATE ".$this->tbl."_abonnement SET ";
		$query.="actif='non', ";
		$query.="uid_maj=$gl_uid, dte_maj='".now()."' ";
		$query.="WHERE abonum = '".$this->abonum."'";
		$sql->Update($query);
	  }

	function NewRevision()
	  {
		$sql=$this->sql;

		if ($this->id=="")
		  {
  			$query = "SELECT abonum FROM ".$this->tbl."_abonnement ORDER BY abonum DESC LIMIT 0,1";
			$res = $sql->QueryRow($query);
			if ($res["abonum"]=="")
			  { $num="100001A"; }
			else
			  { $num=(substr($res["abonum"],0,6)+1)."A"; }
		  }
		else
		  {
			$query = "SELECT abonum FROM ".$this->tbl."_abonnement WHERE abonum LIKE '".substr($this->abonum,0,6)."%' ORDER BY abonum DESC LIMIT 0,1";
			$res = $sql->QueryRow($query);
	
			$rev=chr(ord(substr($res["abonum"],6,1))+1);
			if ($rev!="Z")
			  {
				$num=substr($res["abonum"],0,6).$rev;
			  }
			else
			  {
	  			$query = "SELECT abonum FROM ".$this->tbl."_abonnement ORDER BY abonum DESC LIMIT 0,1";
				$res = $sql->QueryRow($query);
				$num=(substr($res["abonum"],0,6)+1)."A";
			  }
		  }
		return $num;
	  }

	function Delete()
	{ global $uid;
		$sql=$this->sql;
		$query="UPDATE ".$this->tbl."_abonnement SET actif='non', uid_maj=$uid, dte_maj='".now()."' WHERE abonum='$this->abonum'";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'reservation', '".$this->tbl."_calendrier', '".$this->id."', '$uid', '".now()."', 'DEL', 'Delete subscription')";
		$sql->Insert($query);
	}

	function LoadLignes()
	{
		$sql=$this->sql;
		$query="SELECT ".$this->tbl."_abo_ligne.id AS id, ".$this->tbl."_abo_ligne.mouvid AS mouvid,".$this->tbl."_mouvement.description AS description,".$this->tbl."_abo_ligne.montant AS montant FROM ".$this->tbl."_abo_ligne LEFT JOIN ".$this->tbl."_mouvement ON ".$this->tbl."_abo_ligne.mouvid=".$this->tbl."_mouvement.id WHERE abonum='$this->abonum' ORDER BY ".$this->tbl."_mouvement.ordre,".$this->tbl."_mouvement.description";
		$sql->Query($query);

		$res=array();
		$tot=0;
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$res[$sql->data["id"]]["mouvid"]=$sql->data["mouvid"];
			$res[$sql->data["id"]]["description"]=$sql->data["description"];
			$res[$sql->data["id"]]["montant"]=$sql->data["montant"];
			$tot=$tot+$sql->data["montant"];
		  }
		$this->lignes=$res;
		$this->total=sprintf("%01.2f",$tot);
	}

	function Actif()
	{
		$deb=strtotime(date2sql($this->dte_deb)." 00:00:00");
		$fin=strtotime(date2sql($this->dte_fin)." 23:59:59");

		if (($deb<=time()) && ($fin>=time()))
		{
		  	return true;
		}
		else
		{
			return false;
		}
	}
}



function ListAbonnement($sql,$uid)
{
	global $MyOpt;
	
	$query="SELECT id, abonum FROM ".$MyOpt["tbl"]."_abonnement WHERE actif='oui' ".(($uid>0) ? "AND uid='$uid'" : "" )." ORDER BY abonum";
	$lstabo=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$lstabo[$i]=$sql->data["id"];
	  }

	return $lstabo;
  }	

function TodayAbonnement($sql,$dte,$uid=0)
  {
	$query ="SELECT abo.id, abo.uid, abo.abonum,ligne.mouvid,ligne.montant, mvt.j0, mvt.j1, mvt.j2, mvt.j3, mvt.j4, mvt.j5, mvt.j6, mvt.j7 FROM ".$MyOpt["tbl"]."_abonnement AS abo ";
	$query.="LEFT JOIN ".$MyOpt["tbl"]."_abo_ligne AS ligne ON abo.abonum=ligne.abonum ";
	$query.="LEFT JOIN ".$MyOpt["tbl"]."_mouvement AS mvt ON ligne.mouvid=mvt.id ";
	$query.="WHERE abo.actif='oui' ".(($uid>0) ? "AND abo.uid='$uid'" : "" )." AND abo.dtedeb<='$dte' AND abo.dtefin>='$dte'";

	$lstabo=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$lstabo[$i]=$sql->data;
	  }

	return $lstabo;
    }

function CountAbonnement($sql,$dte)
  { global $MyOpt;
	$lstabo=array();
  	$todayNum=date("w",strtotime($dte));
	$query="SELECT id FROM ".$MyOpt["tbl"]."_vacances WHERE dtedeb<='$dte' AND dtefin>='$dte'";
	$res=$sql->QueryRow($query);

	if (($res["id"]>0) && ($MyOpt["tabPresenceJour"][$todayNum]!=""))
	  {
		$todayNum=7;
	  }

  	$lstabo["type"]=$todayNum;

	$query ="SELECT abo.id, abo.uid, abo.abonum,ligne.mouvid,ligne.montant, mvt.j0, mvt.j1, mvt.j2, mvt.j3, mvt.j4, mvt.j5, mvt.j6, mvt.j7 FROM ".$this->tbl."_abonnement AS abo ";
	$query.="LEFT JOIN ".$MyOpt["tbl"]."_abo_ligne AS ligne ON abo.abonum=ligne.abonum ";
	$query.="LEFT JOIN ".$MyOpt["tbl"]."_mouvement AS mvt ON ligne.mouvid=mvt.id ";
	$query.="LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS usr ON abo.uid=usr.id ";
	$query.="WHERE abo.actif='oui' AND usr.actif='oui' AND abo.dtedeb<='$dte' AND abo.dtefin>='$dte'";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		if ($sql->data["j".$todayNum]!="")
		  {
//			if ($sql->data["j".$todayNum]!="J")
			
			if (!is_array($MyOpt["tabPresencePlage"][$MyOpt["tabPresenceJour"][$todayNum].$sql->data["j".$todayNum]]))
			  {
			  	$lstabo[$sql->data["j".$todayNum]][$sql->data["uid"]]=1;
			  }
			else
			  {
			  	foreach($MyOpt["tabPresencePlage"][$MyOpt["tabPresenceJour"][$todayNum].$sql->data["j".$todayNum]] as $k=>$v)
			  	  {
			  		$lstabo[$v][$sql->data["uid"]]=1;
				  }
			  }

		  }
	  }

	foreach ($lstabo as $type=>$t)
	  {
	  	if ($type!="type")
	  	  {
			foreach ($t as $i=>$v)
			  {
				$lstabo[$type]["sum"]=$lstabo[$type]["sum"]+1;
			  }
		  }
	  }

	return $lstabo;
    }

?>
