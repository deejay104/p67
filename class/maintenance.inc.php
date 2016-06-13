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

// Class Maintenance
class maint_class{
	# Constructor
	function __construct($id=0,$sql){
		$this->sql=$sql;
		$this->actif="oui";
		$this->dte_deb=date("Y-m-d");
		$this->dte_fin=date("Y-m-d");
		$this->status="planifie";
		$this->potentiel=0;
		$this->uid_lastresa=0;
		
		$this->nom_atelier="";
		$this->mail_atelier="";

		$this->uid_maj="0";
		$this->dte_maj=date("Y-m-d H:i:s");

		$this->data["dte_deb"]=$this->dte_deb;
		$this->data["dte_fin"]=$this->dte_fin;
		$this->data["potentiel"]=$this->potentiel;
		$this->data["uid_lastresa"]=$this->uid_lastresa;
		$this->data["status"]=$this->status;

		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Load user informations
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT maint.*, atelier.nom, atelier.mail FROM p67_maintenance AS maint LEFT JOIN p67_maintatelier AS atelier ON maint.uid_atelier=atelier.id WHERE maint.id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->uid_ressource=$res["uid_ressource"];
		$this->uid_atelier=$res["uid_atelier"];
		$this->actif=$res["actif"];
		$this->status=$res["status"];
		$this->dte_deb=$res["dte_deb"];
		$this->dte_fin=$res["dte_fin"];
		$this->potentiel=$res["potentiel"];
		$this->uid_lastresa=$res["uid_lastresa"];
		$this->uid_creat=$res["uid_creat"];
		$this->dte_creat=$res["dte_creat"];
		$this->uid_maj=$res["uid_maj"];
		$this->dte_maj=$res["dte_maj"];
		
		$this->nom_atelier=$res["nom"];
		$this->mail_atelier=$res["mail"];
	
		$this->data=$res;
	}


	function aff($key,$typeaff="html"){
		$txt=$this->data[$key];
		
		if ($key=="dte_deb")
		  { $ret=sql2date($this->dte_deb,"jour"); }
		else if ($key=="dte_fin")
		  { $ret=sql2date($this->dte_fin,"jour"); }
		else
		  { $ret=$txt; }

		if ($typeaff=="form")
		  {
			if ($key=="status")
		  	  {
				$ret ="<select name=\"form_status\">";
				$ret.="<option value=\"planifie\" ".(($txt=="planifie")?"selected":"").">Planifi�</OPTION>";
				$ret.="<option value=\"confirme\" ".(($txt=="confirme")?"selected":"").">Confirm�</OPTION>";
				$ret.="<option value=\"effectue\" ".(($txt=="effectue")?"selected":"").">Effectu�</OPTION>";
				$ret.="<option value=\"cloture\" ".(($txt=="cloture")?"selected":"").">Clotur�</OPTION>";
				$ret.="</select>";

		  	  }
			else if ($key=="dte_deb")
			  {
			  	$ret="<INPUT name=\"form_dte_deb\" id=\"form_dte_deb\" value=\"$ret\">";
			  }
			else if ($key=="dte_fin")
			  {
			  	$ret="<INPUT name=\"form_dte_fin\" id=\"form_dte_fin\" value=\"$ret\">";
			  }
			else if ($key=="potentiel")
			  {
			  	$ret="<INPUT name=\"form_potentiel\" value=\"$ret\">";
			  }
			else
			  {
			  	$ret="<INPUT name=\"form_info[$key]\" value=\"$ret\">";
			  }
		  }
		else
		  {
			if ($key=="status")
		  	  {
				$ret =(($txt=="planifie")?"Planifi�":"");
				$ret.=(($txt=="confirme")?"Confirm�":"");
				$ret.=(($txt=="effectue")?"Effectu�":"");
				$ret.=(($txt=="cloture")?"Clotur�":"");

				$ret =(($this->actif=="non")?"[Supprim�]":$ret);
		  	  }

		  }
		return $ret;
		
	  }
		
	function Valid($k,$v) 
	{
	}


	function Save()
	{ global $uid;
		$sql=$this->sql;

		$this->data["uid_ressource"]=$this->uid_ressource;
		$this->data["uid_atelier"]=$this->uid_atelier;
		$this->data["actif"]=$this->actif;
		$this->data["status"]=$this->status;
		$this->data["dte_deb"]=$this->dte_deb;
		$this->data["dte_fin"]=$this->dte_fin;
		$this->data["potentiel"]=$this->potentiel;
		$this->data["uid_lastresa"]=$this->uid_lastresa;
		$this->data["uid_creat"]=$this->uid_creat;
		$this->data["dte_creat"]=$this->dte_creat;
		$this->data["uid_maj"]=$this->uid_maj;
		$this->data["dte_maj"]=$this->dte_maj;
		$this->data["nom"]=$this->nom_atelier;
		$this->data["mail"]=$this->mail_atelier;

		if (($this->dte_deb=="") && ($this->dte_fin==""))
		  { return "La date est obligatoire"; }

		if ($this->dte_deb=="")
		  { $this->dte_fin=$this->dte_deb; }
		else if ($this->dte_fin=="")
		  { $this->dte_deb=$this->dte_fin; }

		// V�rifie la date/heure de d�but
		if ( (!preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/",$this->dte_deb)) && (!preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/",$this->dte_deb)) )
		  { return "La date de d�but n'a pas un format correct (jj/mm/aaaa).<br />"; }

