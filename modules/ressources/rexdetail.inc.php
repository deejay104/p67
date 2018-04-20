<?
/*
    SoceIt v2.4
    Copyright (C) 2018 Matthieu Isorez

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
	$tmpl_x = new XTemplate (MyRep("rexdetail.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

	require_once ("class/rex.inc.php");

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- VÃ©rifie les variables
	if (!is_numeric($id))
	{ $id=0; }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	$msg_erreur="";
	$msg_confirmation="";

// ---- Sauvegarde
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	{
		$rex=new rex_class($id,$sql);
		if (count($form_rex)>0)
		{
			foreach($form_rex as $k=>$v)
		  	{
		  		$msg_erreur.=$rex->Valid($k,$v);
		  	}
			$msg_confirmation.="Vos données ont été enregistrées.<BR>";
		}

		$rex->Save();
		if ($id==0)
		{
			$id=$rex->id;
		}

		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	}

// ---- Suppression
	if ($fonc=="supprimer")
	{
		$rex=new rex_class($id,$sql);
		$rex->Delete();
		$affrub="rex";
	}
// ---- Type d'affichage

	$typeaff="aff";
	if (($id==0) || ($fonc=="editer"))
	{
		$typeaff="form";
		$tmpl_x->parse("corps.form_submit");
	}
	
// ---- Affiche les informations
	$rex=new rex_class($id,$sql);
	$tmpl_x->assign("id",$id);

	foreach($rex->data as $k=>$v)
	{
		$tmpl_x->assign("form_".$k,$rex->Aff($k,$typeaff));
	}
	
	$ress=new ress_class($rex->data["uid_avion"],$sql);
	$tmpl_x->assign("form_avion",$ress->Aff("Immat","html"));
	
// ---- Messages
	if ($msg_erreur!="")
	{
		affInformation($msg_erreur,"error");
	}		

	if ($msg_confirmation!="")
	{
		affInformation($msg_confirmation,"ok");
	}

// ---- Menu
	if ((GetDroit("ModifRex")) || ($gl_uid==$rex->data["uid_creat"]))
	{
		$tmpl_x->parse("corps.editer");
	}
	if (GetDroit("SupprimeRex"))
	{
		$tmpl_x->parse("corps.supprimer");
	}

// ---- Infos de dernières maj
	$usrmaj = new user_class($rex->data["uid_modif"],$sql);
	$tmpl_x->assign("info_maj", $usrmaj->aff("fullname")." le ".sql2date($rex->data["dte_modif"]));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>