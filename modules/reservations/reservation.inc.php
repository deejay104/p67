<?
// ---------------------------------------------------------------------------------------------
//   Page de saisie d'une réservation
//     ($Author: miniroot $)
//     ($Date: 2016-04-22 20:48:24 +0200 (ven., 22 avr. 2016) $)
//     ($Revision: 456 $)
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
    Copyright (C) 2005 Matthieu Isorez

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
	require_once ("class/echeance.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("reservation.htm"));

	if (!is_numeric($ress))
	{
		$ress=0;
	}

	
// ---- Charge les données de la réservation
	$res=array();

	$if=($id>0) ? $id : 0;


	if (($id>0) && ($ok!=3))
	{
		// Charge une nouvelle réservation
		$resa["resa"]=new resa_class($id,$sql);
		$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);
		$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql);
	}
	else if ($ok!=3)
	{
		$id="";
		if ($heure=="") { $heure="8"; }
		if ($jour=="") { $jour=date("Y-m-d"); }

		$dte_deb=$jour." ".$heure.":00:00";
		$dte_fin=$jour." ".($heure+1).":00:00";
		if ($jstart>0)
		  {
				$fh=date("O",floor($jstart)/1000+4*3600)/100;
				$dte_deb=date("Y-m-d H:i:s",floor($jstart)/1000-$fh*3600);
			}
			
		if ($jend>0)
		  {
				$fh=date("O",floor($jend)/1000+4*3600)/100;
				$dte_fin=date("Y-m-d H:i:s",floor($jend)/1000-$fh*3600);
		  }

		$resa["resa"]=new resa_class(0,$sql);

		$resa["resa"]->dte_deb=$dte_deb;
		$resa["resa"]->dte_fin=$dte_fin;
		$resa["resa"]->uid_pilote=$uid;
		$resa["resa"]->uid_instructeur=$res["uid_instructeur"];
		$resa["resa"]->uid_ressource=$ress;
		$resa["resa"]->type=$res_user["type"];
		$resa["resa"]->uid_maj=$uid;
		$resa["resa"]->dte_maj=date("Y-m-d H:i:s");

		$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);
		$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql);
	}
	else if ($ok==3)
	{
	  	// Il y a eu une erreur on recharge les valeurs postées
		$ress=$form_uid_ress;

		$resa["resa"]=new resa_class(($id>0) ? $id : 0,$sql);

	  	$resa["resa"]->dte_deb=date2sql($form_dte_deb)." $form_hor_deb";
	  	$resa["resa"]->dte_fin=date2sql($form_dte_fin)." $form_hor_fin";
	  	$resa["resa"]->uid_pilote=$form_uid_pilote;
	  	$resa["resa"]->uid_instructeur=$form_uid_instructeur;
	  	$resa["resa"]->uid_ressource=$form_uid_ress;
	  	$resa["resa"]->tpsestime=$form_tpsestime;
	  	$resa["resa"]->tpsreel=$form_tpsreel;
	  	$resa["resa"]->tpsreel=$form_tpsreel;
	  	$resa["resa"]->horadeb=$form_horadeb;
	  	$resa["resa"]->horafin=$form_horafin;
	  	$resa["resa"]->potentielh=$form_potentielh;
	  	$resa["resa"]->potentielm=$form_potentielm;
	  	$resa["resa"]->carbavant=$carbavant;
	  	$resa["resa"]->carbapres=$carbapres;
	  	$resa["resa"]->prixcarbu=$form_prixcarbu;
	  	$resa["resa"]->destination=$form_destination;
	  	$resa["resa"]->nbpersonne=$form_nbpersonne;
	  	$resa["resa"]->invite=$form_invite;
	  	$resa["resa"]->description=$form_description;
		$resa["resa"]->uid_maj=$uid;
	  	$resa["resa"]->dte_maj=date("Y-m-d H:i:s");

		$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);
		$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql);
	}
	
	$lstress=ListeRessources($sql);
	if ($resa["resa"]->uid_ressource==0)
	{
		foreach($lstress as $i=>$rid)
		{
			if ($resa["resa"]->uid_ressource==0)
			{
				$resa["resa"]->uid_ressource=$rid;
			}
		}
	}

