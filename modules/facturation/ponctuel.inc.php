<?
// ---------------------------------------------------------------------------------------------
//   Saisie d'une présence ponctuelle
//     ($Author: miniroot $)
//     ($Date: 2014-09-16 21:09:58 +0200 (mar., 16 sept. 2014) $)
//     ($Revision: 435 $)
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
	if (!GetDroit("AccesPresence")) { FatalError("Accès non autorisé (AccesPresence)"); }

	require_once ("class/abonnement.inc.php");
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("ponctuel.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialise les variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Rempli le tableau des plages horaires
	$query="SELECT * FROM ".$MyOpt["tbl"]."_plage";
	$sql->Query($query);
	$tabPlage=array();

	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);

		$tabPlage[$sql->data["plage"]]["nom"]=$sql->data["titre"];
		$tabPlage[$sql->data["plage"]]["deb"]=$sql->data["deb"];
		$tabPlage[$sql->data["plage"]]["fin"]=$sql->data["fin"];
		$tabPlage[$sql->data["plage"]]["jour"][$sql->data["jour"]]=1;
	  }

// ---- Récupère la liste des mouvements
	$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement ORDER BY ordre,description";
	$sql->Query($query);
	$tabPoste=array();
	for($i=0; $i<$sql->rows; $i++)
	  { 
		$sql->GetRow($i);
		$tabPoste[$sql->data["id"]]["description"]=$sql->data["description"];
		$tabPoste[$sql->data["id"]]["montant"]=$sql->data["montant"];
	  }

	$myColor[0]="F0F0F0";
	$myColor[1]="F7F7F7";
	$col=1;