		// V�rifie la date/heure de fin
		if ( (!preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4}$/",$this->dte_fin)) && (!preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/",$this->dte_fin)) )
		  { return "La date de fin n'a pas un format correct (jj/mm/aaaa).<br />"; }

		if (date_diff_txt($this->dte_deb,$this->dte_fin)<0) 
		  {
			$dte=$this->dte_fin;
			$this->dte_fin=$this->dte_deb;
			$this->dte_deb=$dte;
		  }

		if (!is_numeric($this->uid_ressource))
		  { return "Il faut s�lectionner un avion.<br />"; }

		if (!is_numeric($this->uid_lastresa))
		  { return "L'id de reservation dois �tre num�rique.<br />"; }

		if (!is_numeric($this->potentiel))
		  { return "Il faut que le potentiel soit num�rique.<br />"; }

		if ($this->id==0)
		  {
			$query="INSERT INTO p67_maintenance SET uid_creat=$uid, dte_creat=NOW()";
			$this->id=$sql->Insert($query);

			$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
			$query.="VALUES (NULL , 'maintenance', 'p67_maintenance', '".$this->id."', '$uid', NOW(), 'ADD', 'Create maintenance')";
			$sql->Insert($query);
		  }

		// Met � jour les infos
		$query ="UPDATE p67_maintenance SET ";
		$query.="uid_ressource='$this->uid_ressource',";
		$query.="uid_atelier='$this->uid_atelier',";
		$query.="status='$this->status',";
		$query.="dte_deb='$this->dte_deb',";
		$query.="dte_fin='$this->dte_fin',";
		$query.="potentiel='$this->potentiel',";
		$query.="uid_lastresa='$this->uid_lastresa',";
		$query.="uid_maj=$uid, dte_maj=NOW() ";
		$query.="WHERE id=$this->id";
		$sql->Update($query);

		$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'maintenance', 'p67_maintenance', '".$this->id."', '$uid', NOW(), 'MOD', 'Modifiy maintenance')";
		$sql->Insert($query);


		if (($this->uid_lastresa>0) && ($this->status!='planifie'))
		  {
//		  	$this->SetIntervention();
			$query="UPDATE p67_calendrier SET idmaint='0',potentiel='0' WHERE idmaint='".$this->id."'";
			$res=$sql->Update($query);

			$query="UPDATE p67_calendrier SET idmaint='".$this->id."',potentiel='".$this->potentiel."' WHERE id='".$this->uid_lastresa."'";
			$res=$sql->Update($query);
		  }
		return "";
	}

	function Delete()
	{ global $uid;
		$sql=$this->sql;
		$query="UPDATE p67_maintenance SET actif='non', uid_maj=$uid, dte_maj=NOW() WHERE id='$this->id'";
		$sql->Update($query);

		$query="UPDATE p67_calendrier SET idmaint=0, uid_maj=$uid, dte_maj=NOW() WHERE idmaint='$this->id'";
		$sql->Update($query);

		$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'maintenance', 'p67_maintenance', '".$this->id."', '$uid', NOW(), 'DEL', 'Delete maintenance')";
		$sql->Insert($query);
	}

	function SetIntervention()
	{
		$sql=$this->sql;

		$query="UPDATE p67_calendrier SET idmaint='0' WHERE idmaint='".$this->id."'";
		$res=$sql->Update($query);

		$query="SELECT dte_deb FROM p67_maintenance WHERE dte_deb>'".$this->dte_fin."' ORDER BY dte_deb LIMIT 0,1";
		$res=$sql->QueryRow($query);
		if (sql2date($res["dte_deb"])!=$res["dte_deb"])
		  { $lastresa="AND dte_fin<'".$res["dte_deb"]."'"; }
		else
		  { $lastresa=""; }

		$query="SELECT dte_fin FROM p67_calendrier WHERE id='".$this->uid_lastresa."'";
		$res=$sql->QueryRow($query);

		$query="UPDATE p67_calendrier SET idmaint='".$this->id."' WHERE uid_avion='".$this->uid_ressource."' AND dte_deb>='".$res["dte_fin"]."' ".$lastresa;
		$res=$sql->Update($query);

		echo "'$query'";
	}

}



