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

	require_once ("class/compte.inc.php");

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Enregistre le mouvement
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$dte=date2sql($form_date);
		if ($dte=="nok")
		{
		  	$msg_result="DATE INVALIDE !!!";
		  	$dte="";
		}
		$mvt = new compte_class(0,$sql);
		$tmpl_x->assign("enr_mouvement",$mvt->AfficheEntete());
		$tmpl_x->parse("corps.enregistre.lst_visualisation");

		$ventil=array();
		$ventil["ventilation"]=$form_ventilation;
		
		foreach($form_montant_ventil as $i=>$p)
		{
			if ($p<>0)
			{
				$ventil["data"][$i]["poste"]=$form_poste_ventil[$i];
				$ventil["data"][$i]["tiers"]=$form_tiers_ventil[$i];
				$ventil["data"][$i]["montant"]=$form_montant_ventil[$i];
			}
		}

		$mvt = new compte_class(0,$sql);
		$mvt->Generate($form_tiers,$form_poste,trim($form_commentaire),date2sql($form_date),$form_montant,$ventil,($form_facture=="") ? "NOFAC" : "");
		$mvt->Save();
		$tmpl_x->assign("enr_mouvement",$mvt->Affiche());
		$tmpl_x->parse("corps.enregistre.lst_visualisation");


		$_SESSION['tab_checkpost'][$checktime]=$checktime;

		if ($msg_result!="")
		{
			affInformation($msg_result,"error");
			$fonc="";
		}
		else
		{
		  	$tmpl_x->assign("form_date", $form_date);
		  	$tmpl_x->assign("form_poste", $form_poste);
		  	$tmpl_x->assign("form_commentaire", $form_commentaire);
		  	$tmpl_x->assign("form_montant", $form_montant);
		  	$tmpl_x->assign("form_tiers", $form_tiers);

		  	$tmpl_x->assign("msg_resultat", "");
			$tmpl_x->parse("corps.enregistre");
		}
	}


// ---- Enregistre les opérations
	else if (($fonc=="Valider") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$ret="";
		$nbmvt="";
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
		}

		$form_tiers=0;
		affInformation($nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret,($ret!="") ? "error" : "ok");
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
	if ($fonc!="Enregistrer")
	{
		if (!isset($form_tiers))
		{
			$form_tiers=0;
		}
		if (!isset($form_id))
		{
			$form_id=0;
		}
		if (!isset($form_poste))
		{
			$form_poste=0;
		}			
		if (!isset($form_date))
		{
			$form_date=date("Y-m-d");
		}		
		if (!isset($form_montant))
		{
			$form_montant=0;
		}		
		if (!isset($form_commentaire))
		{
			$form_commentaire="";
		}		

		// Liste des mouvements
		$query = "SELECT * FROM ".$MyOpt["tbl"]."_mouvement WHERE actif='oui' ORDER BY ordre,description";
		$sql->Query($query);
		$montant=0;

		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
		
			$tmpl_x->assign("id_mouvement", $sql->data["id"]);
			$tmpl_x->assign("nom_mouvement", $sql->data["description"].((($sql->data["debiteur"]=="0") || ($sql->data["crediteur"]=="0")) ? "" : " (sans tiers)"));
			$tmpl_x->assign("chk_mouvement", (($form_id==$sql->data["id"]) || ($form_poste==$sql->data["id"])) ? "selected" : "");
			$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_mouvement");
			$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_ventilation.lst_mouvement");
			if (($form_id==$sql->data["id"]) || ($form_poste==$sql->data["id"]))
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

		$dte=sql2date($form_date);

		if ((isset($_REQUEST["form_dte"])) && ($_REQUEST["form_dte"]!=''))
		  { $dte=$_REQUEST["form_dte"]; }
		if ($dte=="")
		  { $dte=date("d/m/Y"); }

		$tmpl_x->assign("date_mouvement", $dte);
		$tmpl_x->assign("form_montant", (($form_montant<>0) ? $form_montant : $montant));
		$tmpl_x->assign("form_commentaire", $form_commentaire);

		$tmpl_x->AUTORESET=0;
		for ($iii=1; $iii<=$MyOpt["ventilationNbLigne"]; $iii++)
		{
			$tmpl_x->assign("ventilid",$iii);
			$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement.lst_ventilation");
		}
		$tmpl_x->AUTORESET=1;
	
		$tmpl_x->parse("corps.aff_mouvement.lst_aff_mouvement");

		$tmpl_x->assign("form_page", "mvt");
	  	$tmpl_x->parse("corps.aff_mouvement");
	}



// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
