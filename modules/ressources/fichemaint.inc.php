<?
// ---------------------------------------------------------------------------------------------
//   Page de saisie pour la maintenance
//   
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

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("fichemaint.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérification des données
	if (!is_numeric($uid_avion))
	  { $uid_avion=0; }
	if (!is_numeric($form_avion))
	  { $form_avion=0; }

	$form_description=preg_replace("/<BR[^>]*>/i","\n",$form_description);
	$form_description=preg_replace("/<[^>]*>/i","",$form_description);

	
// ---- Enregistre
	$msg_erreur="";
	$affrub="";
	if (($fonc=="Enregistrer") && ($form_avion>0) && ($form_description!="") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$fiche=new fichemaint_class(0,$sql);
		
		$fiche->uid_avion=$form_avion;
		$fiche->uid_valid=0;
		$fiche->dte_valid="";
		$fiche->traite="";
		$fiche->uid_planif=0;
		$fiche->description=$form_description;

		$fiche->Save();

		if ($fiche->id>0)
		  {
		  	$_SESSION['tab_checkpost'][$checktime]=$checktime;
		  	$form_description="";
		  	$msg_ok="Fiche créée.<BR>";
		  }
		else
		  {
		  	$msg_erreur="Erreur lors de l'enregistrement !<BR>";
		  }

	  }
	else if (($fonc=="Enregistrer") && ($form_avion>0) && ($form_description==""))
	  {
		$msg_erreur="Il faut saisir une description pour l'incident !<BR>";
	  }
	else if ($fonc=="Retour")
	  {
	  	$form_description="";
	  	$affrub="index";
	  }
	else
	  {
	  	$form_description="";
	  }

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Charge les templates
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Messages
	if ($msg_erreur!="")
	{
		affInformation($msg_erreur,"error");
	}		

	if ($msg_ok!="")
	{
		affInformation($msg_ok,"ok");
	}
	
// ---- Liste les avions
	$lst=ListeRessources($sql);

	foreach($lst as $i=>$rid)
	  {
		$resr=new ress_class($rid,$sql);
		
		$tmpl_x->assign("uid_avion", $resr->id);
		$tmpl_x->assign("nom_avion", $resr->aff("immatriculation","val"));
		if ($uid_avion==$resr->id)
		  { $tmpl_x->assign("chk_avion", "selected"); }
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.lst_avion");
	  }

// ---- Affiche la description
	$tmpl_x->assign("form_description", htmlentities($form_description));

// ---- Affiche les liens
	if (GetDroit("PlanifieMaintenance"))
	  {
		$tmpl_x->parse("corps.aff_planif");
	  }

// ---- Affecte les variables d'affichage
	if ($affrub=="")
	  {
		$tmpl_x->parse("icone");
		$icone=$tmpl_x->text("icone");
		$tmpl_x->parse("infos");
		$infos=$tmpl_x->text("infos");
		$tmpl_x->parse("corps");
		$corps=$tmpl_x->text("corps");
	  }
?>