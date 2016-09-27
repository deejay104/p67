<?
// ---------------------------------------------------------------------------------------------
//   Saisie des mouvements
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

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("vols.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	if (!GetDroit("AccesPageVols")) { FatalError("Accès non autorisé"); }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);


// ---- Charge les tarifs
  	$tabTarif=array();
	$query="SELECT * FROM ".$MyOpt["tbl"]."_tarifs";		
	$sql->Query($query);		
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["pilote"]=$sql->data["pilote"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["instructeur"]=$sql->data["instructeur"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["reduction"]=$sql->data["reduction"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["defaut_pil"]=$sql->data["defaut_pil"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["defaut_ins"]=$sql->data["defaut_ins"];
		$tabTarif[$sql->data["ress_id"]][$sql->data["code"]]["nom"]=$sql->data["nom"];
	  }


// ---- Valide les vols à enregistrer
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		if (is_array($form_tempsresa))
		  {
			foreach ($form_tempsresa as $k=>$tps)
			  {
				if ($tps!="")
				  {
				  	$query="SELECT * FROM ".$MyOpt["tbl"]."_calendrier WHERE id=$k";
					$res=$sql->QueryRow($query);

					// Récupérer tarifs pilote depuis la base
					$p=round($tabTarif[$res["uid_avion"]][$form_tarif[$k]]["pilote"]*$tps/60,2);

					// Calcul du tarif instructeur
					// Si tarif instructeur mis dans la fiche prendre celui là à la place de l'avion
					$pi=0;
					if ($res["uid_instructeur"]>0)
					  {
						$pi=round($tabTarif[$res["uid_avion"]][$form_tarif[$k]]["instructeur"]*$tps/60,2);

					  	$resinst=new user_class($res["uid_instructeur"],$sql,false,true);
						if ($resinst->data["tarif"]>0)
					  	  { $pi=round($resinst->data["tarif"]*$tps/60,2); }
					  }

					// S'il y a une réduction de temps on l'a soustrait au temps pilote
					if ($tabTarif[$res["uid_avion"]][$form_tarif[$k]]["reduction"]>0)
					  {
						$p=round($tabTarif[$res["uid_avion"]][$form_tarif[$k]]["pilote"]*($tps-$tabTarif[$res["uid_avion"]][$form_tarif[$k]]["reduction"])/60,2);
					  }
			
					$dte=sql2date(preg_replace("/^([0-9]*-[0-9]*-[0-9]*)[^$]*$/","\\1",$res["dte_deb"]));
				  	$query="UPDATE ".$MyOpt["tbl"]."_calendrier SET temps='$tps', tpsreel='".$form_blocresa[$k]."', tarif='".$form_tarif[$k]."' WHERE id=$k";
				  	$sql->Update($query);

					DebiteVol($k,$tps,$res["uid_avion"],($res["uid_debite"]>0) ? $res["uid_debite"] : $res["uid_pilote"],$res["uid_instructeur"],$form_tarif[$k],$p,$pi,$dte);
				  }
			  }
		  }

		if (is_array($form_temps))
		  {
			foreach ($form_temps as $k=>$tps)
			  {
				if ($tps!="")
				  {
					$dte=date2sql($form_date[$k]);
					if (($dte=="nok") || ($form_date[$k]==""))
					  {
					  	$msg_result="DATE INVALIDE !!!";
					  }
					else
					  {
						$uid_p=$form_pilote[$k];
						$uid_i=$form_instructeur[$k];
						$uid_a=$idavion;
						$tarif=$form_tarif[$k];
						$horadeb=$form_horadeb[$k];
						$horafin=$form_horafin[$k];

						// Récupérer tarifs pilote depuis la base
						$p=round($tabTarif[$uid_a][$form_tarif[$k]]["pilote"]*$tps/60,2);

						// Calcul du tarif instructeur
						// Si tarif instructeur mis dans la fiche prendre celui là à la place de l'avion
						$pi=0;
						if ($uid_i>0)
						  {
							$pi=round($tabTarif[$uid_a][$form_tarif[$k]]["instructeur"]*$tps/60,2);
	
						  	$resinst=new user_class($uid_i,$sql,false,true);
							if ($resinst->data["tarif"]>0)
						  	  { $pi=round($resinst->data["tarif"]*$tps/60,2);; }
						  }

						// S'il y a une réduction de temps on l'a soustrait au temps pilote
						if ($tabTarif[$uid_a][$form_tarif[$k]]["reduction"]>0)
						  {
							$p=round($tabTarif[$uid_a][$form_tarif[$k]]["pilote"]*($tps-$tabTarif[$uid_a][$form_tarif[$k]]["reduction"])/60,2);
						  }

					  	if (!isset($_SESSION['tab_checkpost'][$checktime]))
						  {
// A rajouter la période de facture ,dte='".date("Ym",strtotime($dte))."'" -> A tester
								$query="INSERT INTO ".$MyOpt["tbl"]."_calendrier SET dte_deb='$dte', dte_fin='$dte', uid_pilote='$uid_p', uid_instructeur='$uid_i', uid_avion='$uid_a', tarif='$tarif', temps='$tps', reel='non', horadeb='$horadeb', horafin='$horafin', dte_maj='".now()."', uid_maj=$uid, actif='oui'";
						  	$id=$sql->Insert($query);
						  }

						DebiteVol($id,$tps,$uid_a,$uid_p,$uid_i,$tarif,$p,$pi,sql2date($dte));
					  }
				  }
			  }
		  }

		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		if ($msg_result!="")
		  { $tmpl_x->assign("msg_resultat", "<FONT color=red>$msg_result</FONT>"); }
		else
		  { $tmpl_x->assign("msg_resultat", ""); }

		$tmpl_x->assign("form_page", "vols");
		$tmpl_x->parse("corps.enregistre");
	  }

// ---- Enregistre les opérations
	else if (($fonc=="Valider") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		if (is_array($form_id))
		  {
			foreach ($form_id as $k=>$idcal)
			  {
		  		$query ="INSERT ".$MyOpt["tbl"]."_compte SET ";
		  		$query.="uid='".$form_uid[$k]."', ";
		  		$query.="tiers='".$form_uidt[$k]."', ";
		  		$query.="montant='".$form_montant[$k]."', ";
		  		$query.="mouvement='".$form_mouvement[$k]."', ";
		  		$query.="commentaire='".addslashes($form_commentaire[$k])."', ";
		  		$query.="date_valeur='".date2sql($form_date[$k])."', ";
		  		$query.="uid_creat=$uid, date_creat='".now()."'";
		  		//echo "$query<BR>";
		  		$sql->Insert($query);

				if (is_numeric($idcal))
				  {
				  	$query="UPDATE ".$MyOpt["tbl"]."_calendrier SET prix='".(-$form_montant[$k])."', edite='non' WHERE id=$idcal";
				  	//echo "$query<BR>";
				  	$sql->Update($query);
  				  }
			  }
		  }


		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		$tmpl_x->assign("msg_resultat", "<FONT color=green>Mouvement(s) enregistré(s)</FONT>");

		$tmpl_x->assign("form_page", "vols");
		$tmpl_x->parse("corps.msg_enregistre");
	  }



// ---- Affiche la page demandée
	if ($fonc!="Enregistrer")
	  {
		// Liste les ressources
		$lst=ListeRessources($sql,array("oui","off"));
	
		foreach($lst as $i=>$id)
		  {
			$ress=new ress_class($id,$sql);

			$tab_avions[$id]=$ress->data;
			$tmpl_x->assign("id_avion", $id);
			if ($id==$idavion)
			  {
				$tmpl_x->assign("sel_avion", "selected");
				$tmpl_x->assign("nom_avion", $ress->Aff("immatriculation","val")." *");
			  }
			else
			  {
			  	$tmpl_x->assign("sel_avion", "");
				$tmpl_x->assign("nom_avion", $ress->Aff("immatriculation","val"));
			  }
			$tmpl_x->parse("corps.aff_vols.lst_avion");
		  }
		
		if ((!isset($idavion)) || (!is_numeric($idavion)))
		  { $t=current($tab_avions); $idavion=$t["id"]; }

		$tmpl_x->assign("nom_avion_edt",$tab_avions[$idavion]["immatriculation"]);

		// Récupère la plus vieille date de saisie des vols
		$query = "SELECT dte_deb FROM ".$MyOpt["tbl"]."_calendrier WHERE prix>0 AND uid_avion='$idavion' ORDER BY dte_deb DESC LIMIT 0,1";
		$res=$sql->QueryRow($query);
		$dte=$res["dte_deb"];
	
		// Liste des vols réservés
		$query = "SELECT id ";
		$query.= "FROM ".$MyOpt["tbl"]."_calendrier ";
		$query.= "WHERE dte_deb>='$dte' AND dte_deb<'".now()."' AND actif='oui' AND prix=0 AND uid_avion='$idavion' ORDER BY dte_deb,horadeb";
		$sql->Query($query);
		$tvols=array();
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$tvols[$i]=$sql->data["id"];
		  }

		foreach ($tvols as $i=>$id)
		  {
			$resa["resa"]=new resa_class($id,$sql);
			$resa["pilote"]=new user_class($resa["resa"]->uid_pilote,$sql);
			$resa["instructeur"]=new user_class($resa["resa"]->uid_instructeur,$sql);

			$tmpl_x->assign("date_vols", sql2date(preg_replace("/^([0-9]*-[0-9]*-[0-9]*)[^$]*$/","\\1",$resa["resa"]->dte_deb)));

			if ($resa["resa"]->uid_debite>0)
			  {
				$resa["debite"]=new user_class($resa["resa"]->uid_debite,$sql);

				$p = $resa["debite"]->Aff("fullname");
				$p.=" (".$resa["pilote"]->Aff("fullname").")";
			  }
			else
			  { $p=$resa["pilote"]->Aff("fullname"); }

			$tmpl_x->assign("pilote_vols", $p);
			$tmpl_x->assign("instructeur_vols", $resa["instructeur"]->Aff("fullname"));

			$tmpl_x->assign("id_ligne", $id);

			foreach ($tabTarif[$idavion] as $c=>$t)
			  { 
				$tmpl_x->assign("tarif_code", $c);
				$tmpl_x->assign("tarif_nom", $t["nom"]);

				if ($c==$resa["resa"]->tarif)
				  {
					$tmpl_x->assign("tarif_selected", "selected");
				  }
				else if ( ($t["defaut_ins"]=="oui") && ($resa["resa"]->uid_instructeur>0) && ($resa["resa"]->tarif=="") )
				  {
					$tmpl_x->assign("tarif_selected", "selected");
				  }
				else if ( ($t["defaut_pil"]=="oui") && ($resa["resa"]->tarif=="") )
				  {
					$tmpl_x->assign("tarif_selected", "selected");
				  }
				else
				  {
					$tmpl_x->assign("tarif_selected", "");
				  }
				$tmpl_x->parse("corps.aff_vols.lst_vols.lst_tarifs");	
			  }


	
			if ($resa["resa"]->temps>0)
			  { $tps=$resa["resa"]->temps; }
			else if (($resa["resa"]->horadeb>0) && ($resa["resa"]->horafin>0))
			  {
				$resr=new ress_class($resa["resa"]->uid_ressource,$sql);
				$tps=$resr->CalcHorametre($resa["resa"]->horadeb,$resa["resa"]->horafin);
			  }
			else
			  { $tps=""; }

			if ($resa["resa"]->tpsreel==0)
			  { $tbl=$tps; }
			else
			  { $tbl=$resa["resa"]->tpsreel; }
			  
			$tmpl_x->assign("idresa", $id);
			$tmpl_x->assign("horadeb", $resa["resa"]->horadeb);
			$tmpl_x->assign("horafin", $resa["resa"]->horafin);
			$tmpl_x->assign("temps_vols", " <INPUT type=\"text\" name=\"form_tempsresa[".$id."]\" size=5 value=\"".$tps."\">");
			$tmpl_x->assign("bloc_vols", " <INPUT type=\"text\" name=\"form_blocresa[".$id."]\" size=5 value=\"".$tbl."\">");

			$tmpl_x->parse("corps.aff_vols.lst_vols");
		  }



		// Liste vierge
		for($ii=0; $ii<5; $ii++)
		  { 
			// Liste des pilotes
			$lst=ListActiveUsers($sql,"prenom,nom","!membre,!invite","");
		
			foreach($lst as $i=>$id)
			  {
			  	$resusr=new user_class($id,$sql);

				$tmpl_x->assign("id_pilote", $resusr->idcpt);
				$tmpl_x->assign("nom_pilote",  $resusr->Aff("fullname","val"));
				$tmpl_x->parse("corps.aff_vols.lst2_vols.lst_pilote");
			  }

			// Liste des instructeurs
			$lst=ListActiveUsers($sql,"prenom,nom","instructeur");
			foreach($lst as $i=>$id)
			  { 
				$resusr=new user_class($id,$sql);
				$tmpl_x->assign("id_instructeur", $resusr->idcpt);
				$tmpl_x->assign("nom_instructeur", $resusr->Aff("fullname","val"));
				$tmpl_x->parse("corps.aff_vols.lst2_vols.lst_instructeur");
			  }

			foreach ($tabTarif[$idavion] as $c=>$t)
			  { 
				$tmpl_x->assign("tarif_code", $c);
				$tmpl_x->assign("tarif_nom", $t["nom"]);

				if ( ($t["defaut_pil"]=="oui") && ($resa["resa"]->tarif=="") )
				  {
					$tmpl_x->assign("tarif_selected", "selected");
				  }
				else
				  {
					$tmpl_x->assign("tarif_selected", "");
				  }
				$tmpl_x->parse("corps.aff_vols.lst2_vols.lst_tarifs");	
			  }

			$tmpl_x->assign("id_new", "N$ii");
			$tmpl_x->parse("corps.aff_vols.lst2_vols");
		  }


		$tmpl_x->assign("form_page", "vols");
	  	$tmpl_x->parse("corps.aff_vols");
	  }


// ---- FONCTIONS

function DebiteVol($idvol,$temps,$idavion,$uid_pilote,$uid_instructeur,$tarif,$p,$pi,$dte)
	{ global $MyOpt, $uid,$tmpl_x,$sql;

		$ress = new ress_class($idavion,$sql);
		$pilote = new user_class($uid_pilote,$sql);

		$tmpl_x->assign("enr_mouvement", "Vol");

		$tmpl_x->assign("enr_id", $idvol);
		$tmpl_x->assign("enr_idcal", $idvol);
		$tmpl_x->assign("enr_uid_deb", $uid_pilote);
		$tmpl_x->assign("enr_uid_cre", $MyOpt["uid_club"]);
		$tmpl_x->assign("enr_date", $dte);
		$tmpl_x->assign("enr_commentaire", "Vol de $temps min (".$ress->Aff("immatriculation","val")."/$tarif)");
		$tmpl_x->assign("enr_tiers_deb", $pilote->Aff("fullname","val"));
		$tmpl_x->assign("enr_affmontant_deb", AffMontant(-$p));
		$tmpl_x->assign("enr_montant_deb", -$p);
		$tmpl_x->assign("enr_tiers_cre", "Club");
		$tmpl_x->assign("enr_affmontant_cre", AffMontant($p));
		$tmpl_x->assign("enr_montant_cre", $p);

		$tmpl_x->parse("corps.enregistre.lst_enregistre");


		if ($pi<>0)
		  {
			$inst = new user_class($uid_instructeur,$sql);

			$tmpl_x->assign("enr_id", $idvol."i");
			$tmpl_x->assign("enr_idcal", "");
			$tmpl_x->assign("enr_uid_deb", $MyOpt["uid_club"]);
			$tmpl_x->assign("enr_uid_cre", $uid_instructeur);
			$tmpl_x->assign("enr_date", $dte);
			$tmpl_x->assign("enr_commentaire", "Remb. vol d'instruction de $temps min (".$ress->Aff("immatriculation","val")."/$tarif)");
			$tmpl_x->assign("enr_tiers_cre", $inst->Aff("fullname","val"));
			$tmpl_x->assign("enr_affmontant_cre", AffMontant($pi));
			$tmpl_x->assign("enr_montant_cre", $pi);
			$tmpl_x->assign("enr_tiers_deb", "Club");
			$tmpl_x->assign("enr_affmontant_deb", AffMontant(-$pi));
			$tmpl_x->assign("enr_montant_deb", -$pi);

			$tmpl_x->parse("corps.enregistre.lst_enregistre");
		  }
	}

// ---- Affecte les variables d'affichage
	if (GetModule("aviation"))
	  {  	$tmpl_x->parse("infos.vols"); }

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
