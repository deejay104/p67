<?
// ---------------------------------------------------------------------------------------------
//   Saisie des mouvements
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2
    Copyright (C) 2017 Matthieu Isorez

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
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables

	if (!GetDroit("AccesPageMouvements")) { FatalError("Accès non autorisé"); }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Enregistre le mouvement
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$dte=date2sql($form_date);
		if ($dte=="nok")
		  {
		  	$msg_result="DATE INVALIDE !!!";
		  	$dte="";
		  }

		// Récupère les infos sur le type de mouvement
		$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE id=$form_mouvement";
		$res=$sql->QueryRow($query);

		$deb=array();
		if ($res["debiteur"]=="B")
		  { $deb[0]=$MyOpt["uid_banque"]; }
		else if ($res["debiteur"]=="C")
		  { $deb[0]=$MyOpt["uid_club"]; }
		else if ($res["debiteur"]>0)
		  { $deb[0]=$res["debiteur"]; }
		else if ($form_tiers=="*")
		  {
			$query = "SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs WHERE actif='oui' AND virtuel='non'";
			$sql->Query($query);
			for($i=0; $i<$sql->rows; $i++)
			  { 
				$sql->GetRow($i);
				$deb[$i]=$sql->data["id"];
			  }
		  }
		else
		  { $deb[0]=$form_tiers; }

		$cre=array();
		if ($res["crediteur"]=="B")
		  { $cre[0]=$MyOpt["uid_banque"]; }
		else if ($res["crediteur"]=="C")
		  { $cre[0]=$MyOpt["uid_club"]; }
		else if ($res["crediteur"]>0)
		  { $cre[0]=$res["crediteur"]; }
		else if ($form_tiers=="*")
		  {
			$query = "SELECT id FROM ".$MyOpt["tbl"]."_utilisateurs WHERE actif='oui' AND virtuel='non'";
			$sql->Query($query);
			for($i=0; $i<$sql->rows; $i++)
			  { 
				$sql->GetRow($i);
				$cre[$i]=$sql->data["id"];
			  }
		  }
		else
		  { $cre[0]=$form_tiers; }

		$enrid=0;
		foreach ($deb as $d)
		{
		    foreach ($cre as $c)
		    {
				if (($c>0) && ($d>0) && ($dte!=""))
				{
					// Vérifie le montant
					preg_match("/^(-?[0-9]*)\.?,?([0-9]*)?$/",$form_mvtmontant,$t);
					$form_mvtmontant=$t[1].".".$t[2];

					$mid=GetMouvementID($sql);
 					
					// Récupère les infos du débiteur
					$res_usr = new user_class($d,$sql);
					
					// Affiche les champs commun
					$tmpl_x->assign("enr_mid", $mid);
					$tmpl_x->assign("enr_commentaire", $form_commentaire);
					$tmpl_x->assign("enr_facture", $form_facture);
					$tmpl_x->assign("enr_date", sql2date($dte));

					$ventil_totmontant=0;
					if ($form_ventilation=="debiteur")
					{
						foreach($form_mouvement_ventil as $i=>$v)
						{
							preg_match("/^(-?[0-9]*)\.?,?([0-9]*)?$/",$form_mvtmontant_ventil[$i],$t);
							$ventil_montant=$t[1].".".$t[2];

							if ($ventil_montant<>0)
							{
								$ventil_totmontant=$ventil_totmontant+$ventil_montant;

								$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE id=$v";
								$resv=$sql->QueryRow($query);

								$dv=(($form_tiers_ventil[$i]>0) ? $form_tiers_ventil[$i] : $d);
								$res_user_vent = new user_class($dv,$sql);
								
								$tmpl_x->assign("enr_id", $enrid);
								$tmpl_x->assign("enr_compte", $resv["compte"]);
								$tmpl_x->assign("enr_uid_deb", $dv);
								$tmpl_x->assign("enr_uid_cre", $c);
								$tmpl_x->assign("enr_posteid", $resv["id"]);
								$tmpl_x->assign("enr_mouvement", $resv["description"]);
								$tmpl_x->assign("enr_tiers",$res_user_vent->fullname);
								$tmpl_x->assign("enr_montant", -$ventil_montant);
								$tmpl_x->assign("enr_affmontant", AffMontant(-$ventil_montant));
								$tmpl_x->parse("corps.enregistre.lst_enregistre");
								$enrid++;
							}							
						}
						if ($ventil_totmontant<>$form_mvtmontant)
						{
							$tmpl_x->assign("enr_id", $enrid);
							$tmpl_x->assign("enr_compte", $res["compte"]);
							$tmpl_x->assign("enr_uid_deb", $d);
							$tmpl_x->assign("enr_uid_cre", $c);
							$tmpl_x->assign("enr_posteid", $res["id"]);
							$tmpl_x->assign("enr_mouvement", $res["description"]);
							$tmpl_x->assign("enr_tiers",$res_usr->fullname);
							$tmpl_x->assign("enr_montant", -$form_mvtmontant+$ventil_totmontant);
							$tmpl_x->assign("enr_affmontant", AffMontant(-$form_mvtmontant+$ventil_totmontant));
							$tmpl_x->parse("corps.enregistre.lst_enregistre");
							$enrid++;
						}
					}					
					else
					{
						$tmpl_x->assign("enr_id", $enrid);
						$tmpl_x->assign("enr_compte", $res["compte"]);
						$tmpl_x->assign("enr_uid_deb", $d);
						$tmpl_x->assign("enr_uid_cre", $c);
						$tmpl_x->assign("enr_posteid", $res["id"]);
						$tmpl_x->assign("enr_mouvement", $res["description"]);
						$tmpl_x->assign("enr_tiers",$res_usr->fullname);
						$tmpl_x->assign("enr_montant", -$form_mvtmontant);
						$tmpl_x->assign("enr_affmontant", AffMontant(-$form_mvtmontant));
						$tmpl_x->parse("corps.enregistre.lst_enregistre");
						$enrid++;
					}
					
					// Récupère les infos du créditeur
					$res_usr = new user_class($c,$sql);

					// Affiche les champs commun
					$tmpl_x->assign("enr_posteid", $form_mouvement);
					$tmpl_x->assign("enr_mid", $mid);
					$tmpl_x->assign("enr_commentaire", $form_commentaire);
					$tmpl_x->assign("enr_facture", $form_facture);
					$tmpl_x->assign("enr_date", sql2date($dte));


					$ventil_totmontant=0;
					if ($form_ventilation=="crediteur")
					{
						foreach($form_mouvement_ventil as $i=>$v)
						{
							preg_match("/^(-?[0-9]*)\.?,?([0-9]*)?$/",$form_mvtmontant_ventil[$i],$t);
							$ventil_montant=$t[1].".".$t[2];

							if ($ventil_montant<>0)
							{
								$ventil_totmontant=$ventil_totmontant+$ventil_montant;

								$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE id=$v";
								$resv=$sql->QueryRow($query);
								
								$cv=(($form_tiers_ventil[$i]>0) ? $form_tiers_ventil[$i] : $c);
								$res_user_vent = new user_class($cv,$sql);

								$tmpl_x->assign("enr_id", $enrid);
								$tmpl_x->assign("enr_compte", $resv["compte"]);
								$tmpl_x->assign("enr_uid_deb", $cv);
								$tmpl_x->assign("enr_uid_cre", $d);
								$tmpl_x->assign("enr_posteid", $resv["id"]);
								$tmpl_x->assign("enr_mouvement", $resv["description"]);
								$tmpl_x->assign("enr_tiers",$res_user_vent->fullname);
								$tmpl_x->assign("enr_montant", $ventil_montant);
								$tmpl_x->assign("enr_affmontant", AffMontant($ventil_montant));
								$tmpl_x->parse("corps.enregistre.lst_enregistre");
								$enrid++;
							}							
						}
						if ($ventil_totmontant<>$form_mvtmontant)
						{
							$tmpl_x->assign("enr_id", $enrid);
							$tmpl_x->assign("enr_compte", $res["compte"]);
							$tmpl_x->assign("enr_uid_deb", $c);
							$tmpl_x->assign("enr_uid_cre", $d);
							$tmpl_x->assign("enr_posteid", $res["id"]);
							$tmpl_x->assign("enr_mouvement", $res["description"]);
							$tmpl_x->assign("enr_tiers",$res_usr->fullname);
							$tmpl_x->assign("enr_montant", $form_mvtmontant-$ventil_totmontant);
							$tmpl_x->assign("enr_affmontant", AffMontant($form_mvtmontant-$ventil_totmontant));
							$tmpl_x->parse("corps.enregistre.lst_enregistre");
							$enrid++;
						}
					}					
					else
					{
						$tmpl_x->assign("enr_id", $enrid);
						$tmpl_x->assign("enr_compte", $res["compte"]);
						$tmpl_x->assign("enr_uid_deb", $c);
						$tmpl_x->assign("enr_uid_cre", $d);
						$tmpl_x->assign("enr_posteid", $res["id"]);
						$tmpl_x->assign("enr_mouvement", $res["description"]);
						$tmpl_x->assign("enr_tiers", $res_usr->fullname);
						$tmpl_x->assign("enr_montant", $form_mvtmontant);
						$tmpl_x->assign("enr_affmontant", AffMontant($form_mvtmontant));
						$tmpl_x->parse("corps.enregistre.lst_enregistre");
						$enrid++;
					}				}
		    }
		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		if ($msg_result!="")
		  {
			$tmpl_x->assign("msg_resultat", "<FONT color=red>$msg_result</FONT>");
			$tmpl_x->parse("corps.msg_enregistre");
			$fonc="";
		  }
		else
		  {
		  	$tmpl_x->assign("msg_resultat", "");
			$tmpl_x->parse("corps.enregistre");
		  }
	  }


