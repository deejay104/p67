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
	require_once ("class/compte.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("vols.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	if (!GetDroit("AccesPageVols")) { FatalError("Accès non autorisé"); }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

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
	if ((($fonc=="Enregistrer") || ($fonc=="Débiter")) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$mvt = new compte_class(0,$sql);
		$tmpl_x->assign("enr_mouvement",$mvt->AfficheEntete());
		$tmpl_x->parse("corps.enregistre.lst_enregistre");
		$msg_result="";

		if (is_array($form_tempsresa))
		{
			foreach ($form_tempsresa as $k=>$tps)
			{
				if ($tps!="")
				{
				  	// $query="SELECT * FROM ".$MyOpt["tbl"]."_calendrier WHERE id=$k";
					// $res=$sql->QueryRow($query);
					$res=new resa_class($k,$sql);

					// Récupérer tarifs pilote depuis la base
					$p=round($tabTarif[$res->uid_ressource][$form_tarif[$k]]["pilote"]*$tps/60,2);

					// Calcul du tarif instructeur
					// Si tarif instructeur mis dans la fiche prendre celui là à la place du tarif sélectionné
					$pi=0;
					if ($res->uid_instructeur>0)
					{
						$pi=round($tabTarif[$res->uid_ressource][$form_tarif[$k]]["instructeur"]*$tps/60,2);

					  	$resinst=new user_class($res->uid_instructeur,$sql,false,true);
						if ($resinst->data["tarif"]>0)
					  	{
							$pi=round($resinst->data["tarif"]*$tps/60,2);
						}
					}

					// S'il y a une réduction de temps on l'a soustrait au temps pilote
					if ($tabTarif[$res->uid_ressource][$form_tarif[$k]]["reduction"]>0)
					{
						$p=round($tabTarif[$res->uid_ressource][$form_tarif[$k]]["pilote"]*($tps-$tabTarif[$res->uid_ressource][$form_tarif[$k]]["reduction"])/60,2);
					}
			
				  	// $query="UPDATE ".$MyOpt["tbl"]."_calendrier SET temps='$tps', tpsreel='".$form_blocresa[$k]."', tarif='".$form_tarif[$k]."' WHERE id=$k";
				  	// $sql->Update($query);
					$res->horadeb=$form_horadeb[$k];
					$res->horafin=$form_horafin[$k];
					$res->temps=$tps;
					$res->tpsreel=$form_blocresa[$k];
					$res->tarif=$form_tarif[$k];
					$msg_result.=$res->Save();

					if ($fonc=="Débiter")
					{
						DebiteVol($k,$tps,$res->uid_ressource,($res->uid_debite>0) ? $res->uid_debite : $res->uid_pilote,$res->uid_instructeur,$form_tarif[$k],$p,$pi,date('Y-m-d',strtotime($res->dte_deb)));
					}
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
					  	$msg_result.="DATE INVALIDE !!!";
					}
					else
					{
						$uid_p=$form_pilote[$k];
						$uid_i=$form_instructeur[$k];
						$uid_a=$idavion;
						$tarif=$form_tarif[$k];
						$horadeb=$form_horadeb[$k];
						$horafin=$form_horafin[$k];
						$tpsreel=$form_bloc[$k];
						
						// Récupérer tarifs pilote depuis la base
						$p=round($tabTarif[$uid_a][$form_tarif[$k]]["pilote"]*$tps/60,2);

						// Calcul du tarif instructeur
						$pi=0;
						if ($uid_i>0)
						{
							$pi=round($tabTarif[$uid_a][$form_tarif[$k]]["instructeur"]*$tps/60,2);
	
						  	$resinst=new user_class($uid_i,$sql,false,true);
							if ($resinst->data["tarif"]>0)
						  	{
								$pi=round($resinst->data["tarif"]*$tps/60,2);
							}
						}

						// S'il y a une réduction de temps on l'a soustrait au temps pilote
						if ($tabTarif[$uid_a][$form_tarif[$k]]["reduction"]>0)
						{
							$p=round($tabTarif[$uid_a][$form_tarif[$k]]["pilote"]*($tps-$tabTarif[$uid_a][$form_tarif[$k]]["reduction"])/60,2);
						}

					  	if (!isset($_SESSION['tab_checkpost'][$checktime]))
						{
							// $query="INSERT INTO ".$MyOpt["tbl"]."_calendrier SET dte_deb='$dte', dte_fin='$dte', uid_pilote='$uid_p', uid_instructeur='$uid_i', uid_avion='$uid_a', tarif='$tarif', temps='$tps', reel='non', horadeb='$horadeb', horafin='$horafin', dte_maj='".now()."', uid_maj=$uid, actif='oui'";
						  	// $id=$sql->Insert($query);
							$res=new resa_class(0,$sql);
							$res->uid_pilote=$uid_p;
							$res->uid_debite=0;
							$res->uid_instructeur=$uid_i;
							$res->uid_ressource=$uid_a;
							$res->tarif=$tarif;
							$res->dte_deb=date('d/m/Y 00:00:00',strtotime($form_date[$k]));
							$res->dte_fin=date('d/m/Y 00:00:00',strtotime($form_date[$k]));
							$res->reel='non';
							$res->accept='oui';
							$res->temps=$tps;
							$res->tpsreel=$tpsreel;
							$res->tpsestime="60";
							$res->horadeb=$horadeb;
							$res->horafin=$horafin;
							$msg_result.=$res->Save();
						}

						if ($fonc=="Débiter")
						{
							DebiteVol($id,$tps,$uid_a,$uid_p,$uid_i,$tarif,$p,$pi,$dte);
						}
					}
				}
			}
		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		if ($msg_result!="")
		{
			// $tmpl_x->assign("msg_confirmation", $msg_result);
			// $tmpl_x->assign("msg_confirmation_class", "msgerror");
			// $tmpl_x->parse("corps.msg_enregistre");
		affInformation($msg_result,"error");
		}
	  
		if ($fonc=="Débiter")
		{
			$tmpl_x->assign("form_page", "vols");
			$tmpl_x->parse("corps.enregistre");
		}
	}

// ---- Enregistre les opérations
	else if (($fonc=="Valider") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		if (is_array($form_mid))
		{
			$ret="";
			$nbmvt=0;
			$ok=0;

			foreach ($form_mid as $id=>$d)
			{			
				$mvt = new compte_class($id,$sql);
				$nbmvt=$nbmvt+$mvt->Debite();
				
				if ($mvt->erreur!="")
				{
					$ret.=$mvt->erreur;
					$ok=1;
				}
				
				if ($form_calid[$id]>0)
				{
				  	$query="UPDATE ".$MyOpt["tbl"]."_calendrier SET prix='".$mvt->montant."', edite='non' WHERE id=".$form_calid[$id];
				  	// echo "$query<BR>";
				  	$sql->Update($query);
  				}
			}

			// $tmpl_x->assign("msg_confirmation", $nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret);
			// $tmpl_x->assign("msg_confirmation_class", ($ret!="") ? "msgerror" : "msgok");			
			// $tmpl_x->parse("corps.msg_enregistre");
			affInformation($nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret,($ret!="") ? "error" : "ok");

		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		$tmpl_x->assign("form_page", "vols");
	}

// ---- Annule les enregistrements
	else if ($fonc=="Annuler")
	{
		if (is_array($form_mid))
		{
			foreach ($form_mid as $id=>$d)
			{			
				$mvt = new compte_class($id,$sql);
				$mvt->Annule();
			}
		}
	}

// ---- Affiche la page demandée
	if ($fonc!="Débiter")
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
		{
			$t=current($tab_avions); 
			$idavion=$t["id"];
		}

		$tmpl_x->assign("nom_avion_edt",$tab_avions[$idavion]["immatriculation"]);

		// Récupère la plus vieille date de saisie des vols
		$query = "SELECT dte_deb,horafin FROM ".$MyOpt["tbl"]."_calendrier WHERE prix>0 AND uid_avion='$idavion' ORDER BY dte_deb DESC LIMIT 0,1";
		$res=$sql->QueryRow($query);
		$dte=$res["dte_deb"];
		$horadeb_prec=$res["horafin"];

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
			{
				$p=$resa["pilote"]->Aff("fullname");
			}

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
			{
				$tps=$resa["resa"]->temps;
			}
			else if (($resa["resa"]->horadeb>0) && ($resa["resa"]->horafin>0))
			{
				$resr=new ress_class($resa["resa"]->uid_ressource,$sql);
				$tps=$resr->CalcHorametre($resa["resa"]->horadeb,$resa["resa"]->horafin);
			}
			else
			{
				$tps="";
			}

			if ($resa["resa"]->tpsreel==0)
			{
				$tbl=$tps;
			}
			else
			{
				$tbl=$resa["resa"]->tpsreel;
			}

			if ($horadeb_prec==0)
			{ $horadeb_prec=$resa["resa"]->horadeb; }
			
			$tmpl_x->assign("idresa", $id);
			$tmpl_x->assign("horadeb",  " <INPUT type=\"text\" id=\"form_horadeb_".$id."\" name=\"form_horadeb[".$id."]\" size=5 value=\"".$resa["resa"]->horadeb."\" style='".(($resa["resa"]->horadeb!=$horadeb_prec) ? "color: #ff0000; background-color: #FFBBAA;" : "")."' OnChange=\"calcHorametre(".$id.");\">");
			$horadeb_prec=$resa["resa"]->horafin;

			$tmpl_x->assign("horafin", "<INPUT type=\"text\" id=\"form_horafin_".$id."\" name=\"form_horafin[".$id."]\" size=5 value=\"".$resa["resa"]->horafin."\" OnChange=\"calcHorametre(".$id.");\">");
			$tmpl_x->assign("temps_vols", " <INPUT type=\"text\" id=\"form_tempsresa_".$id."\" name=\"form_tempsresa[".$id."]\" size=5 value=\"".$tps."\">");
			$tmpl_x->assign("bloc_vols", " <INPUT type=\"text\" name=\"form_blocresa[".$id."]\" size=5 value=\"".$tbl."\">");

			$tmpl_x->assign("destination_vols", $resa["resa"]->destination);
			
			$tmpl_x->assign("distance_vols", "0");
			if ($resa["resa"]->destination!="LOCAL")
			{
				$query="SELECT description, lon, lat FROM ".$MyOpt["tbl"]."_navpoints AS wpt WHERE nom='".$resa["resa"]->destination."'";
				$res=$sql->QueryRow($query);

				if ($res["description"]!="")
				{
					$dist=round(getDistance($MyOpt["terrain"]["latitude"], $MyOpt["terrain"]["longitude"], $res["lat"], $res["lon"], "N"),0)." N";
					$tmpl_x->assign("distance_vols", $dist);
				}
			}

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

			if (is_array($tabTarif[$idavion]))
			{
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
			}
			$tmpl_x->assign("id_new", "N$ii");
			$tmpl_x->parse("corps.aff_vols.lst2_vols");
		}


		$tmpl_x->assign("form_page", "vols");
	  	$tmpl_x->parse("corps.aff_vols");
	}

