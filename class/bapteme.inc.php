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
    ($Date: 2013-05-01 00:30:19 +0200 (mer., 01 mai 2013) $)
    ($Revision: 423 $)
*/

class bapteme_class{
	# Constructor
	function __construct($id=0,$sql){
		global $MyOpt;
		global $gl_uid;

		$this->sql=$sql;
		$this->tbl=$MyOpt["tbl"];

		$this->id="";
		$this->num="";
		$this->nom="";
		$this->telephone="";
		$this->mail="";
		$this->nb=0;
		$this->dte="";
		$this->actif="oui";
		$this->status="0";
		$this->type="btm";
		$this->paye="non";
		$this->id_pilote="0";
		$this->id_avion="0";
		$this->id_resa="0";
		$this->description="";

		$this->uid_creat="0";
		$this->dte_creat=date("Y-m-d H:i:s");
		$this->uid_maj="0";
		$this->dte_maj=date("Y-m-d H:i:s");

// A recopier à la fin
		$tabStatus=array();
		$tabStatus[0]="Nouveau";
		$tabStatus[1]="Affecté";
		$tabStatus[2]="Planifié";
		$tabStatus[3]="Effectué";
		$tabStatus[4]="Annulé";

		$this->tabStatus=$tabStatus;

		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Load user informations
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM ".$this->tbl."_bapteme WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->num=$res["num"];
		$this->nom=$res["nom"];
		$this->nb=$res["nb"];
		$this->telephone=$res["telephone"];
		$this->mail=$res["mail"];
		$this->dte=$res["dte"];
		$this->actif=$res["actif"];
		$this->status=$res["status"];
		$this->type=$res["type"];
		$this->paye=$res["paye"];
		$this->id_pilote=$res["id_pilote"];
		$this->id_avion=$res["id_avion"];
		$this->id_resa=$res["id_resa"];
		$this->description=$res["description"];

		$this->uid_creat=$res["uid_creat"];
		$this->dte_creat=$res["dte_creat"];
		$this->uid_maj=$res["uid_maj"];
		$this->dte_maj=$res["dte_maj"];
	}

	function Create(){
		global $uid;
		$sql=$this->sql;
		$query="INSERT INTO ".$this->tbl."_bapteme SET uid_maj='$uid', dte_maj='".now()."'";
		$this->id=$sql->Insert($query);

		$query="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'bapteme', '".$this->tbl."_bapteme', '".$this->id."', '$uid', '".now()."', 'ADD', 'Create bapteme')";
		$sql->Insert($query);
	}

	function Delete(){
		global $uid;
		$sql=$this->sql;
		$query="UPDATE ".$this->tbl."_bapteme SET actif='non', uid_maj='$uid', dte_maj='".now()."' WHERE id='$this->id'";
		$this->id=$sql->Update($query);

		$query="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'bapteme', '".$this->tbl."_bapteme', '".$this->id."', '$uid', '".now()."', 'DEL', 'Delete bapteme')";
		$sql->Insert($query);
	}