// ---- Enregistre les opérations
	else if (($fonc=="Valider") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$nbmvt=0;
		$totmnt=0;
		if (is_array($form_id))
		{
 
			foreach ($form_id as $k=>$idcal)
			  {
				$query ="INSERT ".$MyOpt["tbl"]."_compte SET ";
		  		$query.="mid='".$form_mid[$k]."', ";
		  		$query.="uid='".$form_uid[$k]."', ";
		  		$query.="tiers='".$form_uidt[$k]."', ";
		  		$query.="montant='".$form_montant[$k]."', ";
		  		$query.="mouvement='".addslashes($form_mouvement[$k])."', ";
		  		$query.="commentaire='".addslashes($form_commentaire[$k])."', ";
		  		$query.="facture='".(($form_facture[$k]=="") ? "NOFAC" : "")."', ";
		  		$query.="date_valeur='".date2sql($form_date[$k])."', ";
		  		$query.="dte='".date("Ym",strtotime(date2sql($form_date[$k])))."', ";
		  		$query.="compte='".$form_compte[$k]."', ";
		  		$query.="uid_creat=$uid, date_creat='".now()."'";
		  		//echo "$query<BR>";
		  		$sql->Insert($query);
				$nbmvt++;
				$totmnt=$totmnt+$form_montant[$k];
			  }
		  }


		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		if ($totmnt==0)
		{
			$tmpl_x->assign("msg_confirmation", $nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré(s)");
			$tmpl_x->assign("msg_confirmation_class", "msgok");
		}
		else
		{
			$tmpl_x->assign("msg_confirmation", "Total non nul : ".$totmnt."€ d'écart<br/>".$nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré(s)");
			$tmpl_x->assign("msg_confirmation_class", "msgerror");
		}
		$tmpl_x->parse("corps.msg_enregistre");
		
		$tmpl_x->assign("form_page", "vols");
	  }

// ---- Affiche la page demandée
	if ($fonc!="Enregistrer")
	  {
		if (!is_array($form_id))
		  { $form_id=array(); }

	  if (!is_array($form_mouvement))
		  { $form_mouvement=array(); }

		if (!is_array($form_uid))
		  { $form_uid=array(); }

		if (!is_array($form_date))
		  { $form_date=array(); }

		if (!is_array($form_montant))
		  { $form_montant=array(); }

		if (!is_array($form_commentaire))
		  { $form_commentaire=array(); }


		reset($form_id);
		reset($form_mouvement);
		reset($form_uid);
		reset($form_date);
		reset($form_montant);
		reset($form_commentaire);

		for ($ii=0; $ii<1;$ii++)
		  {
			// Liste des mouvements
			$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE actif='oui' ORDER BY ordre,description";
			$sql->Query($query);
			$montant=0;
			for($i=0; $i<$sql->rows; $i++)
			  { 
				$sql->GetRow($i);
			
				$tmpl_x->assign("id_mouvement", $sql->data["id"]);
				$tmpl_x->assign("nom_mouvement", $sql->data["description"].((($sql->data["debiteur"]=="0") || ($sql->data["crediteur"]=="0")) ? "" : " (sans tiers)"));
				$tmpl_x->assign("chk_mouvement", ((current($form_id)==$sql->data["id"]) || ($form_mvt==$sql->data["id"])) ? "selected" : "");
				$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_mouvement");
				$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_ventilation.lst_mouvement");
				if ((current($form_id)==$sql->data["id"]) || ($form_mvt==$sql->data["id"]))
				  { $montant=$sql->data["montant"]; }
			  }
	
			// Liste des tiers
			$lst=ListActiveUsers($sql,"std",$MyOpt["restrict"]["comptes"],"");
		
			foreach($lst as $i=>$tmpuid)
			  {
			  	$resusr=new user_class($tmpuid,$sql);
			
				$tmpl_x->assign("id_tiers", $resusr->data["idcpt"]);
				$tmpl_x->assign("nom_tiers", $resusr->fullname);
				$tmpl_x->assign("chk_tiers", ($form_tiers==$tmpuid) ? "selected" : "");
				$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_tiers");
				$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_ventilation.lst_tiers");
			  }

			$dte=sql2date(current($form_date));

			if ($_REQUEST["form_dte"]!='')
			  { $dte=$_REQUEST["form_dte"]; }
			if ($dte=="")
			  { $dte=date("d/m/Y"); }
	
			$tmpl_x->assign("date_mouvement", $dte);
			$tmpl_x->assign("form_montant", ((current($form_montant)<>0) ? -current($form_montant) : $montant));
			$tmpl_x->assign("form_commentaire", current($form_commentaire));

			$tmpl_x->AUTORESET=0;
			for ($iii=1; $iii<=$MyOpt["ventilationNbLigne"]; $iii++)
			{
				$tmpl_x->assign("ventilid",$iii);
				$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_ventilation");
			}
			$tmpl_x->AUTORESET=1;
		
			$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement");
		  }

		$tmpl_x->assign("form_page", "mvt");
	  	$tmpl_x->parse("corps.aff_mouvement");
	  }

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
