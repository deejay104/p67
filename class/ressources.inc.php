<?
/*
    SoceIt v2.0
    Copyright (C) 2009 Matthieu Isorez

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

class ress_class{
	# Constructor
	function __construct($id=0,$sql){
		$this->sql=$sql;

		$this->id="";
		$this->nom="";
		$this->immatriculation="";
		$this->actif="oui";
		$this->marque="";
		$this->modele="";
		$this->couleur="A9D7FE";
		$this->description="";

		$this->places="0";
		$this->puissance="0";
		$this->massemax="0";
		$this->vitesse="0";
		$this->tolerance="0";
		$this->centrage="0";

		$this->tarif="0";
		$this->tarif_reduit="0";
		$this->tarif_double="0";
		$this->tarif_nue="0";

		$this->typehora="";
		$this->uid_maj="0";
		$this->dte_maj=date("Y-m-d H:i:s");
		
		$this->data["nom"]="";
		$this->data["immatriculation"]="";
		$this->data["actif"]="oui";
		$this->data["marque"]="";
		$this->data["modele"]="";
		$this->data["couleur"]="A9D7FE";
		$this->data["description"]="";

		$this->data["places"]="0";
		$this->data["puissance"]="0";
		$this->data["massemax"]="0";
		$this->data["vitesse"]="0";
		$this->data["tolerance"]="";
		$this->data["centrage"]="";

		$this->data["tarif"]="0";
		$this->data["tarif_reduit"]="0";
		$this->data["tarif_double"]="0";
		$this->data["tarif_nue"]="0";

		$this->data["typehora"]="";

		$this->data["uid_maj"]="0";
		$this->data["dte_maj"]=date("Y-m-d H:i:s");

		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Load user informations
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM p67_ressources WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->nom=$res["nom"];
		$this->immatriculation=strtoupper($res["immatriculation"]);
		$this->actif=$res["actif"];
		$this->marque=$res["marque"];
		$this->modele=$res["modele"];
		$this->couleur=$res["couleur"];
		$this->description=$res["description"];

		$this->places=$res["places"];
		$this->puissance=$res["puissance"];
		$this->massemax=$res["massemax"];
		$this->vitesse=$res["vitesse"];
		$this->tolerance=$res["tolerance"];
		$this->centrage=$res["centrage"];

		$this->tarif=$res["tarif"];
		$this->tarif_reduit=$res["tarif_reduit"];
		$this->tarif_double=$res["tarif_double"];
		$this->tarif_nue=$res["tarif_nue"];

		$this->typehora=$res["typehora"];

		$this->uid_maj=$res["uid_maj"];
		$this->dte_maj=$res["dte_maj"];
		
		$this->data=$res;
	}

	function Create(){
		global $uid;
		$sql=$this->sql;
		$query="INSERT INTO p67_ressources SET uid_maj='$uid', dte_maj='".now()."'";
		$this->id=$sql->Insert($query);

		$query="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'ressources', 'p67_ressources', '".$this->id."', '$uid', '".now()."', 'ADD', 'Create ressource')";
		$sql->Insert($query);
	}

	function Delete(){
		global $uid;
		$sql=$this->sql;
		$query="UPDATE p67_ressources SET actif='non', uid_maj='$uid', dte_maj='".now()."' WHERE id='$this->id'";
		$this->id=$sql->Update($query);

		$query="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'ressources', 'p67_ressources', '".$this->id."', '$uid', '".now()."', 'DEL', 'Delete ressource')";
		$sql->Insert($query);
	}

	function Desactive(){
		global $uid;
		$sql=$this->sql;
		$query="UPDATE p67_ressources SET actif='off', uid_maj='$uid', dte_maj='".now()."' WHERE id='$this->id'";
		$this->id=$sql->Update($query);

		$query="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'ressources', 'p67_ressources', '".$this->id."', '$uid', '".now()."', 'DEL', 'Disable ressource')";
		$sql->Insert($query);
	}

	# Show user informations
	function aff($key,$typeaff="html"){
		$txt=$this->data[$key];
		if ($key=="immatriculation")
		  { $ret=strtoupper($txt); }
		else if ($key=="modele")
		  { $ret=strtoupper($txt); }
		else if ($key=="marque")
		  { $ret=strtoupper($txt); }
		else if ($key=="couleur")
		  { $ret=strtoupper($txt); }
		else if ($key=="typehora")
		  {
	  	  	if ($txt=="dix")
	  	  	  { $ret="Dixième"; }
	  	  	else if ($txt=="cen")
	  	  	  { $ret="Centième"; }
	  	  	else if ($txt=="min")
	  	  	  { $ret="Minute"; }
		  }
		else
		  { $ret=$txt; }

		if ($typeaff=="form")
		  {
			if ($key=="description")
		  	  { $ret="<TEXTAREA name=\"form_ress[$key]\" cols=60 rows=5>$ret</TEXTAREA>"; }
			else if ($key=="centrage")
		  	  { $ret="<TEXTAREA name=\"form_ress[$key]\" cols=60 rows=5>$ret</TEXTAREA>"; }
			else if ($key=="typehora")
			  {
		  	  	$ret ="<SELECT name=\"form_ress[$key]\">";
		  	  	$ret.="<OPTION value=\"dix\" ".(($txt=="dix")?"selected":"").">Dixième</OPTION>";
		  	  	$ret.="<OPTION value=\"cen\" ".(($txt=="cen")?"selected":"").">Centième</OPTION>";
		  	  	$ret.="<OPTION value=\"min\" ".(($txt=="min")?"selected":"").">Minute</OPTION>";
		  	  	$ret.="</SELECT>";
			  }
			else
		  	  { $ret="<INPUT name=\"form_ress[$key]\" value=\"$ret\">"; }
		  }
		else if ($typeaff=="val")
		  {

		  }
		else
		  {
			if ($key=="description")
			  { $ret=nl2br(htmlentities($ret)); }
			else if ($key=="centrage")
			  { $ret=nl2br(htmlentities($ret)); }
			else
		  	  { $ret="<a href=\"ressources.php?rub=detail&id=".$this->id."\">".$ret."</a>"; }
		  }

		return $ret;
	}

	function Valid($k,$v,$ret=false){
		$vv="**none**";

	  	if ($k=="tarif")
	  	  { $vv=(is_numeric($v)?$v:0); }
	  	else if ($k=="tarif_double")
	  	  { $vv=(is_numeric($v)?$v:0); }
	  	else if ($k=="tarif_reduit")
	  	  { $vv=(is_numeric($v)?$v:0); }
	  	else if ($k=="tarif_nue")
	  	  { $vv=(is_numeric($v)?$v:0); }
	  	else if ($k=="description")
	  	  { $vv=$v; }
	  	else if ($k=="centrage")
	  	  { $vv=$v; }
	  	else if ($k=="tolerance")
	  	  { $vv=$v; }
	  	else if ($k=="couleur")
	  	  { $vv=strtoupper($v); }
	  	else
	  	  { $vv=strtolower($v); }

		if ( (!is_numeric($k)) && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->data[$k]=$vv; }
		else if ($ret==true)
		  { return addslashes($vv); }
	}

	function Save(){
		global $uid;
		$sql=$this->sql;

		$query ="UPDATE p67_ressources SET ";
		foreach($this->data as $k=>$v)
		  { 
			if (!is_numeric($k))
			  {
				$vv=$this->Valid($k,$v,true);
			  	$query.="$k='$vv',";
			  }
		  }
		$query.="uid_maj=$uid, dte_maj='".now()."' ";
		$query.="WHERE id='$this->id'";
		$sql->Update($query);

		$query="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'ressources', 'p67_ressources', '".$this->id."', '$uid', '".now()."', 'MOD', 'Modify ressource')";
		$sql->Insert($query);
	}

	function CalcHorametre($deb,$fin){
		if ($this->typehora=="min")
		  {
		  	preg_match("/^([0-9]*)(\.([0-9]{1,2}))?$/",$deb,$tdeb);
		  	preg_match("/^([0-9]*)(\.([0-9]{1,2}))?$/",$fin,$tfin);

			$t=round(($tfin[1]-$tdeb[1])*60+($tfin[3]-$tdeb[3]));
		  }
		else if ($this->typehora=="dix")
		  {
		  	preg_match("/^([0-9]*)(\.([0-9]{1,2}))?$/",$deb,$tdeb);
		  	preg_match("/^([0-9]*)(\.([0-9]{1,2}))?$/",$fin,$tfin);

			$t=round(($tfin[1]-$tdeb[1])*60+($tfin[3]-$tdeb[3])*6);
		  }
		else if ($this->typehora=="cen")
		  {
		  	preg_match("/^([0-9]*)(\.([0-9]{1,2}))?$/",$deb,$tdeb);
		  	preg_match("/^([0-9]*)(\.([0-9]{1,2}))?$/",$fin,$tfin);

			$t=round((($tfin[1]-$tdeb[1])*100+($tfin[3]-$tdeb[3]))*60/100);
		  }
		else
		  { $t=($fin-$deb)*60; }

		return $t;
	}

	function CalcTempsVol($type="all"){
			
	  }

	function CheckDispo($deb,$fin)
	  { global $MyOpt;
		$sql=$this->sql;
  		$query="SELECT id FROM ".$MyOpt["tbl"]."_calendrier AS cal WHERE uid_avion='$this->id' AND dte_deb<'".date("Y-m-d H:i:s",$fin)."' AND dte_fin>'".date("Y-m-d H:i:s",$deb)."'";
		$sql->Query($query);

		if ($sql->rows>0)
		  {
		  	return false;
		  }
		else
		  {
		  	return true;
		  }
}
}



function ListeRessources($sql,$actif=array("oui"))
{
	$txt="1=0";
	foreach($actif as $a)
	  {
	  	$txt.=" OR actif='$a'";
	  }
	$query = "SELECT id FROM p67_ressources WHERE ($txt ".((GetDroit("SupprimeRessource")) ? "OR actif='off'" : "" ).") ";
	$sql->Query($query);
	$res=array();
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }
	return $res;
}

function AffListeRessources($sql,$form_uid,$name,$actif=array("oui"))
 { global $MyOpt;

	$txt="1=0";
	foreach($actif as $a)
	  {
	  	$txt.=" OR actif='$a'";
	  }
	$query = "SELECT id,immatriculation FROM ".$MyOpt["tbl"]."_ressources WHERE ($txt ".((GetDroit("SupprimeRessource")) ? "OR actif='off'" : "" ).") ORDER BY immatriculation";
	$sql->Query($query);

	$lstress ="<select id=\"$name\" name=\"$name\">";
	$lstress.="<option value=\"0\">Aucun</option>";

	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$lstress.="<option value=\"".$sql->data["id"]."\" ".(($form_uid==$sql->data["id"]) ? "selected" : "").">".strtoupper($sql->data["immatriculation"])."</option>";
	  }
	$lstress.="</select>";

	return $lstress;
  }
?>
