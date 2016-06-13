<?
// ---------------------------------------------------------------------------------------------
//   Saisie des présences
// ---------------------------------------------------------------------------------------------
//   Variables  : 
//	$dte - Jour à traiter
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2 ($Revision: 413 $)
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
	if (!GetDroit("AccesVacances")) { FatalError("Accès non autorisé (AccesVacances)"); }

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("vacances.htm"));
	$tmpl_x->assign("path_module","$module/$mod");



// ---- Enregistre

	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$query="INSERT INTO ".$MyOpt["tbl"]."_vacances SET dtedeb='".date2sql($form_dtedeb)."', dtefin='".date2sql($form_dtefin)."', comment='".addslashes($form_comment)."'";
		$sql->Insert($query);
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Liste des vacances
	$tabTitre=array();
	$tabTitre["dtedeb"]["aff"]="Début";
	$tabTitre["dtedeb"]["width"]=100;
	$tabTitre["dtefin"]["aff"]="Fin";
	$tabTitre["dtefin"]["width"]=100;

	$tabTitre["comment"]["aff"]="Description";
	$tabTitre["comment"]["width"]=400;


	// Charger la table des présences
	$query="SELECT * FROM ".$MyOpt["tbl"]."_vacances";
	$sql->Query($query);
	$tabValeur=array();
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$tabValeur[$i]["dtedeb"]["val"]=$sql->data["dtedeb"];
		$tabValeur[$i]["dtedeb"]["aff"]=$sql->data["dtedeb"];
		$tabValeur[$i]["dtefin"]["val"]=$sql->data["dtefin"];
		$tabValeur[$i]["dtefin"]["aff"]=$sql->data["dtefin"];
		$tabValeur[$i]["comment"]["val"]=$sql->data["comment"];
		$tabValeur[$i]["comment"]["aff"]=$sql->data["comment"];
	  }

	if ($order=="") { $order="dtedeb"; }
	if ($trie=="") { $trie="i"; }

	$tmpl_x->assign("aff_tableau",AfficheTableau($tabValeur,$tabTitre,$order,$trie,""));


// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=&$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=&$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=&$tmpl_x->text("corps");

function CalcNextDay($dte,$p)
  { global $tabPresenceJour;
	$d="";
	$d1=strtotime($dte);
	while($tabPresenceJour[$d]=="")
	  {
		$d1=$d1+$p*3600*24;
		$d=date("w",$d1);
	  }
	return date("Y-m-d",$d1);
  }
?>
