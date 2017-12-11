<?
/*
    SoceIt v2.10
    Copyright (C) 2016 Matthieu Isorez

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

//if (file_exists("config/roles.inc.php"))
//  { require ("config/roles.inc.php"); }

// Valeur d'affichage des types
$tabTypeNom["pilote"]="Pilote";
$tabTypeNom["eleve"]="Eleve";
$tabTypeNom["instructeur"]="Instructeur";
$tabTypeNom["membre"]="Membre";
$tabTypeNom["invite"]="Invité";
$tabTypeNom["employe"]="Employé";

$tabTypeNom["parent"]="Parent";
$tabTypeNom["enfant"]="Enfant";


// Class Utilisateur
class user_class{
	# Constructor
	function __construct($uid=0,$sql,$me=false,$setdata=true){
		global $MyOpt;
		global $gl_uid;

		$this->tbl=$MyOpt["tbl"];

		$this->uid=$uid;
		$this->idcpt=$uid;
		$this->sql=$sql;
		$this->me=$me;

		$this->data["nom"]="";
		$this->data["prenom"]="";
		$this->data["fullname"]="";
		$this->data["initiales"]="";
		$this->data["password"]="";
		$this->data["idcpt"]="0";
		$this->data["pere"]="0";
		$this->data["mere"]="0";
		$this->data["mail"]="";
		$this->data["tel_fixe"]="";
		$this->data["tel_portable"]="";
		$this->data["tel_bureau"]="";
		$this->data["adresse1"]="";
		$this->data["adresse2"]="";
		$this->data["ville"]="";
		$this->data["codepostal"]="";
		$this->data["commentaire"]="";
		$this->data["avatar"]="";
		$this->data["droits"]="";
		$this->data["actif"]="oui";
		$this->data["virtuel"]="non";
		$this->data["type"]="pilote";
		$this->data["decouvert"]="0";
		$this->data["zone"]="";
		$this->data["dte_inscription"]=date("Y-m-d");
		$this->data["dte_login"]="";
		$this->data["poids"]="75";
		$this->data["notification"]="oui";
		$this->data["aff_rapide"]="n";
		$this->data["aff_mois"]="";
		$this->data["aff_jour"]="";
		$this->data["aff_msg"]="0";
		$this->data["sexe"]="NA";

		$this->data["uid_maj"]="0";
		$this->data["dte_maj"]=date("Y-m-d H:i:s");

		// Obsolète
		$this->data["dte_naissance"]="";
		$this->data["dte_licence"]="";
		$this->data["dte_medicale"]="";

		// Déplacer dans variable utilisateur
		$this->data["regime"]="";
		$this->data["tarif"]="0";
		$this->data["profession"]="";
		$this->data["employeur"]="";
		$this->data["num_caf"]="";
		$this->data["nom_medecin"]="";
		$this->data["tel_medecin"]="";
		$this->data["adr_medecin"]="";
		$this->data["type_repas"]="standard";
		$this->data["maladies"]="";
		$this->data["handicap"]="non";
		$this->data["allergie_asthme"]="N";
		$this->data["allergie_medicament"]="N";
		$this->data["allergie_alimentaire"]="N";
		$this->data["allergie_commentaire"]="";
		$this->data["remarque_sante"]="";
		$this->data["aut_prelevement"]="N";


		if ($uid>0)
		  {
				$this->load($uid,$setdata,$me);
		  }
	}

	# Load user informations
	function load($uid,$setdata=true,$me)
	  { global $Droits;

			$this->uid=$uid;
			$sql=$this->sql;
			if ($setdata)
			  { $query = "SELECT * FROM ".$this->tbl."_utilisateurs WHERE id='$uid'"; }
			else
			  { $query = "SELECT id,prenom,nom,actif,virtuel,type,mail,uid_maj,dte_maj,droits FROM ".$this->tbl."_utilisateurs WHERE id='$uid'"; }
			$res = $sql->QueryRow($query);
			if (!is_array($res))
			  {
			  	return "";
			  }
	
			// Charge les variables
			$this->prenom=($res["prenom"]!="")?ucwords($res["prenom"]):"";
			$this->nom=($res["nom"]!="")?strtoupper($res["nom"]):"";
			$this->actif=$res["actif"];
			$this->virtuel=$res["virtuel"];
			$this->type=$res["type"];
			$this->mail=$res["mail"];
			$this->dte_naissance=$res["dte_naissance"];
			$this->zone=$res["zone"];
			$this->uidmaj=$res["uid_maj"];
			$this->dtemaj=$res["dte_maj"];
			$this->idcpt=$res["idcpt"];
	
			if ($setdata)
			{ 
				$this->data=$res;

				// Charge les droits
				$query = "SELECT groupe FROM ".$this->tbl."_droits WHERE uid='$uid'";
				$sql->Query($query);
				$this->data["droits"]="";
				$s="";
				for($i=0; $i<$sql->rows; $i++)
				{ 
					$sql->GetRow($i);
				 	$this->groupe[$sql->data["groupe"]]=true;
					$this->data["droits"].=$s.$sql->data["groupe"];
					$s=",";
				}
			}

			$this->data["fullname"]=AffFullName($this->prenom,$this->nom);
			$this->fullname=$this->data["fullname"];

			if ($me)
			{
				$this->loadRoles();
			}
		}

	function loadRoles(){
			// Charge les roles
			$this->role=array();
			$this->role[""]=true;
			$sql=$this->sql;
			$query = "SELECT roles.role FROM ".$this->tbl."_roles AS roles LEFT JOIN ".$this->tbl."_droits AS droits ON droits.groupe=roles.groupe  WHERE (uid='".$this->uid."' OR roles.groupe='ALL') AND roles.role IS NOT NULL GROUP BY roles.role";
			$sql->Query($query);

			for($i=0; $i<$sql->rows; $i++)
			{ 
				$sql->GetRow($i);
				$this->role[$sql->data["role"]]=true;
			}


/*	
			if (isset($Droits[$sql->data["groupe"]]))
			  {
					foreach($Droits[$sql->data["groupe"]] as $droit=>$val)
					  { $this->role[$droit]=true; }
			  }
			if (is_array($Droits["ALL"]))
			  {
					foreach($Droits["ALL"] as $droit=>$val)
					  { $this->role[$droit]=true; }
			  }
*/
//print_r($this->role);
	}

	# Load user informations
	function loadLache(){
		$query = "SELECT avion.id AS aid, ".$this->tbl."_lache.id AS lid, ".$this->tbl."_lache.uid_creat AS uid FROM ".$this->tbl."_utilisateurs AS usr ";
		$query.= "LEFT JOIN ".$this->tbl."_ressources AS avion ON 1=1 ";
		$query.= "LEFT JOIN ".$this->tbl."_lache ON avion.id=".$this->tbl."_lache.id_avion AND usr.id=".$this->tbl."_lache.uid_pilote ";
		$query.= "WHERE usr.id='$this->uid' AND avion.actif='oui'";

		$sql=$this->sql;
		$sql->Query($query);

		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$this->data["lache"][$i]["idlache"]=$sql->data["lid"];
			$this->data["lache"][$i]["idavion"]=$sql->data["aid"];
			$this->data["lache"][$i]["idusr"]=$sql->data["uid"];
		  }

		if (is_array($this->data["lache"]))
		  {
			foreach($this->data["lache"] as $i=>$val)
			  {
				$this->data["lache"][$i]["avion"]=new ress_class($val["idavion"],$sql);
				$this->data["lache"][$i]["usr"]=new user_class($val["idusr"],$sql,false,false);
			  }
		  }
	}

	# Charge la liste d'enfants
	function LoadEnfants()
	{
		$sql=$this->sql;
		// if ($this->data["type"]=="enfant")
		  // {
		  	if ($this->data["pere"]>0)
			  { $this->data["pere"]=new user_class($this->data["pere"],$sql,false,false); }
			else
			  { $this->data["pere"]=array(); }
		  	if ($this->data["mere"]>0)
			  { $this->data["mere"]=new user_class($this->data["mere"],$sql,false,false); }
			else
			  { $this->data["mere"]=array(); }
		  // }
		// else
		  // {


		$query = "SELECT id FROM ".$this->tbl."_utilisateurs WHERE (pere='".$this->uid."' OR mere='".$this->uid."') AND actif='oui'";
			$sql->Query($query);

			$this->data["enfant"]=array();
			for($i=0; $i<$sql->rows; $i++)
			{ 
				$sql->GetRow($i);
				$this->data["enfant"][$i]["id"]=$sql->data["id"];
			}

			if (is_array($this->data["enfant"]))
			{
				foreach($this->data["enfant"] as $i=>$val)
				{
					$this->data["enfant"][$i]["usr"]=new user_class($val["id"],$sql,false,false);
				}
			}
		  // }
		  
	}


	# Charge les lachés avions
	function CheckLache($ress){
		$query = "SELECT * FROM ".$this->tbl."_lache WHERE uid_pilote='$this->uid' AND id_avion='$ress'";
		$sql=$this->sql;
		$res=$sql->QueryRow($query);

		if (!is_numeric($res["id"]))
		  { return false; }
		return true;
	}



	# Show user informations
	function aff($key,$typeaff="html",$formname="form_info")
	{ global $MyOpt,$tabTypeNom;
		$txt=$this->data[$key];


		// Ne prend que ceux qui sont activés
		$tabType=array();
		foreach ($MyOpt["type"] as $t=>$d)
		  {
		  	if ($d!="")
					{ $tabType[$t]=$tabTypeNom[$t]; }
		  }

		if (is_numeric($key))
		  { $ret="******"; }
		else if ($key=="prenom")
		  {
			$ret=preg_replace("/-/"," ",$txt);
			$ret=ucwords($ret);
			$ret=preg_replace("/ /","-",$ret);
		  }
		else if ($key=="nom")
		  { $ret=strtoupper($txt); }
		else if ($key=="fullname")
		  { $ret=$txt; }
		else if ($key=="mail")
		  { $ret=strtolower($txt); $type="email"; }
		else if ($key=="initiales")
		  { $ret=strtoupper($txt); }
		else if ($key=="ville")
		  { $ret=strtoupper($txt); }
		else if (($key=="tel_fixe") || ($key=="tel_portable") || ($key=="tel_bureau") || ($key=="tel_medecin"))
		  { $ret=AffTelephone($txt); $type="tel";}
		else if ($key=="zone")
		  { $ret=$tabZone[$txt]; }
		else if ($key=="codepostal")
		  { $ret=$txt; $type="number"; }
		else if ($key=="regime")
		  { $ret=$tabRegime[$txt]; }
		else if ($key=="type_repas")
		  { $ret=ucwords($txt); }
		else if ($key=="aff_rapide")
		  { $ret=($txt=="n") ? "Normal" : "Rapide"; }
		else if (($key=="dte_licence") || ($key=="dte_medicale") || ($key=="dte_naissance") || ($key=="dte_inscription"))
		  { $ret=$txt; $type="date"; }
		else if ($key=="poids")
		  { $ret=$txt; $type="number"; }
		else if ($key=="decouvert")
		  { $ret=$txt; $type="number"; }
		else if ($key=="tarif")
		  { $ret=$txt; $type="number"; }
		else if ($key=="type")
		  { $ret=$tabType[$txt]; }
		else if ($key=="nom_medecin")
		  { $ret=ucwords($txt); }
		else if ($key=="sexe")
	  	  { $ret=(($txt=="NA") ? "-" : (($txt=="M") ? "Masculin" : "Féminin") ); }
		else if (($key=="allergie_asthme") || ($key=="allergie_medicament") || ($key=="allergie_alimentaire"))
	  	  { $ret=(($txt=="N") ? "Non" : "Oui" ); }
		else if ($key=="aut_prelevement")
	  	  { $ret=(($txt=="N") ? "Non" : "Oui" ); }
		else if ($key=="password")
		  { $ret="******"; }
		else if ($key=="virtuel")
		  { $ret="******"; }
		else if ($key=="uid_maj")
		  { $ret="******"; }
		else
		  { $ret=$txt; }

		// Défini les droits de modification des utilisateurs
		$mycond=$this->me;	// Le user a le droit de modifier toutes ses données

		// Si on a le droit de modif on autorise
		if (GetDroit("ModifUser"))
		  { $mycond=true; }

		// Test les exceptions
		if ($key=="prenom")
		  {
			if (!GetDroit("ModifUser"))
			  { $mycond=false; }
		  }
		else if ($key=="nom")
		  {
			if (!GetDroit("ModifUser"))
			  { $mycond=false; }
		  }
		else if ($key=="zone")
		  {
			if (!GetDroit("ModifUser"))
			  { $mycond=false; }
		  }
		else if ($key=="dte_medicale")
		  {
			if (GetDroit("ModifUserDteMedicale"))
 		  	  { $mycond=true; }
			else
 		  	  { $mycond=false; }
 		  }
		else if ($key=="dte_licence")
		  {
			if (GetDroit("ModifUserDteLicence"))
 		  	  { $mycond=true; }
			else
 		  	  { $mycond=false; }
 		  }
		else if ($key=="dte_inscription")
		  {
			if (GetDroit("ModifUserDteInscription"))
 		  	  { $mycond=true; }
			else
 		  	  { $mycond=false; }
 		  }
		else if ($key=="lache")
		  {
				if (GetDroit("ModifUserLache"))
 		  	  { $mycond=true; }
				else
 		  	  { $mycond=false; }
 		  }
		else if ($key=="donnees")
		  {
				if (GetDroit("ModifUserDonnees"))
 		  	  { $mycond=true; }
				else
 		  	  { $mycond=false; }
 		  }
		else if ($key=="decouvert")
		  {
			if (GetDroit("ModifUserDecouvert"))
 		  	  { $mycond=true; }
			else
 		  	  { $mycond=false; }
		  }
		else if ($key=="idcpt")
		  {
			if (GetDroit("ModifUserIdCpt"))
 		  	  { $mycond=true; }
			else
 		  	  { $mycond=false; }
 		  }
		else if ($key=="tarif")
		  {
			if (GetDroit("ModifUserTarif"))
 		  	  { $mycond=true; }
			else
 		  	  { $mycond=false; }
		  }
		else if ($key=="type")
		  {
			if (GetDroit("ModifUserType"))
 		  	  { $mycond=true; }
			else
 		  	  { $mycond=false; }
		  }
		else if ($key=="pere")
		  {
			if (GetDroit("ModifParents"))
 		  	  { $mycond=true; }
			else
 		  	  { $mycond=false; }
		  }
		else if ($key=="mere")
		  {
			if (GetDroit("ModifParents"))
 		  	  { $mycond=true; }
			else
 		  	  { $mycond=false; }
		  }

		// Si l'utilisateur a le droit de tout modifier alors on force
		if (GetDroit("ModifUserAll"))
		  { $mycond=true; }

		// Si on a pas le droit on repasse en visu
		if ((!$mycond) && ($typeaff!="val"))
		  { $typeaff="html"; }
 	
		if ($typeaff=="form")
		  {
			if ($key=="commentaire")
		  	  { $ret="<TEXTAREA id='".$key."'  name=\"".$formname."[$key]\" rows=5>$ret</TEXTAREA>"; }
			else if ($key=="allergie_commentaire")
		  	  { $ret="<TEXTAREA id='".$key."'   name=\"".$formname."[$key]\" rows=5>$ret</TEXTAREA>"; }
			else if ($key=="remarque_sante")
		  	  { $ret="<TEXTAREA id='".$key."'   name=\"".$formname."[$key]\" rows=5>$ret</TEXTAREA>"; }
			else if ($key=="type")
		  	  {
		  	  	$ret ="<SELECT id='".$key."'  name=\"".$formname."[$key]\">";

				foreach($tabType as $typeid=>$typetxt)
				  {
		  	  		$ret.="<OPTION value=\"".$typeid."\" ".(($txt==$typeid)?"selected":"").">".$typetxt."</OPTION>";
				  }

		  	  	$ret.="</SELECT>";
		  	  }
			else if ($key=="sexe")
		  	  {
		  	  	$ret ="<SELECT id='".$key."'  name=\"".$formname."[$key]\">";
		  	  	$ret.="<OPTION value=\"M\" ".(($txt=="M")?"selected":"").">Masculin</OPTION>";
		  	  	$ret.="<OPTION value=\"F\" ".(($txt=="F")?"selected":"").">Féminin</OPTION>";
		  	  	$ret.="</SELECT>";
		  	  }
			else if ($key=="zone")
		  	  {
		  	  	$ret ="<SELECT id='".$key."'  name=\"".$formname."[$key]\">";

				if (is_array($tabZone))
				{
					foreach($tabZone as $typeid=>$typetxt)
					  {
			  	  		$ret.="<OPTION value=\"".$typeid."\" ".(($txt==$typeid)?"selected":"").">".$typetxt."</OPTION>";
					  }
				}
		  	  	$ret.="</SELECT>";
		  	  }
			else if ($key=="aff_rapide")
		  	  {
		  	  	$ret ="<SELECT id='".$key."'  name=\"".$formname."[$key]\">";
		  	  	$ret.="<OPTION value=\"n\" ".(($txt=="n")?"selected":"").">Normal</OPTION>";
		  	  	$ret.="<OPTION value=\"y\" ".(($txt=="y")?"selected":"").">Rapide</OPTION>";
		  	  	$ret.="</SELECT>";
		  	  }
			else if ($key=="notification")
		  	  {
		  	  	$ret ="<SELECT id='".$key."'  name=\"".$formname."[$key]\">";
		  	  	$ret.="<OPTION value=\"oui\" ".(($txt=="oui")?"selected":"").">Oui</OPTION>";
		  	  	$ret.="<OPTION value=\"non\" ".(($txt=="non")?"selected":"").">Non</OPTION>";
		  	  	$ret.="</SELECT>";
		  	  }
 			else if ($key=="lache")
		  	  {
				$ret="";
		  	  	foreach($this->data[$key] as $avion)
		  	  	  {
		  	  		$ret.="<input type='checkbox' name='form_lache[".$avion["idavion"]."]' ".(($avion["idlache"]>0) ? "checked" : "")." value='".(($avion["idlache"]>0) ? $avion["idlache"] : "N")."' /> ".$avion["avion"]->immatriculation."<br />";
		  	  	  }
			  }
 			else if ($key=="donnees")
			{
				$ret="";
		  	  	foreach($this->data[$key] as $did=>$donnees)
		  	  	{
					$ret.="<label>".$donnees["nom"]."</label><input name='form_donnees[".$did."]' value='".$donnees["valeur"]."'></br>";
		  	  	}
			}
			else if ($key=="pere")
		    {
					$sql=$this->sql;
					$ret=AffListeMembres($sql,$txt->uid,$formname."[".$key."]","","M","std","non");
			  }			
			else if ($key=="mere")
		    {
					$sql=$this->sql;
					$ret=AffListeMembres($sql,$txt->uid,$formname."[".$key."]","","F","std","non");
			  }			
			else if ($key=="enfant")
		  	 {
					$ret="";
					if (is_array($this->data[$key]))
					  {
				  	  	foreach($this->data[$key] as $enfant)
				  	  	  {
				  	  		if ($enfant["id"]>0)
				  	  		  { $ret.="<a href=\"membres.php?rub=detail&id=".$enfant["id"]."\">".$enfant["usr"]->fullname."</a><br />"; }
				  	  	  }
					  }
					if ($ret=="") { $ret="Aucun"; }
			  }			
			else if ($key=="idcpt")
		    {
		    	$ret ="<select id='".$key."'  name=\"".$formname."[$key]\">";
		    	$ret.="<option value=\"".$this->uid."\" ".(($txt==$this->uid)?"selected":"").">$this->fullname</option>";
					if (is_array($this->data["enfant"]))
					  {
				  	  	foreach($this->data["enfant"] as $enfant)
				  	  	  {
				  	  		if ($enfant["id"]>0)
				  	  		  {
				  	  		  	$ret.="<a href=\"membres.php?rub=detail&id=".$enfant["id"]."\">".$enfant["usr"]->fullname."</a><br />";
						  	  	$ret.="<option value=\"".$enfant["id"]."\" ".(($txt==$enfant["id"])?"selected":"").">".$enfant["usr"]->fullname."</option>";
				  	  		  }
				  	  	  }
					  }
					$ret.="</select>";
			  }			
			else
			  { $ret="<INPUT id='".$key."'  name=\"".$formname."[$key]\" id=\"$key\" value=\"$ret\" size=40 ".(($type!="") ? "type=\"".$type."\"" : "").">"; }
		  }
		else if ($typeaff=="val")
		  {
				if ($key=="commentaire")
			  	  { $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1")); }
	
				else if ($key=="mail")
			  	  { $ret=strtolower($ret); }
				else if (($key=="dte_naissance") || ($key=="dte_licence") || ($key=="dte_medicale") || ($key=="dte_inscription"))
			  	  {
			  	  	if ($txt=="0000-00-00")
			 			{ $ret="-"; }
			  	  }
				else if ($key=="lache")
			  	{
					$ret="";
			  	 	foreach($this->data[$key] as $avion)
			  	 	{
			  	  		if ($avion["idlache"]>0)
			  			  	{ $ret.=$avion["avion"]->immatriculation."<br />"; }
			  	  	}
					if ($ret=="") { $ret="Aucun"; }
				}
				else if ($key=="pere")
			  	{
					$t=$this->data[$key];
					$ret=$t->fullname;
				}
				else if ($key=="mere")
			  	{
			  	 	$t=$this->data[$key];
					$ret=$t->fullname;
				}
		  }
		else
		{
			if ($key=="commentaire")
			{ $ret=nl2br(htmlentities($ret,ENT_HTML5,"ISO-8859-1"));  }
			else if ($key=="mail")
			{ $ret="<A href=\"mailto:".strtolower($ret)."\">".strtolower($ret)."</A>"; }
			else if (($key=="dte_licence") || ($key=="dte_medicale"))
			{
				$ret=AffDate($ret);
			}
			else if ($key=="dte_naissance")
			{
				$ret=sql2date($ret)." (".(date("Y")-date("Y",strtotime($ret))-1)." ans)";
			}
			else if ($key=="dte_inscription")
			{
				$ret=sql2date($ret);
			}
			else if ($key=="lache")
			{
				$ret="";
				foreach($this->data[$key] as $avion)
				{
					if ($avion["idlache"]>0)
					{ $ret.=$avion["avion"]->immatriculation." <font size=1><i>(par ".$avion["usr"]->prenom." ".$avion["usr"]->nom.")</i></font><br />"; }
				}
				if ($ret=="") { $ret="Aucun"; }
			}
 			else if ($key=="donnees")
			{
				$ret="";			
		  	  	foreach($this->data[$key] as $did=>$donnees)
		  	  	{
					$ret.="<label>".$donnees["nom"]."</label>".$donnees["valeur"]."</br>";
		  	  	}
			}
			else if ($key=="enfant")
			{
				$ret="";
				if (is_array($this->data[$key]))
				  {
					foreach($this->data[$key] as $enfant)
					  {
						if ($enfant["id"]>0)
						  { $ret.="<a href=\"membres.php?rub=detail&id=".$enfant["id"]."\">".$enfant["usr"]->fullname."</a><br />"; }
					  }
				  }
				if ($ret=="") { $ret="Aucun"; }
			}			
			else if ($key=="idcpt")
			{
				if ($txt==$this->uid)
				{
					$ret=$this->fullname;
				}
				else if (is_array($this->data["enfant"]))
				{
					foreach($this->data["enfant"] as $enfant)
					{
						if ($enfant["id"]==$txt)
						{ $ret="<a href=\"membres.php?rub=detail&id=".$enfant["id"]."\">".$enfant["usr"]->fullname."</a>"; }
					}
				}
			}
			else if ($key=="pere")
			{
				$t=$this->data[$key];
				$ret="<a href=\"membres.php?rub=detail&id=".$t->uid."\">".$t->fullname."</a>";
			}
			else if ($key=="mere")
			{
				$t=$this->data[$key];
				$ret="<a href=\"membres.php?rub=detail&id=".$t->uid."\">".$t->fullname."</a>";
			}
			else if ( ($key=="fullname") && ($this->actif=="off"))
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\"><s>".$ret."</s></a>";
			}
			else if ( ($key=="fullname") && ($this->actif=="non"))
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\"><s style='color:#ff0000;'>".$ret."</s></a>";
			}
			else if ($key=="fullname")
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\">".$ret."</a>";
			}
			else if ( ($key=="nom") && ($this->actif=="off"))
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\"><s>".$ret."</s></a>";
			}
			else if ( ($key=="nom") && ($this->data["password"]=="") && (GetDroit("ModifUserPassword")))
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\"><i>".$ret." (*)</i></a>";
			}
			else if ( ($key=="nom") && ($this->actif=="non"))
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\"><s style='color:#ff0000;'>".$ret."</s></a>";
			}
			else if ($key=="nom")
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\">".$ret."</a>";
			}
			else if ( ($key=="prenom") && ($this->actif=="off"))
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\"><s>".$ret."</s></a>";
			}
			else if ( ($key=="prenom") && ($this->actif=="non"))
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\"><s style='color:#ff0000;'>".$ret."</s></a>";
			}
			else if ($key=="prenom")
			{
				$ret="<a href=\"membres.php?rub=detail&id=".$this->uid."\">".$ret."</a>";
			}
			else if ($key=="droits")
			{
				$sql=$this->sql;
				$query="SELECT droits.groupe, groupe.description FROM ".$this->tbl."_droits AS droits LEFT JOIN ".$this->tbl."_groupe AS groupe ON droits.groupe=groupe.groupe WHERE uid='".$this->uid."'";
				$sql->Query($query);
		
				$ret="";
				for($i=0; $i<$sql->rows; $i++)
				{ 
					$sql->GetRow($i);
					$ret.=(($sql->data["description"]!="") ? $sql->data["description"]." (".$sql->data["groupe"].")" : $sql->data["groupe"])."<br/>";
				}
			}
