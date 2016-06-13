<?
// ---------------------------------------------------------------------------------------------
//   Calendrier des manips
//     ($Author: miniroot $)
//     ($Date: 2014-09-16 21:09:58 +0200 (mar., 16 sept. 2014) $)
//     ($Revision: 435 $)
// ---------------------------------------------------------------------------------------------
//   Variables  :
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.2
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
	if (!GetDroit("AccesPresences")) { FatalError("Accès non autorisé (AccesPresences)"); }

	require_once ("class/abonnement.inc.php");
// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("presences.htm"));
	$tmpl_x->assign("path_module","$module/$mod");


// ---- Année
	if ((!isset($dtey)) || (!is_numeric($dtey)))
	  { $dtey=date("Y"); }

	if ((!isset($dtem)) || (!is_numeric($dtem)))
	  { $dtem=date("m"); }

	$tmpl_x->assign("year1",date("Y")-1);
	$tmpl_x->assign("year2",date("Y"));
	$tmpl_x->assign("dtey_prev", ($dtem<2) ? $dtey-1 : $dtey);
	$tmpl_x->assign("dtem_prev", ($dtem<2) ? "12" : $dtem-1);
	$tmpl_x->assign("dtey_next", ($dtem>11) ? $dtey+1 : $dtey);
	$tmpl_x->assign("dtem_next", ($dtem>11) ? "1" : $dtem+1);
	
	$dtem=CompleteTxt($dtem,"2","0");

	$tabmois=array();
	$tabmois["01"]="Janvier";
	$tabmois["02"]="F&eacute;vrier";
	$tabmois["03"]="Mars";
	$tabmois["04"]="Avril";
	$tabmois["05"]="Mai";
	$tabmois["06"]="Juin";
	$tabmois["07"]="Juillet";
	$tabmois["08"]="Ao&ucirc;t";
	$tabmois["09"]="Septembre";
	$tabmois["10"]="Octobre";
	$tabmois["11"]="Novembre";
	$tabmois["12"]="D&eacute;cembre";


	$tmpl_x->assign("aff_date_deb", $tabmois[$dtem]);
	$tmpl_x->assign("aff_date_fin", $tabmois[CompleteTxt(($dtem+5),"2","0")]);
	$tmpl_x->assign("aff_date", $dtey);

// ---- Affiche les menus
	if (GetDroit("AccesVacances")) { $tmpl_x->parse("corps.AffVacances"); }
	if (GetDroit("AccesPlage")) { $tmpl_x->parse("corps.AffPlage"); }


// ---- Rempli le tableau des plages horaires
	$query="SELECT * FROM ".$MyOpt["tbl"]."_plage ORDER BY deb";
	$sql->Query($query);
	$tabPlage=array();

	for($i=0; $i<$sql->rows; $i++)
	  {
		$sql->GetRow($i);

		$tabPlage[$sql->data["plage"]]["nom"]=$sql->data["titre"];
		$tabPlage[$sql->data["plage"]]["deb"]=$sql->data["deb"];
		$tabPlage[$sql->data["plage"]]["fin"]=$sql->data["fin"];
		$tabPlage[$sql->data["plage"]]["jour"][$sql->data["jour"]]=1;
	  }


// ---- Affiche les colonnes
	$tabTitre=array();
	foreach($tabPlage as $i=>$v)
	  {
		$tabTitre[$v["nom"]]=1;
	  }

	foreach($tabTitre as $i=>$v)
	  {
		$tmpl_x->assign("aff_periode", $i);
		$tmpl_x->parse("corps.lst_periode");
	  }

