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

// Class Reservation
class resa_class{
	# Constructor
	function __construct($id=0,$sql){
		global $MyOpt;
		$this->tbl=$MyOpt["tbl"];

		$this->sql=$sql;
		$this->id=0;
		$this->destination="LOCAL";
		$this->tarif="P";
		$this->actif="oui";
		$this->reel="oui";
		$this->edite="oui";
		$this->horadeb="0";
		$this->horafin="0";
		$this->tpsestime="";
		if ($id>0)
		  {
			$this->load($id);
		  }
	}

	# Load user informations
	function load($id){
		$this->id=$id;
		$sql=$this->sql;
		$query = "SELECT * FROM ".$this->tbl."_calendrier WHERE id='$id'";
		$res = $sql->QueryRow($query);

		// Charge les variables
		$this->description=$res["description"];
		$this->actif=$res["actif"];
		$this->reel=$res["reel"];
		$this->tarif=$res["tarif"];
		$this->prix=$res["prix"];
		$this->temps=$res["temps"];
		$this->tpsestime=$res["tpsestime"];
		$this->tpsreel=$res["tpsreel"];
		$this->horadeb=$res["horadeb"];
		$this->horafin=$res["horafin"];
		$this->edite=$res["edite"];
		$this->dte_deb=$res["dte_deb"];
		$this->dte_fin=$res["dte_fin"];
		$this->uid_pilote=$res["uid_pilote"];
		$this->uid_debite=$res["uid_debite"];
		$this->uid_instructeur=$res["uid_instructeur"];
		$this->uid_ressource=$res["uid_avion"];
		$this->destination=$res["destination"];
		$this->nbpersonne=$res["nbpersonne"];
		$this->potentiel=$res["potentiel"];
		$this->potentielh=floor($res["potentiel"]/60);
		$this->potentielm=$res["potentiel"]-$this->potentielh*60;
		$this->accept=$res["accept"];
		$this->uid_maj=$res["uid_maj"];
		$this->dte_maj=$res["dte_maj"];

		if ($this->horadeb==0)
		  {
				$query = "SELECT horafin FROM p67_calendrier WHERE dte_fin<='".$res["dte_deb"]."' AND uid_avion='".$res["uid_avion"]."' ORDER BY dte_fin DESC LIMIT 0,1";
				$res2 = $sql->QueryRow($query);
				$this->horadeb=$res2["horafin"];
		  }

	}

	function Valid($k,$v) 
	{
	}

	function AffTemps(){
		$t=abs($this->temps);

		$signe="";
		if ($this->temps<0)
		  { $signe="-"; }
		  	
		return $signe.AffTemps($t);
	}

	function AffTempsReel(){
		$t=abs($this->tpsreel);

		$signe="";
		if ($this->tempsreel<0)
		  { $signe="-"; }
		  	
		return $signe.AffTemps($t);
	}

	function AffPrix(){
		return AffMontant($this->prix);
	}

	function AffDate(){
		$t1=sql2date($this->dte_deb,"jour");
		$t2=sql2date($this->dte_fin,"jour");

		if ($t1!=$t2)
		  { $dte=$t1." - ".$t2; }
		else if (sql2time($this->dte_deb)!="00:00:00")
		  { $dte=$t1." (".sql2time($this->dte_deb,"nosec")." � ".sql2time($this->dte_fin,"nosec").")"; }
		else
		  { $dte=$t1." (N/A)"; }
		return "<a href='reservations.php?rub=reservation&id=".$this->id."'>".$dte."</a>";
	}

