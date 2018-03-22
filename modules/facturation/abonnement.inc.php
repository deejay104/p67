<?
// ---------------------------------------------------------------------------------------------
//   Abonnement
// ---------------------------------------------------------------------------------------------
//   Variables  :
//	$id - id abonnement
//	$usr - id user
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2 ($Revision: 437 $)
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
	if (!GetDroit("AccesAbonnements")) { FatalError("Accès non authorisé (AccesAbonnements)"); }

	require_once ("class/abonnement.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("abonnement.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Annule
	if ($fonc=="Annuler")
	  {
	  	$mode="";
	  }

// ---- Initialise les variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

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

// ---- Affiche l'abonnement demandé
	if (!is_numeric($id))
	  { $id=0; }
	$abo = new abonnement_class($id,$sql);

// ---- Charge les lignes de l'abonnement
	$abo->LoadLignes();

	$tot=0;

// ---- Mets à jour les lignes en cas de refresh
// && ($fonc=="Enregistrer"))
	if (($mode=="edit") || ($mode=="copy"))
	  {
	  	if (isset($form_poste))
	  	  {
			$ii=-1;
		  	foreach ($form_poste as $i=>$mouvid)
		  	  {
		  	  	if ($i>0)
		  	  	  {
		  	  	  	$abo->lignes[$i]["mouvid"]=$mouvid;
		  	  	  	$abo->lignes[$i]["montant"]=$form_montant[$i];
		  	  	  }
				else if ($mouvid>0)
		  	  	  {
		  	  	  	$abo->lignes[$ii]["mouvid"]=$mouvid;
		  	  	  	$abo->lignes[$ii]["montant"]=$form_montant[$i];
		  	  	  	$ii=$ii-1;
		  	  	  }
			  }
		  }

		if ($fonc2!="")
		  {
			$abo->dte_deb=$form_dte_deb;
			$abo->dte_fin=$form_dte_fin;

			$abo->jour_num=$form_jour_num;
			$abo->jour_sem=$form_jour_sem;
		  }
	  }

	if ($form_uid>0)
	  { $abo->uid=$form_uid; }

	$abonum=$abo->abonum;
	if ($mode=="edit")
	  {
		$abonum=$abo->NewRevision();
	  }

// ---- Sauvegarde
	if (($fonc=="Enregistrer") && ($mode=="edit") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$id=$abo->Save();
		
		if (!is_numeric($id))
		  {
		  	echo $id;
		  }
		else
		  {
			$mode="";
		  }

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }
	else if (($fonc=="Enregistrer") && ($mode=="copy") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$id=$abo->Copy();

		if (!is_numeric($id))
		  {
		  	echo $id;
		  }
		else
		  {
			$abo = new abonnement_class($id,$sql);
			$abo->LoadLignes();
			$abonum=$abo->abonum;
			$mode="";
		  }

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }

	else if ($fonc=="desactive")
	  {
		$abo->Desactive();
		$affrub="abonnements";
	  }

// ---- Impute sur le compte
	if (($fonc=="facturer") && ($id>0))
	  {
		$dte=date("Y-m-d");
		$query ="SELECT usr.prenom, usr.nom, usr.idcpt, abo.id, abo.uid, abo.abonum, abo.jour_num, abo.jour_sem, ligne.mouvid,ligne.montant,mvt.description,mvt.compte FROM ".$MyOpt["tbl"]."_abonnement AS abo ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_abo_ligne AS ligne ON abo.abonum=ligne.abonum ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_mouvement AS mvt ON ligne.mouvid=mvt.id ";
		$query.="LEFT JOIN ".$MyOpt["tbl"]."_utilisateurs AS usr ON abo.uid=usr.id ";
		$query.="WHERE abo.actif='oui' AND usr.actif='oui' AND abo.dtedeb<='$dte' AND abo.dtefin>='$dte' AND abo.id='$id'";
		$sql->Query($query);

		$tabValeur=array();
		for($i=0; $i<$sql->rows; $i++)
		  {
			$sql->GetRow($i);
			$tabValeur[$i]=$sql->data;
		  }

		if (count($tabValeur)>0)
		  {		
			foreach($tabValeur as $i=>$d)
			  {
				$val=$d["montant"];
		  		$query ="INSERT ".$MyOpt["tbl"]."_compte SET ";
		  		$query.="uid='".$d["idcpt"]."', ";
		  		$query.="tiers='".$MyOpt["uid_club"]."', ";
		  		$query.="montant='".(-$val)."', ";
		  		$query.="mouvement='".addslashes($d["description"])."', ";
		  		$query.="commentaire='Abonnement ".strtoupper($tabMois[date("n")])." (".$d["abonum"].")', ";
		  		$query.="date_valeur='".$dte."', ";
		  		$query.="dte='".date("Ym",strtotime($dte))."', ";
		  		$query.="compte='".$d["compte"]."', ";
		  		$query.="uid_creat=0, date_creat='".now()."'";
		  		$sql->Insert($query);
	
		  		$query ="INSERT ".$MyOpt["tbl"]."_compte SET ";
		  		$query.="uid='".$MyOpt["uid_club"]."', ";
		  		$query.="tiers='".$d["idcpt"]."', ";
		  		$query.="montant='".$val."', ";
		  		$query.="mouvement='".addslashes($d["description"])."', ";
		  		$query.="commentaire='Abonnement ".strtoupper($tabMois[date("n")])." (".$d["abonum"].")', ";
		  		$query.="date_valeur='".$dte."', ";
		  		$query.="dte='".date("Ym",strtotime($dte))."', ";
		  		$query.="compte='".$d["compte"]."', ";
		  		$query.="uid_creat=0, date_creat='".now()."'";
				$sql->Insert($query);			
			  }
		
			$tmpl_x->assign("msg_confirmation", "L'imputation sur le compte a été faites.");
			$tmpl_x->assign("msg_type", "msgok");
		  }
		else
		  {
			$tmpl_x->assign("msg_confirmation", "L'abonnement n'est pas actif ou invalide.");
			$tmpl_x->assign("msg_type", "msgerror");
		  }
		$tmpl_x->parse("corps.msgok");		
	  }



// ---- Affiche le titre
	$tmpl_x->assign("abonnement_titre", "Abonnement ".(($mode=="copy") ? "" : $abonum));

	$tmpl_x->assign("form_uid", $abo->uid);
	$tmpl_x->assign("form_dte_deb", $abo->dte_deb);
	$tmpl_x->assign("form_dte_fin", $abo->dte_fin);
	$tmpl_x->assign("form_jour", ($abo->jour_num>0) ? "le ".$abo->jour_num." du mois" : (($abo->jour_sem!="-") ? "Tous les ".$tabJour[$abo->jour_sem] : "aucun") );
	$tmpl_x->assign("mode",$mode);
	$tmpl_x->assign("form_id",$id);
	for($i=1; $i<29; $i++)
	  {
		$tmpl_x->assign("id_jour_num", $i);
		$tmpl_x->assign("nom_jour_num", $i);
		if ($abo->jour_num==$i)
		  { $tmpl_x->assign("chk_jour_num", "selected"); }
		else
		  { $tmpl_x->assign("chk_jour_num", ""); }
		
		$tmpl_x->parse("corps.aff_famille_modif.lst_compte_num");
	  }

	foreach ($tabJour as $i=>$txt)
	  {
		$tmpl_x->assign("id_jour_sem", $i);
		$tmpl_x->assign("nom_jour_sem", $txt);
		if ($abo->jour_sem."a"==$i."a")
		  { $tmpl_x->assign("chk_jour_sem", "selected"); }
		else
		  { $tmpl_x->assign("chk_jour_sem", ""); }
		$tmpl_x->parse("corps.aff_famille_modif.lst_compte_sem");
	  }

// ---- Affiche les lignes
	foreach($abo->lignes as $i=>$ligne)
	  { 

		$tmpl_x->assign("ligne_color", $myColor[$col]);
		$col=1-$col;

		$tmpl_x->assign("ligne_id", $i);

		$tmpl_x->assign("poste_id", "");
		$tmpl_x->assign("poste_description", "-");
		$tmpl_x->assign("chk_poste", "");
		$tmpl_x->parse("corps.lst_ligne.lst_modif_poste.lst_poste");

		foreach($tabPoste as $idPoste=>$t)
		  {
			$tmpl_x->assign("poste_id", $idPoste);
			$tmpl_x->assign("poste_description", $t["description"]);
			$tmpl_x->assign("chk_poste", (($idPoste==$ligne["mouvid"]) ? "selected" : "" ));
			$tmpl_x->parse("corps.lst_ligne.lst_modif_poste.lst_poste");

			if ( ($mode!="copy") && ($mode!="edit") && ($idPoste==$ligne["mouvid"]) && ($idPoste>0))
			  {
				$tmpl_x->parse("corps.lst_ligne.lst_affiche_poste");
			  }
		  }

		
		if (($mode=="copy") || ($mode=="edit"))
		  {
			$tmpl_x->parse("corps.lst_ligne.lst_modif_poste");
			$tmpl_x->assign("poste_montant", sprintf("%01.2f",$tabPoste[$ligne["mouvid"]]["montant"]));
			$tot=$tot+$tabPoste[$ligne["mouvid"]]["montant"];
		  }
		else
		  {
			$tmpl_x->assign("poste_montant", sprintf("%01.2f",$ligne["montant"]));
			$tot=$tot+$ligne["montant"];
		  }
		$tmpl_x->parse("corps.lst_ligne");

	  }

	if (($mode=="copy") || ($mode=="edit"))
	  {
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

		$tmpl_x->parse("corps.submit");
	  }

	$lst=ListActiveUsers($sql,"std",$MyOpt["restrict"]["facturation"],"");
	foreach($lst as $i=>$tmpuid)
	  {
	  	$resusr=new user_class($tmpuid,$sql);
		$tmpl_x->assign("id_compte", $resusr->data["id"]);
		$tmpl_x->assign("chk_compte", ($resusr->data["id"]==$abo->uid) ? "selected" : "") ;
		$tmpl_x->assign("nom_compte", $resusr->fullname);
		if ($resusr->data["id"]==$abo->uid)
		  {
		  	$tmpl_x->assign("nom_famille", $resusr->Aff("fullname"));
		  }
		$tmpl_x->parse("corps.aff_famille_modif.aff_famille_copy.lst_compte");
	}

	if ($mode=="edit")
	  {
		$tmpl_x->parse("corps.aff_famille_modif.aff_famille_edit");
		$tmpl_x->parse("corps.aff_famille_modif");
	  }
	else if ($mode=="copy")
	  {
		$tmpl_x->parse("corps.aff_famille_modif.aff_famille_copy");
		$tmpl_x->parse("corps.aff_famille_modif");
	  }
	else
	  {
		$tmpl_x->parse("corps.aff_famille");
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
