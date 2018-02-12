<?
// ---------------------------------------------------------------------------------------------
//   Détail d'un utilisateur
//     ($Author: miniroot $)
//     ($Date: 2016-04-22 20:48:24 +0200 (ven., 22 avr. 2016) $)
//     ($Rev: 456 $)
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
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("chgpwd.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialisation des variables
	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

	$msg_erreur="";
	$msg_confirmation="";

	if ($id>0)
	  { $usr = new user_class($id,$sql,((GetMyId($id)) ? true : false)); }
	else
	  { $usr = new user_class(0,$sql,false); }

	if ((!GetMyId($id)) && (!GetDroit("ModifUserPassword")))
	{
		FatalError("Accès non autorisé (ModifUserPassword)");
	}

// ---- Modification du mot de passe

	if ( ($fonc=="Enregistrer") && ($form_newmdp!="") && ($form_newmdp!="**NONE**") && ( ((GetMyId($id)) && ($form_oldmdp!="")) || (GetDroit("ModifUserPassword")) ) )
	{
		if (($usr->data["password"]==$form_oldmdp) || (GetDroit("ModifUserPassword")))
		{
			$ret=$usr->SaveMdp($form_newmdp);

			if ($ret=="") 
			{
				// ENVOIE DU MDP PAR MAIL (+LOGIN)
				// ---- Récupère l'adresse email de l'émetteur
				$from=$myuser->data["mail"];
				SendMailFromFile($from,$usr->data["mail"],"","[".$MyOpt["site_title"]."] : Changement de votre mot de passe",array("username"=>$usr->fullname,"initiales"=>$usr->data["initiales"]),"chgpwd");
			
				$msg_confirmation.="Votre mot de passe a été mis à jour.<br />";
			}
			else
			{ 
				$msg_erreur.="Erreur lors de l'enregistrement du mot de passe.<br />";
			}
		}
		else
		{
		 	$msg_erreur.="L'ancien mot de passe ne correspond pas !<br />";
		}
	}
	else if ($fonc=="Enregistrer")
	{
	 	$msg_erreur.="Erreur lors de la mise à jour du mot de passe !<br />";
	}

	if ($fonc=="Annuler")
	{
		$affrub="detail";
	}
	  
// ---- Affiche les infos
	if ((is_numeric($id)) && ($id>0))
	{
			$usr = new user_class($id,$sql,((GetMyId($id)) ? true : false));
			$usrmaj = new user_class($usr->uidmaj,$sql);
	
			$tmpl_x->assign("id", $id);
			$tmpl_x->assign("info_maj", $usrmaj->prenom." ".$usrmaj->nom." le ".sql2date($usr->dtemaj));	
	}


	if ($usr->data["password"]=="")
	  { $tmpl_x->parse("corps.neverset"); }
	  	

	foreach($usr->data as $k=>$v)
	  { $tmpl_x->assign("form_$k", $usr->aff($k,$typeaff)); }

	if (!GetDroit("ModifUserPassword"))
	  { $tmpl_x->parse("corps.oldpwd"); }
	else
	  { $tmpl_x->parse("corps.oldpwdadm"); }

	if ((GetMyId($id)) || (GetDroit("ModifUser")))
	  { $tmpl_x->parse("infos.modification"); }

	if (GetDroit("CreeUser"))
	  { $tmpl_x->parse("infos.ajout"); }

	if ((GetDroit("DesactiveUser")) && ($usr->actif=="oui"))
	  { $tmpl_x->parse("infos.desactive"); }

	if ((GetDroit("SupprimeUser")) && ($usr->actif=="off"))
	  { $tmpl_x->parse("infos.suppression"); }

// ---- Messages
	if ($msg_erreur!="")
	{
		affInformation($msg_erreur,"error");
	}		

	if ($msg_confirmation!="")
	{
		affInformation($msg_confirmation,"ok");
	}


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