	function Save($ValidResa=false)
	{ global $uid,$MyOpt;

		$sql=$this->sql;

		if (($this->dte_deb=="") && ($this->dte_fin==""))
		  { return "La date est obligatoire"; }

		if ($this->dte_deb=="")
		  { $this->dte_fin=$this->dte_deb; }
		else if ($this->dte_fin=="")
		  { $this->dte_deb=$this->dte_fin; }

		// V�rifie la date/heure de d�but
		if ( (!preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4} [^$]*$/",$this->dte_deb)) && (!preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [^$]*$/",$this->dte_deb)) )
		  { return "La date de d�but n'a pas un format correct (jj/mm/aaaa).<br />"; }
		if (!preg_match("/^[^ ]* [0-9]{1,2}(:[0-9]{1,2}(:[0-9]{1,2})?)?$/",$this->dte_deb))
		  { return "L'heure de d�but n'a pas un format correct (hh:mm:ss).<br />"; }

		$hdeb=preg_replace('/^[^ ]* ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\1', $this->dte_deb);
		$mdeb=preg_replace('/^[^ ]* ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\2', $this->dte_deb);
		$dte=date2sql($this->dte_deb,"jour")." $hdeb".(($mdeb!="") ? ":$mdeb" : ":00");
		$this->dte_deb=$dte;

		// V�rifie la date/heure de fin
		if ( (!preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2,4} [^$]*$/",$this->dte_fin)) && (!preg_match("/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2} [^$]*$/",$this->dte_fin)) )
		  { return "La date de fin n'a pas un format correct (jj/mm/aaaa).<br />"; }
		if (!preg_match("/^[^ ]* [0-9]{1,2}(:[0-9]{1,2}(:[0-9]{1,2})?)?$/",$this->dte_fin))
		  { return "L'heure de fin n'a pas un format correct (hh:mm:ss).<br />"; }

		$hdeb=preg_replace('/^[^ ]* ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\1', $this->dte_fin);
		$mdeb=preg_replace('/^[^ ]* ([0-9]{1,2}):?([0-9]{1,2})?:?([0-9]{1,2})?$/','\\2', $this->dte_fin);
		$dte=date2sql($this->dte_fin,"jour")." $hdeb".(($mdeb!="") ? ":$mdeb" : ":00");
		$this->dte_fin=$dte;

		if (date_diff_txt($this->dte_deb,$this->dte_fin)<0) 
		  {
			$dte=$this->dte_fin;
			$this->dte_fin=$this->dte_deb;
			$this->dte_deb=$dte;
		  }

		if (!is_numeric($this->uid_pilote))
		  { return "Erreur avec l'id pilote"; }

		if (!is_numeric($this->uid_debite))
		  { return "Erreur avec l'id du debite"; }

		if (!is_numeric($this->uid_instructeur))
		  { return "Erreur avec l'id isntructeur"; }

		if (!is_numeric($this->uid_ressource))
		  { return "Il faut s�lectionner un avion.<br />"; }

		if ($this->destination=="")
		  { return "La destination est obligatoire"; }

		if (!is_numeric($this->tpsestime))
	  	  { $this->tpsestime=0; }

		if ($this->tpsestime==0)
	  	  { return "Vous devez saisir un temps de vol estim�.<br />"; }


		if (!is_numeric($this->tpsreel))
	  	  { $this->tpsreel=0; }

		if (!is_numeric($this->potentielh))
	  	  { $this->potentielh=0; }
		if (!is_numeric($this->potentielm))
	  	  { $this->potentielm=0; }

		$this->potentiel=$this->potentielh*60+$this->potentielm;
		if (!is_numeric($this->potentiel))
	  	  { $this->potentiel=0; }

		// V�rifie si la saisi d'horametre est coh�rente
		$this->horadeb=preg_replace("/,/",".",$this->horadeb);
		$this->horafin=preg_replace("/,/",".",$this->horafin);
		if (!is_numeric($this->horadeb))
	  	  { $this->horadeb=0; }
		if (!is_numeric($this->horafin))
	  	  { $this->horafin=0; }

		if ($this->horafin==0)
				{ $this->horadeb=0; }

		// Tps horametre >= Tps r�el
		// Deb horametre >= dernier vol

		// V�rifie si les conditions ont �t� accept�es
		if ($this->accept!="oui")
		  { $this->accept="non"; }

		if (($MyOpt["ChkValidResa"]=="oui") && ($this->accept!="oui") && ($ValidResa==false))
		  { return "Vous devez accepter les conditions de vol.<br />"; }



		// V�rifie si la r�servation n'empi�te pas sur une autre
		$query ="SELECT cal.*,usr.nom AS nom ,usr.prenom AS prenom,usr.initiales,ins.nom AS insnom,ins.prenom AS insprenom,avion.immatriculation ";
		$query.="FROM ".$this->tbl."_calendrier AS cal ";
		$query.="LEFT JOIN ".$this->tbl."_utilisateurs AS usr ON cal.uid_pilote=usr.id ";
		$query.="LEFT JOIN ".$this->tbl."_utilisateurs AS ins ON cal.uid_instructeur=ins.id ";
		$query.="LEFT JOIN ".$this->tbl."_ressources AS avion ON cal.uid_avion=avion.id ";
		$query.="WHERE cal.actif='oui' ";
		$query.="AND cal.uid_avion='$this->uid_ressource' ";
		$query.="AND cal.dte_deb<'$this->dte_fin' ";
		$query.="AND cal.dte_fin>'$this->dte_deb' ";
		$query.="AND cal.id<>'$this->id' ORDER BY dte_deb";
	

		$sql->Query($query);
		$msg_err_t="<U>Cette r�servation est � la m�me heure que la/les r�servations suivantes</U> :<BR>";
		$okresa=0;
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$msg_err_t.="&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;".$sql->data["immatriculation"]." - ".ucwords($sql->data["prenom"])." ".strtoupper($sql->data["nom"])." ";
			if ($sql->data["insnom"]!="")
				{
					$msg_err_t.="avec ".ucwords($sql->data["insprenom"])." ".strtoupper($sql->data["insnom"])." ";
				}
			$msg_err_t.="de ".sql2date($sql->data["dte_deb"])." � ".sql2date($sql->data["dte_fin"]).".</ br>";

			$okresa=1;
		  }


		// V�rifie si l'instructeur est disponible
		if ($this->uid_instructeur>0)
		  {
			$query ="SELECT cal.*,usr.nom AS nom ,usr.prenom AS prenom,usr.initiales,ins.nom AS insnom,ins.prenom AS insprenom,avion.immatriculation ";
			$query.="FROM ".$this->tbl."_calendrier AS cal ";
			$query.="LEFT JOIN ".$this->tbl."_utilisateurs AS usr ON cal.uid_pilote=usr.id ";
			$query.="LEFT JOIN ".$this->tbl."_utilisateurs AS ins ON cal.uid_instructeur=ins.id ";
			$query.="LEFT JOIN ".$this->tbl."_ressources AS avion ON cal.uid_avion=avion.id ";
			$query.="WHERE cal.actif='oui' ";
			$query.="AND ((cal.uid_instructeur>0 AND cal.uid_instructeur='$this->uid_instructeur') OR (cal.uid_pilote='$this->uid_instructeur')) ";
			$query.="AND cal.dte_deb<'$this->dte_fin' ";
			$query.="AND cal.dte_fin>'$this->dte_deb' ";
			$query.="AND cal.id<>'$this->id' ORDER BY dte_deb";

			$sql->Query($query);
			$msg_err_t="<U>L'instructeur a d�j� une r�servation � cette heure l�</U> :<BR>";
			$okresa=0;
			for($i=0; $i<$sql->rows; $i++)
			  { 
				$sql->GetRow($i);
				$msg_err_t.="&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;".$sql->data["immatriculation"]." - ".ucwords($sql->data["prenom"])." ".strtoupper($sql->data["nom"])." ";
				if ($sql->data["insnom"]!="")
					{
						$msg_err_t.="avec ".ucwords($sql->data["insprenom"])." ".strtoupper($sql->data["insnom"])." ";
					}
				$msg_err_t.="de ".sql2date($sql->data["dte_deb"])." � ".sql2date($sql->data["dte_fin"]).".</ br>";
	
				$okresa=1;
			  }
		  }

		// Traite les erreurs
		
		if ($okresa==1)
		  {
		  	return $msg_err_t;
		  }

		if ($this->id==0)
		  {
			$query="INSERT INTO ".$this->tbl."_calendrier SET uid_maj=$uid, description='', edite='$this->edite'";
			$this->id=$sql->Insert($query);

			$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
			$query.="VALUES (NULL , 'reservation', '".$this->tbl."_calendrier', '".$this->id."', '$uid', NOW(), 'ADD', 'Create schedule')";
			$sql->Insert($query);
		  }

		// Met � jour les infos
		$query ="UPDATE ".$this->tbl."_calendrier SET ";
		$query.="description='".addslashes($this->description)."',";
		$query.="dte_deb='$this->dte_deb',";
		$query.="dte_fin='$this->dte_fin',";
		$query.="uid_pilote='$this->uid_pilote',";
		$query.="uid_debite='$this->uid_debite',";
		$query.="uid_instructeur='$this->uid_instructeur',";
		$query.="uid_avion='$this->uid_ressource',";
		$query.="destination='$this->destination',";
		$query.="nbpersonne='$this->nbpersonne',";
		$query.="accept='$this->accept',";
		$query.="tpsestime='$this->tpsestime',";
		$query.="tpsreel='$this->tpsreel',";
		$query.="horadeb='$this->horadeb',";
		$query.="horafin='$this->horafin',";
		$query.="potentiel='$this->potentiel',";
		$query.="uid_maj=$uid, dte_maj=NOW() ";
		$query.="WHERE id=$this->id";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'reservation', '".$this->tbl."_calendrier', '".$this->id."', '$uid', NOW(), 'MOD', 'Modify schedule')";
		$sql->Insert($query);

		$query ="DELETE FROM ".$this->tbl."_masses ";
		$query.="WHERE uid_vol='$this->id' AND uid_place=1";
		$sql->Delete($query);

		if ($this->uid_instructeur>0)
		  {
			$query ="DELETE FROM ".$this->tbl."_masses ";
			$query.="WHERE uid_vol='$this->id' AND uid_place=2";
			$sql->Delete($query);
		  }

		return "";
	}

	function Delete()
	{ global $uid;
		$sql=$this->sql;
		$query="UPDATE ".$this->tbl."_calendrier SET actif='non', uid_maj=$uid, dte_maj=NOW() WHERE id='$this->id'";
		$sql->Update($query);

		$query ="INSERT INTO ".$this->tbl."_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
		$query.="VALUES (NULL , 'reservation', '".$this->tbl."_calendrier', '".$this->id."', '$uid', NOW(), 'DEL', 'Delete schedule')";
		$sql->Insert($query);
	}

	function Historique()
	{
		$sql=$this->sql;
		$query="SELECT * FROM ".$this->tbl."_historique WHERE class='reservation' AND `table`='".$this->tbl."_calendrier' AND idtable='$this->id'";
		$sql->Query($query);

		$res=array();
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$res[$i]["uid"]=$sql->data["uid_maj"];
			$res[$i]["dte"]=$sql->data["dte_maj"];
			$res[$i]["type"]=$sql->data["type"];
			$res[$i]["comment"]=$sql->data["comment"];
		  }
	
		return $res;
	}	
}