//				$ret="<span  id='".$key."'>".$ret."</span>";
		}
	
		return $ret;
	}

	function AffTel(){
		if ($this->data["tel_fixe"]!="")
		  { $tel=$this->data["tel_fixe"]; }
		else if ($this->data["tel_portable"]!="")
		  { $tel=$this->data["tel_portable"]; }
		else if ($this->data["tel_bureau"]!="")
		  { $tel=$this->data["tel_bureau"]; }
		else
		  { $tel="-"; }

		return AffTelephone($tel);
	}

	function AffNbHeuresVol(){
		$t=$this->NbHeuresVol();

		$ret=(($t>0) ? AffTemps($t) : "0h 00");
		return "<a href='vols.php?id=$this->uid'>$ret</a>";
	}

	function AffNbHeuresAn(){
		$t=$this->NbHeuresAn();
	
		$t=$t+$res["nb"];

		$ret=(($t>0) ? AffTemps($t) : "0h 00");
		return "<a href='vols.php?id=$this->uid'>$ret</a>";
	}

	function AffNbHeures12mois() {
		$t=$this->NbHeures12mois();

		if ($t>30*60)
		  { $ret="<font color=green>".AffTemps($t)."</font>"; }
		else if ($t>0)
		  { $ret=AffTemps($t); }
		else
		  { $ret="0h 00"; }
		

		return "<a href='vols.php?id=$this->uid'>$ret</a>";
	}

	function AffNbHeuresProrogation() {
		$t=$this->NbHeuresProrogation();

		if ($t>30*60)
		  { $ret="<font color=green>".AffTemps($t)."</font>"; }
		else if ($t>0)
		  { $ret=AffTemps($t); }
		else
		  { $ret="0h 00"; }

		return "<a href='vols.php?id=$this->uid'>$ret</a>";
	}

	function NbHeuresVol(){
		$sql=$this->sql;
		$query="SELECT SUM(tpsreel) AS nb FROM `".$this->tbl."_calendrier` WHERE uid_pilote = '$this->uid' AND (prix<>0 OR tpsreel>0)";
		$res=$sql->QueryRow($query);

		$t=$res["nb"];

		$query="SELECT SUM(tpsreel) AS nb FROM `".$this->tbl."_calendrier` WHERE uid_instructeur = '$this->uid' AND (prix<>0 OR tpsreel>0)";
		$res=$sql->QueryRow($query);

		$t=$t+$res["nb"];
		return (($t>0) ? $t : "0");
	}

	function NbHeuresAn(){
		$sql=$this->sql;
		$query="SELECT SUM( tpsreel ) AS nb FROM `".$this->tbl."_calendrier` WHERE uid_pilote = '$this->uid' AND dte_deb>='".date("Y")."-01-01' AND (prix<>0 OR tpsreel<>0)";
		$res=$sql->QueryRow($query);

		$t=$res["nb"];

		$query="SELECT SUM( tpsreel ) AS nb FROM `".$this->tbl."_calendrier` WHERE uid_instructeur = '$this->uid' AND dte_deb>='".date("Y")."-01-01' AND (prix<>0 OR tpsreel<>0)";
		$res=$sql->QueryRow($query);
	
		$t=$t+$res["nb"];
		return (($t>0) ? $t : "0");
	}

	function NbHeures12mois() {
		$sql=$this->sql;
		$query="SELECT SUM( tpsreel ) AS nb FROM `".$this->tbl."_calendrier` WHERE uid_pilote = '$this->uid' AND dte_deb>'".(date("Y")-1)."-".date("m")."-".date("d")."' AND dte_deb<='".date("Y")."-".date("m")."-".date("d")."' AND (prix<>0 OR tpsreel<>0)";
		$res=$sql->QueryRow($query);

		$t=$res["nb"];

		$query="SELECT SUM( tpsreel ) AS nb FROM `".$this->tbl."_calendrier` WHERE uid_instructeur = '$this->uid' AND dte_deb>'".(date("Y")-1)."-".date("m")."-".date("d")."' AND dte_deb<='".date("Y")."-".date("m")."-".date("d")."' AND (prix<>0 OR tpsreel<>0)";
		$res=$sql->QueryRow($query);
	
		$t=$t+$res["nb"];
		return (($t>0) ? $t : "0");
	}

	function NbHeuresProrogation() {
		$sql=$this->sql;
		$query="SELECT SUM( tpsreel ) AS nb FROM `".$this->tbl."_calendrier` WHERE uid_pilote = '$this->uid' AND dte_deb>'".(date("Y-m-d",strtotime($this->data["dte_licence"]." -1 year")))."'AND dte_deb<='".$this->data["dte_licence"]."' AND (prix<>0 OR tpsreel<>0)";
		$res=$sql->QueryRow($query);
		$t=$res["nb"];

		$query="SELECT SUM( tpsreel ) AS nb FROM `".$this->tbl."_calendrier` WHERE uid_instructeur = '$this->uid' AND dte_deb>'".(date("Y")-1)."-".date("m")."-".date("d")."' AND dte_deb<='".date("Y")."-".date("m")."-".date("d")."' AND (prix<>0 OR tpsreel<>0)";
		$res=$sql->QueryRow($query);
	
		$t=$t+$res["nb"];
		return (($t>0) ? $t : "0");
	}

	function AffDernierVol() {
		$sql=$this->sql;
		if ($this->type=="eleve")
		  {
			// Dernier vol en DC
//			$query="SELECT dte_deb AS dte, uid_instructeur AS ins FROM `".$this->tbl."_calendrier` WHERE uid_pilote = ".$this->uid." AND prix>0 ORDER BY dte_deb DESC LIMIT 0,1";
//			$res=$sql->QueryRow($query);
			$res=$this->DernierVol("",0);

			$dc = (($res["ins"]>0) ? "(DC)" : "");
			$d=sql2date($res["dte"],"jour");
			$l=floor((time()-strtotime($res["dte"]))/86400);

			$ret=(($l<30) ? $d." $dc" : (($l<45) ? "<font color=orange>$d $dc</font>": "<font color=red>$d $dc</font>"));
		  }
		else if (($this->type!="invite") && ($this->type!="membre"))
		  {
//			$query="SELECT dte_deb AS dte FROM `".$this->tbl."_calendrier` WHERE uid_pilote = ".$this->uid." AND prix>0 ORDER BY dte_deb DESC LIMIT 0,1";
//			$res=$sql->QueryRow($query);
			$res=$this->DernierVol("",0);

			$d=sql2date($res["dte"],"jour");
			$l=floor((time()-strtotime($res["dte"]))/86400);

			$ret=(($l<60) ? $d : (($l<90) ? "<font color=orange>$d</font>": "<font color=red>$d</font>"));
		  }

		return $ret;
	}

	function DernierVol($type="",$tps=0) {
		$sql=$this->sql;
		if ($type=="DC")
		  {
				// Dernier vol en DC
				$query="SELECT id, tpsreel, dte_deb AS dte, uid_instructeur AS ins FROM `".$this->tbl."_calendrier` WHERE uid_pilote = ".$this->uid." AND uid_instructeur>0 AND ".(($tps>0) ? "tpsreel>='".$tps."'" : "tpsreel>0")." ORDER BY dte_deb DESC LIMIT 0,1";
				$res=$sql->QueryRow($query);
		  }
		else
		  {
				$query="SELECT id, tpsreel, dte_deb AS dte, uid_instructeur AS ins FROM `".$this->tbl."_calendrier` WHERE uid_pilote = ".$this->uid." AND ".(($tps>0) ? "tpsreel>='".$tps."'" : "tpsreel>0")." ORDER BY dte_deb DESC LIMIT 0,1";
				$res=$sql->QueryRow($query);
		  }

		return $res;
	}

	function NombreVols($nbmois="3",$type="aff") {
		$sql=$this->sql;
		// Dernier vol en DC
		$query="SELECT COUNT(*) AS nb FROM `".$this->tbl."_calendrier` WHERE (uid_pilote = ".$this->uid." OR uid_instructeur = ".$this->uid.") AND (prix>0 OR tpsreel>0) AND dte_deb>'".((date("n")<=$nbmois)?(date("Y")-1):date("Y"))."-".((date("n")<=$nbmois)?(12+date("n")-$nbmois):(date("n")-$nbmois))."-".date("d")."'";
		$res=$sql->QueryRow($query);

		$ret=$res["nb"];

		if ($type=="val")
		  { return $ret; }
		else
		  {
			if ($ret>=3)
			  { $ret="<font color='green'>$ret</font>"; }
			else
			  { $ret="<font color='red'><b>$ret</b></font>"; }
		  	return "<a href='vols.php?id=$this->uid'>".$ret."</a>";
		  }
	}

	function AffSolde(){
		global $MyOpt;
		$sql=$this->sql;
		$query = "SELECT SUM(".$this->tbl."_compte.montant) AS total FROM ".$this->tbl."_compte WHERE ".$this->tbl."_compte.uid='$this->idcpt'";
		$res=$sql->QueryRow($query);

		$solde=(($res["total"]=="") ? AffMontant(0) : (($res["total"]<0) ? "<FONT color=red><B>".AffMontant($res["total"])."</B></FONT>" : AffMontant($res["total"])));

		return "<a href=\"comptes.php?id=$this->idcpt\">$solde</a>";
	}

	function CalcSoldeFacture(){
		$sql=$this->sql;
		$query = "SELECT SUM(".$this->tbl."_compte.montant) AS total FROM ".$this->tbl."_compte WHERE ".$this->tbl."_compte.uid='$this->idcpt' AND ".$this->tbl."_compte.facture='NOFAC'";
		$res=$sql->QueryRow($query);

		$solde=((is_numeric($res["total"])) ? $res["total"] : "0");

		return $solde;
	}

	function CalcSolde(){
		$sql=$this->sql;
		$query = "SELECT SUM(".$this->tbl."_compte.montant) AS total FROM ".$this->tbl."_compte WHERE ".$this->tbl."_compte.uid='$this->idcpt'";
		$res=$sql->QueryRow($query);

		$solde=((is_numeric($res["total"])) ? $res["total"] : "0");

		return $solde;
	}

	function CalcAge($dte){
		global $uid;
		if ($this->dte_naissance!="0000-00-00")
		  {
			$age=floor((strtotime($dte)-strtotime($this->dte_naissance))/(365.25*24*3600));
		  }
		else
		  {
			$age=0;
		  }
		
		return $age;
	}	

	function Photo(){
		$rep=dir("membres");
		$rep->read();
		$rep->read();

		while($f = $rep->read())
		{
			if (preg_match("/^".$this->uid."\.[a-z]*$/",$f))
			  { return "membres/$f"; }
		}
	
		return "images/none.gif";
	}

	# Save Password
	function SaveMdp($mdp){
		$sql=$this->sql;
		$this->data["password"]=$mdp;
		$query = "UPDATE ".$this->tbl."_utilisateurs SET password='$mdp' WHERE id='$this->uid'";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'user', '".$this->tbl."_utilisateurs', '".$this->uid."', '$uid', '".now()."', 'MOD', 'Modify password')";
		$sql->Insert($query);
		return "";
	}

	function Create(){
		global $uid;
		$sql=$this->sql;
		$query="INSERT INTO ".$this->tbl."_utilisateurs SET uid_maj='$uid', dte_maj='".now()."'";
		$this->uid=$sql->Insert($query);
		$this->data["idcpt"]=$this->uid;

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'user', '".$this->tbl."_utilisateurs', '".$this->uid."', '$uid', '".now()."', 'ADD', 'Create user')";
		$sql->Insert($query);
		
		return $this->uid;
	}
	
	function Valid($k,$v,$ret=false){
		$vv="**none**";
		if ($k=="initiales")
		  {
			if ($v=="")
			  { 
				$this->data["initiales"]=substr($this->data["prenom"],0,1).substr($this->data["nom"],0,2);
				$vv=$this->data["initiales"];
			  }
			else
			  {
			  	$vv=$v;
			  }

			$sql=$this->sql;
			$query = "SELECT COUNT(*) AS nb FROM ".$this->tbl."_utilisateurs WHERE initiales='".$vv."' AND id<>'".$this->uid."' AND actif='oui'";
			$res = $sql->QueryRow($query);
			if (($res["nb"]>0) && ($ret==false) && ($v!=""))
			  {
				return "Les initiales choisies existent déjà !<br />";
			  }
			else if ($res["nb"]>0)
			  {
			  	$vv="";
			  }
			else
			  {
			  	$vv=strtolower($vv);
			  }
		  }
		else if ($k=="prenom")
		  {	
		  	if ($v=="")
		  	  {
		  	  	return "Le prénom est vide.<br />";
			  }
			$vv=preg_replace("/ /","-",$v);
			$vv=strtolower($vv);
		  }
		else if ($k=="nom")
		  {
		  	if ($v=="")
		  	  {
		  	  	return "Le nom est vide.<br />";
			  }
			$vv=strtolower($v);
		  }
		else if (($k=="tel_fixe") || ($k=="tel_portable") || ($k=="tel_bureau") || ($k=="tel_medecin"))
	  	  {
	  	  	$vv=str_replace(" ","",$v);
	  	  	$vv=str_replace(".","",$vv);
	  	  }
	  	else if (($k=="dte_licence") || ($k=="dte_medicale") || ($k=="dte_naissance") || ($k=="dte_inscription") || ($k=="aff_jour"))
	  	  {
	  	  	if (date2sql($v)!="nok")
	  	  	  { $vv=date2sql($v); }
	  	  	else if (preg_match("/^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})([0-9: ]*)$/",$v))
	  	  	  { $vv=$v; }
	  	  }
	  	else if ($k=="commentaire")
	  	  { $vv=$v; }
	  	else if ($k=="decouvert")
	  	  { $vv=abs($v); }
	  	else if ($k=="tarif")
	  	  {
			if (is_numeric($v))
			  { $vv=$v; }
			else
			  { $vv=0; }
	  	  }
	  	else if ($k=="droits")
	  	  { $vv=strtoupper($v); }
	  	else if ($k=="zone")
	  	  { $vv=strtoupper($v); }
	  	else if ($k=="regime")
	  	  { $vv=strtoupper($v); }
	  	else if ($k=="sexe")
	  	  { $vv=strtoupper($v); }
	  	else if ($k=="aut_prelevement")
	  	  {
	  	  	$vv=(($v=="y") ||($v=="Y")) ? "Y" : "N";
	  	  }
	  	else if ($k=="idcpt")
	  	  { 
	  	  	if ($v==0)
	  	  	  { $vv=$this->uid; }
	  	  	else
	  	  	  { $vv=$v; }
	  	  }
	  	else
	  	  { $vv=strtolower($v); }

		if ( (!is_numeric($k)) && ("($vv)"!="(**none**)") && ($ret==false))
		  { $this->data[$k]=$vv; }
		else if ($ret==true)
		  { return addslashes($vv); }
	}

	function Save()
	{
		global $uid;
		$sql=$this->sql;

		$query ="UPDATE ".$this->tbl."_utilisateurs SET ";
		foreach($this->data as $k=>$v)
		  { 
			if ((!is_numeric($k)) && ($k!="fullname") && ($k!="password"))
			  {
				$vv=$this->Valid($k,$v,true);
			  	$query.="$k='$vv',";
			  }
		  }
		$query.="uid_maj=$uid, dte_maj='".now()."' ";
		$query.="WHERE id='$this->uid'";
		$sql->Update($query);

		$this->RazGroupe();

		$trole=preg_split("/,/",$this->data["droits"]);
		foreach ($trole AS $grp)
		  {
				$this->AddGroupe($grp);
		  }

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'user', '".$this->tbl."_utilisateurs', '".$this->uid."', '$uid', '".now()."', 'MOD', 'Modify user')";
		$sql->Insert($query);
	}

	function AddGroupe($grp) {
		global $uid;
		$sql=$this->sql;

		if (trim($grp)!="")
		  {	
				$query ="INSERT INTO ".$this->tbl."_droits (`groupe` ,`uid` ,`uid_creat` ,`dte_creat`) ";
				$query.="VALUES ('".trim($grp)."' , '".$this->uid."', '$uid', '".now()."')";
				$sql->Insert($query);
			}
	}

	function DelGroupe($grp) {
		$sql=$this->sql;
		$query="DELETE FROM ".$this->tbl."_droits WHERE uid='$this->uid' AND groupe='$grp'";
		$sql->Delete($query);
	}

	function RazGroupe() {
		$sql=$this->sql;
		$query="DELETE FROM ".$this->tbl."_droits WHERE uid='$this->uid'";
		$sql->Delete($query);
	}


	function UpdateResa(){
		global $uid;
		$sql=$this->sql;

		$query ="UPDATE ".$this->tbl."_utilisateurs SET aff_jour='".$this->data["aff_jour"]."', aff_mois='".$this->data["aff_mois"]."'";
		$query.="WHERE id='$this->uid'";
		$sql->Update($query);
	}

	
	function SaveLache($tablache)
	{
		global $uid;
		$sql=$this->sql;
		// Charge les enregistrements
		$query = "SELECT * FROM ".$this->tbl."_lache WHERE uid_pilote='$this->uid'";
		$sql->Query($query);
		$tlache=array();
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$tlache[$sql->data["id_avion"]]["bd"]=$sql->data["id"];
		}
		
		// Charge les nouvelles valeurs
		if (is_array($tablache))
		  {
			foreach($tablache as $avion=>$lid)
			  {
				$tlache[$avion]["new"]=$lid;
			  }
		  }
		// Vérifie la différence
		foreach($tlache as $avion=>$v)
		  {
			if (($v["bd"]=="") && ($v["new"]=="N"))
			  {
				$query="INSERT INTO ".$this->tbl."_lache SET uid_pilote='$this->uid', id_avion='$avion', uid_creat='$uid', dte_creat='".now()."'";
				$sql->Insert($query);
			  }
			else if (($v["bd"]>0) && ($v["new"]==""))
			  {
				$query="DELETE FROM ".$this->tbl."_lache WHERE id='".$v["bd"]."'";
				$sql->Delete($query);
			  }
		  }

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'user', '".$this->tbl."_utilisateurs', '".$this->uid."', '$uid', '".now()."', 'MOD', 'Modify ressource access')";
		$sql->Insert($query);
	}

	function SaveDonnees($form_donnees)
	{ 
		global $uid;
		$sql=$this->sql;
		
		foreach($form_donnees as $did=>$d)
		{
			$query = "SELECT id FROM ".$this->tbl."_utildonnees WHERE uid='".$this->uid."' AND did='".$did."'";
			$res=$sql->QueryRow($query);
			if ($res["id"]>0)
			{
				$query="UPDATE ".$this->tbl."_utildonnees SET valeur='".$d."' WHERE uid='".$this->uid."' AND did='".$did."'";
				$sql->Update($query);
			}
			else
			{
				$query="INSERT INTO ".$this->tbl."_utildonnees SET valeur='".$d."', uid='".$this->uid."', did='".$did."'";
				$sql->Insert($query);
			}
		}
		
		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'user', '".$this->tbl."_utildonnees', '".$this->uid."', '$uid', '".now()."', 'MOD', 'Modify user data')";
		$sql->Insert($query);
	}

	function Desactive(){
		global $gl_uid;
		$sql=$this->sql;
		$this->actif="off";

		$query="UPDATE ".$this->tbl."_utilisateurs SET actif='off', uid_maj=$gl_uid, dte_maj='".now()."' WHERE id=$this->uid";
		$sql->Update($query);

		$query="UPDATE ".$this->tbl."_abonnement SET actif='non', uid_maj=$gl_uid, dte_maj='".now()."' WHERE uid=$this->uid";
		$sql->Update($query);

		$query="UPDATE ".$this->tbl."_calendrier SET actif='non', uid_maj=$gl_uid, dte_maj='".now()."' WHERE uid_pilote=$this->uid AND dte_deb>'".now()."'";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'user', '".$this->tbl."_utilisateurs', '".$this->uid."', '$gl_uid', '".now()."', 'DEL', 'Disable user')";
		$sql->Insert($query);
	}

	function Active(){
		global $gl_uid;
		$sql=$this->sql;
		$this->actif="oui";

		$query="UPDATE ".$this->tbl."_utilisateurs SET actif='oui', uid_maj=$gl_uid, dte_maj='".now()."' WHERE id=$this->uid";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'user', '".$this->tbl."_utilisateurs', '".$this->uid."', '$gl_uid', '".now()."', 'DEL', 'Enable user')";
		$sql->Insert($query);
	}
	
	function Delete(){
		global $uid;
		$sql=$this->sql;
		$this->actif="non";

		$query="UPDATE ".$this->tbl."_utilisateurs SET actif='non', uid_maj=$uid, dte_maj='".now()."' WHERE id=$this->uid";
		$sql->Update($query);

		$query="UPDATE ".$this->tbl."_document SET actif='non' WHERE uid=$this->uid";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'user', '".$this->tbl."_utilisateurs', '".$this->uid."', '$uid', '".now()."', 'DEL', 'Delete user')";
		$sql->Insert($query);
	}	

	function LoadDonneesComp()
	{
		$sql=$this->sql;
		$query = "SELECT def.id,def.nom,donnees.valeur FROM ".$this->tbl."_utildonneesdef AS def LEFT JOIN ".$this->tbl."_utildonnees AS donnees ON donnees.did=def.id WHERE donnees.uid='$this->uid' OR donnees.uid IS NULL ORDER BY ordre, nom";
		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$this->data["donnees"][$sql->data["id"]]["nom"]=$sql->data["nom"];
			$this->data["donnees"][$sql->data["id"]]["valeur"]=$sql->data["valeur"];
		}
	}
} # End of class




