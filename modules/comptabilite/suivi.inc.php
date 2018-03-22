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
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("suivi.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Vérifie les variables
	if (!GetDroit("AccesPageSuivi")) { FatalError("Accès non autorisé"); }

	$tmpl_x->assign("form_checktime",$_SESSION['checkpost']);

// ---- Affiche le menu
	$aff_menu="";
	require_once("modules/".$mod."/menu.inc.php");
	$tmpl_x->assign("aff_menu",$aff_menu);


// ---- Enregistre le suivi
	if (($fonc=="Enregistrer") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$tabmaj=array();
		$query = "SELECT * FROM ".$MyOpt["tbl"]."_compte WHERE (pointe='' OR pointe='P') AND uid='".$MyOpt["uid_banque"]."'";
		$sql->Query($query);
		for($i=0; $i<$sql->rows; $i++)
		  { 
			$sql->GetRow($i);
			$tabmaj[$sql->data["id"]]="";
		  }

		if (is_array($form_releve))
		  {
			foreach ($form_releve as $k=>$p)
			  { $tabmaj[$k]="P"; }
		  }

		foreach ($tabmaj as $k=>$p)
		  {
			$query="UPDATE ".$MyOpt["tbl"]."_compte SET pointe='$p' WHERE id='$k'";
			//echo "$query<BR>";
			$sql->Update($query);
		  }
		$_SESSION['tab_checkpost'][$checktime]=$checktime;
	  }
	else if (($fonc=="Clore") && (!isset($_SESSION['tab_checkpost'][$checktime])))
	  {
		$query="UPDATE ".$MyOpt["tbl"]."_compte SET pointe='R' WHERE pointe='P' AND uid='".$MyOpt["uid_banque"]."'";
		$sql->Update($query);
	  }


// ---- Affiche les infos
	if (GetModule("aviation"))
	  {  	$tmpl_x->parse("corps.vols"); }

	$query = "SELECT SUM(montant) AS nb FROM ".$MyOpt["tbl"]."_compte WHERE pointe='R' AND uid='".$MyOpt["uid_banque"]."'";
	$res=$sql->QueryRow($query);
	$tmpl_x->assign("ancien_solde", (is_numeric($res["nb"])) ? -$res["nb"] : "0");

	$query = "SELECT ".$MyOpt["tbl"]."_compte.* FROM ".$MyOpt["tbl"]."_compte WHERE (pointe='' OR pointe='P') AND uid='".$MyOpt["uid_banque"]."' ORDER BY date_valeur,mouvement,commentaire";
	$sql->Query($query);
	$col=50;
	$myColor[50]="F0F0F0";
	$myColor[55]="F7F7F7";

	$tabCompte=array();
	for($i=0; $i<$sql->rows; $i++)
	{ 
		$sql->GetRow($i);
		$tabCompte[$sql->data["id"]]=$sql->data;
	}

	foreach($tabCompte as $id=>$d)
	{
		$tmpl_x->assign("id_suivi", $d["id"]);
		$tmpl_x->assign("date_suivi", sql2date($d["date_valeur"]));
		$tmpl_x->assign("mouvement_suivi", $d["mouvement"]);
		$tmpl_x->assign("commentaire_suivi", $d["commentaire"]);

		$tier = new user_class($d["tiers"],$sql,false);
		$tmpl_x->assign("tiers_suivi", $tier->Aff("fullname"));

		$tmpl_x->assign("montant_suivi", AffMontant(-$d["montant"]));

		if ($d["pointe"]=="P")
		  { $tmpl_x->assign("chk_suivi", "checked"); }
		else
		  { $tmpl_x->assign("chk_suivi", ""); }
		$tmpl_x->assign("color_suivi", $myColor[$col]);
		$col = abs($col-105);

		$tmpl_x->parse("corps.aff_suivi.lst_suivi");
	  }

	$query = "SELECT SUM(montant) AS nb FROM ".$MyOpt["tbl"]."_compte WHERE uid='".$MyOpt["uid_banque"]."'";
	$res=$sql->QueryRow($query);
	$tmpl_x->assign("nouveau_solde", (is_numeric($res["nb"])) ? AffMontant(-$res["nb"]) : "-");

	$query = "SELECT SUM(montant) AS nb FROM ".$MyOpt["tbl"]."_compte WHERE (pointe='R' OR pointe='P') AND uid='".$MyOpt["uid_banque"]."'";
	$res=$sql->QueryRow($query);
	$tmpl_x->assign("pointe_solde", (is_numeric($res["nb"])) ? AffMontant(-$res["nb"]) : "-");

	$tmpl_x->assign("form_page", "suivi");
 	$tmpl_x->parse("corps.aff_suivi");

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