function GetReservation($sql,$jstart,$jend,$ress)
{ global $MyOpt;
	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id,".$MyOpt["tbl"]."_calendrier.dte_deb,".$MyOpt["tbl"]."_calendrier.dte_fin FROM ".$MyOpt["tbl"]."_calendrier WHERE ".$MyOpt["tbl"]."_calendrier.actif='oui' AND dte_fin>='".$jstart." 00:00:00' AND dte_deb<='".$jend." 00:00:00' AND uid_avion='$ress' ORDER BY ".$MyOpt["tbl"]."_calendrier.dte_deb";
	$res=array();
	$sql->Query($query);
	$iii=0;
	for($i=0; $i<$sql->rows; $i++)
	  {
			$sql->GetRow($i);

			$nbj=date_diff_txt(date("Y-m-d",strtotime($sql->data["dte_deb"])),date("Y-m-d",strtotime($sql->data["dte_fin"])))/86400;

			$res[$iii]["id"]=$sql->data["id"];
	  	$res[$iii]["deb"]=$sql->data["dte_deb"];
	  	$res[$iii]["fin"]=$sql->data["dte_fin"];
			$iii=$iii+1;
	  }

	return $res;
}	


function ListReservation($sql,$id,$idavion=0,$top=0,$where="")
{ global $MyOpt;
	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id FROM ".$MyOpt["tbl"]."_calendrier WHERE ".$MyOpt["tbl"]."_calendrier.actif='oui' ".(($id>0) ? "(".$MyOpt["tbl"]."_calendrier.uid_pilote='$id' OR ".$MyOpt["tbl"]."_calendrier.uid_instructeur='$id')" :"")." ".(($idavion>0) ? "AND ".$MyOpt["tbl"]."_calendrier.uid_avion=$idavion" : "")." ORDER BY dte_deb DESC ".(($top>0) ? "LIMIT 0,$top" : "");
	$lstress=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$lstress[$i]=$sql->data["id"];
	  }

	return $lstress;
}	

