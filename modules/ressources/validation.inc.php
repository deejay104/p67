<?
// ---------------------------------------------------------------------------------------------
//   Page de visualisation des maintenances
//     ($Author: miniroot $)
//     ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 fÃ©vr. 2016) $)
//     ($Revision: 445 $)
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
	require_once ("class/maintenance.inc.php");
	require_once ("class/ressources.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("validation.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérification des données
	if (!is_numeric($uid_avion))
	  { $uid_avion=0; }
// ---- Enregistre
	$msg_erreur="";

	if ((GetDroit("ValideFichesMaintenance")) && ($fonc=="Accepter") && (is_array($form_valid)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
			foreach($form_valid as $fid=>$k)
			  {
				$fiche=new fichemaint_class($fid,$sql);
				$fiche->uid_valid=$uid;
				$fiche->traite="non";
				$fiche->Save();
			  }	
	  }
	else if ((GetDroit("RefuserFicheMaintenance")) && ($fonc=="Refuser") && (is_array($form_valid)) && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
			foreach($form_valid as $fid=>$k)
			  {
				$fiche=new fichemaint_class($fid,$sql);
				$fiche->uid_valid=$uid;
				$fiche->traite="ref";
				$fiche->Save();
	
			  }
	  }

 
	if ($msg_erreur=="")
	  {
			$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Charge les templates
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);
	$tmpl_x->assign("msg_erreur", $msg_erreur);

// ---- Affiche la liste des opérations à valider
		$tabTitre=array();
		$tabTitre["valid"]["aff"]=" ";
		$tabTitre["valid"]["width"]=30;
		$tabTitre["ress"]["aff"]="Avion";
		$tabTitre["ress"]["width"]=100;
		$tabTitre["auteur"]["aff"]="Auteur";
		$tabTitre["auteur"]["width"]=150;
		$tabTitre["dtecreat"]["aff"]="Date";
		$tabTitre["dtecreat"]["width"]=120;
		$tabTitre["description"]["aff"]="Description";
		$tabTitre["description"]["width"]=400;
	
		$lstFiche=GetValideFiche($sql,$uid_avion);
		if (count($lstFiche)>0)
		  {
			foreach($lstFiche as $i=>$id)
			  {
				$fiche = new fichemaint_class($id,$sql,false);

				$tabValeur[$i]["valid"]["val"]="";
				$tabValeur[$i]["valid"]["aff"]="<input type='checkbox' name='form_valid[".$id."]'>";

				$ress = new ress_class($fiche->uid_avion,$sql,false);
				$tabValeur[$i]["ress"]["val"]=strtoupper($ress->immatriculation);
				$tabValeur[$i]["ress"]["aff"]=strtoupper($ress->immatriculation);
				
				$usr = new user_class($fiche->uid_creat,$sql,false);
				$tabValeur[$i]["auteur"]["val"]=$usr->Aff("fullname","val");
				$tabValeur[$i]["auteur"]["aff"]=$usr->Aff("fullname");
				$tabValeur[$i]["dtecreat"]["val"]=sql2date($fiche->dte_creat,"jour");
				$tabValeur[$i]["dtecreat"]["aff"]=sql2date($fiche->dte_creat,"jour");
				$tabValeur[$i]["description"]["val"]=$fiche->description;
				$tabValeur[$i]["description"]["aff"]=htmlentities($fiche->description);
		
			  }
		  }
		else
		  {
				$tabValeur[$i]["ress"]["val"]="";
				$tabValeur[$i]["ress"]["aff"]="";
				$tabValeur[$i]["auteur"]["val"]="";
				$tabValeur[$i]["auteur"]["aff"]="";
				$tabValeur[$i]["dtecreat"]["val"]="";
				$tabValeur[$i]["dtecreat"]["aff"]="";
				$tabValeur[$i]["description"]["val"]="-Aucune fiche en cours-";
				$tabValeur[$i]["description"]["aff"]="-Aucune fiche en cours-";
		  }
		if ($order=="") { $order="ress"; }
		if ($trie=="") { $trie="d"; }
	
		$tmpl_x->assign("aff_tabavalider",AfficheTableau($tabValeur,$tabTitre,$order,$trie));

	if (GetDroit("ValideFichesMaintenance"))
	  {
		$tmpl_x->parse("corps.aff_valide");
	  }

// ---- Liste des avions
	$lst=ListeRessources($sql);

	foreach($lst as $i=>$id)
	  {
		$ress=new ress_class($id,$sql);

		$tmpl_x->assign("uid_avion", $ress->id);
		$tmpl_x->assign("nom_avion", strtoupper($ress->immatriculation));
		if ($uid_avion==$ress->id)
		  { $tmpl_x->assign("chk_avion", "selected"); }
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.lst_avion");
	  }

// ---- Affiche la liste des fiches

	$tabTitre=array();
	$tabTitre["ress"]["aff"]="Avion";
	$tabTitre["ress"]["width"]=60;
	$tabTitre["auteur"]["aff"]="Auteur";
	$tabTitre["auteur"]["width"]=150;
	$tabTitre["dtecreat"]["aff"]="Date";
	$tabTitre["dtecreat"]["width"]=80;
	$tabTitre["description"]["aff"]="Description";
	$tabTitre["description"]["width"]=350;
	$tabTitre["dteresolv"]["aff"]="Prévision";
	$tabTitre["dteresolv"]["width"]=80;

	$tabValeur=array();

	$lstFiche=GetActiveFiche($sql,$uid_avion);
	if (count($lstFiche)>0)
	  {
		foreach($lstFiche as $i=>$id)
		  {
			$fiche = new fichemaint_class($id,$sql);

			$ress = new ress_class($fiche->uid_avion,$sql,false);
			$tabValeur[$i]["ress"]["val"]=strtoupper($ress->immatriculation);
			$tabValeur[$i]["ress"]["aff"]=strtoupper($ress->immatriculation);
			
			$usr = new user_class($fiche->uid_creat,$sql,false);
			$tabValeur[$i]["auteur"]["val"]=$usr->Aff("fullname","val");
			$tabValeur[$i]["auteur"]["aff"]=$usr->Aff("fullname");

			$tabValeur[$i]["dtecreat"]["val"]=sql2date($fiche->dte_creat,"jour");
			$tabValeur[$i]["dtecreat"]["aff"]=sql2date($fiche->dte_creat,"jour");
			$tabValeur[$i]["description"]["val"]=$fiche->description;
			$tabValeur[$i]["description"]["aff"]=htmlentities($fiche->description);

			if ($fiche->uid_planif>0)
			  {
			  	$maint = new maint_class($fiche->uid_planif,$sql);
				$tabValeur[$i]["dteresolv"]["val"]=sql2date($maint->dte_deb,"jour");
				$tabValeur[$i]["dteresolv"]["aff"]="&nbsp;&nbsp;<a href='maintenance.php?rub=detail&id=".$maint->id."'>".sql2date($maint->dte_deb,"jour")."</a>";
			  }
			else
			  {
				$tabValeur[$i]["dteresolv"]["val"]="0";
				$tabValeur[$i]["dteresolv"]["aff"]="&nbsp;&nbsp;"."N/A";
			  }
	
		  }
	  }
	else
	  {
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
	  }



	if ($order=="") { $order="ress"; }
	if ($trie=="") { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
