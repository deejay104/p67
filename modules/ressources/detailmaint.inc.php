<?
// ---------------------------------------------------------------------------------------------
//   Page de saisie pour la maintenance
//   
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
	require_once ("class/reservation.inc.php");
	require_once ("class/maintenance.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("detailmaint.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	if ($fonc=="imprimer")
	{
		$tmpl_prg = new XTemplate (MyRep("print.htm"));
	}

// ---- Vérification des données
	if (!is_numeric($uid_ressource))
	  { $uid_ressource=0; }

// ---- Charge les infos
	if (!is_numeric($id))
	  { $id=0; }

	$maint=new maint_class($id,$sql);

	if ($form_ressource>0)
	  { $maint->uid_ressource=$form_ressource; }
	if ($form_atelier>0)
	  { $maint->uid_atelier=$form_atelier; }
	if ($form_status!="")
	  { $maint->status=$form_status; }
	if ($form_commentaire!="")
	  { $maint->data["commentaire"]=$form_commentaire; }
	if ($form_dte_deb!="")
	  { $maint->dte_deb=date2sql($form_dte_deb); }
	if ($form_dte_fin!="")
	  { $maint->dte_fin=date2sql($form_dte_fin); }
	if ($form_potentiel!="")
	  { $maint->potentiel=$form_potentiel; }
	if ($form_resa!="")
	  { $maint->uid_lastresa=$form_resa; }

// ---- Enregistre
	$msg_erreur="";

	if (GetDroit("EnregistreMaintenance") && ($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$msg_erreur=$maint->Save();

		$lstFiche=GetActiveFiche($sql,$maint->uid_ressource,$maint->id);
	
		if (count($lstFiche)>0)
		  {
			foreach($lstFiche as $i=>$fid)
			  {
				$fiche=new fichemaint_class($fid,$sql);

				if ($form_fiche[$fid]!="")
				  {
				  	$fiche->uid_planif=$id;
					if ($maint->status=='cloture')
					  {
					  	$fiche->traite="oui";
					  }
				  	$fiche->Save();
				  }
				else if ($fiche->uid_planif==$id)
				  {
				  	$fiche->Affecte(0);
				  }
			  }
		  }


		if ($msg_erreur=="")
		  { $msg_ok="Enregistrement effectué."; }
	  }
	else if (GetDroit("SupprimeMaintenance") && ($fonc=="Supprimer"))
	  {
	  	$msg_erreur=$maint->Delete();
			$mod="ressources";
			$affrub="liste";
	  }
	else if ($fonc=="Retour")
	  {
			$mod="ressources";
			$affrub="liste";
	  }
// ---- Messages
	if ($msg_erreur!="")
	{
		affInformation($msg_erreur,"error");
	}		

	if ($msg_ok!="")
	{
		affInformation($msg_ok,"ok");
	}
	
// ---- Charge les templates
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	$tmpl_x->assign("id", $maint->id);

	if (($maint->status!="cloture") && ($maint->actif=="oui"))
	  { $typeaff="form"; }
	else
	  { $typeaff="html"; }

// ---- Liste les avions
	$lst=ListeRessources($sql);

	foreach($lst as $i=>$rid)
	  {
			$resr=new ress_class($rid,$sql);
	
			$tmpl_x->assign("uid_ressource", $resr->id);
			$tmpl_x->assign("nom_ressource", $resr->aff("immatriculation","val"));
	
			if ($resr->id==$maint->uid_ressource)
			  {
					$tmpl_x->assign("chk_ressource", "selected");
					$tmpl_x->assign("nom_ressource_visu", $resr->aff("immatriculation"));
			  }
			else
			  {
					$tmpl_x->assign("chk_ressource", "");
				}
			$tmpl_x->parse("corps.aff_ressource.lst_ressource");
  	}

	if (($maint->status!="cloture") && ($maint->actif=="oui"))
	  { $tmpl_x->parse("corps.aff_ressource"); }
	else
	  { $tmpl_x->parse("corps.aff_ressource_visu"); }

// ---- Infos de dernières maj
	$usrmaj = new user_class($maint->uid_maj,$sql);
	$tmpl_x->assign("info_maj", $usrmaj->aff("fullname")." le ".sql2date($maint->dte_maj));

// ---- Liste les ateliers
	$lst=GetActiveAteliers($sql);

	foreach($lst as $i=>$aid)
	  {
		$atelier=new atelier_class($aid,$sql);

		$tmpl_x->assign("uid_atelier", $atelier->id);
		$tmpl_x->assign("nom_atelier", $atelier->nom);

		if ($atelier->id==$maint->uid_atelier)
		  {
			$tmpl_x->assign("chk_atelier", "selected");
			$tmpl_x->assign("nom_atelier_visu", $atelier->nom);
		  }
		else
		  { $tmpl_x->assign("chk_atelier", ""); }
		$tmpl_x->parse("corps.lst_atelier");
		$tmpl_x->parse("corps.aff_atelier.lst_atelier");
	  }
	if (($maint->status!="cloture") && ($maint->actif=="oui"))
	  { $tmpl_x->parse("corps.aff_atelier"); }
	else
	  { $tmpl_x->parse("corps.aff_atelier_visu"); }

// ---- Affiche les informations

	foreach($maint->data as $k=>$v)
	  { $tmpl_x->assign("form_$k", $maint->aff($k,$typeaff)); }


// ---- Affiche la liste des derniers vols

	$tabTitre=array();
	$tabTitre["chk"]["aff"]="";
	$tabTitre["chk"]["width"]=30;
	$tabTitre["dtecreat"]["aff"]="Date";
	$tabTitre["dtecreat"]["width"]=220;
	$tabTitre["pilote"]["aff"]="Pilote";
	$tabTitre["pilote"]["width"]=150;
	$tabTitre["temps"]["aff"]="Temps";
	$tabTitre["temps"]["width"]=80;

	$tabValeur=array();

	$lstFiche=ListLastReservation($sql,0,$maint->uid_ressource,5,$maint->dte_deb);

	$chk="";

	if (count($lstFiche)>0)
	  {
		if ($maint->uid_lastresa==0)
		  {
			$maint->uid_lastresa=$lstFiche[0];
		  }

		$ii=0;

		foreach($lstFiche as $i=>$fid)
		  {
			$resa = new resa_class($fid,$sql);

			$tabValeur[$ii]["chk"]["val"]="";
			$tabValeur[$ii]["chk"]["aff"]="<input type='radio' name='form_resa' ".(($resa->id==$maint->uid_lastresa) ? "checked='checked'" : "")." value='".$resa->id."'>";
			if ($resa->id==$maint->uid_lastresa)
			  { $chk="ok"; }

			$usr = new user_class($resa->uid_pilote,$sql,false);
			$tabValeur[$ii]["pilote"]["val"]=$usr->aff("fullname","val");
			$tabValeur[$ii]["pilote"]["aff"]=$usr->aff("fullname");
			
			$tabValeur[$ii]["dtecreat"]["val"]="aa".$ii;
			$tabValeur[$ii]["dtecreat"]["aff"]=$resa->AffDate();

			$tabValeur[$ii]["temps"]["val"]=$resa->temps;
			$tabValeur[$ii]["temps"]["aff"]=$resa->AffTempsReel();
			
			$ii++;
		  }
	  }

	if ($orderv=="") { $orderv="dtecreat"; }
	if ($triev=="") { $triev="d"; }

	$tmpl_x->assign("aff_resa",AfficheTableau($tabValeur,$tabTitre,$orderv,$triev));

// ---- Affiche la liste des fiches

	$tabTitre=array();
	$tabTitre["chk"]["aff"]="";
	$tabTitre["chk"]["width"]=30;
	$tabTitre["ress"]["aff"]="Avion";
	$tabTitre["ress"]["width"]=70;
	$tabTitre["auteur"]["aff"]="Auteur";
	$tabTitre["auteur"]["width"]=200;
	$tabTitre["dtecreat"]["aff"]="Date";
	$tabTitre["dtecreat"]["width"]=100;
	$tabTitre["description"]["aff"]="Description";
	$tabTitre["description"]["width"]=380;
	$tabTitre["maint"]["aff"]="";
	$tabTitre["maint"]["width"]=20;

	$tabValeur=array();

	$lstFiche=GetActiveFiche($sql,$maint->uid_ressource,$maint->id);

	if (count($lstFiche)>0)
	  {
		foreach($lstFiche as $i=>$fid)
		  {
			$fiche = new fichemaint_class($fid,$sql);

			if ((($maint->status!="cloture") && (GetDroit("EnregistreMaintenance") && ($maint->actif=="oui"))) || ($maint->id==$fiche->uid_planif))
			  {

				$tabValeur[$i]["chk"]["val"]=(($fiche->uid_planif==$maint->id) ? "1" : "0");
				if (($maint->status!="cloture") && ($maint->actif=="oui"))
				  {
					$tabValeur[$i]["chk"]["aff"]="<input type='checkbox' name='form_fiche[".$fid."]' ".(($fiche->uid_planif==$maint->id) ? "checked" : "").">";
				} else {
					$tabValeur[$i]["chk"]["aff"]=" ";
				  }
				
				$ress = new ress_class($fiche->uid_avion,$sql,false);
				$tabValeur[$i]["ress"]["val"]=$ress->aff("immatriculation","val");
				$tabValeur[$i]["ress"]["aff"]=$ress->aff("immatriculation");
				
				$usr = new user_class($fiche->uid_creat,$sql,false);
				$tabValeur[$i]["auteur"]["val"]=$usr->aff("fullname","val");
				$tabValeur[$i]["auteur"]["aff"]=$usr->aff("fullname");
	
				$tabValeur[$i]["dtecreat"]["val"]=sql2date($fiche->dte_creat,"jour");
				$tabValeur[$i]["dtecreat"]["aff"]=sql2date($fiche->dte_creat,"jour");
				$tabValeur[$i]["description"]["val"]=$fiche->description;
				$tabValeur[$i]["description"]["aff"]=htmlentities($fiche->description);

				$tabValeur[$i]["maint"]["val"]=(($fiche->uid_planif>0) ? "1" : "0");
				$tabValeur[$i]["maint"]["aff"]=((($fiche->uid_planif>0) && ($fiche->uid_planif!=$id)) ? "<a href='maintenance.php?rub=detailmaint&id=$fiche->uid_planif' title='Cette fiche est déjà affectée à une autre maintenance'><img src='images/12_feuilles.gif' border='0'></a>" : " ");
			  }	
		  }
	  }
	else
	  {
			$tabValeur[$i]["chk"]["val"]="";
			$tabValeur[$i]["chk"]["aff"]="";
			$tabValeur[$i]["ress"]["val"]="";
			$tabValeur[$i]["ress"]["aff"]="";
			$tabValeur[$i]["auteur"]["val"]="";
			$tabValeur[$i]["auteur"]["aff"]="";
			$tabValeur[$i]["dtecreat"]["val"]="";
			$tabValeur[$i]["dtecreat"]["aff"]="";
			$tabValeur[$i]["description"]["val"]="-Aucune fiche en cours-";
			$tabValeur[$i]["description"]["aff"]="-Aucune fiche en cours-";
			$tabValeur[$i]["dteresolv"]["val"]="";
			$tabValeur[$i]["dteresolv"]["aff"]="";
			$tabValeur[$i]["maint"]["val"]="";
			$tabValeur[$i]["maint"]["aff"]="";
	  }


	if ($order=="") { $order="dtecreat"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_fiche",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	if (($maint->status!="cloture") && (GetDroit("EnregistreMaintenance")) && ($maint->actif=="oui"))
	  {
			$tmpl_x->parse("corps.form_submit.aff_bouttons");
	  }
	if (GetDroit("SupprimeMaintenance"))
	  {
			$tmpl_x->parse("infos.supprimemaint");
	  }

// ---- Bouttons du formulaire
	if ($fonc!="imprimer")
	{
		$tmpl_x->parse("corps.form_submit");
	}

// ---- Affecte les variables d'affichage
	if (($fonc!="Retour") && ($fonc!="Supprimer"))
	  {
			$tmpl_x->parse("icone");
			$icone=$tmpl_x->text("icone");
			$tmpl_x->parse("infos");
			$infos=$tmpl_x->text("infos");
			$tmpl_x->parse("corps");
			$corps=$tmpl_x->text("corps");
	  }
	else
	  {
	  	$order="dte_deb";
	  	$trie="i";
	  }
?>