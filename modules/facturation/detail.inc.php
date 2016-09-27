<?
// ---------------------------------------------------------------------------------------------
//   Facturation
//     (@Revision: $usr$ )
// ---------------------------------------------------------------------------------------------
//   Variables  :
//	$usr - id user
//	$facid - id facture	
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
  	require_once ("class/facture.inc.php");
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("detail.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialise les variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);


// ---- Création d'une facture
	if (($fonc=="Enregistrer") && GetDroit("EnregistreFacture") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$fac = new facture_class(0,$sql);
		// Enregistre les valeurs
		$fac->uid=$usr;
		$fac->comment="Facture ".$tabMois[date("n")]." ".date("Y");

		// Enregistre les lignes
		$i=0;
	  	if (is_array($form_facture))
	  	  {
			foreach($form_facture as $idcpt=>$val)
			  {
				$fac->lignes[$i]["id"]=$idcpt;
				$fac->lignes[$i]["montant"]=$val;
		  	  	$total=$total+$val;
		  	  	$i=$i+1;
			  }
		  }

		// Sauvegarde la facture  
		$fac->Save();
		$facid=$fac->id;

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }

// ---- Masque une ligne
	if (($hideline>0) && GetDroit("EnregistreFacture"))
	  {
		$query="UPDATE ".$MyOpt["tbl"]."_compte SET facture='NOFAC' WHERE id='".$hideline."'";
		$sql->Update($query);
	  }

// ---- Relance une facture
	if (($fonc=="relance") && ($facid>0) && (GetDroit("PayerFacture")))
	{
		$query ="SELECT factures.*, utilisateurs.id AS myid, utilisateurs.prenom, utilisateurs.nom, utilisateurs.mail FROM ".$MyOpt["tbl"]."_factures AS factures LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS utilisateurs ON factures.uid=utilisateurs.id WHERE factures.id='$facid' ";
		//$query.=" AND utilisateurs.id=162 ";
		$val=$sql->QueryRow($query);

		$tmail=file("config/mail.relance.txt");

		$mail = '';
		foreach($tmail as $ligne)
		  { $mail.=$ligne; }

		$mail=str_replace("{facnum}",$val["id"],nl2br($mail));
		$mail=str_replace("{facdate}",$tabMois[date("n",strtotime($val["dte"]))]." ".date("Y",strtotime($val["dte"])),$mail);

		// Charge la facture en PDF
  	$fac = new facture_class($facid,$sql);
		$fac->ChargeLignes();
		$fac->ChargeReglements();
		$attach[0]["nom"]="facture.pdf";
		$attach[0]["type"]="text";
		$attach[0]["mime"]="text/plain";
		$attach[0]["data"]=$fac->FacturePDF("S");


		MyMail($from,$val["mail"],"","[".$MyOpt["site_title"]."] : Relance facture ".$val["id"]." pour ".$tabMois[date("n",strtotime($val["dte"]))]." ".date("Y",strtotime($val["dte"])),$mail,"",$attach);

		$tmpl_x->parse("corps.msgok");
	}

// ---- Charge la facture
	$fac=new facture_class($facid,$sql);

	if ($facid=="")
	  {
		$fac->uid=$usr;
	  }

	if ((!GetDroit("AccesFactures")) && (!GetMyId($fac->uid)))
	  { FatalError("Accès non autorisé (AccesFactures)"); }


	$fac->ChargeLignes();
	$fac->ChargeReglements();

// ---- Paiement d'une facture
	if ((($fonc=="Chèque") || ($fonc=="Virement") || ($fonc=="Prélèvement") || ($fonc=="Compte")) && ($facid!="") && GetDroit("PayerFacture") && (!isset($_SESSION['tab_checkpost'][$checktime])) )
	  {
		$restant=$fac->Restant();

		if ($fonc=="Compte")
		  {
		  	$montant=$montantcpt;
		  }

		if (abs($montant)<=abs($restant))
		  {
			$query="SELECT description FROM ".$MyOpt["tbl"]."_mouvement WHERE id='".$MyOpt["id_credit"]."'";
			$res2=$sql->QueryRow($query);

			// Rembourse le compte membre
			if ($fonc=="Compte")
			  {
		 	  	$query="INSERT INTO ".$MyOpt["tbl"]."_compte SET uid='$usr', tiers='".$MyOpt["uid_banque"]."', montant='".$montant."', mouvement='".$res2["description"]."', commentaire='Règlement facture $facid (".$fonc.")', date_valeur='".now()."', facture='$facid', uid_creat='$uid', date_creat='".now()."'";
				$sql->Insert($query);
		 	  	$query="INSERT INTO ".$MyOpt["tbl"]."_compte SET uid='$usr', tiers='".$MyOpt["uid_banque"]."', montant='-".$montant."', mouvement='".$res2["description"]."', commentaire='Compensation compte pour facture $facid', date_valeur='".now()."', facture='NOFAC', uid_creat='$gl_uid', date_creat='".now()."'";
				$sql->Insert($query);
			  }
			else
			  {
		 	  	$query="INSERT INTO ".$MyOpt["tbl"]."_compte SET uid='".$MyOpt["uid_banque"]."', tiers='$usr', montant='-".$montant."', mouvement='".$res2["description"]."', commentaire='Règlement facture $facid (".$fonc.")', date_valeur='".now()."', facture='$facid', uid_creat='$gl_uid', date_creat='".now()."'";
				$sql->Insert($query);
		 	  	$query="INSERT INTO ".$MyOpt["tbl"]."_compte SET uid='$usr', tiers='".$MyOpt["uid_banque"]."', montant='".$montant."', mouvement='".$res2["description"]."', commentaire='Règlement facture $facid (".$fonc.")', date_valeur='".now()."', facture='$facid', uid_creat='$uid', date_creat='".now()."'";
				$sql->Insert($query);

				if ($MyOpt["CompenseClub"]==1)
				  {
				  	$facusr=new user_class($fac->uid,$sql);
				  	
					// Compte le compte du club
			 	  	$query="INSERT INTO ".$MyOpt["tbl"]."_compte SET uid='".$MyOpt["uid_club"]."', tiers='".$MyOpt["uid_banque"]."', montant='-".$montant."', mouvement='".$res2["description"]."', commentaire='Facture $facid (".$facusr->fullname.")', date_valeur='".now()."', facture='$facid', uid_creat='$gl_uid', date_creat='".now()."'";
					$sql->Insert($query);
			 	  	$query="INSERT INTO ".$MyOpt["tbl"]."_compte SET uid='".$MyOpt["uid_banque"]."', tiers='".$MyOpt["uid_club"]."', montant='".$montant."', mouvement='".$res2["description"]."', commentaire='Facture $facid (".$facusr->fullname.")', date_valeur='".now()."', facture='$facid', uid_creat='$uid', date_creat='".now()."'";
					$sql->Insert($query);
				  }
			  }


			$_SESSION['tab_checkpost'][$checktime]=$checktime;
		  }
		else
		  {
		  	echo "Le montant dépasse le restant à payer";
		  }
	  }

// ---- Liste des comptes
	if ((GetDroit("ListeFactures")) && ($liste==""))
	  {
		if (!isset($usr))
		  { $usr=$gl_uid; }

		$lst=ListActiveUsers($sql,"std",$MyOpt["restrict"]["facturation"],"");
	
		foreach($lst as $i=>$tmpuid)
		  {
		  	$resusr=new user_class($tmpuid,$sql);
			$tmpl_x->assign("id_compte", $resusr->data["id"]);
			$tmpl_x->assign("chk_compte", ($resusr->data["id"]==$usr) ? "selected" : "") ;
			$tmpl_x->assign("nom_compte", $resusr->fullname);
			$tmpl_x->parse("corps.aff_compte.lst_compte");
		}
		$tmpl_x->parse("corps.aff_compte");
	  }
	else
	  {
	  	$usr=$gl_uid;
	  }


// ---- Affiche la facture demandée
	$tmpl_x->assign("id_facture",$facid);

	// Nom de l'utilisateur
	$cptusr=new user_class($usr,$sql);
	$tmpl_x->assign("nom_compte", $cptusr->Aff("prenom")." ".$cptusr->Aff("nom"));
	$tmpl_x->assign("id_user",$usr);

	// Définition des variables
	$myColor[0]="F0F0F0";
	$myColor[1]="F7F7F7";
	if (!is_numeric($start))
	  { $start = 0; }

	$tmpl_x->assign("titre_facture",$fac->comment);

// ---- Affiche les lignes de factures
	$col=1;
	$nb=0;
	if (is_array($fac->lignes))
	{
		foreach($fac->lignes as $i=>$v)
		{
			$tmpl_x->assign("ligne_color", $myColor[$col]);
			$col=1-$col;
			$tmpl_x->assign("id_ligne", $v["id"]);
			$tmpl_x->assign("designation_ligne", htmlentities($v["mouvement"],ENT_HTML5,"ISO-8859-1"));
			$tmpl_x->assign("comment_ligne", htmlentities($v["commentaire"],ENT_HTML5,"ISO-8859-1"));
			$tmpl_x->assign("date_ligne", date("d/m/Y",strtotime($v["date_valeur"])));
			$tmpl_x->assign("montant_ligne", htmlentities(AffMontant(round(-$v["montant"],2)),ENT_HTML5,"ISO-8859-1"));
	
			if ($facid=="")
			  {
				$tmpl_x->assign("chk_ligne", "checked");
				$tmpl_x->parse("corps.lst_ligne.edit_ligne");
			  }
	
			$tmpl_x->parse("corps.lst_ligne");
			$nb=$nb+1;
		}
	}

	$r=$fac->restant();
	$c=$cptusr->CalcSoldeFacture();
	$tmpl_x->assign("texte_paiement",$texte_paiement);
	$tmpl_x->assign("total_facture",AffMontant(round($fac->total,2)));
	$tmpl_x->assign("reste_facture",AffMontant($r));
	$tmpl_x->assign("reste_facture1",$r);
	$tmpl_x->assign("reste_facture2",($r>$c) ? $c : $r);
	$tmpl_x->assign("reste_compte",AffMontant($c));

	$fac->updateTotal();

	if ($fac->restant()==0)
	  {
		$fac->Paye();
	  }
	else
	  {
		$fac->NonPaye();
	  }

// ---- Affiche les reglements
	$col=1;
	$fac->ChargeReglements();
	if (is_array($fac->reglements))
	{
		foreach($fac->reglements as $i=>$v)
		{
			$tmpl_x->assign("ligne_color", $myColor[$col]);
			$col=1-$col;
			$tmpl_x->assign("id_ligne", $v["id"]);
			$tmpl_x->assign("designation_ligne", htmlentities($v["mouvement"]));
			$tmpl_x->assign("comment_ligne", htmlentities($v["commentaire"]));
			$tmpl_x->assign("date_ligne", date("d/m/Y",strtotime($v["date_valeur"])));
			$tmpl_x->assign("montant_ligne", AffMontant(htmlentities(round($v["montant"],2))));

			$tmpl_x->parse("corps.show_facture.lst_reglement");
		}
	}

// ---- Affiche les blocs de la page
	if (($facid=="") && ($usr>0) && ($nb>0))
	  { 
		if (!GetDroit("CreeFacture"))
		  { FatalError("Accès non autorisé à cette facture (CreerFacture)"); }
		if  ($nb>0)
		  {
		  	$tmpl_x->parse("corps.edit_facture");
			$tmpl_x->parse("corps.show_total");
		  }

	  }
	else if ($facid>0)
	  {
		$tmpl_x->parse("corps.show_facture");
		$tmpl_x->parse("corps.aff_pdf");
	  }

	if (($fac->paid!="Y") && ($facid>0) && (GetDroit("PayerFacture")))
	  {
		$tmpl_x->parse("corps.aff_payer");
		$tmpl_x->parse("corps.aff_relance");
	  }

// ---- Affiche la page
	$tmpl_x->parse("icone");
	$icone=&$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=&$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=&$tmpl_x->text("corps");


?>