// ---- Enregistre
	$msg_result="";
	if ($fonc=="Enregistrer")
	{
		// Vérifie la date
		$dte=date2sql($form_dte);
		if (date2sql($form_dte)=="nok")
		  {
		  	$msg_result="DATE INVALIDE !!!";
		  	$dte="";
		  	$dtedeb=0;
		  }
		else
		  {
		  	$dtedeb=strtotime(date2sql($form_dte));
		  }

		if ((date2sql($form_dte_end)=="nok") || ($form_dte_end==""))
		  {
		  	$dteend=strtotime($dte)+1;
		  }
		else
		  {
		  	$dteend=strtotime(date2sql($form_dte_end));
		  }


		// Défini le type de jour pour la présence
		$todayNum=date("w",$dtedeb);
		$query="SELECT id FROM ".$MyOpt["tbl"]."_vacances WHERE dtedeb<='$dte' AND dtefin>='$dte'";
		$resvac=$sql->QueryRow($query);
	
		if (($resvac["id"]>0) && ($tabPresenceJour[$todayNum]!=""))
		  {
			$todayNum=7;
		  }
		$todayType=$tabPresenceJour[$todayNum];

		$usr = new user_class($form_uid,$sql);

		if ((is_array($form_poste)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
		{
			$f="";
			for ($d=$dtedeb; $d<=$dteend; $d=$d+86400)
			  {
			  	$dte=date("Y-m-d",$d);
				
				foreach($form_poste as $i=>$ligne)
				  { 
					if ($ligne>0)
					{
						$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE id=$ligne";
						$res=$sql->QueryRow($query);
	
						$type=$todayType.$res["j".$todayNum];
	
						if ($res["j".$todayNum]=="")
						  {
						  	$msg_result.="Pas de type de présence défini pour le ".$tabJour[$todayNum]."<br \>";
						  }
	
	
						if ($res["debiteur"]=="B")
						  { $deb=$MyOpt["uid_banque"]; }
						else if ($res["debiteur"]=="C")
						  { $deb=$MyOpt["uid_club"]; }
						else if ($res["debiteur"]>0)
						  { $deb=$res["debiteur"]; }
						else
						  { $deb=$form_uid; }
				
						if ($res["crediteur"]=="B")
						  { $cre=$MyOpt["uid_banque"]; }
						else if ($res["crediteur"]=="C")
						  { $cre=$MyOpt["uid_club"]; }
						else if ($res["crediteur"]>0)
						  { $cre=$res["crediteur"]; }
						else
						  { $cre=$form_uid; }
	
						if (($cre>0) && ($deb>0) && ($dte!=""))
						  {

							if ($f=="")
							  {
						  		$query ="INSERT ".$MyOpt["tbl"]."_compte SET ";
						  		$query.="uid='".$deb."', ";
						  		$query.="tiers='".$cre."', ";
						  		$query.="montant='".(-$res["montant"])."', ";
						  		$query.="mouvement='".addslashes($res["description"])."', ";
						  		$query.="commentaire='Présence ponctuelle le ".$form_dte."', ";
						  		$query.="date_valeur='".$dte."', ";
						  		$query.="dte='".date("Ym",strtotime($dte))."', ";
						  		$query.="compte='".$res["compte"]."', ";
						  		$query.="uid_creat=$uid, date_creat='".now()."'";
						  		$sql->Insert($query);

						  		$query ="INSERT ".$MyOpt["tbl"]."_compte SET ";
						  		$query.="uid='".$cre."', ";
						  		$query.="tiers='".$deb."', ";
						  		$query.="montant='".($res["montant"])."', ";
						  		$query.="mouvement='".addslashes($res["description"])."', ";
						  		$query.="commentaire='Présence ponctuelle le ".$form_dte."', ";
						  		$query.="date_valeur='".$dte."', ";
						  		$query.="dte='".date("Ym",strtotime($dte))."', ";
						  		$query.="compte='".$res["compte"]."', ";
						  		$query.="uid_creat=$uid, date_creat='".now()."'";
						  		$sql->Insert($query);

					  		  }
					


							if ($res["j".$todayNum]!="")
							  {
	
								if (is_array($MyOpt["tabPresencePlage"][$type]))
								  {
	
									foreach($MyOpt["tabPresencePlage"][$type] as $ii=>$t) 
									  {
										$st=$tabPlage[$t]["deb"];
										$et=$tabPlage[$t]["fin"];
										$query ="INSERT ".$MyOpt["tbl"]."_presence SET uid='".$usr->uid."',dte='".date("Ym",strtotime($dte))."',dtedeb='$dte ".$st.":00',dtefin='$dte ".$et.":00',type='".substr($type,0,1).$t."',zone='".$usr->zone."',regime='".$usr->data["regime"]."',tpspaye='".($et-$st)."',tpsreel='".($et-$st)."',age='".$usr->CalcAge($dte)."',handicap='".$usr->data["handicap"]."'";
										$sql->Insert($query);
	
									  }
								  }
								else
								  {
									$st=$tabPlage[substr($type,1,1)]["deb"];
									$et=$tabPlage[substr($type,1,1)]["fin"];
									$query ="INSERT ".$MyOpt["tbl"]."_presence SET uid='".$usr->uid."',dte='".date("Ym",strtotime($dte))."',dtedeb='$dte ".$st.":00',dtefin='$dte ".$et.":00',type='".$type."',zone='".$usr->zone."',regime='".$usr->data["regime"]."',tpspaye='".($et-$st)."',tpsreel='".($et-$st)."',age='".$usr->CalcAge($dte)."',handicap='".$usr->data["handicap"]."'";
									$sql->Insert($query);
								  }
						  	  }

						}
					}
				}
			  	$f="ok";
			  }
			$msg_result.="<font color=\"green\"><b>La présence ponctuelle a été enregistrée.</b></font><br />";
		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		$form_poste=array();
	}

// ---- Annule
	if ($fonc=="Annuler")
	{
		$form_poste=array();
	}

	if ($msg_result!="")
	  { $tmpl_x->assign("msg_result",$msg_result); }

// ---- Affiche le titre

	$tmpl_x->assign("form_dte", $form_dte);
	$tmpl_x->assign("form_dte_end", $form_dte_end);

// ---- Affiche les lignes
	$ii=0;
	if (is_array($form_poste))
	{
		foreach($form_poste as $i=>$ligne)
		  { 
			$ii=$ii+1;

			if ($ligne>0)
			{
				$tmpl_x->assign("ligne_color", $myColor[$col]);
				$col=1-$col;
		
				$tmpl_x->assign("ligne_id", $ii);
		
				$tmpl_x->assign("poste_id", "");
				$tmpl_x->assign("poste_description", "-");
				$tmpl_x->assign("chk_poste", "");
				$tmpl_x->parse("corps.lst_ligne.lst_modif_poste.lst_poste");
		
				foreach($tabPoste as $idPoste=>$t)
				  {
					$tmpl_x->assign("poste_id", $idPoste);
					$tmpl_x->assign("poste_description", $t["description"]);
					$tmpl_x->assign("chk_poste", (($idPoste==$ligne) ? "selected" : "" ));
					$tmpl_x->parse("corps.lst_ligne.lst_modif_poste.lst_poste");
		
				  }
		
				
				$tmpl_x->parse("corps.lst_ligne.lst_modif_poste");
				$tmpl_x->assign("poste_montant", sprintf("%01.2f",$tabPoste[$ligne]["montant"]));
				$tot=$tot+$tabPoste[$ligne]["montant"];
				$tmpl_x->parse("corps.lst_ligne");
			}	
		  }
	}


	$tmpl_x->assign("ligne_color", $myColor[$col]);

	$tmpl_x->assign("ligne_id", "0");
	$tmpl_x->assign("poste_id", "");
	$tmpl_x->assign("poste_description", "-");
	$tmpl_x->assign("chk_poste", "");
	$tmpl_x->parse("corps.lst_ligne.lst_modif_poste.lst_poste");

	foreach($tabPoste as $idPoste=>$t)
	  {
		$tmpl_x->assign("poste_id", $idPoste);
		$tmpl_x->assign("poste_description", $t["description"]);
		$tmpl_x->assign("chk_poste", "");
		$tmpl_x->parse("corps.lst_ligne.lst_modif_poste.lst_poste");
	  }
	$tmpl_x->parse("corps.lst_ligne.lst_modif_poste");

	$tmpl_x->assign("poste_montant", "0.00");
	$tmpl_x->parse("corps.lst_ligne");



// ---- Affiche la liste des familles
	$lst=ListActiveUsers($sql,"std",$MyOpt["restrict"]["facturation"],"");
	foreach($lst as $i=>$tmpuid)
	  {
	  	$resusr=new user_class($tmpuid,$sql);
		$tmpl_x->assign("id_compte", $resusr->data["idcpt"]);
		$tmpl_x->assign("chk_compte", ($resusr->data["idcpt"]==$form_uid) ? "selected" : "") ;
		$tmpl_x->assign("nom_compte", $resusr->fullname);
		if ($resusr->data["id"]==$abo->uid)
		  {
		  	$tmpl_x->assign("nom_famille", $resusr->Aff("fullname"));
		  }
		$tmpl_x->parse("corps.lst_compte");
	}


	$tmpl_x->assign("form_total", sprintf("%01.2f",$tot));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=&$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=&$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=&$tmpl_x->text("corps");

?>
