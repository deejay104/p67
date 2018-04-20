<?
// ---------------------------------------------------------------------------------------------
//   Page de liste des maintenances
//   
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
    Copyright (C) 2007 Matthieu Isorez

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
	require_once ("class/maintenance.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("liste.htm"));
	$tmpl_x->assign("path_module","$module/$mod");


// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche les liens
	if (GetDroit("CreeMaintenance"))
	  { $tmpl_x->parse("corps.creemaint"); }

// ---- Affiche la liste des maintenances

	if (!is_numeric($ress))
	  { $ress=0; }

	$tabTitre=array();
	$tabTitre["ress"]["aff"]="Avion";
	$tabTitre["ress"]["width"]=70;
	$tabTitre["dte_deb"]["aff"]="Début";
	$tabTitre["dte_deb"]["width"]=100;
	$tabTitre["dte_fin"]["aff"]="Fin";
	$tabTitre["dte_fin"]["width"]=100;
	$tabTitre["status"]["aff"]="Status";
	$tabTitre["status"]["width"]=100;
	$tabTitre["atelier"]["aff"]="Atelier";
	$tabTitre["atelier"]["width"]=220;

	$lstFiche=GetAllMaintenance($sql,$ress);
	$tabValeur=array();

	if (count($lstFiche)>0)
	  {
		foreach($lstFiche as $i=>$id)
		  {
			$maint = new maint_class($id,$sql);

			$ress = new ress_class($maint->uid_ressource,$sql,false);
			$tabValeur[$i]["ress"]["val"]=$ress->aff("immatriculation","val");
			$tabValeur[$i]["ress"]["aff"]=$ress->aff("immatriculation");
			
			$tabValeur[$i]["dte_deb"]["val"]=strtotime($maint->dte_deb);
			$tabValeur[$i]["dte_deb"]["aff"]="<a href='index.php?mod=ressources&rub=detailmaint&id=$id'>".$maint->aff("dte_deb")."</a>";
			$tabValeur[$i]["dte_fin"]["val"]=strtotime($maint->dte_fin);
			$tabValeur[$i]["dte_fin"]["aff"]="<a href='index.php?mod=ressources&rub=detailmaint&id=$id'>".$maint->aff("dte_fin")."</a>";

			$tabValeur[$i]["status"]["val"]=$maint->aff("status");
			$tabValeur[$i]["status"]["aff"]="<a href='index.php?mod=ressources&rub=detailmaint&id=$id'>".$maint->aff("status")."</a>";

			$tabValeur[$i]["atelier"]["val"]=$maint->nom_atelier;
			$tabValeur[$i]["atelier"]["aff"]=$maint->nom_atelier;
	
		  }

		if ($order=="") { $order="dte_deb"; }
		if ($trie=="") { $trie="i"; }
		
		$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));
	  }
	else
	  {
		$tmpl_x->assign("aff_tableau","-Aucune maintenance de saisie-");
	  }



// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>