// ---- Charge le template


	if ($resa["resa"]->edite=='non')
	{
		$tmpl_x = new XTemplate (MyRep("reservation-visu.htm"));
	  	$tmpl_hora = new XTemplate (MyRep("horametre-visu.htm"));
	}
	else
	{
	  	$tmpl_hora = new XTemplate (MyRep("horametre.htm"));
	}

// ---- Initialise les variables
	$ok_aff=0;
	$ok_save=0;
	$ok_inst=0;

	$resusr=new user_class($resa["resa"]->uid_pilote,$sql,true);
	$resa["resa"]->pilote_data=$resusr->data;

// ---- Vérifie les échéances
	$lstdte=VerifEcheance($sql,$resa["resa"]->uid_pilote);

	if ( (is_array($lstdte)) && (count($lstdte)>0) )
	{
		foreach($lstdte as $i=>$d)
		{
			if ($d["dte_echeance"]!="")
			{
				$m ="<b><font color='red'>L'échéance ".$d["description"]." a été dépassée (".sql2date($d["dte_echeance"]).").</font></b><br />";
			}
			else
			{
				$m ="<b><font color='red'>Vous n'avez pas de date d'échéance pour ".$d["description"].".</font></b><br />";
			}
			
			if ($d["resa"]=="instructeur")
			{
				$m.="La présence d'un instructeur est obligatoire.<br />";
				// $tmpl_x->assign("msg_warning", $m);
				// $tmpl_x->parse("corps.msg_warning");
				affInformation($m,"warning");

				$ok_inst=1;
			}
			else if ($d["resa"]=="obligatoire")
			{
				$m.="La réservation n'est pas possible.<br />";
				// $tmpl_x->assign("msg_error", $m);
				// $tmpl_x->parse("corps.msg_error");
				affInformation($m,"error");
				$save=1;
			}

		}
	}

// ---- Vérifie si le compte est provisionné

	$s=$resa["pilote"]->CalcSolde();
	if ($s<-$resa["pilote"]->data["decouvert"])
	{
		$m ="<b><font color='red'>Le compte du pilote est NEGATIF ($s €).</font></b><br />";
		$m.="Appeller le trésorier pour l'autorisation d'un découvert.<br />";
  		// $tmpl_x->assign("msg_error", $m);
		// $tmpl_x->parse("corps.msg_error");
		affInformation($m,"error");

		if ($id==0)
		{
			$ok_save=1;
		}
	}


// ---- Vérifie si l'utilisateur est laché sur l'avion
	if (!$resa["pilote"]->CheckLache($resa["resa"]->uid_ressource))
	{
		// $tmpl_x->assign("msg_warning", "<b><font color='red'>Vous n'êtes pas laché sur cet avion.</font></b><br />La présence d'un instructeur est obligatoire.");
		// $tmpl_x->parse("corps.msg_warning");
		$m="<b><font color='red'>Vous n'êtes pas laché sur cet avion.</font></b><br />La présence d'un instructeur est obligatoire.";
		affInformation($m,"warning");

		$ok_inst=1;
	}
	
// ---- Initialisation des variables
	$tmpl_x->assign("id", $id);
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Affiche les messages d'erreurs
	if ($msg_err!="")
	{ 
		// $tmpl_x->assign("msg_error",$msg_err);
		// $tmpl_x->parse("corps.msg_error");
		affInformation($msg_err,"error");
	}
	

// ---- Affiche les infos de la réservation

	// Dernière mise à jour
	$maj=new user_class($resa["resa"]->uid_maj,$sql);
	$tmpl_x->assign("info_maj", $maj->fullname." le ".sql2date($resa["resa"]->dte_maj));


	// Historique des modifications
	$lstmaj=$resa["resa"]->Historique();
	$txtmaj="";
	foreach($lstmaj as $i=>$k)
	  {
	     $maj=new user_class($k["uid"],$sql);
	  	$txtmaj.="&nbsp;&nbsp;".sql2date($k["dte"])." - ";

		if ($k["type"]=="ADD")
			$txtmaj.="Création par";
		else if ($k["type"]=="MOD")
			$txtmaj.="Modification par";
		else if ($k["type"]=="DEL")
			$txtmaj.="Suppression par";

	  	$txtmaj.=" ".$maj->fullname." &nbsp;&nbsp;<br> ";
	  }
	$tmpl_x->assign("info_historique",$txtmaj);

	

