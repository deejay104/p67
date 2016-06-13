<?
// ---------------------------------------------------------------------------------------------
//   Détail d'un utilisateur
//     ($Author: miniroot $)
//     ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 fÃ©vr. 2016) $)
//     ($Revision: 445 $)
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
        if (!is_numeric($id))
          { $id=0; }

	if ( (!GetDroit("AccesMembre")) && (!GetMyId($id)) )
	  { FatalError("Accès non autorisé (AccesMembre)"); }

	require_once ("class/document.inc.php");
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("detail.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialisation des variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	$msg_erreur="";
	$msg_confirmation="";

	if ($id>0)
	  { $usr = new user_class($id,$sql,((GetMyId($id)) ? true : false)); }
	else
	  { $usr = new user_class(0,$sql,false); }


// ---- Sauvegarde les infos
	if (($fonc=="Enregistrer") && (($id=="") || ($id==0)) && ((GetDroit("CreeUser"))) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$usr->Create();
		$id=$usr->uid;
	  }
	else if (($fonc=="Enregistrer") && ($id=="") && (isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$mod="membres";
		$affrub="index";
	  }

	if (($fonc=="Enregistrer") && ((GetMyId($id)) || (GetDroit("ModifUserSauve"))) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		// Sauvegarde les données
		if (count($form_info)>0)
		  {
			foreach($form_info as $k=>$v)
		  	  {
		  		$msg_erreur.=$usr->Valid($k,$v);
		  	  }
		  }

		$usr->Save($uid);
		$msg_confirmation.="Vos données ont été enregistrées.<BR>";

		// Sauvegarde la photo
		$form_photo=$_FILES["form_photo"];
		if ($form_photo["name"]!="")
		  {
			$lstdoc=ListDocument($sql,$id,"avatar");
		  	
			if (count($lstdoc)>0)
			  {
				foreach($lstdoc as $i=>$did)
				  {
					$doc = new document_class($did,$sql);
					$doc->Delete();
				  }
			  }
		  	$doc = new document_class(0,$sql,"avatar");
		  	$doc->droit="ALL";
		  	$doc->Save($id,$_FILES["form_photo"]);
				$doc->Resize(200,240);

			//$msg_erreur.=$usr->SavePicture($form_photo["name"],$form_photo["tmp_name"],$form_photo["type"]);
		  }

		// Sauvegarde un document
		if ($_FILES["form_adddocument"]["name"]!="")
		  {
		  	$doc = new document_class(0,$sql);
		  	$doc->Save($id,$_FILES["form_adddocument"]);
		  }

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }

	// Sauvegarde le lache
	if (($fonc=="Enregistrer") && ($id>0) && (GetDroit("ModifUserLache")))
	  {
		$msg_erreur.=$usr->SaveLache($form_lache,$uid);

	  }

// ---- Supprimer l'utilisateur
	if (($fonc=="delete") && ($id>0) && (GetDroit("SupprimeUser")))
	  {
		$usr->Delete();
		$mod="membres";
		$affrub="index";
	  }

	if (($fonc=="desactive") && ($id>0) && (GetDroit("DesactiveUser")))
	  {
		$usr->Desactive();
	  }

// ---- Modifie les infos
	if (($fonc=="modifier") && ((GetMyId($id)) || (GetDroit("ModifUser"))))
	  {
		$typeaff="form";
	  }
	else
	  {
		$typeaff="html";
	  }
	
// ---- Affiche les infos
	if ((is_numeric($id)) && ($id>0))
	  {
		$usr = new user_class($id,$sql,((GetMyId($id)) ? true : false));
		if (GetModule("aviation"))
		  { $usr->LoadLache(); }
		if (GetModule("creche"))
		  { $usr->LoadEnfants(); }
		$usrmaj = new user_class($usr->uidmaj,$sql);

		$tmpl_x->assign("id", $id);
		$tmpl_x->assign("info_maj", $usrmaj->prenom." ".$usrmaj->nom." le ".sql2date($usr->dtemaj));
	
	  }
	else if (GetDroit("CreeUser"))
	  {
		$tmpl_x->assign("titre", "Saisie d'un nouvel utilisateur");

		$usr = new user_class("0",$sql,false);
		$usrmaj = new user_class($usr->uidmaj,$sql);

		$tmpl_x->assign("id", $id);
		$tmpl_x->assign("info_maj", $usrmaj->prenom." ".$usrmaj->nom." le ".sql2date($usr->dtemaj));

		$typeaff="form";
	  }
	else
	  {
		FatalError("Paramètre d'id non valide");
	  }

	$tmpl_x->assign("unitPoids", $MyOpt["unitPoids"]);

	foreach($usr->data as $k=>$v)
	  { $tmpl_x->assign("form_$k", $usr->aff($k,$typeaff)); }

	if ((GetMyId($id)) || (GetDroit("ModifUser")))
	  { $tmpl_x->parse("infos.modification"); }

	if ((GetMyId($id)) || (GetDroit("ModifUserPassword")))
	  { $tmpl_x->parse("infos.password"); }

	if (GetDroit("CreeUser"))
	  { $tmpl_x->parse("infos.ajout"); }

	if ((GetDroit("DesactiveUser")) && ($usr->actif=="oui"))
	  { $tmpl_x->parse("infos.desactive"); }

	if ((GetDroit("SupprimeUser")) && ($usr->actif=="off"))
	  { $tmpl_x->parse("infos.suppression"); }

	if ($typeaff=="form")
	  {
		$tmpl_x->parse("corps.photos");
		$tmpl_x->parse("corps.submit");
		if ((is_numeric($id)) && ($id>0))
		  { $tmpl_x->assign("titre", "Modification : ".$usr->Aff("prenom")." ".$usr->Aff("nom")); }
	  }
	else if ($typeaff=="html")
	  {	$tmpl_x->assign("titre", "Détail de ".$usr->Aff("prenom")." ".$usr->Aff("nom")); }

	if (($typeaff=="form") && ((GetMyId($id)) || (GetDroit("ModifUserPassword"))))
	  {
		$tmpl_x->parse("corps.modif_mdp");
	  }

	$tmpl_x->parse("corps.type");

	if (((GetDroit("ModifUserDecouvert")) || (GetMyId($id))) && (GetModule("compta")))
	  { $tmpl_x->parse("corps.decouvert"); }

	if ((((GetDroit("ModifUserTarif")) || (GetMyId($id))) && (GetModule("compta"))) && (GetModule("aviation")))
	  { $tmpl_x->parse("corps.tarif"); }

	if (GetDroit("ModifUserDroits"))
	  { $tmpl_x->parse("corps.droits"); }

  	if (GetModule("aviation"))
	  {
	  	$tmpl_x->parse("corps.mod_aviation_validite");
	  	$tmpl_x->parse("corps.mod_aviation_lache");
	  }

  	if ((GetModule("creche")) && ($usr->type=="enfant"))
	  {
	  	$tmpl_x->parse("corps.mod_creche_enfant");
	  }
  	else if (GetModule("creche"))
	  {
	  	$tmpl_x->parse("corps.mod_creche_parent");
	  }

  	if ((is_numeric($id)) && ($id>0))
	  { 

		// Photo du membre
		$lstdoc=ListDocument($sql,$id,"avatar");
		if (count($lstdoc)>0)
		  {
			$doc = new document_class($lstdoc[0],$sql);
		  	$tmpl_x->assign("id_photo",$lstdoc[0]);
		  }
		else
		  {
		  	$tmpl_x->assign("id_photo","0");
		  }


		if (GetModule("aviation"))
		  {
			// ---- Affiche les infos 
			// Nb d'heure de vol
			$tmpl_x->assign("nbheurevol", $usr->AffNbHeuresVol()."&nbsp;");
	
			// Total d'heures année courante
			$tmpl_x->assign("nbheuresan", $usr->AffNbHeuresAn());
	
			// ---- Total d'heures 12 derniers mois
			$tmpl_x->assign("nbheuresderan", $usr->AffNbHeuresProrogation());
	
			// ---- Solde du compte
			$tmpl_x->assign("solde", $usr->AffSolde());
		  }

		// ---- Affiche solde et nb heures de vol
		if (((GetMyId($id)) || GetDroit("ModifUserComptes")) && (GetModule("aviation")))
		  { $tmpl_x->parse("corps.mod_aviation_detail"); }

		// ---- Affiche les documents
		$lstdoc=ListDocument($sql,$id,"document");

		if ($typeaff=="form")
		  {
			$doc = new document_class(0,$sql);
			$doc->editmode="form";
			$tmpl_x->assign("form_document",$doc->Affiche());
			$tmpl_x->parse("corps.lst_document");
		  }
		  	
		if (is_array($lstdoc))
		  {
			foreach($lstdoc as $i=>$did)
			  {
				$doc = new document_class($did,$sql);
				$doc->editmode=($typeaff=="form") ? "edit" : "std";
				$tmpl_x->assign("form_document",$doc->Affiche());
				$tmpl_x->parse("corps.lst_document");
			  }
		  }
	  }

// ---- Messages
	if ($msg_erreur!="")
	  {
		$tmpl_x->assign("msg_erreur", $msg_erreur);
		$tmpl_x->parse("corps.msgerror");
	  }		

	if ($msg_confirmation!="")
	  {
		$tmpl_x->assign("msg_confirmation", $msg_confirmation);
		$tmpl_x->parse("corps.msgok");
	  }		

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
