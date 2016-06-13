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

	if ( (!GetDroit("AccesBapteme")) && (!GetMyId($id)) )
	  { FatalError("Accès non autorisé (AccesBapteme)"); }

	require_once ("class/bapteme.inc.php");
	require_once ("class/ressources.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("bapteme.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialisation des variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	$msg_erreur="";
	$msg_confirmation="";

	if ($id>0)
	  { $btm = new bapteme_class($id,$sql); }
	else
	  { $btm = new bapteme_class(0,$sql); }


// ---- Sauvegarde les infos
	if (($fonc=="Enregistrer") && (($id=="") || ($id==0)) && ((GetDroit("CreeBapteme"))) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
			$btm->Create();
			$id=$btm->uid;
	  }
	else if (($fonc=="Enregistrer") && ($id=="") && (isset($_SESSION['tab_checkpost'][$checktime])))
	  {
			$mod="aviation";
			$affrub="baptemes";
	  }

	if (($fonc=="Enregistrer") && ((GetMyId($id)) || (GetDroit("ModifBapteme"))) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		// Sauvegarde les données
		if (count($form_info)>0)
		  {
		  	$form_info["dte"]=date2sql($form_info["dte_j"])." ".$form_info["dte_h"];
			foreach($form_info as $k=>$v)
		  	  {
		  		$msg_erreur.=$btm->Valid($k,$v,false);
		  	  }
		  }

		if ( ($form_info["id_pilote"]>0) && ($form_info["id_avion"]>0) && ($form_info["dte_j"]!='0000-00-00') && ($form_info["dte_h"]!='00:00') )
		  { $btm->Valid("status","2"); }

		$btm->Save();
		$msg_confirmation.="Vos données ont été enregistrées.<BR>";

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }
	else if (($fonc=="Enregistrer") && (($btm->status==0) || ($btm->status==1) || ($btm->status==2)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$btm->Valid("id_pilote",$form_info["id_pilote"],false);
		$btm->Valid("id_avion",$form_info["id_avion"],false);
		$btm->Valid("dte",date2sql($form_info["dte_j"])." ".$form_info["dte_h"],false);

		if ( ($form_info["id_pilote"]>0) && ($form_info["id_avion"]>0) && ($form_info["dte_j"]!='0000-00-00') && ($form_info["dte_h"]!='00:00') )
		  { $btm->Valid("status","2"); }

		$btm->Save();
		$msg_confirmation.="Vos données ont été enregistrées.<BR>";

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }

// ---- Réserve l'avion
	if (($fonc=="reserver") && ($btm->id_resa==0))
	  {
		require_once ("class/reservation.inc.php");
		$resa=new resa_class(0,$sql);

		$resa->description="Bapteme ".$btm->nom."\nTéléphone: ".$btm->telephone;
		$resa->uid_pilote=$btm->id_pilote;
		$resa->uid_debite=$MyOpt["uid_bapteme"];
		$resa->uid_instructeur=0;
		$resa->uid_ressource=$btm->id_avion;
		$resa->destination="LOCAL";
		$resa->nbpersonne=$btm->nb;
		$resa->tpsestime="20";
		$resa->dte_deb=sql2date($btm->dte);
		$resa->dte_fin=date("d/m/Y H:i:s",strtotime($btm->dte)+60*45);
		$resa->tpsreel="";
		$resa->horadeb="";
		$resa->horafin="";

		$msg_resa=$resa->Save(true);

		$btm->id_resa=$resa->id;
		$btm->status=2;
		$btm->Save();


		$msg_confirmation.=($msg_resa!="") ? $msg_resa : "Réservation confirmée.<BR>";

		$_SESSION['tab_checkpost'][$checktime]=$checktime;

	}

// ---- Attribuer le bapteme
	if (($fonc=="affecte") && ($id>0))
	  {
		$btm->id_pilote=$uid;
		$btm->status=1;
		$btm->Save();
	  }

// ---- Vol effectué
	if (($fonc=="effectue") && ($id>0))
	  {
		$btm->status=3;
		$btm->Save();
	  }

// ---- Supprimer l'utilisateur
	if (($fonc=="delete") && ($id>0) && (GetDroit("SupprimeBapteme")))
	  {
		$btm->Delete();
		$mod="aviation";
		$affrub="baptemes";
	  }


// ---- Modifie les infos
	if (($fonc=="modifier") && (GetDroit("ModifBapteme")))
	  {
			$typeaff="form";
	  	$tmpl_x->parse("corps.submit");
	  }
	else if (($fonc=="add") && (GetDroit("CreeBapteme")))
	  {
			$typeaff="form";
	  	$tmpl_x->parse("corps.submit");
	  }
/*
	else if (($fonc=="planifier") && (($btm->status==1) || ($btm->status==2)))
	  {
		$typeaff="html";
	  	$tmpl_x->parse("corps.submit");
	  }
*/
	else
	  {
		$typeaff="html";
	  }
	
	
// ---- Affiche les infos

	$tmpl_x->assign("id", $id);

	$tmpl_x->assign("form_num", $btm->Aff("num",$typeaff));
	$tmpl_x->assign("form_nom", $btm->Aff("nom",$typeaff));
	$tmpl_x->assign("form_nb", $btm->Aff("nb",$typeaff));
	$tmpl_x->assign("form_telephone", $btm->Aff("telephone",$typeaff));
	$tmpl_x->assign("form_mail", $btm->Aff("mail",$typeaff));

	$tmpl_x->assign("form_status", $btm->Aff("status",$typeaff));
	$tmpl_x->assign("form_type", $btm->Aff("type",$typeaff));
	$tmpl_x->assign("form_paye", $btm->Aff("paye",$typeaff));
	$tmpl_x->assign("form_dte_j", $btm->Aff("dte_j",$typeaff));
	$tmpl_x->assign("form_dte_h", $btm->Aff("dte_h",$typeaff));
	$tmpl_x->assign("form_description", $btm->Aff("description",$typeaff));

	$tmpl_x->assign("uid_avion", $btm->id_avion);
	$tmpl_x->assign("id_resa", $btm->id_resa);
	$tmpl_x->assign("deb", strtotime($btm->dte));
	$tmpl_x->assign("fin", strtotime($btm->dte)+45*60);

	if (GetDroit("CreeBapteme"))
	  {
	  	$tmpl_x->parse("infos.ajout");
	  }
	if (GetDroit("ModifBapteme"))
	  {
	  	$tmpl_x->parse("infos.modification");	  	
	  }
	if (GetDroit("SupprimeBapteme"))
	  {
	  	$tmpl_x->parse("infos.suppression");
	  }

	
	$usr = new user_class($btm->id_pilote,$sql,true);
	$ress = new ress_class($btm->id_avion,$sql);

	if ($btm->id_pilote==0)
	  {
	  	$tmpl_x->parse("infos.affecter");
	  }

	if (($btm->status==1) || ($btm->id_resa==0))
	  {
	  	$tmpl_x->parse("infos.planifier");
	  }

	if (($ress->CheckDispo(strtotime($btm->dte),strtotime($btm->dte)+45*60)) && ($btm->id_pilote>0) && ($btm->id_avion>0) && ($btm->dte!='0000-00-00 00:00'))
	  {
	  	$tmpl_x->parse("infos.reserver");
	  }

	if ($btm->status==2)
	  {
	  	$tmpl_x->parse("infos.effectue");
	  }


	if (($fonc=="planifier") && (($btm->status==0) || ($btm->status==1) || ($btm->status==2)))
	  {
		$tmpl_x->assign("form_id_pilote", AffListeMembres($sql,$btm->id_pilote,"form_info[id_pilote]",$type="",$sexe="",$order="std",$virtuel="non"));
		$tmpl_x->assign("form_id_avion",AffListeRessources($sql,$btm->id_avion,"form_info[id_avion]",array("oui")));
		$tmpl_x->assign("form_dte_j", $btm->Aff("dte_j","form"));
		$tmpl_x->assign("form_dte_h", $btm->Aff("dte_h","form"));
	  	$tmpl_x->parse("corps.submit");
	  }

	else if ($typeaff=="html")
	  {
		$tmpl_x->assign("form_id_pilote", $usr->Aff("fullname"));
		$tmpl_x->assign("form_id_avion", strtoupper($ress->immatriculation));
	  }
	else
	  {
		$tmpl_x->assign("form_id_pilote", AffListeMembres($sql,$btm->id_pilote,"form_info[id_pilote]",$type="",$sexe="",$order="std",$virtuel="non"));
		$tmpl_x->assign("form_id_avion",AffListeRessources($sql,$btm->id_avion,"form_info[id_avion]",array("oui")));
	  }

// ---- Liste des dispos
	$lst=ListeRessources($sql,array("oui"));
	foreach($lst as $i=>$id)
	  {
		$ress = new ress_class($id,$sql);
		$tmpl_x->assign("lst_uid_avion", $id);
		$tmpl_x->assign("dispo_immat", $ress->immatriculation);
		$tmpl_x->parse("corps.lst_dispo");
		
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
