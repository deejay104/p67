<?
// ---------------------------------------------------------------------------------------------
//   Liste des abonnements
//     ($Author: miniroot $)
//     ($Date: 2007-04-23 21:41:11 $)
//     ($Revision: 378 $)
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
	if (!GetDroit("AccesAbonnements")) { FatalError("Accès non authorisé (AccesAbonnements)"); }

	require_once ("class/abonnement.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("abonnements.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialise les variables

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);


// ---- Affiche la liste des abonnements

	// Définition des variables
	$myColor[50]="F0F0F0";
	$myColor[60]="F7F7F7";
	if (!is_numeric($start))
	  { $start = 0; }

	// Entete du tableau d'affichage
	$tabTitre=array();
	$tabTitre["num"]["aff"]="Numéro";
	$tabTitre["num"]["width"]=85;
	$tabTitre["prenom"]["aff"]="Prénom";
	$tabTitre["prenom"]["width"]=100;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=150;
	$tabTitre["dte"]["aff"]="Période";
	$tabTitre["dte"]["width"]=300;
	$tabTitre["actif"]["aff"]="Actif";
	$tabTitre["actif"]["width"]=50;
	$tabTitre["total"]["aff"]="Prix";
	$tabTitre["total"]["width"]=60;

	$lstusr=ListAbonnement($sql,0);

	foreach($lstusr as $i=>$aboid)
	  {
	  	$abo = new abonnement_class($aboid,$sql);
		$usr = new user_class($abo->uid,$sql,false);
		$tabValeur[$i]["num"]["val"]=$abo->abonum;
		$tabValeur[$i]["num"]["aff"]="<a href=index.php?mod=facturation&rub=abonnement&id=".$abo->id.">".$abo->abonum."</a>";
		$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
		$tabValeur[$i]["prenom"]["aff"]=$usr->aff("prenom","val");
		$tabValeur[$i]["nom"]["val"]=$usr->nom;
		$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom","val");
		$tabValeur[$i]["dte"]["val"]=strtotime(date2sql($abo->dte_deb));
		$tabValeur[$i]["dte"]["aff"]="Du ".$abo->dte_deb. " au ".$abo->dte_fin;
		$tabValeur[$i]["actif"]["val"]=(($abo->Actif()) ? "oui" : "non");
		$tabValeur[$i]["actif"]["aff"]=(($abo->Actif()) ? "oui" : "non");
		
		$abo->LoadLignes();
		$tabValeur[$i]["total"]["val"]=$abo->total;
		$tabValeur[$i]["total"]["aff"]=$abo->total;
	  }

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=&$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=&$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=&$tmpl_x->text("corps");

?>
