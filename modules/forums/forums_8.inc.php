<?
// ---------------------------------------------------------------------------------------------
//   Page de recherche dans les forums - Site de prom's 197 - (c) ch197
//   
//   27/12/2001 : DeeJay - Création de la page
// ---------------------------------------------------------------------------------------------
//   Variables  : $critere - Critères de recherches
// ---------------------------------------------------------------------------------------------
/*
    SoceIt v2.0
    Copyright (C) 2008 Matthieu Isorez

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
	$tmpl_x = new XTemplate (MyRep("forums_8.htm"));
	$tmpl_x->assign("path_module","$module/$mod");

// ---- Initialisation des variables
	$tmpl_x->assign("pageIndex", $pageIndex);

	$tmpl_x->assign("critere", $critere);
	$tmpl_x->assign("fid", $fid);

// ---- Effectue la recherche

	$critere = trim($critere);

	if ($critere!="")
	  {
			$tabcrit = explode(" ",$critere);
		
			$tmpl_x->assign("crit",$critere);
		
			$okok=false;
			if (count($tabcrit)>0)
			  {
				$query ="SELECT forum.".$field_forum["fid"]." AS fid,";
				$query.="forum.".$field_forum["id"]." AS mid,";
				$query.="f_forum.".$field_forum["titre"]." AS ftitre,";
				$query.="forum.".$field_forum["titre"]." AS titre,";
				$query.="forum.".$field_forum["corps"]." AS corps,";
				$query.="forum.".$field_forum["pseudo"]." AS pseudo,";
				$query.="forum.".$field_forum["uid_creat"]." AS uid_creat,";
				$query.="forum.".$field_forum["date_maj"]." AS date_maj,";
				$query.="user.".$field_user["pseudo"]." AS initiales ";
				$query.="FROM ".$field_forum["tablename"]." forum, ".$field_forum["tablename"]." f_forum, ".$field_user["tablename"]." AS user ";
				$query.="WHERE forum.".$field_forum["uid_creat"]."=user.".$field_user["id"]." AND forum.".$field_forum["fid"]."=f_forum.".$field_forum["id"];
				$sql->Query($query);
		
				$col = 50;
				for($i=0; $i<$sql->rows; $i++)
				  {
					$sql->GetRow($i);
					$ok = true;
					foreach ($tabcrit as $crit)
					  {
					  	if ((!eregi($crit,$sql->data["titre"])) && (!eregi($crit,strip_tags($sql->data["corps"])))) { $ok = false; }
					  }
		
					if ($ok == true)
					  {
						$okok = true;
			
						$tmpl_x->assign("bgcolor",CalcColor($MyOpt["col_titre"]["value"],$col,$MyOpt["col_prg"]["value"]));
						$tmpl_x->assign("forum_msg",$sql->data["ftitre"]);
						$tmpl_x->assign("titre_msg",($sql->data["titre"]=="") ? "-" : $sql->data["titre"]);
						$tmpl_x->assign("pseudo_msg",($sql->data["uid_creat"]>0) ? strtoupper($sql->data["initiales"]) : $sql->data["pseudo"]);
						$tmpl_x->assign("date_msg",sql2date($sql->data["date_maj"]));
						$tmpl_x->assign("fid_msg",$sql->data["fid"]);
						$tmpl_x->assign("mid_msg",$sql->data["mid"]);
						
					  	$tmpl_x->parse("corps.resultat.lst_msg");
						$col = abs($col-110) ;
					  }
				  }
				if ($okok==false)
				  {
				  	$tmpl_x->parse("corps.resultat.noresponse");
				  }
		
			  	$tmpl_x->parse("corps.resultat");
			  }
	  }

// ---- Affecte les variables d'affichage
	$tmpl_x->parse("icone");
	$icone=$tmpl_x->text("icone");
	$tmpl_x->parse("infos");
	$infos=$tmpl_x->text("infos");
	$tmpl_x->parse("corps");
	$corps=$tmpl_x->text("corps");

?>