// **************************************

	$tmpl_x->assign("form_uid_ress", $resa["resa"]->uid_ressource);

	// Récupère la liste des ressources
	$lstress=ListeRessources($sql);

	foreach($lstress as $i=>$rid)
	{
		$resr=new ress_class($rid,$sql);

		// Initilialise l'id de ressouce s'il est vide

		// Rempli la liste dans le template
		$tmpl_x->assign("uid_avion", $resr->id);
		$tmpl_x->assign("nom_avion", strtoupper($resr->immatriculation));
		if ($resa["resa"]->uid_ressource==$resr->id)
		{
			$tmpl_x->assign("chk_avion", "selected");
			$tmpl_x->assign("uid_avionrmq", $resr->id);
			$tmpl_x->assign("aff_nom_avion", strtoupper($resr->immatriculation));
		}
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.aff_reservation.lst_avion");
	}
	
	// Liste des pilotes	
	$lst=ListActiveUsers($sql,"prenom,nom","!membre,!invite");

	
	$txt="-";
	foreach($lst as $i=>$tmpuid)
	{
		$resusr=new user_class($tmpuid,$sql);
		$tmpl_x->assign("uid_pilote", $resusr->uid);
		$tmpl_x->assign("nom_pilote", $resusr->Aff("fullname","val"));
		if ($resa["resa"]->uid_pilote==$resusr->uid)
		{
			$tmpl_x->assign("chk_pilote", "selected");
			$txt=$resusr->Aff("fullname");
		}
		else
		{
			$tmpl_x->assign("chk_pilote", "");
		}
		$tmpl_x->parse("corps.aff_reservation.lst_pilote");
	}
	$tmpl_x->assign("aff_nom_pilote", $txt);

	// Liste des pilotes débité	
	$lst=ListActiveUsers($sql,"prenom,nom","","");

	$txt="-";
	foreach($lst as $i=>$tmpuid)
	  {
	  	$resusr=new user_class($tmpuid,$sql);
			$tmpl_x->assign("uid_debite", $resusr->uid);
			$tmpl_x->assign("nom_debite", $resusr->Aff("fullname","val"));
			if ($resa["resa"]->uid_debite==$resusr->uid)
			  {
			  	$tmpl_x->assign("chk_debite", "selected");
			  	$txt=$resusr->Aff("fullname");
			  }
			else
			  { $tmpl_x->assign("chk_debite", ""); }
			$tmpl_x->parse("corps.aff_reservation.lst_debite");
	  }
	$tmpl_x->assign("aff_nom_debite", $txt);

	
	if ($ok_inst==0)
	{
		$tmpl_x->assign("uid_instructeur", "0");
		$tmpl_x->assign("nom_instructeur", "Aucun");
		$tmpl_x->assign("chk_instructeur", "");
		$tmpl_x->parse("corps.aff_reservation.aff_instructeur.lst_instructeur");
	}


	// Liste des instructeurs
	$lst=ListActiveUsers($sql,"prenom,nom","instructeur");
	$tmpl_x->assign("aff_nom_instructeur", "-");

	$txt="-";
	foreach($lst as $i=>$tmpuid)
	{ 
		$resusr=new user_class($tmpuid,$sql);
		$tmpl_x->assign("uid_instructeur", $resusr->uid);
		$tmpl_x->assign("nom_instructeur", $resusr->Aff("fullname","val"));
		if ($resa["resa"]->uid_instructeur==$resusr->uid)
		{
			$tmpl_x->assign("chk_instructeur", "selected");
			$txt=$resusr->Aff("fullname");
		}
		else
		{
			$tmpl_x->assign("chk_instructeur", "");
		}
		$tmpl_x->parse("corps.aff_reservation.aff_instructeur.lst_instructeur");
	}
	$tmpl_x->assign("aff_nom_instructeur", $txt);
	$tmpl_x->assign("form_uid_instructeur", $resa["resa"]->uid_instructeur);

	if ($ok==2)
	{
		$tmpl_x->assign("deb", strtotime(date2sql($form_dte_deb)));
		$tmpl_x->assign("fin", strtotime(date2sql($form_dte_fin)));
	}
	else
	{
		$tmpl_x->assign("deb", strtotime(date2sql($resa["resa"]->dte_deb)));
		$tmpl_x->assign("fin", strtotime(date2sql($resa["resa"]->dte_fin)));
	}

	$tmpl_x->parse("corps.aff_reservation.aff_instructeur");

	
	
	// Horaires
	if ($ok==2)
	  {
		$tmpl_x->assign("form_dte_deb", $form_dte_deb);
		$tmpl_x->assign("form_dte_debsql", date2sql($form_dte_deb));
		$tmpl_x->assign("form_hor_deb", $form_hor_deb);
		$tmpl_x->assign("form_dte_fin", $form_dte_fin);
		$tmpl_x->assign("form_dte_finsql", date2sql($form_dte_fin));
		$tmpl_x->assign("form_hor_fin", $form_hor_fin);
		
	  }
	else
	  {
		$tmpl_x->assign("form_dte_deb", sql2date($resa["resa"]->dte_deb,"jour"));
		$tmpl_x->assign("form_dte_debsql", date2sql($resa["resa"]->dte_deb));
		$tmpl_x->assign("form_hor_deb", sql2date($resa["resa"]->dte_deb,"heure"));

		$tmpl_x->assign("form_dte_fin", sql2date($resa["resa"]->dte_fin,"jour"));
		$tmpl_x->assign("form_dte_finsql", date2sql($resa["resa"]->dte_fin));
		$tmpl_x->assign("form_hor_fin", sql2date($resa["resa"]->dte_fin,"heure"));
	  }

	$tmpl_x->assign("form_destination", $resa["resa"]->destination);
	$tmpl_x->assign("chk_passager".$resa["resa"]->nbpersonne, "selected");
	$tmpl_x->assign("form_nbpassager", $resa["resa"]->nbpersonne);
	$tmpl_x->assign("chk_invite_".$resa["resa"]->invite, "selected");

	$tmpl_x->assign("form_tpsestime", $resa["resa"]->tpsestime);

	$tmpl_hora->assign("form_tpsreel", $resa["resa"]->tpsreel);
	$tmpl_hora->assign("form_horadeb", $resa["resa"]->horadeb);
	$tmpl_hora->assign("form_horafin", $resa["resa"]->horafin);

	// Affiche l'horamètre
	$resr=new ress_class($resa["resa"]->uid_ressource,$sql);
	$t=$resr->CalcHorametre($resa["resa"]->horadeb,$resa["resa"]->horafin);
	
	$tmpl_hora->assign("tps_hora", (($t>0) ? AffTemps($t) : "0h 00"));

	$tmpl_hora->parse("aff_horametre");
	$tmpl_x->assign("aff_horametre", $tmpl_hora->text("aff_horametre"));

	// Description de la réservation
	$tmpl_x->assign("form_description", $resa["resa"]->description);

	// Potentiel restant
	$tmpl_x->assign("potentiel", $resa["resa"]->AffPotentiel("prev"));
	
	$tmpl_x->assign("form_potentiel", $resa["resa"]->AffPotentiel("fin"));
	$tmpl_x->assign("form_potentielh", $resa["resa"]->potentielh);
	$tmpl_x->assign("form_potentielm", $resa["resa"]->potentielm);

	$tmpl_x->assign("form_carbavant", $resa["resa"]->carbavant);
	$tmpl_x->assign("form_carbapres", $resa["resa"]->carbapres);
	$tmpl_x->assign("form_prixcarbu", $resa["resa"]->prixcarbu);

	// Texte d'acceptation
	if ($MyOpt["ChkValidResa"]=="on")
	{
		if ($resa["pilote"]->NombreVols(3,"val",$resa["resa"]->uid_ressource,$ddeb)>0)
		{
			$tmpl_x->parse("corps.aff_reservation.aff_chkreservation_ok");
		}
		else
		{
			$tmpl_x->assign("TxtValidResa", $MyOpt["TxtValidResa"]);
			$tmpl_x->parse("corps.aff_reservation.aff_chkreservation");
		}
	}

	if ( ($resa["resa"]->edite!='non') && ($ok_save==0) )
	{
		$tmpl_x->parse("corps.aff_reservation.aff_enregistrer");
	}

	// Affiche le boutton supprimer
	$tmpl_x->parse("infos.supprimer");


	if ($ok_aff==0)
	{ 
      $tmpl_x->parse("corps.aff_reservation"); 
    }


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