function ListActiveUsers($sql,$order="",$tabtype="",$virtuel="non")
 { global $MyOpt;
 	$lstuser=array();
	$type=array();
	
	if ($tabtype!="")
	  { 
	  	$type=explode(",",$tabtype);
	  }

	$reqAnd="";
	$reqOr="";
	if ( (is_array($type)) && (count($type)>0) )
	  {
			foreach($type as $t)
			  {
			  	if (substr($t,0,1)=="!")
			  	  { $reqAnd.=" AND type<>'".substr($t,1,strlen($t)-1)."'"; }
			  	else
			  	  { $reqOr.="type='$t' OR "; }
			  }

			if ($reqOr!="")
			  {
					$reqOr.="1=0";
				}

	  }
	if ($order=="std")
	  { $order=(($MyOpt["globalTrie"]=="nom") ? "nom,prenom" : "prenom,nom"); }

	$query="SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs WHERE (";
	$query.="actif='oui'";

	if ((GetDroit("ListeUserDesactive")) && ($MyOpt["showDesactive"]=="on"))
	{
		$query.=" OR actif='off'";
	}
	if ((GetDroit("ListeUserSupprime")) && ($MyOpt["showSupprime"]=="on"))
	{
		$query.="OR actif='non'";
	}

	$query.=") ";
	$query.=(($virtuel!="") ? " AND virtuel='$virtuel'" : "").(($reqOr!="") ? " AND (".$reqOr.")" : "").(($reqAnd!="") ? $reqAnd : "").(($order!="") ? " ORDER BY $order" : "");
	$sql->Query($query);

	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$lstuser[$i]=$sql->data["id"];
	  }
	return $lstuser;
  }

