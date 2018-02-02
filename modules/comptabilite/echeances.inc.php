<?
// ---------------------------------------------------------------------------------------------
//   Saisie des mouvements
// ---------------------------------------------------------------------------------------------
//   Variables  : 
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2 ($Revision: 445 $)
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
// ---- Refuse l'accès en direct
	if ((!isset($token)) || ($token==""))
	  { header("HTTP/1.0 401 Unauthorized"); exit; }

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("echeances.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	if (!GetDroit("AccesPageEcheances")) { FatalError("Accès non autorisé"); }

	if (!is_numeric($form_id))
	{
		$form_id=0;
	}
	require_once ("class/echeance.inc.php");
	require_once ("class/compte.inc.php");

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);

// ---- Affiche les types d'échéance
	$query="SELECT * FROM ".$MyOpt["tbl"]."_echeancetype ORDER BY description";
	$sql->Query($query);

	$form_poste=0;
	$form_description="";
	for($i=0; $i<$sql->rows; $i++)
	{
		$sql->GetRow($i);
		$tmpl_x->assign("form_echeanceid",$sql->data["id"]);
		$tmpl_x->assign("form_echeance",$sql->data["description"]);
		$tmpl_x->assign("select_echeance",($sql->data["id"]==$form_id) ? "selected" : "");

		if ( ($sql->data["id"]==$form_id) && ($sql->data["poste"]>0))
		{
			$form_poste=$sql->data["poste"];
			$form_commentaire=$sql->data["description"];
			$tmpl_x->assign("form_cout",$sql->data["cout"]);
			if (($fonc!="Débiter") && (GetDroit($sql->data["droit"])))
			{
				$tmpl_x->parse("corps.aff_debite");
			}
		}
		$tmpl_x->parse("corps.lst_echeance");
	}
	$tmpl_x->assign("form_id",$form_id);

// ---- Valide le débit des échéances
	$save=false;

	if ($fonc=="Débiter")
	{
		$tmpl_x->assign("form_date",$form_date);

		$mvt = new compte_class(0,$sql);
		$tmpl_x->assign("enr_mouvement",$mvt->AfficheEntete());
		$tmpl_x->parse("corps.aff_visualisation.lst_visualisation");

		foreach ($form_debite as $id=>$d)
		{
			$dte = new echeance_class($id,$sql,0);
			$usr = new user_class($dte->uid,$sql,false);
			$mvt = new compte_class(0,$sql);

			$mvt->Generate($usr->idcpt,$form_poste,$form_commentaire." jusqu'au ".sql2date($form_date),date("Y-m-d"),$form_cout,array());
			$mvt->Save();

			$tmpl_x->assign("form_mvtid",$mvt->id);
			$tmpl_x->assign("form_dteid",$id);
			$tmpl_x->assign("enr_mouvement",$mvt->Affiche());
			$tmpl_x->parse("corps.aff_visualisation.lst_visualisation");
			

		}
		$tmpl_x->parse("corps.aff_visualisation");
		$save=true;
	}

// ---- Enregistre le débit des échéances
	if ($fonc=="Valider")
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
			
			$dte = new echeance_class($form_dteid[$id],$sql,0);
			$dte->dte_echeance=$form_date;
			$dte->Save();
		}

		// $tmpl_x->assign("msg_confirmation", $nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret);
		// $tmpl_x->assign("msg_confirmation_class", ($ret!="") ? "msgerror" : "msgok");		
		// $tmpl_x->parse("corps.msg_enregistre");
		affInformation($nbmvt." Mouvement".(($nbmvt>1) ? "s" : "")." enregistré".(($nbmvt>1) ? "s" : "")."<br />".$ret,($ret!="") ? "error" : "ok");
	}
	
// ---- Liste des échéances
	if ($save==false)
	{
		$tabTitre=array();
		$tabTitre["prenom"]["aff"]="Prénom";
		$tabTitre["prenom"]["width"]=150;
		$tabTitre["nom"]["aff"]="Nom";
		$tabTitre["nom"]["width"]=250;
		$tabTitre["echeance"]["aff"]="Echéance";
		$tabTitre["echeance"]["width"]=350;
		$tabTitre["debiter"]["aff"]="<input type='checkbox' id='form_debite' OnClick='selectAll();'> Débiter";
		$tabTitre["debiter"]["width"]=100;

		$lstdte=array();
		if ($form_id>0)
		{
			$lstdte=ListeEcheanceType($sql,$form_id);
		}

		$tabValeur=array();
		foreach($lstdte as $i=>$id)
		{
			$dte = new echeance_class($id,$sql,0);
			$usr = new user_class($dte->uid,$sql,false);

			$tabValeur[$i]["prenom"]["val"]=$usr->prenom;
			$tabValeur[$i]["prenom"]["aff"]=$usr->aff("prenom");
			$tabValeur[$i]["nom"]["val"]=$usr->nom;
			$tabValeur[$i]["nom"]["aff"]=$usr->aff("nom");
			$tabValeur[$i]["echeance"]["val"]=$dte->Val();
			$tabValeur[$i]["echeance"]["aff"]=$dte->Affiche();
			$tabValeur[$i]["debiter"]["aff"]="<input type='checkbox' id='form_debite_".$id."' name='form_debite[".$id."]'>";

			$tmpl_x->assign("form_uid",$id);
			$tmpl_x->parse("corps.lst_checkbox");
		}

		if ($order=="") { $order="nom"; }
		if ($trie=="") { $trie="d"; }

		$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"form_id=".$form_id));
	}

	
// ---- Affecte les variables d'affichage
	if (GetModule("aviation"))
	  {  	$tmpl_x->parse("infos.vols"); }

	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>