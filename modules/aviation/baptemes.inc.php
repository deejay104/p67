<?
// ---------------------------------------------------------------------------------------------
//   Liste des membres
//     ($Author: miniroot $)
//     ($Date: 2012-11-26 21:01:41 +0100 (lun., 26 nov. 2012) $)
//     ($Revision: 413 $)
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
	if (!GetDroit("AccesBaptemes")) { FatalError("Accès non autorisé (AccesBaptemes)"); }

	require_once ("class/bapteme.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("baptemes.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ----
	if (GetDroit("CreeBapteme"))
	  {
	  	$tmpl_x->parse("infos.ajout");
	  }

// ---- Liste des status
	if ((!isset($form_status)) || (!is_numeric($form_status)))
	  { $form_status=-2; }

	$tabStatus=ListeStatus();
	foreach($tabStatus as $id=>$txt)
	  {
		$tmpl_x->assign("form_statusid", $id);
		$tmpl_x->assign("form_status", $txt);
		if ($id==$form_status)
		  {
		  	$tmpl_x->assign("form_selected", "selected");
		  }
		else
		  {
		  	$tmpl_x->assign("form_selected", "");
		  }
		$tmpl_x->parse("corps.lst_status");
	  }	

	if ($form_status==-2)
	  {
	  	$tmpl_x->assign("form_selected_open", "selected");
	  }
	if ($form_status==-1)
	  {
	  	$tmpl_x->assign("form_selected_all", "selected");
	  }


// ----
	$tabTitre=array();
	$tabTitre["num"]["aff"]="Numéro";
	$tabTitre["num"]["width"]=150;

	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=200;

	if ($theme!="phone")
		{
			$tabTitre["nb"]["aff"]="Nb";
			$tabTitre["nb"]["width"]=30;
		
			$tabTitre["telephone"]["aff"]="Téléphone";
			$tabTitre["telephone"]["width"]=120;
		}

	$tabTitre["status"]["aff"]="Status";
	$tabTitre["status"]["width"]=80;

	if ($theme!="phone")
		{
			$tabTitre["paye"]["aff"]="Payé";
			$tabTitre["paye"]["width"]=60;
		
			$tabTitre["date"]["aff"]="Date";
			$tabTitre["date"]["width"]=150;
		
			$tabTitre["pilote"]["aff"]="Pilote";
			$tabTitre["pilote"]["width"]=150;
		
			$tabTitre["type"]["aff"]="Type";
			$tabTitre["type"]["width"]=150;
		
			$tabTitre["resa"]["aff"]="Réservation";
			$tabTitre["resa"]["width"]=100;
		}

	$lstusr=ListeBaptemes($sql,array("oui"),$form_status);

	$tabValeur=array();
	foreach($lstusr as $i=>$id)
	  {
		$btm = new bapteme_class($id,$sql);
		$tabValeur[$i]["num"]["val"]=$btm->num;
		$tabValeur[$i]["num"]["aff"]="<a href='index.php?mod=aviation&rub=bapteme&id=".$btm->id."'>".$btm->num."</a>";
		$tabValeur[$i]["nom"]["val"]=$btm->nom;
		$tabValeur[$i]["nom"]["aff"]="<a href='index.php?mod=aviation&rub=bapteme&id=".$btm->id."'>".$btm->aff("nom","html")."</a>";
		$tabValeur[$i]["nb"]["val"]=$btm->nb;
		$tabValeur[$i]["nb"]["aff"]=$btm->nb;;
		$tabValeur[$i]["telephone"]["val"]=$btm->telephone;
		$tabValeur[$i]["telephone"]["aff"]=$btm->aff("telephone","html");
		$tabValeur[$i]["status"]["val"]=$btm->status;
		$tabValeur[$i]["status"]["aff"]="<a href='index.php?mod=aviation&rub=bapteme&id=".$btm->id."'>".$btm->aff("status","html")."</a>";
		$tabValeur[$i]["paye"]["val"]=$btm->paye;
		$tabValeur[$i]["paye"]["aff"]=$btm->aff("paye","html");

		$tabValeur[$i]["date"]["val"]=strtotime($btm->dte);
		$tabValeur[$i]["date"]["aff"]=($btm->dte=="0000-00-00 00:00:00") ? "-" : sql2date($btm->dte,"nosec");

		$usr = new user_class($btm->id_pilote,$sql,true);

		$tabValeur[$i]["pilote"]["val"]=$btm->id_pilote;
		$tabValeur[$i]["pilote"]["aff"]=($btm->id_pilote>0) ? $usr->Aff("fullname") : "-";

		if ($btm->id_avion>0)
		  {
			$ress = new ress_class($btm->id_avion,$sql);
			$tabValeur[$i]["resa"]["val"]=strtoupper($ress->immatriculation);
			$tabValeur[$i]["resa"]["aff"]=strtoupper($ress->immatriculation);
		  }
		else
		  {
		  	$tabValeur[$i]["resa"]["val"]="-";
			$tabValeur[$i]["resa"]["aff"]="-";
		  }
		$tabValeur[$i]["type"]["val"]=$btm->type;
		$tabValeur[$i]["type"]["aff"]=$btm->aff("type","html");

	  }

	if ((!isset($order)) || ($order=="")) { $order="nom"; }
	if ((!isset($trie)) || ($trie=="")) { $trie="d"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,"form_status=".$form_status));

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
