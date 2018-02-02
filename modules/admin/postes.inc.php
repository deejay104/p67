<?
// ---------------------------------------------------------------------------------------------
//   Edition des postes
//     ($Author: miniroot $)
//     ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 fÃ©vr. 2016) $)
//     ($Revision: 445 $)
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
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("postes.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables

	if (!GetDroit("AccesConfigPostes")) { FatalError("Accès non autorisé (AccesConfigPostes)"); }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Enregistre les modifications
	if (($fonc=="Enregistrer") && (is_array($form_poste)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
	  	foreach($form_poste as $id=>$description)
	  	  {
	  	  	if ($id>0)
	  	  	  {
				$query="UPDATE p67_mouvement SET ordre='".substr(trim($form_ordre[$id]),0,4)."', description='".addslashes(trim($description))."', compte='".$form_compte[$id]."', debiteur='".$form_debiteur[$id]."', crediteur='".$form_crediteur[$id]."', montant='".$form_montant[$id]."', ";
				$query.=" j0='".$form_j0[$id]."',";
				$query.=" j1='".$form_j1[$id]."',";
				$query.=" j2='".$form_j2[$id]."',";
				$query.=" j3='".$form_j3[$id]."',";
				$query.=" j4='".$form_j4[$id]."',";
				$query.=" j5='".$form_j5[$id]."',";
				$query.=" j6='".$form_j6[$id]."',";
				$query.=" j7='".$form_j7[$id]."'";
				$query.=" WHERE id='$id'";
				$sql->Update($query);

				$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
				$query.="VALUES (NULL , 'poste', 'p67_mouvement', '$id', '$uid', '".now()."', 'MOD', 'Update movement')";
				$sql->Insert($query);
			  }
			else if (trim($description)!="")
	  	  	  {
				$query="INSERT p67_mouvement SET ordre='".substr(trim($form_ordre[$id]),0,4)."', description='". addslashes(trim($description))."', compte='".$form_compte[$id]."', debiteur='".$form_debiteur[$id]."', crediteur='".$form_crediteur[$id]."', montant='".$form_montant[$id]."',";
				$query.=" j0='".$form_j0[$id]."',";
				$query.=" j1='".$form_j1[$id]."',";
				$query.=" j2='".$form_j2[$id]."',";
				$query.=" j3='".$form_j3[$id]."',";
				$query.=" j4='".$form_j4[$id]."',";
				$query.=" j5='".$form_j5[$id]."',";
				$query.=" j6='".$form_j6[$id]."',";
				$query.=" j7='".$form_j7[$id]."'";
				$sql->Insert($query);

				$query ="INSERT INTO p67_historique (`id` ,`class` ,`table` ,`idtable` ,`uid_maj` ,`dte_maj` ,`type` ,`comment`) ";
				$query.="VALUES (NULL , 'poste', 'p67_mouvement', '$id', '$uid', '".now()."', 'ADD', 'Create movement')";
				$sql->Insert($query);
			  }
	  	  }
	  }

// ---- Supprime un poste
	if ($fonc=="delete")
	  {
		$query="UPDATE p67_mouvement SET actif='non' WHERE id='$id'";
		$sql->Delete($query);
	  }

// ---- Affiche la page demandée

	// Liste des mouvements
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE actif='oui' ORDER BY ordre,description";
	$sql->Query($query);
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
	
		$tmpl_x->assign("form_id", $sql->data["id"]);
		$tmpl_x->assign("ordre_poste", $sql->data["ordre"]);
		$tmpl_x->assign("nom_poste", $sql->data["description"]);
		$tmpl_x->assign("nom_compte", $sql->data["compte"]);

		$tmpl_x->assign("chk_debiteur_0", "");
		$tmpl_x->assign("chk_debiteur_B", "");
		$tmpl_x->assign("chk_debiteur_C", "");
		$tmpl_x->assign("chk_debiteur_".$sql->data["debiteur"], "selected");

		$tmpl_x->assign("chk_crediteur_0", "");
		$tmpl_x->assign("chk_crediteur_B", "");
		$tmpl_x->assign("chk_crediteur_C", "");
		$tmpl_x->assign("chk_crediteur_".$sql->data["crediteur"], "selected");

		$tmpl_x->assign("montant_poste", $sql->data["montant"]);

		$tmpl_x->assign("j0_poste", $sql->data["j0"]);
		$tmpl_x->assign("j1_poste", $sql->data["j1"]);
		$tmpl_x->assign("j2_poste", $sql->data["j2"]);
		$tmpl_x->assign("j3_poste", $sql->data["j3"]);
		$tmpl_x->assign("j4_poste", $sql->data["j4"]);
		$tmpl_x->assign("j5_poste", $sql->data["j5"]);
		$tmpl_x->assign("j6_poste", $sql->data["j6"]);

		$tmpl_x->assign("j7_poste", $sql->data["j7"]);

		$tmpl_x->parse("corps.lst_mouvement");
	  }

	// Ligne vide

	$tmpl_x->assign("form_id", "0");
	$tmpl_x->assign("ordre_poste", "");
	$tmpl_x->assign("nom_poste", "");
	$tmpl_x->assign("nom_compte", "");

	$tmpl_x->assign("chk_debiteur_0", "");
	$tmpl_x->assign("chk_debiteur_B", "");
	$tmpl_x->assign("chk_debiteur_C", "");

	$tmpl_x->assign("chk_crediteur_0", "");
	$tmpl_x->assign("chk_crediteur_B", "");
	$tmpl_x->assign("chk_crediteur_C", "");

	$tmpl_x->assign("montant_poste", "0");

	$tmpl_x->assign("j0_poste", "");
	$tmpl_x->assign("j1_poste", "");
	$tmpl_x->assign("j2_poste", "");
	$tmpl_x->assign("j3_poste", "");
	$tmpl_x->assign("j4_poste", "");
	$tmpl_x->assign("j5_poste", "");
	$tmpl_x->assign("j6_poste", "");
	$tmpl_x->assign("j7_poste", "");

	$tmpl_x->parse("corps.lst_mouvement");

	$_SESSION['tab_checkpost'][$checktime]=$checktime;


	$tmpl_x->assign("form_page", "mvt");
  $tmpl_x->parse("corps.aff_mouvement");

	if (GetModule("aviation"))
	  {  	$tmpl_x->parse("infos.vols"); }


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
