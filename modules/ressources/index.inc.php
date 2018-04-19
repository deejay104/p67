<?
/*
   Easy Aero v2.4
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

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Liste des ressources
	$tabTitre=array();
	$tabTitre["immatriculation"]["aff"]="Immatriculation";
	$tabTitre["immatriculation"]["width"]=150;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=150;
	$tabTitre["hora"]["aff"]="Temps Vol";
	$tabTitre["hora"]["width"]=150;
	$tabTitre["potentiel"]["aff"]="Potentiel";
	$tabTitre["potentiel"]["width"]=150;
	$tabTitre["estimemaint"]["aff"]="Estimation Prochaine maintenance";
	$tabTitre["estimemaint"]["width"]=150;

	$lstusr=ListeRessources($sql);

	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$usr = new ress_class($id,$sql,false);
		$tabValeur[$i]["immatriculation"]["val"]=$usr->immatriculation;
		$tabValeur[$i]["immatriculation"]["aff"]=$usr->aff("immatriculation");
		$tabValeur[$i]["nom"]["val"]=$usr->nom;
		$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");


		$tv=$usr->AffTempsVol();
		$tabValeur[$i]["hora"]["val"]=(($tv>0) ? $tv : "0");
		$tabValeur[$i]["hora"]["aff"]="<A href='index.php?mod=ressources&rub=detail&id=$id'>".$tv."</a>";

		$tp=$usr->AffPotentiel();
		$tabValeur[$i]["potentiel"]["val"]=(($tp>0) ? $tp : "0");
		$tabValeur[$i]["potentiel"]["aff"]="<A href='index.php?mod=ressources&rub=detail&id=$id'>".$tp."</a>";

		$t=$usr->EstimeMaintenance();		
		if (strtotime($t)<time())
		{
			// $t=date("Y-m-d",$t);
		}
		$tabValeur[$i]["estimemaint"]["val"]=$t;
		$tabValeur[$i]["estimemaint"]["aff"]="<A href='index.php?mod=ressources&rub=detail&id=$id'>".sql2date($t,"jour")."</a>";
	}

	if ($order=="") { $order="nom"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	if (GetDroit("CreeRessource"))
	{
		$tmpl_x->parse("corps.ajout");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
