<?
// ---------------------------------------------------------------------------------------------
//   Edition des comptes
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    Easy Aero v2.4
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
	$tmpl_x = new XTemplate (MyRep("comptes.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables

	if (!GetDroit("AccesConfigComptes")) { FatalError("Accès non autorisé (AccesConfigComptes)"); }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Enregistre les modifications
	if (($fonc=="Enregistrer") && (is_array($form_numcpt)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
	  	foreach($form_numcpt as $id=>$num)
	  	{
			if ($num!="")
			{
				$sql->Edit("numcompte",$MyOpt["tbl"]."_numcompte",$id,array("numcpt"=>$num,"description"=>$form_description[$id]));
			}
		}
	}

// ---- Supprime un poste
	if (($fonc=="delete") && ($id>0))
	{
		$sql->Edit("numcompte",$MyOpt["tbl"]."_numcompte",$id,array("actif"=>"non"));		
	}

// ---- Affiche la page demandée

	// Liste des mouvements
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_numcompte WHERE actif='oui' ORDER BY numcpt";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
	
		$tmpl_x->assign("form_id", $sql->data["id"]);
		$tmpl_x->assign("form_numcpt", $sql->data["numcpt"]);
		$tmpl_x->assign("form_description", $sql->data["description"]);
		$tmpl_x->parse("corps.lst_mouvement");
	  }

	// Ligne vide

	$tmpl_x->assign("form_id", "0");
	$tmpl_x->assign("form_numcpt", "");
	$tmpl_x->assign("form_description", "");
	$tmpl_x->parse("corps.lst_mouvement");

	$_SESSION['tab_checkpost'][$checktime]=$checktime;


	$tmpl_x->parse("corps.aff_mouvement");


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
