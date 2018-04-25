<?
// ---------------------------------------------------------------------------------------------
//   Détail d'un utilisateur
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
	$tmpl_x = new XTemplate (MyRep("detail.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	require_once ("class/ressources.inc.php");

// ---- Initialisation des variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	$msg_erreur="";
	$msg_confirmation="";

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Modifie les infos
	if (($fonc=="modifier") && GetDroit("ModifRessource"))
	  {
		$typeaff="form";
	  }
	else
	  {
		$typeaff="html";
	  }

// ---- Vérifie la valeur d'entrée
	if ((is_numeric($id)) && ($id>0))
	  {
	  	$ress = new ress_class($id,$sql);
	  }
	else if (GetDroit("CreeRessource"))
	  {
	  	$ress = new ress_class(0,$sql);
		$typeaff="form";
	  }
	else
	  {
		FatalError("Paramètre d'id non valide");
	  }

// ---- Sauvegarde les infos
	if (($fonc=="Enregistrer") && ($id=="") && ((GetDroit("CreeRessource"))) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
			$ress->Create();
			$id=$ress->id;
	  }
	else if (($fonc=="Enregistrer") && ($id=="") && (isset($_SESSION['tab_checkpost'][$checktime])))
	  {
			$typeaff="html";
	  }

	if (($fonc=="Enregistrer") && (GetDroit("ModifRessourceSauve")) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		// Sauvegarde les données
		if (count($form_ress)>0)
		  {
				foreach($form_ress as $k=>$v)
		  	  {
		  			$msg_erreur.=$ress->Valid($k,$v);
		  	  }
		  }

		$ress->Save($uid);
		$msg_confirmation.="Vos données ont été enregistrées.<BR>";

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }


// ---- Supprimer la ressource
	if (($fonc=="delete") && ($id>0) && (GetDroit("SupprimeRessource")))
	  {
			$ress->Delete();
			$rub="index";
			include("modules/ressources/index.inc.php");
			exit;
	  }

	if (($fonc=="desactive") && ($id>0) && (GetDroit("DesactiveRessource")))
	  {
		$ress->Desactive();
	  }
	
// ---- Affiche les infos
	if ((is_numeric($id)) && ($id>0))
	  {
		$ress = new ress_class($id,$sql);
		$usrmaj = new user_class($ress->uid_maj,$sql);

		$tmpl_x->assign("id", $id);
		$tmpl_x->assign("info_maj", $usrmaj->aff("fullname")." le ".sql2date($ress->dte_maj));
	
	  }
	else if (GetDroit("CreeRessource"))
	  {
		$tmpl_x->assign("titre", "Saisie d'une nouvelle ressource");

		$ress = new ress_class("0",$sql);
		$usrmaj = new user_class($usr->uid_maj,$sql);

		$tmpl_x->assign("id", "");
		$tmpl_x->assign("info_maj", $usrmaj->aff("fullname")." le ".sql2date($usr->dte_maj));

		$typeaff="form";
	  }

	foreach($ress->data as $k=>$v)
	  { $tmpl_x->assign("form_$k", $ress->aff($k,$typeaff)); }

	 $tmpl_x->assign("bk_couleur",$ress->data["couleur"]);
	  
	if (($id==$uid) || (GetDroit("ModifRessource")))
	  { $tmpl_x->parse("corps.modification"); }

	if (GetDroit("CreeRessource"))
	  { $tmpl_x->parse("corps.ajout"); }

	if ((GetDroit("DesactiveRessource")) && ($ress->actif=="oui"))
	  { $tmpl_x->parse("corps.desactive"); }

	if ((GetDroit("SupprimeRessource")) && ($ress->actif=="off"))
	  { $tmpl_x->parse("corps.suppression"); }

	if ($typeaff=="form")
	  {
		$tmpl_x->parse("corps.submit");
		if ((is_numeric($id)) && ($id>0))
		  { $tmpl_x->assign("titre", "Modification : ".$ress->nom); }
	  }
	else if ($typeaff=="html")
	  {	$tmpl_x->assign("titre", "Détail de ".$ress->immatriculation); }

	
	$tmpl_x->parse("corps.caracteristique");

 	if (GetDroit("ModifRessourceParametres"))
	  { $tmpl_x->parse("corps.parametres"); }

// ---- Messages
	if ($msg_erreur!="")
	{
		$tmpl_x->assign("msg_erreur", $msg_erreur);
		$tmpl_x->parse("corps.msg_erreur");
	}

	if ($msg_confirmation!="")
	{
		$tmpl_x->assign("msg_confirmation",  $msg_confirmation);
		$tmpl_x->parse("corps.msg_confirmation");
	}

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