// ---- Liste les jours

        for($i=1; $i<=date("t", strtotime($dtey."-".$dtem."-01")); $i++)
          { 
		$dte="$dtey-$dtem-".CompleteTxt($i,"2","0");

		$col="";

		$tabTypePres=array();


		foreach($tabPlage as $ii=>$v)
		  {
		        $tabTypePres[$ii]["P"]="0";
		        $tabTypePres[$ii]["R"]="0";
		  }

	        
		$query="SELECT type,COUNT(*) AS tpspaye FROM ".$MyOpt["tbl"]."_presence WHERE dte='".$dtey.$dtem."' AND dtedeb>='$dte 00:00:00' AND dtefin<='$dte 23:59:59' GROUP BY BINARY type";
		$sql->Query($query);
		for($ii=0; $ii<$sql->rows; $ii++)
		  { 
		        $sql->GetRow($ii);
		        $tabTypePres[substr($sql->data["type"],1,1)]["P"]=$sql->data["tpspaye"];
		  }

		$query="SELECT type,COUNT(*) AS tpsreel FROM ".$MyOpt["tbl"]."_presence WHERE dte='".$dtey.$dtem."' AND tpsreel=0 AND dtedeb>='$dte 00:00:00' AND dtefin<='$dte 23:59:59' GROUP BY BINARY type";
		$sql->Query($query);
	        for($ii=0; $ii<$sql->rows; $ii++)
	          { 
		        $sql->GetRow($ii);
		        $tabTypePres[substr($sql->data["type"],1,1)]["R"]=$sql->data["tpsreel"];
		  }

		$tabAbo=CountAbonnement($sql,$dte);

		// Pour chaque ligne d'abonnement cocher la journée, midi ou soir en fonction de la valeur par défaut
		// type J : Journée, A : Matin, M : Midi, P : Après-midi, S : Soir
		// jour N : Semaine, M : Mercredi, V : Vacances

		$max=$tabPresenceMax[$tabPresenceJour[$tabAbo["type"]]];

	//	foreach ($tabTypePres as $t=>$v)

		if ( (date("w", strtotime($dte))==0) || (date("w", strtotime($dte))==6) )
		  { $col="bgcolor=\"#E3F2FE\""; }
		else if ($tabAbo["type"]==7)
		  { $col="bgcolor=\"#A9D7FE\""; }

		$tmpl_x->assign("color_manip",$col);
		$tmpl_x->assign("aff_dte",$dte);
		$tmpl_x->assign("aff_jour_manip",$i);
		$tmpl_x->assign("aff_nom_manip",$txt);
		$tmpl_x->assign("aff_lst_manip",$comment);
		$tmpl_x->assign("aff_id_manip",($tabmanip[$dte][0]["id"]=="") ? "&dte=$dte" : $tabmanip[$dte][0]["id"]);

		$tl=array();
		foreach($tabPlage as $t=>$vv)
		  {
		  	$v=$tabTypePres[$t];
		  	$v["P"]=($v["P"]>0) ? $v["P"] : $tabAbo[$t]["sum"];
//			$tmpl_x->assign("aff_presence_".$t,($v["P"]>0) ? ($v["P"]-$v["R"])." / ".$v["P"] .(($v["P"]>$max*1.05) ? " (!)" : "") : "&nbsp;");

			$tl[$tabPlage[$t]["nom"]]["R"]=$tl[$tabPlage[$t]["nom"]]["R"]+$v["R"];
			$tl[$tabPlage[$t]["nom"]]["P"]=$tl[$tabPlage[$t]["nom"]]["P"]+$v["P"];;

		  }

		foreach($tl as $t=>$v)
		  {

			$tmpl_x->assign("aff_presence",($v["P"]>0) ? ($v["P"]-$v["R"])." / ".$v["P"] .(($v["P"]>$max*1.05) ? " (!)" : "") : "&nbsp;");
			$tmpl_x->assign("aff_color_pre",( ($v["P"]>$max*0.9) ? "#FF0000" : "#000000"));
			$tmpl_x->parse("corps.lst_jour.lst_jour_periode");
		  }

		$tmpl_x->parse("corps.lst_jour");
	  }

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=&$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=&$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=&$tmpl_x->text("corps");

?>

<?
function Affmois($sql,$dtey,$dtem,$typeTxt,$affday)
  { global $tmpl_x,$tabmois;
  
	$tmpl_x->reset("aff_mois");
  


	return $tmpl_x->text("aff_mois");
  }
?>
