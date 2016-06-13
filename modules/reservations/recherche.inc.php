<?
// ---------------------------------------------------------------------------------------------
//   Recherche d'une réservation
//     ($Author: miniroot $)
//     ($Date: 2016-04-22 20:48:24 +0200 (ven., 22 avr. 2016) $)
//     ($Revision: 456 $)
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
	require_once ("class/reservation.inc.php");

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("recherche.htm"));
	$tmpl_x->assign("path_module","$module/$mod");


// ---- Charge les listes

	if ($form_dte_deb!="")
	  {
	  	$tmpl_x->assign("form_dte_deb", $form_dte_deb);
	  }
	if ($form_dte_fin!="")
	  {
	  	$tmpl_x->assign("form_dte_deb", $form_dte_fin);
	  }

	// Liste des avions
	$lst=ListeRessources($sql,"oui");

	foreach($lst as $i=>$id)
	  {
		$resr=new ress_class($id,$sql);

		$tmpl_x->assign("uid_avion", $resr->id);
		$tmpl_x->assign("nom_avion", strtoupper($resr->immatriculation));
		if ($resr->id==$form_uid_ress)
		  {
			$tmpl_x->assign("chk_avion", "selected");
			$tmpl_x->assign("uid_avionrmq", $resr->id);
			$tmpl_x->assign("aff_nom_avion", strtoupper($resr->immatriculation));
		  }
		else
		  { $tmpl_x->assign("chk_avion", ""); }
		$tmpl_x->parse("corps.lst_avion");
	  }

	// Liste des pilotes	
	$lst=ListActiveUsers($sql,"prenom,nom","!membre,!invite");

	foreach($lst as $i=>$id)
	  {
	  	$resusr=new user_class($id,$sql);
		$tmpl_x->assign("uid_pilote", $resusr->uid);
		$tmpl_x->assign("nom_pilote", $resusr->Aff("fullname","val"));
		if ($resusr->uid==$form_uid_pilote)
		  { $tmpl_x->assign("chk_pilote", "selected"); }
		else
		  { $tmpl_x->assign("chk_pilote", ""); }
		$tmpl_x->parse("corps.lst_pilote");
	  }

	// Liste des instructeurs
	$lst=ListActiveUsers($sql,"prenom,nom","instructeur");
	$tmpl_x->assign("aff_nom_instructeur", "-");

	foreach($lst as $i=>$id)
	  { 
		$resusr=new user_class($id,$sql);
		$tmpl_x->assign("uid_instructeur", $resusr->uid);
		$tmpl_x->assign("nom_instructeur", $resusr->Aff("fullname","val"));
		if ($resusr->uid==$form_uid_instructeur)
		  {
		  	$tmpl_x->assign("chk_instructeur", "selected");
		  }
		else
		  { $tmpl_x->assign("chk_instructeur", ""); }
		$tmpl_x->parse("corps.lst_instructeur");
	  }


// ---- Effectue la recherche

	if ($fonc=="Rechercher")
	  {

		$tabTitre=array();
		$tabTitre["avion"]["aff"]="Avion";
		$tabTitre["avion"]["width"]=55;
		$tabTitre["date"]["aff"]="Date";
		$tabTitre["date"]["width"]=250;
		$tabTitre["pilote"]["aff"]="Pilote";
		$tabTitre["pilote"]["width"]=170;
		$tabTitre["instructeur"]["aff"]="Instructeur";
		$tabTitre["instructeur"]["width"]=170;
		$tabTitre["status"]["aff"]="Status";
		$tabTitre["status"]["width"]=70;

		$q="1=1 ";
		if ($form_dte_deb!="")
		  {	$q.="AND dte_deb>='".date2sql($form_dte_deb)."' "; }
		  	
		if ($form_dte_fin!="")
		  {	$q.="AND dte_fin<='".date2sql($form_dte_fin)."' "; }

		if ($form_uid_ress!="")
		  {	$q.="AND uid_avion='$form_uid_ress' "; }

		if ($form_uid_pilote!="")
		  {	$q.="AND uid_pilote='$form_uid_pilote' "; }

		if ($form_uid_instructeur!="")
		  {	$q.="AND uid_instructeur='$form_uid_instructeur' "; }

		$lstress=RechercheReservation($sql,$q);

		$tabValeur=array();
		foreach($lstress as $i=>$id)
		  {
			$resa = new resa_class($id,$sql);
			$avion = new ress_class($resa->uid_ressource,$sql);

			$tabValeur[$i]["avion"]["val"]=strtoupper($avion->immatriculation);
			$tabValeur[$i]["avion"]["aff"]="<a href=\"reservations.php?rub=reservation&id=$id\">".strtoupper($avion->immatriculation)."</a>";
			$tabValeur[$i]["date"]["val"]=$resa->dte_deb;
			$tabValeur[$i]["date"]["aff"]="<a href=\"reservations.php?rub=reservation&id=$id\">".sql2date($resa->dte_deb)." à ".sql2date($resa->dte_fin)."</a>";

			$usr = new user_class($resa->uid_pilote,$sql);
			$tabValeur[$i]["pilote"]["val"]=$usr->fullname;
			$tabValeur[$i]["pilote"]["aff"]="<A href='membres.php?rub=detail&id=".$resa->uid_pilote."'>".$usr->fullname."</a>";

			$usr = new user_class($resa->uid_instructeur,$sql);
			$tabValeur[$i]["instructeur"]["val"]=$usr->fullname;
			$tabValeur[$i]["instructeur"]["aff"]="<A href='membres.php?rub=detail&id=".$resa->uid_instructeur."'>".$usr->fullname."</a>";

			$tabValeur[$i]["status"]["val"]=($resa->reel=="oui")?(($resa->actif=="oui")?"Actif":"Supprimé"):"Masqué";
			$tabValeur[$i]["status"]["aff"]="<a href=\"reservations.php?rub=reservation&id=$id\">".(($resa->reel=="oui")?(($resa->actif=="oui")?"Actif":"Supprimé"):"Masqué")."</a>";
		  }

		if ($order=="") { $order="date"; }
		if ($trie=="") { $trie="d"; }

		$url="form_dte_deb=$form_dte_deb&form_dte_fin=$form_dte_fin&form_uid_ress=$form_uid_ress&form_uid_pilote=$form_uid_pilote&form_uid_instructeur=$form_uid_instructeur";
		$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,$url,$start,50));
	  }

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");



?>
