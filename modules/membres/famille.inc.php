<?
// ---------------------------------------------------------------------------------------------
//   Détail d'un utilisateur
//     ($Author: miniroot $)
//     ($Date: 2013-01-21 23:01:53 +0100 (lun., 21 janv. 2013) $)
//     ($Revision: 418 $)
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
	if (!GetDroit("AccesFamille")) { FatalError("Accès non autorisé (AccesFamille)"); }


	require_once ("class/abonnement.inc.php");
	require_once ("class/document.inc.php");
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("famille.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialisation des variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	$msg_erreur="";
	$msg_confirmation="";

	if ($id>0)
	  {
	  	$usr = new user_class($id,$sql,true);
	  	
	  	if (($usr->data["pere"]!=$form_lst_pere) && ($form_lst_pere>0))
	  	  {
	  	  	$usr->data["pere"]=$form_lst_pere;
	  	  	$form_pere=array();
	  	  }

	  	if (($usr->data["mere"]!=$form_lst_mere) && ($form_lst_mere>0))
	  	  {
	  	  	$usr->data["mere"]=$form_lst_mere;
	  	  	$form_mere=array();
	  	  }
	  	
	  	$usr_pere = new user_class($usr->data["pere"],$sql,true);
	  	$usr_mere = new user_class($usr->data["mere"],$sql,true);
	  }
	else
	  { 
	  	$usr = new user_class(0,$sql,false);

	  	if ($form_lst_pere>0)
	  	  {
	  	  	$usr->data["pere"]=$form_lst_pere;
		  	$usr_pere = new user_class($usr->data["pere"],$sql,true);
	  	  	$form_pere=array();
	  	  }
		else
		  {
 		  	$usr_pere = new user_class(0,$sql,false);
		  }

	  	if ($form_lst_mere>0)
	  	  {
	  	  	$usr->data["mere"]=$form_lst_mere;
		  	$usr_mere = new user_class($usr->data["mere"],$sql,true);
	  	  	$form_mere=array();
	  	  }
		else
		  {
	  		$usr_mere = new user_class(0,$sql,false);
	  	  }
	  }


// ---- Sauvegarde les infos
	if (($fonc=="Enregistrer") && (($id=="") || ($id==0) ) && (GetDroit("CreeUser")))
	  {
		if (isset($_SESSION['tab_checkpost'][$checktime]))
		  {
			$mod="membres";
			$affrub="familles";
		  }
		else
		  {
			$id=$usr->Create();

/*
			if ($usr->data["pere"]==0)
			  { $usr->data["pere"]=$usr_pere->Create(); }

			if ($usr->data["mere"]==0)
			  { $usr->data["mere"]=$usr_mere->Create(); }
*/
		  }
	  }

	if (($fonc=="Enregistrer") && (($id==$uid) || (GetDroit("ModifFamilleSauve"))) && ($id>0) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		if ($usr_pere->uid==0)
		  {
			$usr->data["pere"]=$usr_pere->Create();
			$usr_pere->data["idcpt"]=$id;
		  }
		if ($usr_mere->uid==0)
		  {
		  	$usr->data["mere"]=$usr_mere->Create();
			$usr_mere->data["idcpt"]=$id;
		  }

		// Sauvegarde les données du père
		if ($usr_pere->data["nom"]=="")
		  { $form_pere["nom"]=$form_info["nom"]; }

		if (count($form_pere)>0)
		  {
			foreach($form_pere as $k=>$v)
		  	  {
		  		$msg_erreur.=$usr_pere->Valid($k,$v);
				if (($v=="") && ($usr_mere->data[$k]!=""))
				  {
				  	$usr_pere->data[$k]=$usr_mere->data[$k];
				  }
		  	  }
		  }

		$usr_pere->data["zone"]=$usr->data["zone"];
		$usr_pere->data["regime"]=$usr->data["regime"];
		$usr_pere->data["sexe"]="M";
		$usr_pere->data["type"]="parent";
		$usr_pere->Save();

		// Sauvegarde les données de la mère
		if ($usr_mere->data["nom"]=="")
		  { $form_mere["nom"]=$form_info["nom"]; }

		if (count($form_mere)>0)
		  {
			foreach($form_mere as $k=>$v)
		  	  {
		  		$msg_erreur.=$usr_mere->Valid($k,$v);

				if (($v=="") && ($k!="mail"))
				  {
				  	$usr_mere->data[$k]=$usr_pere->data[$k];
				  }
		  	  }
		  }

		$usr_mere->data["zone"]=$usr->data["zone"];
		$usr_mere->data["regime"]=$usr->data["regime"];
		$usr_mere->data["sexe"]="F";
		$usr_mere->data["type"]="parent";
		$usr_mere->Save();


		// Sauvegarde les données de l'enfant
		if (count($form_info)>0)
		  {
			foreach($form_info as $k=>$v)
		  	  {
		  		$msg_erreur.=$usr->Valid($k,$v);
		  	  }
		  }

		// $usr->data["mail"]=(($usr->data["mail"]=="") ? (($usr_mere->data["mail"]=="") ? $usr_pere->data["mail"] : $usr_mere->data["mail"]) : $usr->data["mail"]) ;
		$usr->data["tel_fixe"]=$usr_pere->data["tel_fixe"];
		$usr->data["tel_portable"]=$usr_pere->data["tel_portable"];
		$usr->data["tel_bureau"]=$usr_pere->data["tel_bureau"];
		$usr->data["adresse1"]=$usr_pere->data["adresse1"];
		$usr->data["adresse2"]=$usr_pere->data["adresse2"];
		$usr->data["ville"]=$usr_pere->data["ville"];
		$usr->data["codepostal"]=$usr_pere->data["codepostal"];
		$usr->data["type"]="enfant";
		$usr->Save();

		$msg_confirmation.="Vos données ont été enregistrées.<BR>";


		// Sauvegarde un document
		if ($_FILES["form_adddocument"]["name"]!="")
		  {
		  	$doc = new document_class(0,$sql);
		  	$doc->Save($id,$_FILES["form_adddocument"]);
		  }

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }


// ---- Supprimer l'utilisateur
	if (($fonc=="delete") && ($id>0) && (GetDroit("SupprimeUser")))
	  {
		$usr->Delete();
		
		$usr_pere->LoadEnfants();
		$usr_mere->LoadEnfants();

		if (count($usr_pere->data["enfant"])==0)
		  {
		  	$usr_pere->Delete();
		  }
		if (count($usr_mere->data["enfant"])==0)
		  {
		  	$usr_mere->Delete();
		  }

		$mod="membres";
		$affrub="familles";
	  }

	if (($fonc=="desactive") && ($id>0) && (GetDroit("DesactiveUser")))
	  {
		$usr->Desactive();
	  }

// ---- Modifie les infos
	if (($fonc=="modifier") && (($id==$uid) || (GetDroit("ModifUser"))))
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
		if (GetModule("aviation"))
		  { $usr->LoadLache(); }
		$usrmaj = new user_class($usr->uidmaj,$sql);

		$tmpl_x->assign("id", $id);
		$tmpl_x->assign("info_maj", $usrmaj->prenom." ".$usrmaj->nom." le ".sql2date($usr->dtemaj));	
	  }
	else if (GetDroit("CreeUser"))
	  {
		$tmpl_x->assign("titre", "Saisie d'une nouvelle famille");

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

	// Affiche les infos de l'enfant
	foreach($usr->data as $k=>$v)
	  { $tmpl_x->assign("form_$k", $usr->aff($k,$typeaff)); }

	// Affiche les infos du père
	foreach($usr_pere->data as $k=>$v)
	  { $tmpl_x->assign("form_pere_$k", $usr_pere->aff($k,$typeaff,"form_pere")); }

	// Affiche les infos de la mère
	foreach($usr_mere->data as $k=>$v)
	  { $tmpl_x->assign("form_mere_$k", $usr_mere->aff($k,$typeaff,"form_mere")); }

	// Affiche les bouttons
	if (($id==$uid) || (GetDroit("ModifUser")))
	  { $tmpl_x->parse("infos.modification"); }

	if (($id==$uid) || (GetDroit("ModifUserPassword")))
	  { $tmpl_x->parse("infos.password"); }

	if (GetDroit("CreeUser"))
	  { $tmpl_x->parse("infos.ajout"); }

	if ((GetDroit("DesactiveUser")) && ($usr->actif=="oui"))
	  { $tmpl_x->parse("infos.desactive"); }

	if ((GetDroit("SupprimeUser")) && ($usr->actif=="off"))
	  { $tmpl_x->parse("infos.suppression"); }

	if ($typeaff=="form")
	  {
		$tmpl_x->parse("corps.aff_photo");
		$tmpl_x->parse("corps.submit");
		$tmpl_x->parse("corps.submit1");
		if ((is_numeric($id)) && ($id>0))
		  { $tmpl_x->assign("titre", "Modification de la famille ".$usr->Aff("prenom")." ".$usr->Aff("nom")); }
	  }
	else if ($typeaff=="html")
	  {	$tmpl_x->assign("titre", "Famille de ".$usr->Aff("prenom")." ".$usr->Aff("nom")); }

	if (($typeaff=="form") && (($id==$uid) || (GetDroit("ModifUserPassword"))))
	  {
		$tmpl_x->parse("corps.modif_mdp");
	  }

// ---- Affiche la liste des parents
	if ($typeaff=="form")
	  {
		$tmpl_x->assign(lst_pere,AffListeMembres($sql,$usr->data["pere"],"form_lst_pere","parent","M","std","non"));
		$tmpl_x->assign(lst_mere,AffListeMembres($sql,$usr->data["mere"],"form_lst_mere","parent","F","std","non"));
	  }

// ---- Affiche les documents
	if ($typeaff=="form")
	  {
		$doc = new document_class(0,$sql);
		$doc->editmode="form";
		$tmpl_x->assign("form_document",$doc->Affiche());
		$tmpl_x->parse("corps.lst_document");
	  }
	  	
	$lstdoc=ListDocument($sql,$id,"document");
	foreach($lstdoc as $i=>$did)
	  {
		$doc = new document_class($did,$sql);
		$doc->editmode=($typeaff=="form") ? "edit" : "std";
		$tmpl_x->assign("form_document",$doc->Affiche());
		$tmpl_x->parse("corps.lst_document");
	  }

// ---- Liste les abonnements
	if ((GetModule("abonnement")) && ($id>0) && ($fonc!="modifier"))
	  {
		$lstabo=ListAbonnement($sql,$id);

		foreach($lstabo as $i=>$abonum)
		  { 
			$abo = new abonnement_class($abonum,$sql);
			$tmpl_x->assign("abonnement_id", $abo->id);
			$tmpl_x->assign("abonnement_titre", $abo->abonum." (du ".$abo->dte_deb." au ".$abo->dte_fin.")");

			$abo->LoadLignes();
			$tmpl_x->assign("abonnement_total", $abo->total);

			foreach($abo->lignes as $i=>$ligne)
			  { 
				$tmpl_x->assign("abonnement_ligne", $ligne["description"]);
				$tmpl_x->assign("abonnement_montant", $ligne["montant"]);
				$tmpl_x->parse("corps.abonnement.lst_abonnement.lst_abo_ligne");
			  }
			$tmpl_x->parse("corps.abonnement.lst_abonnement");
		  }

		$tmpl_x->parse("corps.abonnement");
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
	$icone=&$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=&$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=&$tmpl_x->text("corps");

?>
