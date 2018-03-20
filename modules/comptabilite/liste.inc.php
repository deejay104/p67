<?
// ---------------------------------------------------------------------------------------------
//   Saisie des mouvements
//     ($Author: miniroot $)
//     ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 fÃ©vr. 2016) $)
//     ($Revision: 445 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v1.0
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
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("liste.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	if (!GetDroit("AccesPageListeComptes")) { FatalError("Accès non autorisé"); }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche les infos
	$tabTitre=array();
	$tabTitre["prenom"]["aff"]="Prénom";
	$tabTitre["prenom"]["width"]=150;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=250;
	$tabTitre["solde"]["aff"]="Solde";
	$tabTitre["solde"]["width"]=120;
	if (($MyOpt["module"]["aviation"]=="on") && ($theme!="phone"))
	  {
		$tabTitre["lastflight"]["aff"]="Vols/12mois";
		$tabTitre["lastflight"]["width"]=80;
	  }
	$tabTitre["decouvert"]["aff"]="Découvert";
	$tabTitre["decouvert"]["width"]=70;

	$lstusr=ListActiveUsers($sql,"std",$MyOpt["restrict"]["comptes"],"");

	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$usr = new user_class($id,$sql,false);
		$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
		$tabValeur[$i]["prenom"]["aff"]=$usr->aff("prenom");
		$tabValeur[$i]["nom"]["val"]=$usr->nom;
		$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");
		$tabValeur[$i]["solde"]["val"]=$usr->CalcSolde();
		$tabValeur[$i]["solde"]["aff"]=$usr->AffSolde()."&nbsp;&nbsp;";
		$tabValeur[$i]["solde"]["align"]="right";
		if ($MyOpt["module"]["aviation"]=="on")
		  {
			$tabValeur[$i]["lastflight"]["val"]=$usr->NbHeures12mois();
			$tabValeur[$i]["lastflight"]["aff"]=$usr->AffNbHeures12mois()."&nbsp;&nbsp;";
		  }
		$tabValeur[$i]["lastflight"]["align"]="right";
		$tabValeur[$i]["decouvert"]["val"]=$usr->data["decouvert"];
		$tabValeur[$i]["decouvert"]["aff"]=$usr->data["decouvert"]."&nbsp;&nbsp;";
		$tabValeur[$i]["decouvert"]["align"]="right";
	  }

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

// ---- Affecte les variables d'affichage
	if (GetModule("aviation"))
	  {  	$tmpl_x->parse("infos.vols"); }

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
