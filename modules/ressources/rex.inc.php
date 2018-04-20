<?
/*
    SoceIt v2.4
    Copyright (C) 2018 Matthieu Isorez

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
	$tmpl_x = new XTemplate (MyRep("rex.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	require_once ("class/rex.inc.php");

// ---- Affiche le menu
	$aff_menu="";
	require("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Liste des ressources
	$tabTitre=array();
	$tabTitre["dte"]["aff"]="Date";
	$tabTitre["dte"]["width"]=150;
	$tabTitre["titre"]["aff"]="Titre";
	$tabTitre["titre"]["width"]=150;
	$tabTitre["status"]["aff"]="Status";
	$tabTitre["status"]["width"]=150;
	$tabTitre["categorie"]["aff"]="Catégorie";
	$tabTitre["categorie"]["width"]=150;

	$lst=ListRex($sql,array("dte_rex","titre","status","categorie"));

	$tabValeur=array();
	foreach($lst as $i=>$d)
	{
		$tabValeur[$i]["dte"]["val"]=strtotime($d["dte_rex"]);
		$tabValeur[$i]["dte"]["aff"]="<a href='index.php?mod=ressources&rub=rexdetail&id=".$d["id"]."'>".sql2date($d["dte_rex"])."</a>";
		$tabValeur[$i]["titre"]["val"]=$d["titre"];
		$tabValeur[$i]["titre"]["aff"]=$d["titre"];
		$tabValeur[$i]["status"]["val"]=$d["status"];
		$tabValeur[$i]["status"]["aff"]=$tabValeurRex[$d["status"]];
		$tabValeur[$i]["categorie"]["val"]=$d["categorie"];
		$tabValeur[$i]["categorie"]["aff"]=$d["categorie"];
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