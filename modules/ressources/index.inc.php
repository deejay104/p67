<?
// ---------------------------------------------------------------------------------------------
//   Liste des membres
//     ($Author: miniroot $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
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
*/
?>

<?
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	require_once ("class/ressources.inc.php");

// ---- Liste des ressources
	$tabTitre=array();
	$tabTitre["immatriculation"]["aff"]="Immatriculation";
	$tabTitre["immatriculation"]["width"]=150;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=150;
	$tabTitre["hora"]["aff"]="Temps Vol";
	$tabTitre["hora"]["width"]=150;

	$lstusr=ListeRessources($sql);

	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$usr = new ress_class($id,$sql,false);
		$tabValeur[$i]["immatriculation"]["val"]=$usr->immatriculation;
		$tabValeur[$i]["immatriculation"]["aff"]=$usr->aff("immatriculation");
		$tabValeur[$i]["nom"]["val"]=$usr->nom;
		$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");


// **************************************
//	Calcul du temps de vol
		// Récupère la date de la dernière maintenance
		$query="SELECT dte_fin,idmaint FROM p67_calendrier WHERE idmaint>0 AND dte_fin<=NOW() AND uid_avion='".$usr->id."' ORDER BY dte_fin DESC LIMIT 0,1";
		$res["first"]=$sql->QueryRow($query);
	
		$query="SELECT potentiel AS tot FROM p67_maintenance WHERE id='".$res["first"]["idmaint"]."'";
		$respot=$sql->QueryRow($query);
		
//		$query="SELECT SUM(tpsestime) AS tot FROM p67_calendrier WHERE dte_deb>='".$res["first"]["dte_fin"]."' AND dte_fin<='NOW()' AND tpsreel=0 AND actif='oui' AND uid_avion='".$resa["resa"]->uid_ressource."'";
//		$resestim=$sql->QueryRow($query);
	
		$query="SELECT SUM(tpsreel) AS tot FROM p67_calendrier WHERE dte_deb>='".$res["first"]["dte_fin"]."' AND dte_fin<='NOW()' AND tpsreel<>0 AND actif='oui' AND uid_avion='".$usr->id."'";
		$resreel=$sql->QueryRow($query);
	
		$t=$respot["tot"]+$resestim["tot"]+$resreel["tot"];

// **************************************

		$tabValeur[$i]["hora"]["val"]=(($t>0) ? $t : "0");
		$tabValeur[$i]["hora"]["aff"]="<A href='ressources.php?rub=detail&id=$id'>".AffTemps($t)."</a>";
	  }

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