function RechercheReservation($sql,$where="")
{ global $MyOpt;
		$query="SELECT id FROM ".$MyOpt["tbl"]."_calendrier WHERE $where ORDER BY dte_deb";
		$sql->Query($query);
		$lstress=array();
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$lstress[$i]=$sql->data["id"];
		  }

	return $lstress;
}

function ListLastReservation($sql,$id,$idavion=0,$top=0,$dte)
{ global $MyOpt;
	$dte=date2sql(sql2date($dte,"jour"));

	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id FROM ".$MyOpt["tbl"]."_calendrier WHERE ".$MyOpt["tbl"]."_calendrier.actif='oui' ".(($id>0) ? "(".$MyOpt["tbl"]."_calendrier.uid_pilote='$id' OR ".$MyOpt["tbl"]."_calendrier.uid_instructeur='$id')" :"")." ".(($idavion>0) ? "AND ".$MyOpt["tbl"]."_calendrier.uid_avion=$idavion" : "")." AND dte_deb<='$dte 23:59:59' ORDER BY dte_deb DESC ".(($top>0) ? "LIMIT 0,$top" : "");

	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }

	return $res;
}	


function ListReservationPaye($sql,$id)
{ global $MyOpt;
	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id FROM ".$MyOpt["tbl"]."_calendrier WHERE (".$MyOpt["tbl"]."_calendrier.uid_pilote='$id' OR ".$MyOpt["tbl"]."_calendrier.uid_instructeur='$id') AND ".$MyOpt["tbl"]."_calendrier.edite='non' AND actif='oui' ORDER BY dte_deb DESC";
	$res=array();
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$res[$i]=$sql->data["id"];
	  }

	return $res;
}	

function ListReservationVols($sql,$id)
{ global $MyOpt;
	$query="SELECT ".$MyOpt["tbl"]."_calendrier.id FROM ".$MyOpt["tbl"]."_calendrier WHERE (".$MyOpt["tbl"]."_calendrier.uid_pilote='$id' OR ".$MyOpt["tbl"]."_calendrier.uid_instructeur='$id') AND ".$MyOpt["tbl"]."_calendrier.tpsreel>0 AND actif='oui' ORDER BY dte_deb DESC";
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
