<?
// ---------------------------------------------------------------------------------------------
//   Suivi des heures de vol
//     ($Author: miniroot $)
//     ($Date: 2016-04-22 20:48:24 +0200 (ven., 22 avr. 2016) $)
//     ($Revision: 456 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
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
*/
?>

<?
	require_once ("class/reservation.inc.php");


// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("carnet.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);


	if (GetDroit("AccesSuiviVols"))
	{
		$tmpl_x->parse("infos.suiviVols");
	}

	$tabTitre=array();
	$tabTitre["dte_deb"]["aff"]="Date";
	$tabTitre["dte_deb"]["width"]=100;
	$tabTitre["nom"]["aff"]="Equipage";
	$tabTitre["nom"]["width"]=350;

	if ($theme!="phone")
	{
		$tabTitre["tarif"]["aff"]="Tarif";
		$tabTitre["tarif"]["width"]=50;
		$tabTitre["dest"]["aff"]="Lieu";
		$tabTitre["dest"]["width"]=100;
		$tabTitre["heure_deb"]["aff"]="Départ";
		$tabTitre["heure_deb"]["width"]=70;
		$tabTitre["heure_fin"]["aff"]="Arrivée";
		$tabTitre["heure_fin"]["width"]=70;
	}
	$tabTitre["heure"]["aff"]="Heures de vol";
	$tabTitre["heure"]["width"]=100;
	if ($theme=="phone")
	{
		$tabTitre["carbavant"]["aff"]="Avant";
		$tabTitre["carbavant"]["width"]=100;
		$tabTitre["carbapres"]["aff"]="Après";
		$tabTitre["carbapres"]["width"]=100;
		$tabTitre["potentiel"]["aff"]="Total";
		$tabTitre["potentiel"]["width"]=100;
	}
	else
	{
		$tabTitre["carbavant"]["aff"]="Carburant Avant";
		$tabTitre["carbavant"]["width"]=100;
		$tabTitre["carbapres"]["aff"]="Carburant Après";
		$tabTitre["carbapres"]["width"]=100;
		$tabTitre["potentiel"]["aff"]="Total heures de vol";
		$tabTitre["potentiel"]["width"]=100;
	}
		
// ---- Chargement des données

	$lstress=ListeRessources($sql);

	foreach($lstress as $i=>$rid)
	{
		$resr=new ress_class($rid,$sql);

		// Initilialise l'id de ressouce s'il est vide
		if (!is_numeric($id))
		{
			$id=$rid;
		}

		// Rempli la liste dans le template
		$tmpl_x->assign("uid_avion", $resr->id);
		$tmpl_x->assign("nom_avion", strtoupper($resr->immatriculation));
		if ($rid==$id)
		{
			$tmpl_x->assign("chk_avion", "selected");
		}
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.lst_avion");
	}

	
	if ($order=="") { $order="dte_deb"; }
	if ($trie=="") { $trie="i"; }
	if (!is_numeric($ts))
	  { $ts = 0; }
	$tl=40;
	$lstresa=ListCarnetVols($sql,$id,$order,$trie,$ts,$tl);
	
	$totligne=ListCarnetNbLignes($sql,$id);
	
	$tabresa=array();
	foreach($lstresa as $i=>$rid)
	{
		$resa = new resa_class($rid,$sql,false);
		$ress = new ress_class($resa->uid_ressource,$sql);
		$usrpil = new user_class($resa->uid_pilote,$sql);
		if ($resa->uid_instructeur>0)
		{ $usrinst = new user_class($resa->uid_instructeur,$sql); }

		$t1=sql2date($resa->dte_deb,"jour");
		$t2=sql2date($resa->dte_fin,"jour");

		if ($t1!=$t2)
		  { $dte=$t1." - ".$t2; }
		else if ((sql2time($resa->dte_deb)!="00:00:00") && ($theme!="phone"))
		  { $dte=$t1." (".sql2time($resa->dte_deb,"nosec")." à ".sql2time($resa->dte_fin,"nosec").")"; }
		else if  ($theme!="phone")
		  { $dte=$t1." (N/A)"; }
		else
		  { $dte=$t1; }

		$tabValeur[$i]["dte_deb"]["val"]=strtotime($resa->dte_deb);
		$tabValeur[$i]["dte_deb"]["aff"]="<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".$t1."</a>";

		$tabValeur[$i]["nom"]["val"]=$usrpil->fullname;
		$tabValeur[$i]["nom"]["aff"]=$usrpil->Aff("fullname").(($resa->uid_instructeur>0) ? " / ".$usrinst->Aff("fullname") : "");

		$tabValeur[$i]["tarif"]["val"]=$resa->tarif;
		$tabValeur[$i]["tarif"]["aff"]=$resa->tarif;
		$tabValeur[$i]["tarif"]["align"]="center";

		$tabValeur[$i]["dest"]["val"]=$resa->destination;
		$tabValeur[$i]["dest"]["aff"]=$resa->destination;

		$tabValeur[$i]["heure_deb"]["val"]=strtotime($resa->dte_deb);
		$tabValeur[$i]["heure_deb"]["aff"]="<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".sql2time($resa->dte_deb,"nosec")."</a>";
		$tabValeur[$i]["heure_deb"]["align"]="center";
		$tabValeur[$i]["heure_fin"]["val"]=strtotime($resa->dte_deb);
		$tabValeur[$i]["heure_fin"]["aff"]="<a href='index.php?mod=reservations&rub=reservation&id=$rid'>".sql2time($resa->dte_fin,"nosec")."</a>";
		$tabValeur[$i]["heure_fin"]["align"]="center";
	
		$tabValeur[$i]["heure"]["val"]=$resa->tpsreel;
		$tabValeur[$i]["heure"]["aff"]=$resa->AffTempsReel();
		$tabValeur[$i]["heure"]["align"]="center";

		$tabValeur[$i]["carbavant"]["val"]=$resa->carbavant;
		$tabValeur[$i]["carbavant"]["aff"]=($resa->carbavant>0) ? $resa->carbavant."L" : " ";
		$tabValeur[$i]["carbavant"]["align"]="center";

		$tabValeur[$i]["carbapres"]["val"]=$resa->carbapres;
		$tabValeur[$i]["carbapres"]["aff"]=($resa->carbapres>0) ? $resa->carbapres."L" : " ";
		$tabValeur[$i]["carbapres"]["align"]="center";

		$tabValeur[$i]["potentiel"]["val"]="";
		$tabValeur[$i]["potentiel"]["aff"]=$resa->AffPotentiel("fin");
		$tabValeur[$i]["potentiel"]["align"]="center";

	}
	
// ---- Affiche le tableau
	$tmpl_x->assign("tab_liste",AfficheTableauFiltre($tabValeur,$tabTitre,$order,$trie,$url="id=$id",$ts,$tl,$totligne));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>