// ---- Affecte les variables d'affichage
	if (GetModule("aviation"))
	{
		$tmpl_x->parse("infos.vols");
	}

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


// ---- FONCTIONS


function DebiteVol($idvol,$temps,$idavion,$uid_pilote,$uid_instructeur,$tarif,$p,$pi,$dte)
{
	global $MyOpt, $uid,$tmpl_x,$sql;

	$ress = new ress_class($idavion,$sql);
	$pilote = new user_class($uid_pilote,$sql);

	$mvt = new compte_class(0,$sql);
	$mvt->Generate($pilote->idcpt,$ress->poste,"Vol de $temps min (".$ress->Aff("immatriculation","val")."/$tarif)",$dte,$p,array());
	$mvt->Save();
	$tmpl_x->assign("enr_mouvement",$mvt->Affiche());

	$tmpl_x->assign("form_mvtid",$mvt->id);
	$tmpl_x->assign("form_calid",$idvol);
	$tmpl_x->parse("corps.enregistre.lst_enregistre");

	if ($pi<>0)
	{
		$inst = new user_class($uid_instructeur,$sql);
	
		$mvt = new compte_class(0,$sql);
		$mvt->Generate($inst->idcpt,$ress->poste,"Remb. vol d'instruction de $temps min (".$ress->Aff("immatriculation","val")."/$tarif)",$dte,-$pi,array());
		$mvt->Save();
		$tmpl_x->assign("enr_mouvement",$mvt->Affiche());

		$tmpl_x->assign("form_mvtid",$mvt->id);
		$tmpl_x->assign("form_calid","0");
		$tmpl_x->parse("corps.enregistre.lst_enregistre");
	}
}

?>