	# Show user informations
	function aff($key,$typeaff="html"){
		if ($key=="num")
		  { $ret=$this->num; }
		else if ($key=="nb")
		  { $ret=$this->nb; }
		else if ($key=="nom")
		  { $ret=ucwords($this->nom); }
		else if ($key=="dte")
		  { $ret=sql2date($this->dte); }
		else if ($key=="dte_j")
		  { $ret=sql2date($this->dte,"jour"); }
		else if ($key=="dte_h")
		  { $ret=sql2date($this->dte,"heure"); }
		else if ($key=="telephone")
		  { $ret=AffTelephone($this->telephone); }
		else if ($key=="status")
		  { $ret=$this->status; }
		else if ($key=="type")
		  { $ret=$this->type; }
		else if ($key=="paye")
		  { $ret=$this->paye; }
		else if ($key=="mail")
		  { $ret=$this->mail; }
		else if ($key=="description")
		  { $ret=$this->description; }
		else
		  { $ret=""; }

		if ($typeaff=="form")
		  {
			if ($key=="description")
		  	  { $ret="<TEXTAREA name=\"form_info[$key]\" cols=90 rows=10>$ret</TEXTAREA>"; }
			else if ($key=="status")
		  	  {
		  	  	$ret="<select name=\"form_info[$key]\">";
		  	  	$ret.="<option value='0' ".(($this->status==0) ? "selected" : "").">Nouveau</option>";
		  	  	$ret.="<option value='1' ".(($this->status==1) ? "selected" : "").">Affecté</option>";
		  	  	$ret.="<option value='2' ".(($this->status==2) ? "selected" : "").">Planifié</option>";
		  	  	$ret.="<option value='3' ".(($this->status==3) ? "selected" : "").">Effectué</option>";
		  	  	$ret.="<option value='4' ".(($this->status==3) ? "selected" : "").">Annulé</option>";
		  	  	$ret.="</select>";
		  	  }
			else if ($key=="nb")
		  	  {
		  	  	$ret="<select name=\"form_info[$key]\">";
		  	  	$ret.="<option value='1' ".(($this->nb=="2") ? "selected" : "").">1</option>";
		  	  	$ret.="<option value='2' ".(($this->nb=="2") ? "selected" : "").">2</option>";
		  	  	$ret.="<option value='3' ".(($this->nb=="3") ? "selected" : "").">3</option>";
		  	  	$ret.="</select>";
		  	  }
			else if ($key=="type")
		  	  {
		  	  	$ret="<select name=\"form_info[$key]\">";
		  	  	$ret.="<option value='btm' ".(($this->type=="btm") ? "selected" : "").">Baptème</option>";
		  	  	$ret.="<option value='vi' ".(($this->type=="vi") ? "selected" : "").">Vol d'instruction</option>";
		  	  	$ret.="</select>";
		  	  }
			else if ($key=="paye")
		  	  {
		  	  	$ret="<select name=\"form_info[$key]\">";
		  	  	$ret.="<option value='non' ".(($this->paye=="non") ? "selected" : "").">Non</option>";
		  	  	$ret.="<option value='oui' ".(($this->paye=="oui") ? "selected" : "").">Oui</option>";
		  	  	$ret.="</select>";
		  	  }
			else if ($key=="dte_j")
		  	  { $ret="<INPUT name=\"form_info[$key]\" id=\"form_$key\" value=\"".date2sql($this->dte)."\" type=\"date\" OnChange=\"reloadImg();\">"; }
			else if ($key=="dte_h")
		  	{
				  // $ret="<INPUT name=\"form_info[$key]\" id=\"form_$key\" value=\"$ret\" OnChange=\"reloadImg();\">";
				$r=$ret;
				$ret ="<select id=\"form_dte_h\" name=\"form_info[$key]\" OnChange=\"reloadImg();\">";
				for($i=6;$i<20;$i++)
				{ 
					$ret.="<option value='".$i.":00' ".(($i.":00"==date("G:i",strtotime($this->dte))) ? "selected" : "").">".$i.":00</option>";
					$ret.="<option value='".$i.":30' ".(($i.":30"==date("G:i",strtotime($this->dte))) ? "selected" : "").">".$i.":30</option>";
				}
				$ret.="</select>";
				
			}
			else
		  	  { $ret="<INPUT name=\"form_info[$key]\" id=\"form_$key\" value=\"$ret\">"; }
		  }
		else if ($typeaff=="val")
		  {

		  }
		else
		  {
			if ($key=="description")
			  { 
			  	$ret=nl2br(htmlentities($ret, ENT_HTML5, "ISO-8859-1"));
			  }
			else if ($key=="status")
			  {
			  	/*
			  	if ($ret=="0")
			  	  { $ret="Nouveau"; }
			  	else if ($ret=="1")
			  	  { $ret="Affecté"; }
			  	else if ($ret=="2")
			  	  { $ret="Planifié"; }
			  	else if ($ret=="3")
			  	  { $ret="Effectué"; }
			  	else if ($ret=="4")
			  	  { $ret="Annulé"; }
			  	  */
			  	  $ret=$this->tabStatus[$ret];
			  }
			else if ($key=="type")
			  {
			  	if ($ret=="btm")
			  	  { $ret="Baptème"; }
			  	else if ($ret=="vi")
			  	  { $ret="Vol d'instruction"; }
			  }
		  }
		return $ret;
	}

