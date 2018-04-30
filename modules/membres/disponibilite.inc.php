<?
// ---------------------------------------------------------------------------------------------
//   Détail d'un utilisateur
//     ($Author: miniroot $)
//     ($Date: 2016-02-14 23:17:30 +0100 (dim., 14 fÃ©vr. 2016) $)
//     ($Revision: 445 $)
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
	if (!is_numeric($id))
      { $id=0; }

	if ( (!GetDroit("AccesMembre")) && (!GetMyId($id)) )
	  { FatalError("Accès non autorisé (AccesMembre)"); }

  	if ($id>0)
	  { $usr = new user_class($id,$sql,((GetMyId($id)) ? true : false)); }


// ---- Charge le template
	$tmpl_x = new XTemplate (MyRep("disponibilite.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Affiche les menus
  	$tmpl_x->assign("id",$id);

	if ((GetMyId($id)) || (GetDroit("ModifUser")))
	  { $tmpl_x->parse("infos.modification"); }

	if ((GetMyId($id)) || (GetDroit("ModifUserPassword")))
	  { $tmpl_x->parse("infos.password"); }

	if (GetDroit("CreeUser"))
	  { $tmpl_x->parse("infos.ajout"); }

	if ((GetDroit("DesactiveUser")) && ($usr->actif=="oui"))
	  { $tmpl_x->parse("infos.desactive"); }

  	if ((GetDroit("DesactiveUser")) && ($usr->actif=="off"))
	  { $tmpl_x->parse("infos.active"); }

	if ((GetDroit("SupprimeUser")) && ($usr->actif=="off"))
	  { $tmpl_x->parse("infos.suppression"); }

// ---- Variable du calendrier
		
	$h=30;
	$debjour=($MyOpt["debjour"]!="") ? $MyOpt["debjour"] : "6";
	$finjour=($MyOpt["finjour"]!="") ? $MyOpt["finjour"] : "22";
	$larcol="100";
	$jour=date("Y-m-d");

	$tmpl_x->assign("mid",$id);

	$tmpl_x->assign("defaultView","agendaFourWeeks");
	$tmpl_x->assign("headerListe","agendaHeightWeeks,agendaFourWeeks,agendaTwoWeeks,agendaWeek,agendaDay");
	$tmpl_x->assign("TexteTitre","Calendrier pour");
	// $tmpl_x->parse("corps.aff_tooltips");
	$tmpl_x->assign("form_jour",$jour);

	$tmpl_x->assign("form_debjour",$debjour);
	$tmpl_x->assign("form_finjour",$finjour);

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");
	
?>