function GetActiveMaintenace($sql,$ress,$jour="")
{
	$query="SELECT id,status FROM p67_maintenance WHERE (status<>'cloture' OR status<>'supprime') AND actif='oui' AND dte_deb<'$jour 23:59:59' AND dte_fin>='$jour' AND uid_ressource='$ress' ORDER BY dte_deb";
	$res=array();
	$sql->Query($query);
	$status=0;
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
		if ($sql->data["status"]=="planifie")
		  { $status=1; }
		else if ($sql->data["status"]=="confirme")
		  { $status=2; }
		else if ($sql->data["status"]=="effectue")
		  { $status=2; }
		else if ($sql->data["status"]=="cloture")
		  { $status=2; }
		
	  }

	if (($jour!="") && (count($res)>0))
	  { return $status; }
	else if ($jour!="")
	  { return 0; }
	else
	  { return $res; }
}

function GetAllMaintenance($sql,$ress)
{
	$query="SELECT id FROM p67_maintenance WHERE actif='oui' ".(($ress>0) ? "AND uid_ressource='$ress'" : "" )." ORDER BY dte_deb DESC";
	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }

	return $res;
}



// Class Fiche
class fichemaint_class{
	# Constructor
	function __construct($id=0,$sql){
		$this->sql=$sql;
		$this->id=0;
		$this->uid_avion=0;
		$this->uid_valid=0;
		$this->dte_valid="";
		$this->traite="";
		$this->uid_planif=0;
		$this->uid_creat=0;
		$this->dte_creat="";
		$this->uid_maj=0;
		$this->dte_maj="";
		$this->description="";
		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Load user informations
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM p67_maintfiche WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->uid_avion=$res["uid_avion"];
		$this->uid_valid=$res["uid_valid"];
		$this->dte_valid=$res["dte_valid"];
		$this->traite=$res["traite"];
		$this->uid_planif=$res["uid_planif"];
		$this->uid_creat=$res["uid_creat"];
		$this->dte_creat=$res["dte_creat"];
		$this->uid_maj=$res["uid_maj"];
		$this->dte_maj=$res["dte_maj"];
		$this->description=$res["description"];
	}

	function Valid($k,$v) 
	{
	}


	function Save()
	{ global $uid;
		$sql=$this->sql;

		if (!is_numeric($this->uid_avion))
		  { return "Il faut s�lectionner un avion.<br />"; }

		if ($this->id==0)
		  {
			$query="INSERT INTO p67_maintfiche SET uid_creat=$uid, dte_creat=NOW()";
			$this->id=$sql->Insert($query);

			$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
			$query.="VALUES (NULL , 'maintenance', 'p67_maintfiche', '".$this->id."', '$uid', NOW(), 'ADD', 'Create maintenance sheet')";
			$sql->Insert($query);
		  }

		// Met � jour les infos
		$query ="UPDATE p67_maintfiche SET ";
		$query.="uid_avion='$this->uid_avion',";
		$query.="description='".addslashes($this->description)."',";
		$query.="uid_valid='$this->uid_valid',";
		$query.="dte_valid='$this->dte_valid',";
		$query.="traite='$this->traite',";
		$query.="uid_planif='$this->uid_planif',";
		$query.="uid_maj=$uid, dte_maj=NOW() ";
		$query.="WHERE id=$this->id";
		$sql->Update($query);

		$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'maintenance', 'p67_maintfiche', '".$this->id."', '$uid', NOW(), 'MOD', 'Modify maintenance sheet')";
		$sql->Insert($query);

		return "";
	}

	function Delete()
	{ global $uid;
		$sql=$this->sql;
		$query="UPDATE p67_maintfiche SET actif='non', uid_maj=$uid, dte_maj=NOW() WHERE id='$this->id'";
		$sql->Update($query);

		$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'maintenance', 'p67_maintfiche', '".$this->id."', '$uid', NOW(), 'DEL', 'Delete maintenance sheet')";
		$sql->Insert($query);
	}

	function Affecte($id)
	{ global $uid;
		$sql=$this->sql;

		$this->uid_planif=$id;
		$this->Save();

		return "";
	}

}


function GetActiveFiche($sql,$ress=0,$maint=0)
{
	$query="SELECT id FROM p67_maintfiche WHERE uid_valid>0 AND (traite='non' ".(($maint>0) ? " OR uid_planif='$maint'" : "").") ".(($ress>0) ? " AND uid_avion='$ress'" : "")." ORDER BY dte_creat DESC";
	$lstfiche=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$lstfiche[$i]=$sql->data["id"];
	  }

	return $lstfiche;
}

function GetValideFiche($sql,$ress)
{
	$query="SELECT id FROM p67_maintfiche WHERE uid_valid=0 ORDER BY dte_creat DESC";

	$lstfiche=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$lstfiche[$i]=$sql->data["id"];
	  }

	return $lstfiche;
}


class atelier_class{
	# Constructor
	function __construct($id=0,$sql){
		$this->sql=$sql;

		$this->nom="";
		$this->mail="";
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
		$query = "SELECT * FROM p67_maintatelier WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->nom=$res["nom"];
		$this->mail=$res["mail"];
		$this->actif=$res["actif"];
	}

}

function GetActiveAteliers($sql)
{
	$query="SELECT id FROM p67_maintatelier WHERE actif='oui' ORDER BY nom";
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