	function Valid($k,$v,$ret=false){
		$vv="**none**";

	  	if ($k=="nb")
	  	  { $vv=(is_numeric($v)?$v:0); }
	  	else if ($k=="tarif")
	  	  { $vv=(is_numeric($v)?$v:0); }
	  	else if ($k=="description")
	  	  { $vv=$v; }
		else if ($k=="telephone")
	  	  {
	  	  	$vv=str_replace(" ","",$v);
	  	  	$vv=str_replace(".","",$vv);
	  	  }
	  	else
	  	  { $vv=strtolower($v); }

		if ( ($k=="num") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->num=$vv; }
		else if ( ($k=="nom") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->nom=$vv; }
		else if ( ($k=="nb") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->nb=$vv; }
		else if ( ($k=="telephone") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->telephone=$vv; }
		else if ( ($k=="mail") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->mail=$vv; }
		else if ( ($k=="dte") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->dte=$vv; }
		else if ( ($k=="actif") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->actif=$vv; }
		else if ( ($k=="status") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->status=$vv; }
		else if ( ($k=="type") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->type=$vv; }
		else if ( ($k=="paye") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->paye=$vv; }
		else if ( ($k=="id_pilote") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->id_pilote=$vv; }
		else if ( ($k=="id_avion") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->id_avion=$vv; }
		else if ( ($k=="id_resa") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->id_resa=$vv; }
		else if ( ($k=="description") && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->description=$vv; }
		else if ($ret==true)
		  { return addslashes($vv); }
	}

	function Save(){
		global $uid;
		$sql=$this->sql;

		$query ="UPDATE ".$this->tbl."_bapteme SET ";

	  	$query.="num='".$this->Valid("num",$this->num,true)."',";
	  	$query.="nom='".$this->Valid("nom",$this->nom,true)."',";
	  	$query.="nb='".$this->Valid("nb",$this->nb,true)."',";
	  	$query.="telephone='".$this->Valid("telephone",$this->telephone,true)."',";
	  	$query.="mail='".$this->Valid("mail",$this->mail,true)."',";
	  	$query.="dte='".$this->Valid("dte",$this->dte,true)."',";
	  	$query.="actif='".$this->Valid("actif",$this->actif,true)."',";
	  	$query.="status='".$this->Valid("status",$this->status,true)."',";
	  	$query.="type='".$this->Valid("type",$this->type,true)."',";
	  	$query.="paye='".$this->Valid("paye",$this->paye,true)."',";
	  	$query.="id_pilote='".$this->Valid("id_pilote",$this->id_pilote,true)."',";
	  	$query.="id_avion='".$this->Valid("id_avion",$this->id_avion,true)."',";
	  	$query.="id_resa='".$this->Valid("id_resa",$this->id_resa,true)."',";
	  	$query.="description='".$this->Valid("description",$this->description,true)."',";

		$query.="uid_maj=$uid, dte_maj='".now()."' ";
		$query.="WHERE id='$this->id'";

		$sql->Update($query);

		$query="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) VALUES (NULL , 'bapteme', '".$this->tbl."_bapteme', '".$this->id."', '$uid', '".now()."', 'MOD', 'Modify bapteme')";
		$sql->Insert($query);
	}

}



function ListeBaptemes($sql,$actif=array("oui"),$status)
{ global $MyOpt;
	$txt="1=0";
	foreach($actif as $a)
	  {
	  	$txt.=" OR actif='$a'";
	  }

	if (!is_numeric($status))
	  { $status=-2; }
	if ($status==-2)
	  {
	  	$st="status<>4 AND status<>3";
	  }
	else if ($status==-1)
	  {
	  	$st="1";
	  }
	else
	  {
	  	$st="status=".$status;
	  }

	$query = "SELECT id FROM ".$MyOpt["tbl"]."_bapteme WHERE ($txt) AND ($st)";
	$sql->Query($query);
	$res=array();
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }
	return $res;
}

function ListeStatus()
{
	$tabStatus=array();
	$tabStatus[0]="Nouveau";
	$tabStatus[1]="Affecté";
	$tabStatus[2]="Planifié";
	$tabStatus[3]="Effectué";
	$tabStatus[4]="Annulé";

	return $tabStatus;
}

?>
