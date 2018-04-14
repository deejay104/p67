<?
// ---------------------------------------------------------------------------------------------
//   Calendrier des réservations
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
	require_once ("class/reservation.inc.php");
	require_once ("class/maintenance.inc.php");
	require_once ("class/manifestation.inc.php");




// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("index.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

  
// ---- Définition des constantes
	if ((!isset($ress)) || (!is_numeric($ress)))
	{
	  	$ress=0;
	}

	$h=30;
	$debjour=($MyOpt["debjour"]!="") ? $MyOpt["debjour"] : "6";
	$finjour=($MyOpt["finjour"]!="") ? $MyOpt["finjour"] : "22";
	$larcol="100";

	$t=ListeRessources($sql,array("oui"));
	$tress=array();

	$ii=1;
	foreach($t as $rid)
	  {
		$tress[$ii]=new ress_class($rid, $sql);

		$tmpl_x->assign("uid_ress", $rid);
		$tmpl_x->assign("nom_ress", $tress[$ii]->immatriculation);
		if ($ress==$rid)
		  { $tmpl_x->assign("chk_ress", "selected"); }
		else
		  { $tmpl_x->assign("chk_ress", ""); }
		$tmpl_x->parse("infos.lst_ress");
		$ii=$ii+1;
	  }



	// Liste des ressources
	$tmpl_x->assign("ress", $ress);


// ---- Affichage pour la journée
	
	if ($theme=="phone")
	  {

			if ($jour=="-")
			  { $myuser->Valid("aff_jour",date("Y-m-d")); }
			else if ($jour!="")
			  { $myuser->Valid("aff_jour",$jour); }
		
			if (($myuser->data["aff_jour"]=="") || ($myuser->data["aff_jour"]=="0000-00-00"))
			  { $myuser->Valid("aff_jour",date("Y-m-d")); }
		
			$jour=$myuser->data["aff_jour"];
			$tmpl_x->assign("defaultView","agendaDay");
			$tmpl_x->assign("headerListe","agendaWeek,agendaDay");
			$tmpl_x->assign("TexteTitre","");
			
	  }
	else
	  {
			if ($jour=="-")
			  // { $myuser->Valid("aff_jour",date("Y-m")."-".(floor((date("d")-1)/7)*7+1)); }
			  { $myuser->Valid("aff_jour",date("Y-m-d")); }
			else if ($jour!="")
			  { $myuser->Valid("aff_jour",$jour); }
		
			if (($myuser->data["aff_jour"]=="") || ($myuser->data["aff_jour"]=="0000-00-00"))
			  { $myuser->Valid("aff_jour",date("Y-m-d")); }
		
			$jour=$myuser->data["aff_jour"];

			$tmpl_x->assign("defaultView","agendaTwoWeeks");
			$tmpl_x->assign("headerListe","month,agendaFourWeeks,agendaTwoWeeks,agendaWeek,agendaDay");
			$tmpl_x->assign("TexteTitre","Calendrier pour");
			$tmpl_x->parse("corps.aff_tooltips");

	  }

	$tmpl_x->assign("maintconf",$MyOpt["tabcolresa"]["maintconf"]);
	$tmpl_x->assign("maintplan",$MyOpt["tabcolresa"]["maintplan"]);
	$tmpl_x->assign("meeting",$MyOpt["tabcolresa"]["meeting"]);

	$tmpl_x->assign("form_ress",$ress);
	$tmpl_x->assign("form_jour",$jour);

	$tmpl_x->assign("form_debjour",$debjour);
	$tmpl_x->assign("form_finjour",$finjour);
	$tmpl_x->assign("terrain_nom",$MyOpt["terrain"]["nom"]);

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");


?>
