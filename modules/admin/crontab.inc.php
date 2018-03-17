<?
// ---------------------------------------------------------------------------------------------
//   Crontab - Variables
//     ($Author: miniroot $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
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
*/
?>

<?
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("crontab.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie le droit d'accès
	if (!GetDroit("AccesConfigCrontab")) { FatalError("Accès non autorisé (AccesConfigCrontab)"); }

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);


// ---- Execute les scripts
	if (($fonc=="start") && ($id>0))
	{
		$query="SELECT * FROM ".$MyOpt["tbl"]."_cron WHERE id='".$id."'";
		$res=$sql->QueryRow($query);
		
		if ($res["id"]>0)
		{
			$gl_mode="batch";
			$gl_myprint_txt="";
			$gl_id=$id;
			$gl_res="";
			$mod=$sql->data["module"];
			require("modules/".$sql->data["module"]."/".$sql->data["script"].".cron.php");

			$q="UPDATE ".$MyOpt["tbl"]."_cron SET lastrun='".now()."', txtretour='".$gl_res."', txtlog='".addslashes($gl_myprint_txt)."' WHERE id='".$gl_id."'";
			$sql->Update($q);
			
			$tmpl_x->assign("aff_resultat",nl2br(htmlentities(utf8_decode($gl_myprint_txt),ENT_HTML5,"ISO-8859-1")));
			$tmpl_x->parse("corps.resultat");
		}
	}

// ---- Entete du tableau

	$tabTitre=array();
	$tabTitre["description"]["aff"]="Description";
	$tabTitre["description"]["width"]=400;
	$tabTitre["schedule"]["aff"]="Schedule";
	$tabTitre["schedule"]["width"]=100;
	$tabTitre["lastrun"]["aff"]="Dernière exécution";
	$tabTitre["lastrun"]["width"]=200;
	$tabTitre["nextrun"]["aff"]="Prochaine exécution";
	$tabTitre["nextrun"]["width"]=200;
	$tabTitre["resultat"]["aff"]="Résultat";
	$tabTitre["resultat"]["width"]=100;
	$tabTitre["actif"]["aff"]="Actif";
	$tabTitre["actif"]["width"]=100;
	$tabTitre["action"]["aff"]="Action";
	$tabTitre["action"]["width"]=100;


// ---- Charge la liste des taches planifiées
	$query="SELECT * FROM ".$MyOpt["tbl"]."_cron";
	$sql->Query($query);

	$tabValeur=array();

	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);

		$tabValeur[$i]["description"]["val"]=$sql->data["description"];
		$tabValeur[$i]["schedule"]["val"]=$sql->data["schedule"];
		$tabValeur[$i]["schedule"]["aff"]="<div id='schedule_".$sql->data["id"]."' class='fieldAdmin'><a id='schedule_".$sql->data["id"]."_a' onClick='SwitchEdit(\"schedule\",".$sql->data["id"].")'>".$sql->data["schedule"]."</a></div>";
		$tabValeur[$i]["lastrun"]["val"]=$sql->data["lastrun"];
		$tabValeur[$i]["nextrun"]["val"]=sql2date($sql->data["nextrun"]);
		$tabValeur[$i]["resultat"]["val"]=sql2date($sql->data["txtretour"]);
		$tabValeur[$i]["actif"]["val"]=$sql->data["actif"];
		$tabValeur[$i]["actif"]["aff"]="<div id='actif_".$sql->data["id"]."' class='fieldAdmin'><a id='actif_".$sql->data["id"]."_val' onClick='SwitchOn(\"actif\",".$sql->data["id"].")'>".$sql->data["actif"]."</a></div>";
		$tabValeur[$i]["action"]["val"]="Démarrer";
		$tabValeur[$i]["action"]["aff"]="<div class='fieldAdmin'><a href='index.php?mod=admin&rub=crontab&id=".$sql->data["id"]."&fonc=start'>Démarrer</a></div>";
	}
	
	if ($order=="") { $order="groupe"; }
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