function ListActiveMails($sql)
 { global $MyOpt;
		$lstuser=array();

		$query="SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs WHERE actif='oui' AND virtuel='non' AND mail<>'' AND notification='oui'";
		$sql->Query($query);
		
		for($i=0; $i<$sql->rows; $i++)
		{ 
			$sql->GetRow($i);
			$lstuser[$i]=$sql->data["id"];
		}
		return $lstuser;
  }

function AffListeMembres($sql,$form_uid,$name,$type="",$sexe="",$order="std",$virtuel="non")
 { global $MyOpt;
	if ($order=="std")
	  { $order=(($MyOpt["globalTrie"]=="nom") ? "nom,prenom" : "prenom,nom"); }

	$query ="SELECT id,prenom,nom FROM ".$MyOpt["tbl"]."_utilisateurs WHERE actif='oui' ";
	$query.=(($virtuel!="") ? "AND virtuel='$virtuel' " : "");
	$query.=(($type!="") ? "AND type='$type' " : "");
	$query.=(($sexe!="") ? "AND sexe='$sexe' " : "");
	$query.=(($order!="") ? " ORDER BY $order" : "");
	
	$sql->Query($query);

	$lstuser ="<select name=\"$name\">";
	$lstuser.="<option value=\"0\">Aucun</option>";

	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);

		$sql->data["nom"]=strtoupper($sql->data["nom"]);
		$sql->data["prenom"]=ucwords($sql->data["prenom"]);
		$fullname=AffFullName($sql->data["prenom"],$sql->data["nom"]);
		$lstuser.="<option value=\"".$sql->data["id"]."\" ".(($form_uid==$sql->data["id"]) ? "selected" : "").">".$fullname."</option>";
	  }
	$lstuser.="</select>";

	return $lstuser;
  }


function AffFullname($prenom,$nom)
  { global $MyOpt;
		$fullname="";
		$nom=strtoupper($nom);

		$prenom=preg_replace("/-/"," ",$prenom);
		$prenom=ucwords($prenom);
		$prenom=preg_replace("/ /","-",$prenom);

		if ($MyOpt["globalTrie"]=="nom")
		  {
		  	$fullname=$nom;
		  	$fullname.=(($prenom!="") && ($nom!=""))?" ":"";
		  	$fullname.=$prenom;
		  	$fullname.=(($prenom=="")&&($nom==""))?"N/A":"";
		  }		
		else
		  {
		  	$fullname=$prenom;
		  	$fullname.=(($prenom!="") && ($nom!=""))?" ":"";
		  	$fullname.=$nom;
		  	$fullname.=(($prenom=="")&&($nom==""))?"N/A":"";
		  }		
	return $fullname;
  }
  
// ---- Liste les personnes d'un groupe
function ListGroupUser($sql, $grp)
  {
  }

  
?>