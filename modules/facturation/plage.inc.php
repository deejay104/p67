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
	if (!GetDroit("AccesPlage")) { FatalError("Accès non autorisé (AccesPlage)"); }

// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("plage.htm"));
	$tmpl_x->assign("path_module","$module/$mod");



// ---- Enregistre

	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$query ="INSERT INTO ".$MyOpt["tbl"]."_plage SET ";
		$query.="id='".substr($form_jour,0,1).substr($form_plage,0,1)."', ";
		$query.="jour='".substr($form_jour,0,1)."', ";
		$query.="plage='".addslashes(substr($form_plage,0,1))."', ";
		$query.="titre='".addslashes(substr($form_titre,0,20))."', ";
		$query.="nom='".addslashes(substr($form_nom,0,50))."', ";
		$query.="libelle='".addslashes(substr($form_libelle,0,50))."', "; 
		$query.="deb='".substr($form_deb,0,2)."', ";
		$query.="fin='".substr($form_fin,0,2)."'";
		$sql->Insert($query);
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }

// ---- Supprime
	if ($fonc=="del")
	  {
	  	$query="DELETE FROM ".$MyOpt["tbl"]."_plage WHERE id='".substr($id,0,2)."'";
	  	$sql->Delete($query);
	  }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Liste des vacances
	$tabTitre=array();
	$tabTitre["jour"]["aff"]="Jour";
	$tabTitre["jour"]["width"]=50;
	$tabTitre["plage"]["aff"]="Plage";
	$tabTitre["plage"]["width"]=50;

	$tabTitre["titre"]["aff"]="Titre";
	$tabTitre["titre"]["width"]=100;
	$tabTitre["nom"]["aff"]="Nom";
	$tabTitre["nom"]["width"]=200;
	$tabTitre["libelle"]["aff"]="Libelle";
	$tabTitre["libelle"]["width"]=200;

	$tabTitre["deb"]["aff"]="Début";
	$tabTitre["deb"]["width"]=60;
	$tabTitre["fin"]["aff"]="Fin";
	$tabTitre["fin"]["width"]=60;

	$tabTitre["del"]["aff"]=" ";
	$tabTitre["del"]["width"]=77;

	// Charger la table des plages
	$query="SELECT * FROM ".$MyOpt["tbl"]."_plage ORDER BY deb,jour,plage";
	$sql->Query($query);
	$tabValeur=array();
	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);
		$tabValeur[$i]["jour"]["val"]=$sql->data["jour"];
		$tabValeur[$i]["jour"]["aff"]=$sql->data["jour"];
		$tabValeur[$i]["plage"]["val"]=$sql->data["plage"];
		$tabValeur[$i]["plage"]["aff"]=$sql->data["plage"];
		$tabValeur[$i]["titre"]["val"]=$sql->data["titre"];
		$tabValeur[$i]["titre"]["aff"]=$sql->data["titre"];
		$tabValeur[$i]["nom"]["val"]=$sql->data["nom"];
		$tabValeur[$i]["nom"]["aff"]=$sql->data["nom"];
		$tabValeur[$i]["libelle"]["val"]=$sql->data["libelle"];
		$tabValeur[$i]["libelle"]["aff"]=$sql->data["libelle"];
		$tabValeur[$i]["deb"]["val"]=$sql->data["deb"];
		$tabValeur[$i]["deb"]["aff"]=$sql->data["deb"];
		$tabValeur[$i]["fin"]["val"]=$sql->data["fin"];
		$tabValeur[$i]["fin"]["aff"]=$sql->data["fin"];

		$tabValeur[$i]["del"]["val"]="";
		$tabValeur[$i]["del"]["aff"]="<a href='index.php?mod=facturation&rub=plage&fonc=del&id=".$sql->data["jour"].$sql->data["plage"]."'><img src='images/12_poubelle.gif'></a>";

		for($ii=0; $ii<=7; $ii++)
		  {
			if ($MyOpt["tabPresenceJour"][$ii]==$sql->data["jour"])
			  {
				$tmpl_x->assign("divj".$ii."_y",($sql->data["deb"]-8)*20);
				$tmpl_x->assign("divj".$ii."_h",($sql->data["fin"]-$sql->data["deb"])*20);
				$tmpl_x->assign("divj".$ii."_txt",$sql->data["id"]);
				
				$tmpl_x->parse("corps.divj".$ii);	
			  }
		  }
	  }

	if ($order=="") { $order="deb"; }
	if ($trie=="") { $trie="d"; }

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
